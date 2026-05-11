<?php

namespace App\Http\Controllers;

use App\Models\Meter;
use App\Models\MeterGroup;
use App\Models\MeterLog;
use App\Models\Lot;
use App\Models\User;
use App\Models\SavedReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CustomReportController extends Controller
{
    /**
     * Show the custom report input form.
     */
    public function index(Request $request)
    {
        if (Auth::user()->role !== 'contractor') abort(403);

        $savedReports = SavedReport::where('user_id', Auth::id())
            ->orderBy('updated_at', 'desc')
            ->get();

        // If loading a saved template
        $input = '';
        if ($request->filled('template')) {
            $template = SavedReport::where('id', $request->template)
                ->where('user_id', Auth::id())
                ->first();
            if ($template) {
                $input = $template->meter_numbers;
            }
        }

        return view('custom-report.index', array_merge($this->emptyState(), [
            'savedReports' => $savedReports,
            'input'        => $input,
            'loadedTemplate' => $request->template ?? null,
        ]));
    }

    /**
     * Generate the report from pasted meter numbers.
     */
    public function generate(Request $request)
    {
        if (Auth::user()->role !== 'contractor') abort(403);

        $request->validate([
            'meters' => 'required|string',
        ]);

        $parsed = $this->parseInput($request->meters);

        if (empty($parsed)) {
            return back()->with('error', 'No valid meter numbers found in input.');
        }

        [$meters, $missing] = $this->lookupMeters($parsed);

        $savedReports = SavedReport::where('user_id', Auth::id())
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('custom-report.index', array_merge(
            $this->buildViewData($meters, $missing, $request->meters),
            [
                'generated'      => true,
                'savedReports'   => $savedReports,
                'loadedTemplate' => null,
            ]
        ));
    }

    /**
     * Save current meter numbers as a template.
     */
    public function saveTemplate(Request $request)
    {
        if (Auth::user()->role !== 'contractor') abort(403);

        $request->validate([
            'name'   => 'required|string|max:100',
            'meters' => 'required|string',
        ]);

        SavedReport::create([
            'user_id'       => Auth::id(),
            'name'          => $request->name,
            'meter_numbers' => $request->meters,
        ]);

        return back()->with('success', "✅ Template \"{$request->name}\" saved!");
    }

    /**
     * Update an existing template's meter numbers.
     */
    public function updateTemplate(Request $request, SavedReport $savedReport)
    {
        if (Auth::user()->role !== 'contractor') abort(403);
        if ($savedReport->user_id !== Auth::id()) abort(403);

        $request->validate([
            'meters' => 'required|string',
        ]);

        $savedReport->update([
            'meter_numbers' => $request->meters,
        ]);

        return back()->with('success', "✅ Template \"{$savedReport->name}\" updated!");
    }

    /**
     * Delete a saved template.
     */
    public function deleteTemplate(SavedReport $savedReport)
    {
        if (Auth::user()->role !== 'contractor') abort(403);
        if ($savedReport->user_id !== Auth::id()) abort(403);

        $name = $savedReport->name;
        $savedReport->delete();

        return back()->with('success', "🗑️ Template \"{$name}\" deleted.");
    }

    /**
     * Export found meters as CSV (only found, not missing).
     */
    public function export(Request $request): StreamedResponse
    {
        if (Auth::user()->role !== 'contractor') abort(403);

        $parsed = $this->parseInput($request->meters);
        [$meters, $missing] = $this->lookupMeters($parsed);

        return $this->streamCsv('custom_report_' . now()->format('Y-m-d') . '.csv', [
            'Meter Number', 'Type', 'Group', 'Lot', 'Worker', 'Status', 'MI Status',
            'Request #', 'CA #', 'Old Meter #', 'Mobile', 'Latitude', 'Longitude',
            'Created At', 'Updated At',
        ], $meters->map(fn($m) => [
            $m->meter_number,
            strtoupper($m->installationDetail?->install_type ?? ''),
            $m->group?->name ?? '',
            $m->lot?->lot_number ?? '',
            $m->currentWorker?->name ?? 'Unassigned',
            ucfirst(str_replace('_', ' ', $m->status)),
            $m->activation?->mi_status ? ucfirst(str_replace('_', ' ', $m->activation->mi_status)) : '',
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

    /**
     * Import missing meter numbers into the system as stock.
     */
    public function importMissing(Request $request)
    {
        if (Auth::user()->role !== 'contractor') abort(403);

        $request->validate([
            'missing_meters' => 'required|string',
            'lot_id'         => 'nullable|exists:lots,id',
        ]);

        $numbers = $this->parseInput($request->missing_meters);
        $imported = 0;
        $skipped = 0;

        DB::beginTransaction();
        try {
            foreach ($numbers as $num) {
                if (Meter::where('meter_number', $num)->exists()) {
                    $skipped++;
                    continue;
                }

                $meter = Meter::create([
                    'meter_number' => $num,
                    'status'       => 'stock',
                    'lot_id'       => $request->lot_id ?: null,
                ]);

                MeterLog::create([
                    'meter_id'  => $meter->id,
                    'action'    => 'imported',
                    'user_id'   => Auth::id(),
                    'remark'    => 'Imported from Custom Report (missing meter).',
                    'timestamp' => now(),
                ]);

                $imported++;
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }

        $msg = "✅ {$imported} meter(s) imported as stock.";
        if ($skipped > 0) {
            $msg .= " ({$skipped} already existed, skipped.)";
        }

        return redirect()->route('reports.custom')->with('success', $msg);
    }

    /**
     * Bulk change status or lot of found meters.
     */
    public function bulkAction(Request $request)
    {
        if (Auth::user()->role !== 'contractor') abort(403);

        $request->validate([
            'meter_ids'   => 'required|array|min:1',
            'meter_ids.*' => 'exists:meters,id',
            'action'      => 'required|in:status,lot',
            'status'      => 'required_if:action,status|nullable|in:stock,assigned,installed,issue,returned',
            'lot_id'      => 'required_if:action,lot|nullable|exists:lots,id',
        ]);

        $meters = Meter::whereIn('id', $request->meter_ids)->get();
        $count = $meters->count();

        DB::beginTransaction();
        try {
            foreach ($meters as $meter) {
                if ($request->action === 'status') {
                    $old = $meter->status;
                    $meter->update(['status' => $request->status]);
                    MeterLog::create([
                        'meter_id'  => $meter->id,
                        'action'    => 'status_change',
                        'user_id'   => Auth::id(),
                        'remark'    => "Custom Report: status changed from {$old} to {$request->status}.",
                        'timestamp' => now(),
                    ]);
                } elseif ($request->action === 'lot') {
                    $meter->update(['lot_id' => $request->lot_id]);
                    MeterLog::create([
                        'meter_id'  => $meter->id,
                        'action'    => 'lot_change',
                        'user_id'   => Auth::id(),
                        'remark'    => "Custom Report: assigned to lot.",
                        'timestamp' => now(),
                    ]);
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Bulk action failed: ' . $e->getMessage());
        }

        $label = $request->action === 'status'
            ? "Status changed to " . ucfirst($request->status) . " for {$count} meter(s)."
            : "Lot assigned for {$count} meter(s).";

        return back()->with('success', "✅ {$label}");
    }

    // ─── Helpers ───────────────────────────────────────────────

    private function emptyState(): array
    {
        return [
            'meters'    => collect(),
            'missing'   => [],
            'input'     => '',
            'generated' => false,
            'stats'     => [],
            'workers'   => collect(),
            'lots'      => collect(),
        ];
    }

    private function buildViewData($meters, $missing, $input): array
    {
        $stats = ['total' => $meters->count(), 'missing' => count($missing)];

        $statusCounts = $meters->groupBy('status')->map->count();
        foreach (['stock', 'assigned', 'installed', 'issue', 'returned', 'mi_complete', 'mi_issue', 'return_pending'] as $s) {
            $stats[$s] = $statusCounts->get($s, 0);
        }

        $stats['nsc'] = $meters->filter(fn($m) => $m->installationDetail?->install_type === 'nsc')->count();
        $stats['replacement'] = $meters->filter(fn($m) => $m->installationDetail?->install_type === 'replacement')->count();

        $miCounts = $meters->filter(fn($m) => $m->activation)->groupBy(fn($m) => $m->activation->mi_status)->map->count();
        $stats['mi_pending']       = $miCounts->get('pending', 0);
        $stats['mi_complete_act']  = $miCounts->get('complete', 0);
        $stats['mi_issue_act']     = $miCounts->get('issue', 0);
        $stats['mi_jnf']           = $miCounts->get('job_not_found', 0);

        return [
            'meters'  => $meters,
            'missing' => $missing,
            'input'   => $input,
            'stats'   => $stats,
            'workers' => User::where('role', 'worker')->where('is_active', true)->orderBy('name')->get(),
            'lots'    => Lot::orderBy('id', 'desc')->get(),
        ];
    }

    private function parseInput(string $input): array
    {
        $numbers = preg_split('/[\s,;\|]+/', trim($input));
        $numbers = array_filter(array_map('trim', $numbers), fn($n) => $n !== '');
        return array_values(array_unique($numbers));
    }

    private function lookupMeters(array $numbers): array
    {
        $meters = Meter::with(['group', 'lot', 'currentWorker', 'activation', 'installationDetail'])
            ->whereIn('meter_number', $numbers)
            ->get();

        $foundNumbers = $meters->pluck('meter_number')->toArray();
        $missing = array_values(array_diff($numbers, $foundNumbers));

        return [$meters, $missing];
    }

    private function streamCsv(string $filename, array $headers, array $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rows) {
            $handle = fopen('php://output', 'w');
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
