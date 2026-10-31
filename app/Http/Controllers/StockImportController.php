<?php

namespace App\Http\Controllers;

use App\Models\Lot;
use App\Models\Meter;
use App\Models\MeterGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockImportController extends Controller
{
    public function index()
    {
        $lots = Lot::orderBy('id', 'desc')->get();

        return view('stock.import', compact('lots'));
    }

    /**
     * Generate the 5 meter numbers for a given box start number.
     */
    private function generateBoxMeters(string $startMeter): ?array
    {
        preg_match('/(\d+)$/', $startMeter, $matches);
        if (! isset($matches[1])) {
            return null;
        }

        $numberPart = $matches[1];
        $prefix = substr($startMeter, 0, -strlen($numberPart));
        $lastDigit = intval(substr($numberPart, -1));

        if (! in_array($lastDigit, [1, 6])) {
            return null;
        }

        $meters = [];
        for ($i = 0; $i < 5; $i++) {
            $currentNum = intval($numberPart) + $i;
            $meters[] = $prefix.str_pad($currentNum, strlen($numberPart), '0', STR_PAD_LEFT);
        }

        return $meters;
    }

    /**
     * AJAX endpoint: Preview box meters and check which already exist.
     */
    public function previewBox(Request $request)
    {
        $request->validate([
            'start_meter_number' => ['required', 'string', 'regex:/^[A-Z0-9]+$/'],
        ]);

        $meters = $this->generateBoxMeters($request->start_meter_number);

        if ($meters === null) {
            return response()->json([
                'error' => 'Invalid box number. Must end with a digit 1 or 6.',
            ], 422);
        }

        // Check which already exist in DB
        $existing = Meter::whereIn('meter_number', $meters)->pluck('meter_number')->toArray();

        $result = [];
        foreach ($meters as $m) {
            $result[] = [
                'number' => $m,
                'exists' => in_array($m, $existing),
            ];
        }

        return response()->json([
            'meters' => $result,
            'all_exist' => count($existing) === count($meters),
        ]);
    }

    /**
     * Import selected meters from a box.
     */
    public function storeBox(Request $request)
    {
        $request->validate([
            'box_start' => ['required', 'string', 'regex:/^[A-Z0-9]+$/'],
            'selected_meters' => ['required', 'array', 'min:1'],
            'selected_meters.*' => ['string'],
            'lot_id' => ['nullable', 'exists:lots,id'],
        ]);

        $boxMeters = $this->generateBoxMeters($request->box_start);
        if ($boxMeters === null) {
            return back()->withErrors(['box_start' => 'Invalid box number.']);
        }

        // Only allow meters that belong to this box
        $selectedMeters = array_intersect($request->selected_meters, $boxMeters);
        if (empty($selectedMeters)) {
            return back()->withErrors(['selected_meters' => 'No valid meters selected.']);
        }

        // Double-check none of the selected already exist
        $alreadyExist = Meter::whereIn('meter_number', $selectedMeters)->pluck('meter_number')->toArray();
        $toImport = array_diff($selectedMeters, $alreadyExist);

        if (empty($toImport)) {
            return back()->withErrors(['selected_meters' => 'All selected meters already exist.']);
        }

        DB::transaction(function () use ($toImport, $boxMeters, $request) {
            $lotId = $request->lot_id;

            // Find or create the box group
            $boxName = 'Box '.$boxMeters[0].'-'.end($boxMeters);
            $group = MeterGroup::firstOrCreate(
                ['name' => $boxName, 'type' => 'box'],
            );
            if ($lotId && ! $group->lot_id) {
                $group->update(['lot_id' => $lotId]);
            }

            foreach ($toImport as $meterNumber) {
                Meter::create([
                    'meter_number' => $meterNumber,
                    'group_id' => $group->id,
                    'lot_id' => $lotId,
                    'status' => 'stock',
                ]);
            }
        });

        return back()->with('success', count($toImport).' meter(s) imported into box successfully.');
    }

    public function storeNormal(Request $request)
    {
        $request->validate([
            'meter_numbers' => ['required', 'string'],
            'lot_id' => ['nullable', 'exists:lots,id'],
        ]);

        $input = $request->meter_numbers;
        $meterNumbers = array_map('trim', explode(',', $input));
        $meterNumbers = array_filter($meterNumbers);

        if (empty($meterNumbers)) {
            return back()->withErrors(['meter_numbers' => 'No valid meter numbers found.']);
        }

        $existing = Meter::whereIn('meter_number', $meterNumbers)->pluck('meter_number')->toArray();
        if (! empty($existing)) {
            return back()->withErrors(['meter_numbers' => 'Meters already exist: '.implode(', ', $existing)]);
        }

        DB::transaction(function () use ($meterNumbers, $request) {
            foreach ($meterNumbers as $number) {
                Meter::create([
                    'meter_number' => $number,
                    'status' => 'stock',
                    'group_id' => null,
                    'lot_id' => $request->lot_id,
                ]);
            }
        });

        return back()->with('success', count($meterNumbers).' meters imported successfully.');
    }

    public function storeLarge(Request $request)
    {
        $request->validate([
            'start_meter' => ['required', 'string'],
            'end_meter' => ['required', 'string'],
            'lot_id' => ['nullable', 'exists:lots,id'],
        ]);

        $start = $request->start_meter;
        $end = $request->end_meter;

        preg_match('/^([A-Z]*)(\d+)$/', $start, $startMatches);
        preg_match('/^([A-Z]*)(\d+)$/', $end, $endMatches);

        if (! $startMatches || ! $endMatches || $startMatches[1] !== $endMatches[1]) {
            return back()->withErrors(['start_meter' => 'Start and End meters must have the same prefix and valid numeric format.']);
        }

        $prefix = $startMatches[1];
        $startNum = intval($startMatches[2]);
        $endNum = intval($endMatches[2]);
        $length = strlen($startMatches[2]);

        if ($startNum > $endNum) {
            return back()->withErrors(['start_meter' => 'Start meter must be less than End meter.']);
        }

        $total = $endNum - $startNum + 1;
        if ($total > 1000) {
            return back()->withErrors(['start_meter' => 'Range too large (max 1000).']);
        }

        // Generate all meter numbers in range
        $allMeters = [];
        for ($i = 0; $i < $total; $i++) {
            $allMeters[] = $prefix.str_pad($startNum + $i, $length, '0', STR_PAD_LEFT);
        }

        if (Meter::whereIn('meter_number', $allMeters)->exists()) {
            return back()->withErrors(['start_meter' => 'Some meters in this range already exist.']);
        }

        DB::transaction(function () use ($allMeters, $startNum, $prefix, $length, $request) {
            $lotId = $request->lot_id;

            // Group ALL meters into their correct box based on 1/6 rule.
            // Every meter belongs to a box. Box starts at last digit 1 or 6.
            //
            // Last digit mapping:
            // 1,2,3,4,5 → box starts at digit 1 (offset = lastDigit - 1)
            // 6,7,8,9,0 → box starts at digit 6 (offset = (lastDigit - 6 + 10) % 10)

            // Step 1: Compute box start for each meter and group by box
            $boxes = []; // boxStartNum => [meterNumbers]

            foreach ($allMeters as $index => $meterNumber) {
                $num = $startNum + $index;
                $lastDigit = $num % 10;

                if ($lastDigit >= 1 && $lastDigit <= 5) {
                    $boxStartNum = $num - ($lastDigit - 1);
                } else {
                    // 6,7,8,9,0
                    $offset = ($lastDigit - 6 + 10) % 10;
                    $boxStartNum = $num - $offset;
                }

                $boxes[$boxStartNum][] = $meterNumber;
            }

            // Step 2: Create a group for each box and import meters
            foreach ($boxes as $boxStartNum => $metersInBox) {
                $boxEndNum = $boxStartNum + 4;
                $boxStartStr = $prefix.str_pad($boxStartNum, $length, '0', STR_PAD_LEFT);
                $boxEndStr = $prefix.str_pad($boxEndNum, $length, '0', STR_PAD_LEFT);

                $group = MeterGroup::create([
                    'name' => 'Large Batch '.$boxStartStr.'-'.$boxEndStr,
                    'type' => 'large',
                    'lot_id' => $lotId,
                ]);

                foreach ($metersInBox as $meterNumber) {
                    Meter::create([
                        'meter_number' => $meterNumber,
                        'group_id' => $group->id,
                        'lot_id' => $lotId,
                        'status' => 'stock',
                    ]);
                }
            }
        });

        return back()->with('success', count($allMeters).' meters imported into box groups.');
    }
}
