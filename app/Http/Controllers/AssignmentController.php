<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Meter;
use App\Models\MeterGroup;
use App\Models\MeterLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AssignmentController extends Controller
{
    public function create()
    {
        // Only active workers
        $workers = User::where('role', 'worker')
            ->where('is_active', true)
            ->withCount(['assignments as active_meter_count' => function ($q) {
                $q->whereHas('meter', fn($m) => $m->where('status', 'assigned'));
            }])
            ->orderBy('name')
            ->get();

        // Groups with available stock meters
        $groups = MeterGroup::withCount(['meters' => function ($query) {
            $query->where('status', 'stock');
        }])->having('meters_count', '>', 0)->orderBy('name')->get();

        // All stock meters (for individual selection)
        $allStockMeters = Meter::where('status', 'stock')
            ->with('group')
            ->orderBy('meter_number')
            ->get();

        // Loose/ungrouped meters in stock
        $looseMeters = $allStockMeters->whereNull('group_id');

        // Recent assignments (last 15)
        $recentAssignments = Assignment::with(['meter', 'worker', 'assigner'])
            ->orderBy('created_at', 'desc')
            ->limit(15)
            ->get();

        return view('assignments.create', compact('workers', 'groups', 'allStockMeters', 'looseMeters', 'recentAssignments'));
    }

    /**
     * AJAX: Preview meters in a group (only stock status).
     */
    public function previewGroup(Request $request)
    {
        $request->validate(['group_id' => ['required', 'exists:meter_groups,id']]);

        $group = MeterGroup::find($request->group_id);
        $meters = $group->meters()
            ->where('status', 'stock')
            ->orderBy('meter_number')
            ->get(['id', 'meter_number', 'status']);

        return response()->json([
            'group_name' => $group->name,
            'group_type' => $group->type,
            'meters' => $meters,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'worker_id' => ['required', 'exists:users,id'],
            'assignment_type' => ['required', 'in:group,individual,loose'],
        ]);

        $worker = User::find($request->worker_id);
        $assignedCount = 0;
        $batchId = (string) \Illuminate\Support\Str::uuid();

        DB::transaction(function () use ($request, $worker, &$assignedCount, $batchId) {
            $metersToAssign = collect();

            if ($request->assignment_type === 'group') {
                // Specific meter IDs selected via checkboxes
                if ($request->has('meter_ids') && is_array($request->meter_ids) && count($request->meter_ids) > 0) {
                    $metersToAssign = Meter::whereIn('id', $request->meter_ids)
                        ->where('status', 'stock')
                        ->get();
                } else {
                    // Fallback: assign all stock meters in the group
                    $request->validate(['group_id' => ['required', 'exists:meter_groups,id']]);
                    $group = MeterGroup::find($request->group_id);
                    $metersToAssign = $group->meters()->where('status', 'stock')->get();
                }
            } elseif ($request->assignment_type === 'individual') {
                if ($request->has('individual_meter_ids') && is_array($request->individual_meter_ids) && count($request->individual_meter_ids) > 0) {
                    $metersToAssign = Meter::whereIn('id', $request->individual_meter_ids)
                        ->where('status', 'stock')
                        ->get();
                } else {
                    throw new \Exception('No meters selected.');
                }
            } elseif ($request->assignment_type === 'loose') {
                // Loose meter IDs selected via checkboxes
                if ($request->has('loose_meter_ids') && is_array($request->loose_meter_ids)) {
                    $metersToAssign = Meter::whereIn('id', $request->loose_meter_ids)
                        ->where('status', 'stock')
                        ->whereNull('group_id')
                        ->get();
                }
            }

            foreach ($metersToAssign as $meter) {
                $meter->update([
                    'status' => 'assigned',
                    'current_worker_id' => $worker->id,
                ]);

                Assignment::create([
                    'meter_id' => $meter->id,
                    'worker_id' => $worker->id,
                    'assigned_by' => Auth::id(),
                    'assigned_at' => now(),
                    'batch_id' => $batchId,
                ]);

                // Audit log
                MeterLog::create([
                    'meter_id' => $meter->id,
                    'action' => 'assigned',
                    'user_id' => Auth::id(),
                    'remark' => 'Assigned to ' . $worker->name,
                    'timestamp' => now(),
                ]);

                $assignedCount++;
            }
        });

        if ($assignedCount === 0) {
            return back()->withErrors(['error' => 'No meters were assigned. Check that meters are in stock.']);
        }

        try {
            if (\App\Models\Setting::where('key', 'email_assignment_enabled')->value('value') !== 'false') {
                // Determine lot name if group assignment
                $lotName = null;
                if ($request->assignment_type === 'group' && $request->has('group_id')) {
                    $lotName = \App\Models\MeterGroup::find($request->group_id)?->name;
                }
                \Illuminate\Support\Facades\Mail::to($worker->email)->send(new \App\Mail\NewAssignmentMail($worker->name, $assignedCount, $lotName));
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send assignment email: ' . $e->getMessage());
        }

        return redirect()->route('assignments.challan', ['worker' => $worker->id, 'batch' => $batchId])
            ->with('success', "{$assignedCount} meter(s) assigned to {$worker->name} successfully.");
    }

    /**
     * Print challan / receipt for a worker's assigned meters.
     * ?batch=UUID  → specific assignment batch (default if provided)
     * ?view=all    → all meters currently with this worker
     */
    public function challan(Request $request, User $worker)
    {
        if (Auth::user()->role !== 'contractor') abort(403);

        $batchId = $request->input('batch');

        if ($request->input('view') === 'all') {
            // All meters currently with this worker
            $meters = Meter::where('current_worker_id', $worker->id)
                ->whereIn('status', ['assigned', 'installed', 'issue'])
                ->with(['group', 'lot'])
                ->orderBy('group_id')
                ->orderBy('meter_number')
                ->get();
            $batchInfo = null;
        } elseif ($batchId) {
            // Specific batch
            $assignments = Assignment::where('batch_id', $batchId)
                ->where('worker_id', $worker->id)
                ->with('meter.group', 'meter.lot')
                ->get();

            $meters = $assignments->map->meter->filter();
            $batchInfo = [
                'id'   => $batchId,
                'date' => $assignments->first()?->assigned_at,
            ];
        } else {
            // Default: latest batch
            $latestBatch = Assignment::where('worker_id', $worker->id)
                ->whereNotNull('batch_id')
                ->orderBy('assigned_at', 'desc')
                ->first();

            if ($latestBatch) {
                return redirect()->route('assignments.challan', [
                    'worker' => $worker->id,
                    'batch'  => $latestBatch->batch_id,
                ]);
            }

            // No batches exist — show all
            $meters = Meter::where('current_worker_id', $worker->id)
                ->whereIn('status', ['assigned', 'installed', 'issue'])
                ->with(['group', 'lot'])
                ->orderBy('group_id')
                ->orderBy('meter_number')
                ->get();
            $batchInfo = null;
        }

        // Recent batches for this worker (for quick switching)
        $recentBatches = Assignment::where('worker_id', $worker->id)
            ->whereNotNull('batch_id')
            ->selectRaw('batch_id, MIN(assigned_at) as assigned_at, COUNT(*) as meter_count')
            ->groupBy('batch_id')
            ->orderBy('assigned_at', 'desc')
            ->limit(20)
            ->get();

        $groupCounts = $meters->groupBy(fn($m) => $m->group?->name ?? 'Ungrouped')->map->count();

        return view('assignments.challan', compact('worker', 'meters', 'groupCounts', 'batchInfo', 'recentBatches'));
    }

    /**
     * Revoke / Cancel an entire assignment batch (Rollback mistake assignment).
     */
    public function revokeBatch(Request $request, string $batchId)
    {
        if (Auth::user()->role !== 'contractor') abort(403);

        $assignments = Assignment::where('batch_id', $batchId)->with('meter', 'worker')->get();
        if ($assignments->isEmpty()) {
            return back()->withErrors(['error' => 'Assignment batch not found or already removed.']);
        }

        $worker = $assignments->first()->worker;
        $workerName = $worker ? $worker->name : 'Worker';

        $revokedCount = 0;
        $skippedInstalled = 0;

        DB::transaction(function () use ($assignments, $batchId, $workerName, &$revokedCount, &$skippedInstalled) {
            foreach ($assignments as $assignment) {
                $meter = $assignment->meter;
                if (!$meter) {
                    $assignment->delete();
                    continue;
                }

                // If meter is installed, protect it
                if (in_array($meter->status, ['installed', 'mi_complete'])) {
                    $skippedInstalled++;
                    continue;
                }

                // Return meter to stock
                $meter->status = 'stock';
                $meter->current_worker_id = null;
                $meter->save();

                // Log cancellation in MeterLog
                MeterLog::create([
                    'meter_id'  => $meter->id,
                    'action'    => 'batch_revoked',
                    'user_id'   => Auth::id(),
                    'remark'    => "Assignment batch {$batchId} to {$workerName} was revoked by admin. Returned to stock.",
                    'timestamp' => now(),
                ]);

                // Delete the assignment record
                $assignment->delete();
                $revokedCount++;
            }
        });

        $msg = "Assignment batch cancelled: {$revokedCount} meter(s) returned to stock from {$workerName}.";
        if ($skippedInstalled > 0) {
            $msg .= " ({$skippedInstalled} installed meter(s) were kept intact).";
        }

        return redirect()->route('assignments.create')->with('success', $msg);
    }
}
