<?php

namespace App\Http\Controllers;

use App\Models\InstallationDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MapController extends Controller
{
    /**
     * Display a map showing all installed meters with non-null coordinates.
     */
    public function index(Request $request)
    {
        if (Auth::user()->role !== 'contractor') {
            abort(403);
        }

        $query = InstallationDetail::with(['meter', 'worker'])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude');

        // Apply filters
        if ($request->filled('worker_id')) {
            $query->where('worker_id', $request->worker_id);
        }

        if ($request->filled('install_type')) {
            $query->where('install_type', $request->install_type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $installations = $query->get()->map(function ($detail) {
            return [
                'id' => $detail->id,
                'latitude' => (float) $detail->latitude,
                'longitude' => (float) $detail->longitude,
                'install_type' => $detail->install_type,
                'request_number' => $detail->request_number ?? 'N/A',
                'ca_number' => $detail->ca_number ?? 'N/A',
                'old_meter_number' => $detail->old_meter_number ?? 'N/A',
                'remark' => $detail->remark ?? '',
                'created_at' => $detail->created_at->format('Y-m-d H:i'),
                'meter_id' => $detail->meter_id,
                'meter_number' => $detail->meter?->meter_number ?? 'N/A',
                'worker_name' => $detail->worker?->name ?? 'Unknown Worker',
            ];
        });

        $workers = User::where('role', 'worker')->where('is_active', true)->orderBy('name')->get();

        return view('admin.map.index', compact('installations', 'workers'));
    }
}
