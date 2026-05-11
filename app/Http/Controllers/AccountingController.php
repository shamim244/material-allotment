<?php

namespace App\Http\Controllers;

use App\Models\Meter;
use App\Models\Assignment;
use App\Models\InstallationDetail;
use App\Models\ReturnRequest;
use App\Models\MeterActivation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AccountingController extends Controller
{
    public function index()
    {
        if (Auth::user()->role !== 'contractor') {
            abort(403);
        }

        // ── Overall Stats ──
        $totalMeters = Meter::count();
        $inStock = Meter::where('status', 'stock')->count();
        $totalAssigned = Assignment::count();
        $totalReturned = ReturnRequest::where('status', 'approved')->count();
        $withWorkers = Meter::whereIn('status', ['assigned', 'installed', 'issue', 'return_pending'])->count();
        $totalInstalled = Meter::where('status', 'installed')->count();

        // ── Installation Type Breakdown ──
        $nscInstalls = InstallationDetail::where('install_type', 'nsc')->count();
        $replacementInstalls = InstallationDetail::where('install_type', 'replacement')->count();

        // ── MI Status Breakdown ──
        $miPending = MeterActivation::where('mi_status', 'pending')->count();
        $miComplete = MeterActivation::where('mi_status', 'complete')->count();
        $miIssue = MeterActivation::where('mi_status', 'issue')->count();
        $miJnf = MeterActivation::where('mi_status', 'job_not_found')->count();

        // ── Per-worker summary (with returned count) ──
        $workerSummary = DB::table('users')
            ->where('users.role', 'worker')
            ->where('users.is_active', true)
            ->leftJoin('meters', 'users.id', '=', 'meters.current_worker_id')
            ->leftJoin('installation_details', 'meters.id', '=', 'installation_details.meter_id')
            ->select(
                'users.id',
                'users.name',
                DB::raw("SUM(CASE WHEN meters.status IN ('assigned','installed','issue','return_pending') THEN 1 ELSE 0 END) as held_count"),
                DB::raw("SUM(CASE WHEN meters.status = 'assigned' THEN 1 ELSE 0 END) as assigned_count"),
                DB::raw("SUM(CASE WHEN meters.status IN ('installed','mi_complete') THEN 1 ELSE 0 END) as installed_count"),
                DB::raw("SUM(CASE WHEN meters.status = 'issue' THEN 1 ELSE 0 END) as issue_count"),
                DB::raw("SUM(CASE WHEN installation_details.install_type = 'nsc' THEN 1 ELSE 0 END) as nsc_count"),
                DB::raw("SUM(CASE WHEN installation_details.install_type = 'replacement' THEN 1 ELSE 0 END) as replacement_count")
            )
            ->groupBy('users.id', 'users.name')
            ->orderBy('users.name')
            ->get();

        // Add returned count per worker (from return_requests table)
        $returnedPerWorker = DB::table('return_requests')
            ->where('return_requests.status', 'approved')
            ->join('meters', 'return_requests.meter_id', '=', 'meters.id')
            ->join('assignments', function ($join) {
                $join->on('meters.id', '=', 'assignments.meter_id');
            })
            ->select('assignments.worker_id', DB::raw('COUNT(*) as returned_count'))
            ->groupBy('assignments.worker_id')
            ->pluck('returned_count', 'worker_id');

        // Ledger entries
        $ledger = DB::table('accounting_ledger')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return view('accounting.index', compact(
            'totalMeters', 'inStock', 'totalAssigned', 'totalReturned', 'withWorkers',
            'totalInstalled', 'nscInstalls', 'replacementInstalls',
            'miPending', 'miComplete', 'miIssue', 'miJnf',
            'workerSummary', 'returnedPerWorker', 'ledger'
        ));
    }
}
