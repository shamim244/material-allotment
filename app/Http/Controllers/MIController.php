<?php

namespace App\Http\Controllers;

use App\Models\InstallationDetail;
use App\Models\MeterActivation;
use App\Models\MeterActivationImage;
use App\Models\MiIssueType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MIController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = MeterActivation::with(['meter.installationDetail', 'worker', 'images', 'issueType']);

        // Role-based filtering
        if ($user->role === 'worker') {
            $query->where('worker_id', $user->id);
        } else {
            // Contractor: optional filter by worker
            if ($request->filled('worker_id')) {
                $query->where('worker_id', $request->worker_id);
            }
        }

        // Status filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('mi_status', $request->status);
        }

        // Search by meter number
        if ($request->filled('search')) {
            $query->whereHas('meter', function ($q) use ($request) {
                $q->where('meter_number', 'like', '%'.$request->search.'%');
            });
        }

        // Install type filter
        if ($request->filled('install_type') && $request->install_type !== 'all') {
            $query->whereHas('meter.installationDetail', function ($q) use ($request) {
                $q->where('install_type', $request->install_type);
            });
        }

        $activations = $query->orderByRaw("FIELD(mi_status, 'job_not_found', 'pending', 'issue', 'complete')")
            ->latest('updated_at')
            ->paginate(20);

        // Stats
        $statsQuery = MeterActivation::query();
        if ($user->role === 'worker') {
            $statsQuery->where('worker_id', $user->id);
        } elseif ($request->filled('worker_id')) {
            $statsQuery->where('worker_id', $request->worker_id);
        }

        $stats = [
            'total' => (clone $statsQuery)->count(),
            'pending' => (clone $statsQuery)->where('mi_status', 'pending')->count(),
            'issue' => (clone $statsQuery)->where('mi_status', 'issue')->count(),
            'complete' => (clone $statsQuery)->where('mi_status', 'complete')->count(),
            'job_not_found' => (clone $statsQuery)->where('mi_status', 'job_not_found')->count(),
        ];

        $workers = $user->role === 'contractor' ? User::where('role', 'worker')->get() : collect();

        return view('mi.index', compact('activations', 'stats', 'workers'));
    }

    public function show(MeterActivation $activation)
    {
        $user = Auth::user();

        if ($user->role === 'worker' && $activation->worker_id !== $user->id) {
            abort(403);
        }

        $activation->load(['meter', 'worker', 'images.uploader', 'issueType']);

        // Load installation details for this meter
        $installationDetail = InstallationDetail::where('meter_id', $activation->meter_id)
            ->with('images.uploader')
            ->first();

        $issueTypes = MiIssueType::active();

        return view('mi.show', compact('activation', 'installationDetail', 'issueTypes'));
    }

    public function update(Request $request, MeterActivation $activation)
    {
        $user = Auth::user();

        // Workers can only update their own
        if ($user->role === 'worker' && $activation->worker_id !== $user->id) {
            abort(403);
        }

        $request->validate([
            'mi_status' => ['required', 'in:pending,complete,issue,job_not_found'],
            'remark' => ['nullable', 'string', 'max:500'],
            'issue_type_id' => ['nullable', 'exists:mi_issue_types,id'],
            'issue_detail' => ['nullable', 'string', 'max:500'],
        ]);

        $oldMiStatus = $activation->mi_status;

        $updateData = [
            'mi_status' => $request->mi_status,
            'remark' => $request->remark,
            'completed_at' => $request->mi_status === 'complete' ? now() : null,
        ];

        // Handle issue type fields
        if ($request->mi_status === 'issue') {
            $updateData['issue_type_id'] = $request->issue_type_id;
            $updateData['issue_detail'] = $request->issue_type_id ? null : $request->issue_detail;
        } else {
            $updateData['issue_type_id'] = null;
            $updateData['issue_detail'] = null;
        }

        $activation->update($updateData);

        // Update meter status based on MI outcome
        if ($request->mi_status === 'complete') {
            $activation->meter->update(['status' => 'mi_complete']);
        } elseif ($request->mi_status === 'issue') {
            $activation->meter->update(['status' => 'mi_issue']);
        }
        // job_not_found: keep meter status as-is so it can be retried

        // Audit log
        \App\Models\MeterLog::create([
            'meter_id' => $activation->meter_id,
            'action' => 'mi_status_change',
            'user_id' => $user->id,
            'remark' => "MI status changed from {$oldMiStatus} to {$request->mi_status}.".($request->remark ? " Remark: {$request->remark}" : ''),
            'timestamp' => now(),
        ]);

        return back()->with('success', 'MI status updated to '.ucfirst($request->mi_status).'.');
    }

    public function uploadImages(Request $request, MeterActivation $activation)
    {
        $user = Auth::user();

        if ($user->role === 'worker' && $activation->worker_id !== $user->id) {
            abort(403);
        }

        $request->validate([
            'images' => ['required', 'array', 'min:1'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,webp', 'max:5120'], // 5MB max each
        ]);

        foreach ($request->file('images') as $image) {
            $path = $image->store('mi-images', 'public');

            MeterActivationImage::create([
                'meter_activation_id' => $activation->id,
                'image_path' => $path,
                'uploaded_by' => $user->id,
            ]);
        }

        return back()->with('success', 'Images uploaded successfully.');
    }

    public function deleteImage(MeterActivationImage $image)
    {
        $user = Auth::user();

        // Workers can only delete their own uploads
        if ($user->role === 'worker' && $image->uploaded_by !== $user->id) {
            abort(403);
        }

        // Delete file from storage
        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        return back()->with('success', 'Image deleted.');
    }

    public function updateInstallationDetails(Request $request, MeterActivation $activation)
    {
        $user = Auth::user();

        if ($user->role === 'worker' && $activation->worker_id !== $user->id) {
            abort(403);
        }

        $request->validate([
            'request_number' => ['nullable', 'string', 'max:100'],
            'mobile_number' => ['nullable', 'string', 'max:20'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'detail_remark' => ['nullable', 'string', 'max:500'],
        ]);

        InstallationDetail::updateOrCreate(
            ['meter_id' => $activation->meter_id],
            [
                'worker_id' => $activation->worker_id,
                'request_number' => $request->request_number,
                'mobile_number' => $request->mobile_number,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'remark' => $request->detail_remark,
            ]
        );

        return back()->with('success', 'Installation details updated.');
    }

    /**
     * Bulk update MI status for multiple activations at once.
     */
    public function bulkUpdateStatus(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'contractor') {
            abort(403);
        }

        $request->validate([
            'activation_ids' => ['required', 'array', 'min:1'],
            'activation_ids.*' => ['exists:meter_activations,id'],
            'mi_status' => ['required', 'in:pending,complete,issue,job_not_found'],
        ]);

        $count = 0;
        $activations = MeterActivation::whereIn('id', $request->activation_ids)->with('meter')->get();

        foreach ($activations as $activation) {
            $oldStatus = $activation->mi_status;
            if ($oldStatus === $request->mi_status) {
                continue;
            }

            $activation->update([
                'mi_status' => $request->mi_status,
                'completed_at' => $request->mi_status === 'complete' ? now() : null,
            ]);

            // Update meter status
            if ($request->mi_status === 'complete') {
                $activation->meter->update(['status' => 'mi_complete']);
            } elseif ($request->mi_status === 'issue') {
                $activation->meter->update(['status' => 'mi_issue']);
            }

            // Audit log
            \App\Models\MeterLog::create([
                'meter_id' => $activation->meter_id,
                'action' => 'mi_status_change',
                'user_id' => $user->id,
                'remark' => "Bulk MI status changed from {$oldStatus} to {$request->mi_status}.",
                'timestamp' => now(),
            ]);

            $count++;
        }

        return back()->with('success', "{$count} MI record(s) updated to ".ucfirst(str_replace('_', ' ', $request->mi_status)).'.');
    }
}
