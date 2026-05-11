<?php

namespace App\Http\Controllers;

use App\Models\Meter;
use App\Models\MeterActivation;
use App\Models\MeterLog;
use App\Models\InstallationDetail;
use App\Models\Lot;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * Main reports page with tabs
     */
    public function index(Request $request)
    {
        if (Auth::user()->role !== 'contractor') {
            abort(403);
        }

        $tab = $request->get('tab', 'meters');
        $workers = User::where('role', 'worker')->orderBy('name')->get();

        // Lots with completion tracking
        $lots = Lot::withCount([
            'meters',
            'meters as completed_count' => fn($q) => $q->whereIn('status', ['installed', 'mi_complete']),
        ])->orderBy('id', 'desc')->get()->map(function ($lot) {
            $lot->completion_pct = $lot->meters_count > 0
                ? round(($lot->completed_count / $lot->meters_count) * 100)
                : 0;
            $lot->is_complete = $lot->meters_count > 0 && $lot->completion_pct === 100;
            return $lot;
        })->sortBy([
            ['is_complete', 'asc'],  // incomplete first
            ['id', 'desc'],
        ])->values();

        $data = match ($tab) {
            'workers'       => $this->workerPerformanceData($request),
            'mi'            => $this->miReportData($request),
            'installations' => $this->installationData($request),
            default         => $this->metersData($request),
        };

        return view('reports.index', array_merge($data, [
            'tab'     => $tab,
            'workers' => $workers,
            'lots'    => $lots,
            'filters' => $request->only(['worker_id', 'status', 'mi_status', 'search', 'date_from', 'date_to', 'lot_id', 'install_type']),
        ]));
    }

    // ─── TAB: All Meters ───────────────────────────────────────

    private function metersData(Request $request): array
    {
        $query = Meter::with(['group', 'currentWorker', 'activation', 'installationDetail']);

        if ($request->filled('worker_id'))  $query->whereIn('current_worker_id', (array) $request->worker_id);
        if ($request->filled('status'))     $query->whereIn('status', (array) $request->status);
        if ($request->filled('search'))     $query->where('meter_number', 'like', '%' . $request->search . '%');
        if ($request->filled('mi_status'))  $query->whereHas('activation', fn($q) => $q->whereIn('mi_status', (array) $request->mi_status));
        if ($request->filled('install_type')) $query->whereHas('installationDetail', fn($q) => $q->where('install_type', $request->install_type));
        if ($request->filled('lot_id'))     $query->whereIn('lot_id', (array) $request->lot_id);
        if ($request->filled('date_from'))  $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to'))    $query->whereDate('created_at', '<=', $request->date_to);

        return [
            'meters' => $query->orderBy('updated_at', 'desc')->paginate(50)->withQueryString(),
            'summary' => [
                'total'     => Meter::count(),
                'stock'     => Meter::where('status', 'stock')->count(),
                'assigned'  => Meter::where('status', 'assigned')->count(),
                'installed' => Meter::where('status', 'installed')->count(),
                'issue'     => Meter::where('status', 'issue')->count(),
            ],
        ];
    }

    // ─── TAB: Worker Performance ───────────────────────────────

    private function workerPerformanceData(Request $request): array
    {
        $query = User::where('role', 'worker')
            ->withCount([
                'assignments',
                'assignments as installed_count' => fn($q) => $q->whereHas('meter', fn($m) => $m->whereIn('status', ['installed', 'mi_complete', 'mi_issue'])),
            ]);

        if ($request->filled('worker_id')) $query->whereIn('id', (array) $request->worker_id);

        $workers = $query->orderBy('name')->get()->map(function ($worker) {
            $worker->held_count      = Meter::where('current_worker_id', $worker->id)->whereIn('status', ['assigned', 'installed', 'issue', 'return_pending'])->count();
            $worker->issue_count     = Meter::where('current_worker_id', $worker->id)->where('status', 'issue')->count();
            $worker->nsc_count       = InstallationDetail::where('worker_id', $worker->id)->where('install_type', 'nsc')->count();
            $worker->rpl_count       = InstallationDetail::where('worker_id', $worker->id)->where('install_type', 'replacement')->count();
            $worker->mi_pending      = MeterActivation::where('worker_id', $worker->id)->where('mi_status', 'pending')->count();
            $worker->mi_complete     = MeterActivation::where('worker_id', $worker->id)->where('mi_status', 'complete')->count();
            $worker->mi_issue        = MeterActivation::where('worker_id', $worker->id)->where('mi_status', 'issue')->count();
            $worker->job_not_found   = MeterActivation::where('worker_id', $worker->id)->where('mi_status', 'job_not_found')->count();
            return $worker;
        });

        return ['workerStats' => $workers];
    }

    // ─── TAB: MI Report ────────────────────────────────────────

    private function miReportData(Request $request): array
    {
        $query = MeterActivation::with(['meter.group', 'meter.installationDetail', 'worker']);

        if ($request->filled('worker_id'))  $query->whereIn('worker_id', (array) $request->worker_id);
        if ($request->filled('mi_status'))  $query->whereIn('mi_status', (array) $request->mi_status);
        if ($request->filled('search'))     $query->whereHas('meter', fn($q) => $q->where('meter_number', 'like', '%' . $request->search . '%'));
        if ($request->filled('install_type')) $query->whereHas('meter.installationDetail', fn($q) => $q->where('install_type', $request->install_type));
        if ($request->filled('lot_id'))     $query->whereHas('meter', fn($q) => $q->whereIn('lot_id', (array) $request->lot_id));
        if ($request->filled('date_from'))  $query->whereDate('updated_at', '>=', $request->date_from);
        if ($request->filled('date_to'))    $query->whereDate('updated_at', '<=', $request->date_to);

        return [
            'activations' => $query->orderByRaw("FIELD(mi_status, 'job_not_found', 'pending', 'issue', 'complete')")
                ->latest('updated_at')
                ->paginate(50)
                ->withQueryString(),
            'miSummary' => [
                'total'         => MeterActivation::count(),
                'pending'       => MeterActivation::where('mi_status', 'pending')->count(),
                'complete'      => MeterActivation::where('mi_status', 'complete')->count(),
                'issue'         => MeterActivation::where('mi_status', 'issue')->count(),
                'job_not_found' => MeterActivation::where('mi_status', 'job_not_found')->count(),
            ],
        ];
    }

    // ─── TAB: Installation Details ─────────────────────────────

    private function installationData(Request $request): array
    {
        $query = InstallationDetail::with(['meter.group', 'meter.activation', 'worker']);

        if ($request->filled('worker_id'))  $query->whereIn('worker_id', (array) $request->worker_id);
        if ($request->filled('search'))     $query->whereHas('meter', fn($q) => $q->where('meter_number', 'like', '%' . $request->search . '%'));
        if ($request->filled('install_type')) $query->where('install_type', $request->install_type);
        if ($request->filled('lot_id'))     $query->whereHas('meter', fn($q) => $q->whereIn('lot_id', (array) $request->lot_id));
        if ($request->filled('date_from'))  $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to'))    $query->whereDate('created_at', '<=', $request->date_to);

        return [
            'installations' => $query->latest()->paginate(50)->withQueryString(),
            'installSummary' => [
                'total'       => InstallationDetail::count(),
                'nsc'         => InstallationDetail::where('install_type', 'nsc')->count(),
                'replacement' => InstallationDetail::where('install_type', 'replacement')->count(),
            ],
        ];
    }

    // ─── INLINE ACTIONS ────────────────────────────────────────

    public function updateMeterStatus(Request $request, Meter $meter)
    {
        if (Auth::user()->role !== 'contractor') abort(403);

        $request->validate([
            'status' => 'required|in:stock,assigned,installed,issue,returned',
        ]);

        $oldStatus = $meter->status;
        $meter->update(['status' => $request->status]);

        MeterLog::create([
            'meter_id'  => $meter->id,
            'action'    => 'status_change',
            'user_id'   => Auth::id(),
            'remark'    => "Report: status changed from {$oldStatus} to {$request->status}.",
            'timestamp' => now(),
        ]);

        return back()->with('success', "Meter {$meter->meter_number} status updated to {$request->status}.");
    }

    public function updateMIStatus(Request $request, MeterActivation $activation)
    {
        if (Auth::user()->role !== 'contractor') abort(403);

        $request->validate([
            'mi_status' => 'required|in:pending,complete,issue,job_not_found',
        ]);

        $oldStatus = $activation->mi_status;
        $activation->update([
            'mi_status'    => $request->mi_status,
            'completed_at' => $request->mi_status === 'complete' ? now() : null,
        ]);

        if ($request->mi_status === 'complete') {
            $activation->meter->update(['status' => 'mi_complete']);
        } elseif ($request->mi_status === 'issue') {
            $activation->meter->update(['status' => 'mi_issue']);
        }

        \App\Models\MeterLog::create([
            'meter_id'  => $activation->meter_id,
            'action'    => 'mi_status_change',
            'user_id'   => Auth::id(),
            'remark'    => "Report: MI status changed from {$oldStatus} to {$request->mi_status}.",
            'timestamp' => now(),
        ]);

        return back()->with('success', "MI status updated to {$request->mi_status}.");
    }

    // ─── EXCEL (CSV) EXPORTS ───────────────────────────────────

    public function exportMeters(Request $request): StreamedResponse
    {
        if (Auth::user()->role !== 'contractor') abort(403);

        $query = Meter::with(['group', 'currentWorker', 'activation', 'installationDetail']);
        if ($request->filled('worker_id'))  $query->whereIn('current_worker_id', (array) $request->worker_id);
        if ($request->filled('status'))     $query->whereIn('status', (array) $request->status);
        if ($request->filled('search'))     $query->where('meter_number', 'like', '%' . $request->search . '%');
        if ($request->filled('mi_status'))  $query->whereHas('activation', fn($q) => $q->whereIn('mi_status', (array) $request->mi_status));
        if ($request->filled('lot_id'))     $query->whereIn('lot_id', (array) $request->lot_id);
        if ($request->filled('date_from'))  $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to'))    $query->whereDate('created_at', '<=', $request->date_to);

        $meters = $query->orderBy('meter_number')->get();

        return $this->streamCsv('meters_report_' . now()->format('Y-m-d') . '.csv', [
            'Meter Number', 'Type', 'Group', 'Worker', 'Status', 'MI Status', 'Request Number', 'CA Number', 'Old Meter Number', 'Mobile', 'Latitude', 'Longitude', 'Created At', 'Updated At',
        ], $meters->map(fn($m) => [
            $m->meter_number,
            strtoupper($m->installationDetail?->install_type ?? ''),
            $m->group?->name ?? 'N/A',
            $m->currentWorker?->name ?? 'Unassigned',
            ucfirst($m->status),
            $m->activation?->mi_status ? ucfirst(str_replace('_', ' ', $m->activation->mi_status)) : 'N/A',
            $m->installationDetail?->request_number ?? '',
            $m->installationDetail?->ca_number ?? '',
            $m->installationDetail?->old_meter_number ?? '',
            $m->installationDetail?->mobile_number ?? '',
            $m->installationDetail?->latitude ?? '',
            $m->installationDetail?->longitude ?? '',
            $m->created_at?->format('Y-m-d H:i'),
            $m->updated_at?->format('Y-m-d H:i'),
        ])->toArray());
    }

    public function exportWorkers(Request $request): StreamedResponse
    {
        if (Auth::user()->role !== 'contractor') abort(403);

        $data = $this->workerPerformanceData($request);

        return $this->streamCsv('worker_performance_' . now()->format('Y-m-d') . '.csv', [
            'Worker', 'Total Assigned', 'Currently Held', 'Installed', 'Issues', 'NSC', 'Replacement', 'MI Pending', 'MI Complete', 'MI Issue', 'Job Not Found',
        ], $data['workerStats']->map(fn($w) => [
            $w->name,
            $w->assignments_count,
            $w->held_count,
            $w->installed_count,
            $w->issue_count,
            $w->nsc_count,
            $w->rpl_count,
            $w->mi_pending,
            $w->mi_complete,
            $w->mi_issue,
            $w->job_not_found,
        ])->toArray());
    }

    public function exportMI(Request $request): StreamedResponse
    {
        if (Auth::user()->role !== 'contractor') abort(403);

        $query = MeterActivation::with(['meter.group', 'meter.installationDetail', 'worker']);
        if ($request->filled('worker_id'))  $query->whereIn('worker_id', (array) $request->worker_id);
        if ($request->filled('mi_status'))  $query->whereIn('mi_status', (array) $request->mi_status);
        if ($request->filled('search'))     $query->whereHas('meter', fn($q) => $q->where('meter_number', 'like', '%' . $request->search . '%'));
        if ($request->filled('lot_id'))     $query->whereHas('meter', fn($q) => $q->whereIn('lot_id', (array) $request->lot_id));
        if ($request->filled('date_from'))  $query->whereDate('updated_at', '>=', $request->date_from);
        if ($request->filled('date_to'))    $query->whereDate('updated_at', '<=', $request->date_to);

        $activations = $query->orderBy('updated_at', 'desc')->get();

        return $this->streamCsv('mi_report_' . now()->format('Y-m-d') . '.csv', [
            'Meter Number', 'Group', 'Worker', 'Type', 'MI Status', 'Request Number', 'CA Number', 'Old Meter Number', 'Mobile', 'Latitude', 'Longitude', 'Remark', 'Completed At', 'Updated At',
        ], $activations->map(fn($a) => [
            $a->meter->meter_number,
            $a->meter->group?->name ?? 'N/A',
            $a->worker->name,
            strtoupper($a->meter->installationDetail?->install_type ?? 'nsc'),
            ucfirst(str_replace('_', ' ', $a->mi_status)),
            $a->meter->installationDetail?->request_number ?? '',
            $a->meter->installationDetail?->ca_number ?? '',
            $a->meter->installationDetail?->old_meter_number ?? '',
            $a->meter->installationDetail?->mobile_number ?? '',
            $a->meter->installationDetail?->latitude ?? '',
            $a->meter->installationDetail?->longitude ?? '',
            $a->remark ?? '',
            $a->completed_at?->format('Y-m-d H:i') ?? '',
            $a->updated_at->format('Y-m-d H:i'),
        ])->toArray());
    }

    public function exportInstallations(Request $request): StreamedResponse
    {
        if (Auth::user()->role !== 'contractor') abort(403);

        $query = InstallationDetail::with(['meter.group', 'worker']);
        if ($request->filled('worker_id'))  $query->whereIn('worker_id', (array) $request->worker_id);
        if ($request->filled('search'))     $query->whereHas('meter', fn($q) => $q->where('meter_number', 'like', '%' . $request->search . '%'));
        if ($request->filled('lot_id'))     $query->whereHas('meter', fn($q) => $q->whereIn('lot_id', (array) $request->lot_id));
        if ($request->filled('date_from'))  $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to'))    $query->whereDate('created_at', '<=', $request->date_to);

        $details = $query->latest()->get();

        return $this->streamCsv('installation_details_' . now()->format('Y-m-d') . '.csv', [
            'Meter Number', 'Group', 'Worker', 'Type', 'Request Number', 'CA Number', 'Old Meter Number', 'Mobile Number', 'Latitude', 'Longitude', 'Remark', 'Date',
        ], $details->map(fn($d) => [
            $d->meter->meter_number,
            $d->meter->group?->name ?? 'N/A',
            $d->worker->name,
            strtoupper($d->install_type ?? 'nsc'),
            $d->request_number ?? '',
            $d->ca_number ?? '',
            $d->old_meter_number ?? '',
            $d->mobile_number ?? '',
            $d->latitude ?? '',
            $d->longitude ?? '',
            $d->remark ?? '',
            $d->created_at->format('Y-m-d H:i'),
        ])->toArray());
    }

    // ─── CSV helper ────────────────────────────────────────────

    private function streamCsv(string $filename, array $headers, array $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rows) {
            $handle = fopen('php://output', 'w');
            // UTF-8 BOM so Excel recognizes encoding
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($handle, $headers);
            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
