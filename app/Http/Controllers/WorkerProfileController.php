<?php

namespace App\Http\Controllers;

use App\Models\Meter;
use App\Models\MeterActivation;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class WorkerProfileController extends Controller
{
    public function show(User $user)
    {
        if (Auth::user()->role !== 'contractor') {
            abort(403);
        }
        if ($user->role !== 'worker') {
            abort(404);
        }

        // Stats
        $meters = Meter::where('current_worker_id', $user->id);
        $stats = [
            'total_assigned' => $user->assignments()->count(),
            'held' => (clone $meters)->where('status', 'assigned')->count(),
            'installed' => (clone $meters)->whereIn('status', ['installed', 'mi_complete'])->count(),
            'issues' => (clone $meters)->where('status', 'issue')->count(),
            'returned' => Meter::whereHas('assignments', fn ($q) => $q->where('worker_id', $user->id))
                ->where('status', 'returned')->count(),
        ];

        // MI stats
        $miStats = [
            'pending' => MeterActivation::where('worker_id', $user->id)->where('mi_status', 'pending')->count(),
            'complete' => MeterActivation::where('worker_id', $user->id)->where('mi_status', 'complete')->count(),
            'issue' => MeterActivation::where('worker_id', $user->id)->where('mi_status', 'issue')->count(),
            'job_not_found' => MeterActivation::where('worker_id', $user->id)->where('mi_status', 'job_not_found')->count(),
        ];

        // Currently held meters
        $heldMeters = Meter::where('current_worker_id', $user->id)
            ->whereIn('status', ['assigned', 'issue'])
            ->with(['group', 'lot'])
            ->orderBy('updated_at', 'desc')
            ->get();

        // Recent installed meters
        $installedMeters = Meter::where('current_worker_id', $user->id)
            ->whereIn('status', ['installed', 'mi_complete', 'mi_issue'])
            ->with(['group', 'lot', 'activation', 'installationDetail'])
            ->orderBy('updated_at', 'desc')
            ->take(20)
            ->get();

        // Recent MI records
        $miRecords = MeterActivation::where('worker_id', $user->id)
            ->with(['meter.group', 'meter.installationDetail'])
            ->orderBy('updated_at', 'desc')
            ->take(15)
            ->get();

        return view('workers.show', compact('user', 'stats', 'miStats', 'heldMeters', 'installedMeters', 'miRecords'));
    }
}
