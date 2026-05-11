<?php

namespace App\Http\Controllers;

use App\Models\Lot;
use App\Models\Meter;
use App\Models\MeterGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LotController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->role !== 'contractor') abort(403);

        $query = Lot::withCount(['meters', 'groups'])
            ->orderBy('id', 'desc');

        // Search by lot number or description
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('lot_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $lots = $query->get()
            ->map(function ($lot) {
                $lot->stock_count = Meter::where('lot_id', $lot->id)->where('status', 'stock')->count();
                $lot->assigned_count = Meter::where('lot_id', $lot->id)->where('status', 'assigned')->count();
                $lot->installed_count = Meter::where('lot_id', $lot->id)->whereIn('status', ['installed', 'mi_complete'])->count();
                $lot->completion_pct = $lot->meters_count > 0
                    ? round(($lot->installed_count / $lot->meters_count) * 100)
                    : 0;
                $lot->is_complete = $lot->meters_count > 0 && $lot->completion_pct === 100;
                return $lot;
            })
            ->sortBy([
                ['is_complete', 'asc'],  // incomplete first
                ['id', 'desc'],
            ])->values();

        // Meters without a lot
        $unassignedCount = Meter::whereNull('lot_id')->count();

        return view('lots.index', compact('lots', 'unassignedCount'));
    }

    public function create()
    {
        if (Auth::user()->role !== 'contractor') abort(403);
        return view('lots.create');
    }

    public function store(Request $request)
    {
        if (Auth::user()->role !== 'contractor') abort(403);

        $request->validate([
            'description' => ['nullable', 'string', 'max:255'],
            'import_date' => ['nullable', 'date'],
        ]);

        $lot = Lot::create([
            'description' => $request->description,
            'import_date' => $request->import_date ?? now()->toDateString(),
        ]);

        return redirect()->route('lots.index')->with('success', "Lot {$lot->lot_number} created successfully.");
    }

    public function show(Lot $lot)
    {
        if (Auth::user()->role !== 'contractor') abort(403);

        $groups = $lot->groups()->withCount('meters')->get();
        $looseMeters = Meter::where('lot_id', $lot->id)->whereNull('group_id')->get();

        $stats = [
            'total'     => Meter::where('lot_id', $lot->id)->count(),
            'stock'     => Meter::where('lot_id', $lot->id)->where('status', 'stock')->count(),
            'assigned'  => Meter::where('lot_id', $lot->id)->where('status', 'assigned')->count(),
            'installed' => Meter::where('lot_id', $lot->id)->whereIn('status', ['installed', 'mi_complete'])->count(),
            'issue'     => Meter::where('lot_id', $lot->id)->where('status', 'issue')->count(),
        ];

        return view('lots.show', compact('lot', 'groups', 'looseMeters', 'stats'));
    }

    public function update(Request $request, Lot $lot)
    {
        if (Auth::user()->role !== 'contractor') abort(403);

        $request->validate([
            'description' => ['nullable', 'string', 'max:255'],
            'import_date' => ['nullable', 'date'],
        ]);

        $lot->update($request->only('description', 'import_date'));

        return back()->with('success', "Lot {$lot->lot_number} updated.");
    }

    public function assignExisting(Request $request)
    {
        if (Auth::user()->role !== 'contractor') abort(403);

        $request->validate([
            'lot_id' => ['required', 'exists:lots,id'],
        ]);

        $count = Meter::whereNull('lot_id')->update(['lot_id' => $request->lot_id]);

        return back()->with('success', "{$count} unassigned meters moved to lot.");
    }
}
