<?php

namespace App\Http\Controllers;

use App\Models\Meter;
use App\Models\MeterActivation;
use App\Models\MeterGroup;
use App\Models\Assignment;
use App\Models\InstallationDetail;
use App\Models\ReturnRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'contractor') {
            return $this->contractorDashboard();
        }

        return $this->workerDashboard();
    }

    private function contractorDashboard()
    {
        $stats = [
            'total_meters' => Meter::count(),
            'in_stock' => Meter::where('status', 'stock')->count(),
            'assigned' => Meter::where('status', 'assigned')->count(),
            'installed' => Meter::where('status', 'installed')->count(),
            'issues' => Meter::where('status', 'issue')->count(),
            'returned' => Meter::where('status', 'returned')->count(),
            'return_pending' => Meter::where('status', 'return_pending')->count(),
            'total_workers' => User::where('role', 'worker')->count(),
            'pending_returns' => ReturnRequest::where('status', 'pending')->count(),
            'total_groups' => MeterGroup::count(),
            'mi_pending'       => MeterActivation::where('mi_status', 'pending')->count(),
            'mi_issue'         => MeterActivation::where('mi_status', 'issue')->count(),
            'mi_complete'      => MeterActivation::where('mi_status', 'complete')->count(),
            'mi_job_not_found' => MeterActivation::where('mi_status', 'job_not_found')->count(),
            'nsc_installs'     => InstallationDetail::where('install_type', 'nsc')->count(),
            'replacement_installs' => InstallationDetail::where('install_type', 'replacement')->count(),
        ];

        // Recent activity
        $recentAssignments = Assignment::with(['meter', 'worker'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $recentReturns = ReturnRequest::with(['meter', 'worker'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Workers with counts
        $workers = User::where('role', 'worker')
            ->withCount(['meters as assigned_count' => function ($q) {
                $q->where('status', 'assigned');
            }, 'meters as installed_count' => function ($q) {
                $q->where('status', 'installed');
            }, 'meters as issue_count' => function ($q) {
                $q->where('status', 'issue');
            }, 'meterActivations as mi_complete_count' => function ($q) {
                $q->where('mi_status', 'complete');
            }])
            ->get();

        // ── Chart Data ──────────────────────────────────────────

        // 1. Daily installation trend (last 14 days)
        $dailyTrend = Meter::where('status', 'installed')
            ->where('updated_at', '>=', now()->subDays(13)->startOfDay())
            ->selectRaw('DATE(updated_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date');

        $trendLabels = [];
        $trendData = [];
        for ($i = 13; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $trendLabels[] = now()->subDays($i)->format('d M');
            $trendData[] = $dailyTrend[$date] ?? 0;
        }

        // 2. Lot completion % (top 10 lots by meter count)
        $lotStats = \App\Models\Lot::withCount(['meters', 'meters as completed_count' => function ($q) {
                $q->whereIn('status', ['installed', 'mi_complete']);
            }])
            ->having('meters_count', '>', 0)
            ->orderBy('meters_count', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($lot) {
                return [
                    'label' => $lot->lot_number,
                    'pct' => round(($lot->completed_count / $lot->meters_count) * 100),
                ];
            });

        // 3. Worker comparison data (reuse $workers)
        $workerChartLabels = $workers->pluck('name')->toArray();
        $workerAssigned = $workers->pluck('assigned_count')->toArray();
        $workerInstalled = $workers->pluck('installed_count')->toArray();

        $chartData = [
            'trendLabels' => $trendLabels,
            'trendData' => $trendData,
            'lotLabels' => $lotStats->pluck('label')->toArray(),
            'lotData' => $lotStats->pluck('pct')->toArray(),
            'workerLabels' => $workerChartLabels,
            'workerAssigned' => $workerAssigned,
            'workerInstalled' => $workerInstalled,
            'statusLabels' => ['Stock', 'Assigned', 'Installed', 'Issue', 'Returned'],
            'statusData' => [$stats['in_stock'], $stats['assigned'], $stats['installed'], $stats['issues'], $stats['returned']],
        ];

        return view('dashboard.contractor', compact('stats', 'recentAssignments', 'recentReturns', 'workers', 'chartData'));
    }

    private function workerDashboard()
    {
        $workerId = Auth::id();

        $stats = [
            'assigned' => Meter::where('current_worker_id', $workerId)->where('status', 'assigned')->count(),
            'installed' => Meter::where('current_worker_id', $workerId)->where('status', 'installed')->count(),
            'issues' => Meter::where('current_worker_id', $workerId)->where('status', 'issue')->count(),
            'return_pending' => Meter::where('current_worker_id', $workerId)->where('status', 'return_pending')->count(),
            'mi_pending'       => MeterActivation::where('worker_id', $workerId)->where('mi_status', 'pending')->count(),
            'mi_issue'         => MeterActivation::where('worker_id', $workerId)->where('mi_status', 'issue')->count(),
            'mi_complete'      => MeterActivation::where('worker_id', $workerId)->where('mi_status', 'complete')->count(),
            'mi_job_not_found' => MeterActivation::where('worker_id', $workerId)->where('mi_status', 'job_not_found')->count(),
        ];

        $recentMeters = Meter::where('current_worker_id', $workerId)
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        return view('dashboard.worker', compact('stats', 'recentMeters'));
    }
}
