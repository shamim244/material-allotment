<?php

namespace App\Http\Controllers;

use App\Models\Meter;
use App\Models\MeterLog;
use App\Models\ReturnRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReturnRequestController extends Controller
{
    // Contractor: List all return requests
    public function index(Request $request)
    {
        $query = ReturnRequest::with(['meter', 'worker']);

        // Status filter (default: pending)
        $status = $request->input('status', 'pending');
        if ($status !== 'all') {
            $query->where('return_requests.status', $status);
        }

        // Search by meter number or worker name
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('meter', fn($m) => $m->where('meter_number', 'like', "%{$search}%"))
                  ->orWhereHas('worker', fn($w) => $w->where('name', 'like', "%{$search}%"));
            });
        }

        // Stats
        $stats = [
            'total'    => ReturnRequest::count(),
            'pending'  => ReturnRequest::where('status', 'pending')->count(),
            'approved' => ReturnRequest::where('status', 'approved')->count(),
            'rejected' => ReturnRequest::where('status', 'rejected')->count(),
        ];

        $requests = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('returns.index', compact('requests', 'stats'));
    }

    // Worker: Submit a return request
    public function store(Request $request)
    {
        $request->validate([
            'meter_id' => 'required|exists:meters,id',
            'reason' => 'required|string|max:255',
        ]);

        $meter = Meter::findOrFail($request->meter_id);

        if (Auth::user()->role === 'worker' && $meter->current_worker_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $returnRequest = null;
        DB::transaction(function () use ($request, $meter, &$returnRequest) {
            // Create Return Request
            $returnRequest = ReturnRequest::create([
                'meter_id' => $meter->id,
                'worker_id' => Auth::id(),
                'reason' => $request->reason,
                'status' => 'pending',
            ]);

            // Update Meter Status
            $meter->update(['status' => 'return_pending']);

            // Log
            MeterLog::create([
                'meter_id' => $meter->id,
                'action' => 'return_requested',
                'user_id' => Auth::id(),
                'remark' => 'Reason: ' . $request->reason,
                'timestamp' => now(),
            ]);
        });

        if ($returnRequest) {
            try {
                if (\App\Models\Setting::where('key', 'email_return_enabled')->value('value') !== 'false') {
                    $contractors = \App\Models\User::where('role', 'contractor')->where('is_active', true)->get();
                    if ($contractors->isNotEmpty()) {
                        \Illuminate\Support\Facades\Mail::to($contractors)->send(new \App\Mail\ReturnRequestMail($returnRequest));
                    }
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send return request email: ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Return request submitted successfully.');
    }

    // Contractor: Approve or Reject
    public function update(Request $request, ReturnRequest $returnRequest)
    {
        // Check if user is contractor (middleware should handle this, but extra safety)
        if (Auth::user()->role !== 'contractor') {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:approved,rejected',
            'admin_remark' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request, $returnRequest) {
            $returnRequest->update([
                'status' => $request->status,
                'admin_remark' => $request->admin_remark,
            ]);

            $meter = $returnRequest->meter;

            if ($request->status === 'approved') {
                $meter->update([
                    'status' => 'returned',
                    'current_worker_id' => null, // Remove assignment
                ]);
                
                // TODO: Accounting Credit Logic here
                
                MeterLog::create([
                    'meter_id' => $meter->id,
                    'action' => 'return_approved',
                    'user_id' => Auth::id(),
                    'remark' => 'Approved. ' . $request->admin_remark,
                    'timestamp' => now(),
                ]);

            } else {
                // Rejected, revert status to assigned (or issue if it was issue before? Let's default to assigned/issue)
                // Ideally we'd revert to previous status, but 'assigned' is safe.
                $meter->update(['status' => 'assigned']);

                MeterLog::create([
                    'meter_id' => $meter->id,
                    'action' => 'return_rejected',
                    'user_id' => Auth::id(),
                    'remark' => 'Rejected. ' . $request->admin_remark,
                    'timestamp' => now(),
                ]);
            }
        });

        return back()->with('success', 'Return request updated.');
    }
}
