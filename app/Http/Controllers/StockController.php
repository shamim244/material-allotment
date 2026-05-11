<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\InstallationDetail;
use App\Models\Lot;
use App\Models\Meter;
use App\Models\MeterActivation;
use App\Models\MeterGroup;
use App\Models\MeterLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    /**
     * Stock Index — Browse all meters with search, filters, stats, pagination.
     */
    public function index(Request $request)
    {
        // Stats
        $stats = [
            'total' => Meter::count(),
            'stock' => Meter::where('status', 'stock')->count(),
            'assigned' => Meter::where('status', 'assigned')->count(),
            'installed' => Meter::where('status', 'installed')->count(),
            'issue' => Meter::where('status', 'issue')->count(),
            'returned' => Meter::where('status', 'returned')->count(),
        ];

        // Query builder
        $query = Meter::with(['group', 'currentWorker', 'lot']);

        // Search
        if ($search = $request->input('search')) {
            $query->where('meter_number', 'like', '%' . $search . '%');
        }

        // Filter by status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Filter by group
        if ($groupId = $request->input('group')) {
            if ($groupId === 'ungrouped') {
                $query->whereNull('group_id');
            } else {
                $query->where('group_id', $groupId);
            }
        }

        // Filter by lot
        if ($lotId = $request->input('lot')) {
            if ($lotId === 'unassigned') {
                $query->whereNull('lot_id');
            } else {
                $query->where('lot_id', $lotId);
            }
        }

        // Sort
        $sort = $request->input('sort', 'latest');
        if ($sort === 'meter_asc') {
            $query->orderBy('meter_number', 'asc');
        } elseif ($sort === 'meter_desc') {
            $query->orderBy('meter_number', 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $meters = $query->paginate(25)->withQueryString();

        // Groups for filter dropdown
        $groups = MeterGroup::withCount('meters')->orderBy('name')->get();

        // Lots for filter dropdown
        $lots = Lot::withCount('meters')->orderBy('id', 'desc')->get();

        // Workers for bulk assign dropdown
        $workers = User::where('role', 'worker')->where('is_active', true)->orderBy('name')->get();

        return view('stock.index', compact('stats', 'meters', 'groups', 'lots', 'workers'));
    }

    /**
     * Meter Detail — single meter with full history.
     */
    public function show(Meter $meter)
    {
        $meter->load([
            'group',
            'lot',
            'currentWorker',
            'assignments.worker',
            'assignments.assigner',
            'installationDetail.images.uploader',
            'activation.images',
            'activation.worker',
            'returnRequests.worker',
        ]);

        // Get logs ordered chronologically
        $logs = MeterLog::where('meter_id', $meter->id)
            ->with('user')
            ->orderBy('timestamp', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        // Workers for reassign dropdown
        $workers = User::where('role', 'worker')->where('is_active', true)->orderBy('name')->get();

        // Lots for change lot dropdown
        $lots = Lot::orderBy('id', 'desc')->get();

        return view('stock.show', compact('meter', 'logs', 'workers', 'lots'));
    }

    /**
     * Reassign meter to a different worker.
     */
    public function reassign(Request $request, Meter $meter)
    {
        $request->validate([
            'worker_id' => ['required', 'exists:users,id'],
        ]);

        $oldWorker = $meter->currentWorker;
        $newWorker = User::findOrFail($request->worker_id);

        DB::transaction(function () use ($meter, $newWorker, $oldWorker) {
            $meter->update([
                'current_worker_id' => $newWorker->id,
                'status' => 'assigned',
            ]);

            Assignment::create([
                'meter_id' => $meter->id,
                'worker_id' => $newWorker->id,
                'assigned_by' => Auth::id(),
                'assigned_at' => now(),
            ]);

            MeterLog::create([
                'meter_id' => $meter->id,
                'action' => 'reassigned',
                'user_id' => Auth::id(),
                'remark' => 'Reassigned from ' . ($oldWorker ? $oldWorker->name : 'none') . ' to ' . $newWorker->name,
                'timestamp' => now(),
            ]);
        });

        return back()->with('success', 'Meter reassigned to ' . $newWorker->name . '.');
    }

    /**
     * Change meter status (with full MI system integration).
     */
    public function changeStatus(Request $request, Meter $meter)
    {
        $request->validate([
            'status' => ['required', 'in:stock,assigned,installed,issue,returned'],
            // Optional installation details when marking as installed
            'request_number' => ['nullable', 'string', 'max:100'],
            'mobile_number' => ['nullable', 'string', 'max:20'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'install_remark' => ['nullable', 'string', 'max:500'],
        ]);

        $oldStatus = $meter->status;
        $newStatus = $request->status;

        if ($oldStatus === $newStatus) {
            return back()->with('info', 'Status is already ' . $newStatus . '.');
        }

        DB::transaction(function () use ($request, $meter, $oldStatus, $newStatus) {
            $meter->update(['status' => $newStatus]);

            // If changing to stock, clear the worker
            if ($newStatus === 'stock') {
                $meter->update(['current_worker_id' => null]);
            }

            // If marking as installed, create MI record + optional installation details
            if ($newStatus === 'installed') {
                MeterActivation::firstOrCreate(
                    ['meter_id' => $meter->id],
                    [
                        'worker_id' => $meter->current_worker_id ?? Auth::id(),
                        'mi_status' => 'pending',
                    ]
                );

                // Save installation details if any provided
                if ($request->filled('request_number') || $request->filled('mobile_number') || $request->filled('latitude')) {
                    InstallationDetail::updateOrCreate(
                        ['meter_id' => $meter->id],
                        [
                            'worker_id' => $meter->current_worker_id ?? Auth::id(),
                            'request_number' => $request->request_number,
                            'mobile_number' => $request->mobile_number,
                            'latitude' => $request->latitude,
                            'longitude' => $request->longitude,
                            'remark' => $request->install_remark,
                        ]
                    );
                }
            }

            MeterLog::create([
                'meter_id' => $meter->id,
                'action' => 'status_change',
                'user_id' => Auth::id(),
                'remark' => "Admin changed status from {$oldStatus} to {$newStatus}.",
                'timestamp' => now(),
            ]);
        });

        return back()->with('success', "Status changed from {$oldStatus} to {$newStatus}.");
    }

    /**
     * Update meter number.
     */
    public function update(Request $request, Meter $meter)
    {
        $request->validate([
            'meter_number' => ['required', 'string', 'max:50', 'unique:meters,meter_number,' . $meter->id],
        ]);

        $oldNumber = $meter->meter_number;
        $meter->update(['meter_number' => $request->meter_number]);

        // Log the change
        MeterLog::create([
            'meter_id' => $meter->id,
            'action' => 'meter_number_changed',
            'user_id' => Auth::id(),
            'remark' => "Meter number changed from {$oldNumber} to {$request->meter_number}",
            'timestamp' => now(),
        ]);

        return back()->with('success', 'Meter number updated successfully.');
    }

    /**
     * Change lot assignment for a meter.
     */
    public function changeLot(Request $request, Meter $meter)
    {
        if (Auth::user()->role !== 'contractor') abort(403);

        $request->validate([
            'lot_id' => ['nullable', 'exists:lots,id'],
        ]);

        $oldLot = $meter->lot;
        $newLotId = $request->lot_id ?: null;
        $meter->update(['lot_id' => $newLotId]);

        $newLot = $newLotId ? Lot::find($newLotId) : null;
        MeterLog::create([
            'meter_id'  => $meter->id,
            'action'    => 'lot_changed',
            'user_id'   => Auth::id(),
            'remark'    => 'Lot changed from ' . ($oldLot?->lot_number ?? 'None') . ' to ' . ($newLot?->lot_number ?? 'None'),
            'timestamp' => now(),
        ]);

        return back()->with('success', "Meter moved to " . ($newLot?->lot_number ?? 'no lot') . ".");
    }

    /**
     * Bulk delete stock meters (only meters with status = stock).
     */
    public function bulkDelete(Request $request)
    {
        if (Auth::user()->role !== 'contractor') abort(403);

        $request->validate([
            'meter_ids'   => ['required', 'array', 'min:1'],
            'meter_ids.*' => ['exists:meters,id'],
        ]);

        $meters = Meter::whereIn('id', $request->meter_ids)->where('status', 'stock')->get();
        if ($meters->isEmpty()) {
            return back()->withErrors(['error' => 'No stock meters selected for deletion.']);
        }

        $count = $meters->count();
        $numbers = $meters->pluck('meter_number')->join(', ');

        DB::transaction(function () use ($meters) {
            foreach ($meters as $meter) {
                MeterLog::create([
                    'meter_id'  => $meter->id,
                    'action'    => 'deleted',
                    'user_id'   => Auth::id(),
                    'remark'    => 'Bulk deleted from lot management.',
                    'timestamp' => now(),
                ]);
                $meter->delete();
            }
        });

        return back()->with('success', "{$count} stock meter(s) deleted.");
    }

    /**
     * Bulk change status of selected meters.
     */
    public function bulkStatusChange(Request $request)
    {
        if (Auth::user()->role !== 'contractor') abort(403);

        $request->validate([
            'meter_ids'   => ['required', 'array', 'min:1'],
            'meter_ids.*' => ['exists:meters,id'],
            'status'      => ['required', 'in:stock,assigned,installed,issue,returned'],
        ]);

        $meters = Meter::whereIn('id', $request->meter_ids)->get();
        $count = 0;

        DB::transaction(function () use ($meters, $request, &$count) {
            foreach ($meters as $meter) {
                if ($meter->status === $request->status) continue;

                $oldStatus = $meter->status;
                $meter->status = $request->status;

                // Clear worker if moving back to stock
                if ($request->status === 'stock') {
                    $meter->current_worker_id = null;
                }

                $meter->save();
                $count++;

                MeterLog::create([
                    'meter_id'  => $meter->id,
                    'action'    => 'status_changed',
                    'user_id'   => Auth::id(),
                    'remark'    => "Bulk status: {$oldStatus} → {$request->status}",
                    'timestamp' => now(),
                ]);
            }
        });

        return back()->with('success', "{$count} meter(s) status changed to {$request->status}.");
    }

    /**
     * Bulk assign selected meters to a worker.
     */
    public function bulkAssign(Request $request)
    {
        if (Auth::user()->role !== 'contractor') abort(403);

        $request->validate([
            'meter_ids'   => ['required', 'array', 'min:1'],
            'meter_ids.*' => ['exists:meters,id'],
            'worker_id'   => ['required', 'exists:users,id'],
        ]);

        $worker = User::findOrFail($request->worker_id);
        if ($worker->role !== 'worker') {
            return back()->withErrors(['error' => 'Invalid worker selected.']);
        }

        $meters = Meter::whereIn('id', $request->meter_ids)
            ->where('status', 'stock')
            ->get();

        if ($meters->isEmpty()) {
            return back()->withErrors(['error' => 'No stock meters selected. Only meters with "stock" status can be assigned.']);
        }

        $count = $meters->count();

        DB::transaction(function () use ($meters, $worker) {
            foreach ($meters as $meter) {
                $meter->update([
                    'status' => 'assigned',
                    'current_worker_id' => $worker->id,
                ]);

                Assignment::create([
                    'meter_id'  => $meter->id,
                    'worker_id' => $worker->id,
                ]);

                MeterLog::create([
                    'meter_id'  => $meter->id,
                    'action'    => 'assigned',
                    'user_id'   => Auth::id(),
                    'remark'    => "Bulk assigned to {$worker->name}",
                    'timestamp' => now(),
                ]);
            }
        });

        return back()->with('success', "{$count} meter(s) assigned to {$worker->name}.");
    }

    /**
     * Bulk export selected meters as CSV.
     */
    public function bulkExport(Request $request)
    {
        if (Auth::user()->role !== 'contractor') abort(403);

        $request->validate([
            'meter_ids'   => ['required', 'array', 'min:1'],
            'meter_ids.*' => ['exists:meters,id'],
        ]);

        $meters = Meter::whereIn('id', $request->meter_ids)
            ->with(['group', 'currentWorker', 'lot', 'installationDetail'])
            ->get();

        $filename = 'meters_export_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($meters) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Meter Number', 'Status', 'Group', 'Lot', 'Worker', 'Type', 'Request #', 'CA #', 'Mobile', 'Created']);

            foreach ($meters as $meter) {
                fputcsv($file, [
                    $meter->meter_number,
                    ucfirst($meter->status),
                    $meter->group?->name ?? '',
                    $meter->lot?->lot_number ?? '',
                    $meter->currentWorker?->name ?? '',
                    strtoupper($meter->installationDetail?->install_type ?? ''),
                    $meter->installationDetail?->request_number ?? '',
                    $meter->installationDetail?->ca_number ?? '',
                    $meter->installationDetail?->mobile_number ?? '',
                    $meter->created_at->format('Y-m-d H:i'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Delete meter (only if status = stock).
     */
    public function destroy(Meter $meter)
    {
        if ($meter->status !== 'stock') {
            return back()->withErrors(['error' => 'Only meters in stock can be deleted. This meter is currently: ' . $meter->status]);
        }

        $meterNumber = $meter->meter_number;
        $meter->delete();

        return redirect()->route('stock.index')->with('success', "Meter {$meterNumber} deleted successfully.");
    }

    /**
     * Groups listing page.
     */
    public function groups(Request $request)
    {
        $query = MeterGroup::withCount('meters')
            ->withCount([
                'meters as stock_count' => fn($q) => $q->where('status', 'stock'),
                'meters as assigned_count' => fn($q) => $q->where('status', 'assigned'),
                'meters as installed_count' => fn($q) => $q->where('status', 'installed'),
                'meters as issue_count' => fn($q) => $q->where('status', 'issue'),
                'meters as returned_count' => fn($q) => $q->where('status', 'returned'),
            ]);

        // Filter by type
        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }

        // Search
        if ($search = $request->input('search')) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        $groups = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        // Stats
        $stats = [
            'total' => MeterGroup::count(),
            'box' => MeterGroup::where('type', 'box')->count(),
            'large' => MeterGroup::where('type', 'large')->count(),
            'normal' => MeterGroup::where('type', 'normal')->count(),
        ];

        return view('stock.groups', compact('groups', 'stats'));
    }

    /**
     * Show single group with its meters.
     */
    public function groupShow(MeterGroup $group)
    {
        $group->load(['meters' => function ($q) {
            $q->with('currentWorker')->orderBy('meter_number');
        }]);

        return view('stock.group-show', compact('group'));
    }

    /**
     * Delete empty group.
     */
    public function groupDestroy(MeterGroup $group)
    {
        if ($group->meters()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete a group that still contains meters.']);
        }

        $groupName = $group->name;
        $group->delete();

        return redirect()->route('stock.groups')->with('success', "Group \"{$groupName}\" deleted successfully.");
    }
}
