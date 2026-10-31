<?php

namespace App\Http\Controllers;

use App\Models\MeterActivation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActionRequiredController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user->role === 'contractor';

        // Base query: only non-complete MI records
        $query = MeterActivation::with(['meter.group', 'meter.installationDetail', 'worker', 'issueType'])
            ->whereIn('mi_status', ['job_not_found', 'issue', 'pending']);

        // Workers only see their own
        if (! $isAdmin) {
            $query->where('worker_id', $user->id);
        }

        // Worker filter (admin only)
        if ($isAdmin && $request->filled('worker_id')) {
            $query->where('worker_id', $request->worker_id);
        }

        // Tab filter
        $activeTab = $request->get('priority', 'all');
        if ($activeTab !== 'all') {
            $query->where('mi_status', $activeTab);
        }

        // Search
        if ($search = $request->input('search')) {
            $query->whereHas('meter', fn ($q) => $q->where('meter_number', 'like', "%{$search}%"));
        }

        // Stats (scoped to user)
        $statsQuery = MeterActivation::whereIn('mi_status', ['job_not_found', 'issue', 'pending']);
        if (! $isAdmin) {
            $statsQuery->where('worker_id', $user->id);
        } elseif ($request->filled('worker_id')) {
            $statsQuery->where('worker_id', $request->worker_id);
        }

        $stats = [
            'total' => (clone $statsQuery)->count(),
            'job_not_found' => (clone $statsQuery)->where('mi_status', 'job_not_found')->count(),
            'issue' => (clone $statsQuery)->where('mi_status', 'issue')->count(),
            'pending' => (clone $statsQuery)->where('mi_status', 'pending')->count(),
        ];

        // Order by priority: job_not_found > issue > pending, then oldest first
        $records = $query
            ->orderByRaw("FIELD(mi_status, 'job_not_found', 'issue', 'pending')")
            ->orderBy('updated_at', 'asc')
            ->paginate(30);

        // Workers list for admin filter
        $workers = $isAdmin
            ? User::where('role', 'worker')->where('is_active', true)->orderBy('name')->get()
            : collect();

        return view('action-required.index', compact('records', 'stats', 'workers', 'activeTab'));
    }
}
