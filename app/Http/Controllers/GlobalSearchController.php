<?php

namespace App\Http\Controllers;

use App\Models\Meter;
use App\Models\MeterGroup;
use App\Models\Lot;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GlobalSearchController extends Controller
{
    public function search(Request $request)
    {
        $q = trim($request->get('q', ''));

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $results = [];
        $like = '%' . $q . '%';

        // Meters — search meter_number
        $meters = Meter::where('meter_number', 'like', $like)
            ->limit(5)
            ->get();

        foreach ($meters as $meter) {
            $results[] = [
                'type' => 'meter',
                'icon' => '📟',
                'label' => $meter->meter_number,
                'sublabel' => ucfirst($meter->status),
                'url' => route('stock.show', $meter),
            ];
        }

        // Workers — search name, email
        if (Auth::user()->role === 'contractor') {
            $workers = User::where('role', 'worker')
                ->where(function ($query) use ($like) {
                    $query->where('name', 'like', $like)
                          ->orWhere('email', 'like', $like)
                          ->orWhere('mobile', 'like', $like);
                })
                ->limit(5)
                ->get();

            foreach ($workers as $worker) {
                $results[] = [
                    'type' => 'worker',
                    'icon' => '👷',
                    'label' => $worker->name,
                    'sublabel' => $worker->email,
                    'url' => route('workers.show', $worker),
                ];
            }

            // Lots — search lot_number
            $lots = Lot::where('lot_number', 'like', $like)
                ->withCount('meters')
                ->limit(5)
                ->get();

            foreach ($lots as $lot) {
                $results[] = [
                    'type' => 'lot',
                    'icon' => '📦',
                    'label' => $lot->lot_number,
                    'sublabel' => $lot->meters_count . ' meters',
                    'url' => route('lots.show', $lot),
                ];
            }

            // Groups — search name
            $groups = MeterGroup::where('name', 'like', $like)
                ->withCount('meters')
                ->limit(5)
                ->get();

            foreach ($groups as $group) {
                $results[] = [
                    'type' => 'group',
                    'icon' => '📁',
                    'label' => $group->name,
                    'sublabel' => $group->meters_count . ' meters',
                    'url' => route('stock.groups.show', $group),
                ];
            }
        }

        return response()->json($results);
    }
}
