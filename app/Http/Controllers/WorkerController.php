<?php

namespace App\Http\Controllers;

use App\Models\InstallationDetail;
use App\Models\InstallationFieldConfig;
use App\Models\InstallationImage;
use App\Models\Meter;
use App\Models\MeterActivation;
use App\Models\MeterLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WorkerController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user->role === 'contractor';

        // Build meter query based on role
        if ($isAdmin) {
            $statuses = ['assigned', 'issue'];
            if (! $request->filled('worker_id') || $request->worker_id === 'unassigned') {
                $statuses[] = 'stock';
            }
            $query = Meter::whereIn('status', $statuses);

            // Filter by worker or show unassigned
            if ($request->filled('worker_id')) {
                if ($request->worker_id === 'unassigned') {
                    $query->whereNull('current_worker_id');
                } else {
                    $query->where('current_worker_id', $request->worker_id);
                }
            }
        } else {
            $query = Meter::whereIn('status', ['assigned', 'issue'])
                ->where('current_worker_id', $user->id);
        }

        // Status filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Search by meter number
        if ($search = $request->input('search')) {
            $query->where('meter_number', 'like', "%{$search}%");
        }

        // Stats (scoped to the same user/filter context)
        $statsBase = clone $query;
        if ($isAdmin) {
            $statsQuery = Meter::query();
            if ($request->filled('worker_id') && $request->worker_id !== 'unassigned') {
                $statsQuery->where('current_worker_id', $request->worker_id);
            } elseif ($request->filled('worker_id') && $request->worker_id === 'unassigned') {
                $statsQuery->whereNull('current_worker_id');
            }
        } else {
            $statsQuery = Meter::where('current_worker_id', $user->id);
        }

        $stats = [
            'assigned' => (clone $statsQuery)->where('status', 'assigned')->count(),
            'installed' => (clone $statsQuery)->where('status', 'installed')->count(),
            'issues' => (clone $statsQuery)->where('status', 'issue')->count(),
            'return_pending' => (clone $statsQuery)->where('status', 'return_pending')->count(),
            'stock' => $isAdmin ? (clone $statsQuery)->where('status', 'stock')->count() : 0,
        ];

        $assignedMeters = $query->with(['group', 'currentWorker'])
            ->orderBy('updated_at', 'desc')
            ->paginate(24);

        $installedCount = $stats['installed'];

        // Fetch field configs for the installation form
        $fieldConfigs = InstallationFieldConfig::getConfig();

        // Workers list for admin filter dropdown
        $workers = $isAdmin
            ? User::where('role', 'worker')->where('is_active', true)->orderBy('name')->get()
            : collect();

        return view('worker.dashboard', compact('assignedMeters', 'installedCount', 'fieldConfigs', 'workers', 'stats'));
    }

    public function updateStatus(Request $request, Meter $meter)
    {
        $user = Auth::user();

        // Workers can only update their own meters; admins can update any
        if ($user->role === 'worker' && $meter->current_worker_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'status' => 'required|in:installed,issue',
            'remark' => 'nullable|string|max:255',
            'install_images' => ['nullable', 'array', 'max:10'],
            'install_images.*' => ['image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ]);

        // If marking as installed with details, validate installation fields
        if ($request->status === 'installed' && $request->install_mode === 'detailed') {
            $fieldConfigs = InstallationFieldConfig::getConfig();
            $installRules = [
                'install_type' => ['required', 'in:nsc,replacement'],
            ];

            // Type-specific validation
            if ($request->install_type === 'replacement') {
                if ($fieldConfigs->has('ca_number') && $fieldConfigs['ca_number']->is_enabled) {
                    $installRules['ca_number'] = $fieldConfigs['ca_number']->is_required
                        ? ['required', 'string', 'max:100']
                        : ['nullable', 'string', 'max:100'];
                }
                if ($fieldConfigs->has('old_meter_number') && $fieldConfigs['old_meter_number']->is_enabled) {
                    $installRules['old_meter_number'] = $fieldConfigs['old_meter_number']->is_required
                        ? ['required', 'string', 'max:100']
                        : ['nullable', 'string', 'max:100'];
                }
            } else {
                // NSC: use fieldConfig for request_number
                if ($fieldConfigs->has('request_number') && $fieldConfigs['request_number']->is_enabled) {
                    $installRules['request_number'] = $fieldConfigs['request_number']->is_required
                        ? ['required', 'string', 'max:100']
                        : ['nullable', 'string', 'max:100'];
                }
            }

            // Shared fields
            if ($fieldConfigs->has('mobile_number') && $fieldConfigs['mobile_number']->is_enabled) {
                $installRules['mobile_number'] = $fieldConfigs['mobile_number']->is_required
                    ? ['required', 'string', 'max:20']
                    : ['nullable', 'string', 'max:20'];
            }

            if ($fieldConfigs->has('location') && $fieldConfigs['location']->is_enabled) {
                $locRule = $fieldConfigs['location']->is_required ? 'required' : 'nullable';
                $installRules['latitude'] = [$locRule, 'numeric', 'between:-90,90'];
                $installRules['longitude'] = [$locRule, 'numeric', 'between:-180,180'];
            }

            if ($fieldConfigs->has('remark') && $fieldConfigs['remark']->is_enabled) {
                $installRules['install_remark'] = $fieldConfigs['remark']->is_required
                    ? ['required', 'string', 'max:500']
                    : ['nullable', 'string', 'max:500'];
            }

            $request->validate($installRules);
        }

        // Quick mode: validate install_type and replacement fields
        if ($request->status === 'installed' && $request->install_mode === 'quick') {
            $quickRules = ['install_type' => ['nullable', 'in:nsc,replacement']];
            if ($request->install_type === 'replacement') {
                $fieldConfigs = InstallationFieldConfig::getConfig();
                if ($fieldConfigs->has('ca_number') && $fieldConfigs['ca_number']->is_enabled && $fieldConfigs['ca_number']->is_required) {
                    $quickRules['ca_number'] = ['required', 'string', 'max:100'];
                }
                if ($fieldConfigs->has('old_meter_number') && $fieldConfigs['old_meter_number']->is_enabled && $fieldConfigs['old_meter_number']->is_required) {
                    $quickRules['old_meter_number'] = ['required', 'string', 'max:100'];
                }
            }
            $request->validate($quickRules);
        }

        DB::transaction(function () use ($request, $meter, $user) {
            $oldStatus = $meter->status;

            $meter->update([
                'status' => $request->status,
            ]);

            MeterLog::create([
                'meter_id' => $meter->id,
                'action' => 'status_change',
                'user_id' => $user->id,
                'remark' => "Changed status from $oldStatus to {$request->status}. ".$request->remark,
                'timestamp' => now(),
            ]);

            // Save installation details if detailed mode
            if ($request->status === 'installed' && $request->install_mode === 'detailed') {
                InstallationDetail::create([
                    'meter_id' => $meter->id,
                    'worker_id' => $meter->current_worker_id ?? $user->id,
                    'install_type' => $request->install_type ?? 'nsc',
                    'request_number' => $request->install_type === 'nsc' ? $request->request_number : null,
                    'ca_number' => $request->install_type === 'replacement' ? $request->ca_number : null,
                    'old_meter_number' => $request->install_type === 'replacement' ? $request->old_meter_number : null,
                    'mobile_number' => $request->mobile_number,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'remark' => $request->install_remark,
                ]);
            }

            // Save install_type for quick mode (type + replacement fields only)
            if ($request->status === 'installed' && $request->install_mode === 'quick') {
                InstallationDetail::firstOrCreate(
                    ['meter_id' => $meter->id],
                    [
                        'worker_id' => $meter->current_worker_id ?? $user->id,
                        'install_type' => $request->install_type ?? 'nsc',
                        'ca_number' => $request->install_type === 'replacement' ? $request->ca_number : null,
                        'old_meter_number' => $request->install_type === 'replacement' ? $request->old_meter_number : null,
                    ]
                );
            }
            // Save installation images (both quick and detailed modes)
            if ($request->status === 'installed' && $request->hasFile('install_images')) {
                $detail = InstallationDetail::firstOrCreate(
                    ['meter_id' => $meter->id],
                    [
                        'worker_id' => $meter->current_worker_id ?? $user->id,
                        'install_type' => $request->install_type ?? 'nsc',
                        'ca_number' => $request->install_type === 'replacement' ? $request->ca_number : null,
                        'old_meter_number' => $request->install_type === 'replacement' ? $request->old_meter_number : null,
                    ]
                );

                foreach ($request->file('install_images') as $image) {
                    $path = $image->store('installation-images', 'public');
                    InstallationImage::create([
                        'installation_detail_id' => $detail->id,
                        'image_path' => $path,
                        'uploaded_by' => $user->id,
                    ]);
                }
            }

            // Auto-create MI record when meter is installed
            if ($request->status === 'installed') {
                MeterActivation::firstOrCreate(
                    ['meter_id' => $meter->id],
                    [
                        'worker_id' => $meter->current_worker_id ?? $user->id,
                        'mi_status' => 'pending',
                    ]
                );
            }
        });

        return back()->with('success', "Meter {$meter->meter_number} marked as {$request->status}.");
    }
}
