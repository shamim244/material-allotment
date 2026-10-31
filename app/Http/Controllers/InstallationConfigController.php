<?php

namespace App\Http\Controllers;

use App\Models\InstallationFieldConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InstallationConfigController extends Controller
{
    public function index()
    {
        if (Auth::user()->role !== 'contractor') {
            abort(403);
        }

        $configs = InstallationFieldConfig::orderBy('sort_order')->get();

        return view('admin.installation-config', compact('configs'));
    }

    public function update(Request $request)
    {
        if (Auth::user()->role !== 'contractor') {
            abort(403);
        }

        $request->validate([
            'fields' => ['required', 'array'],
            'fields.*.is_required' => ['nullable'],
            'fields.*.is_enabled' => ['nullable'],
        ]);

        foreach ($request->fields as $fieldId => $fieldData) {
            $config = InstallationFieldConfig::find($fieldId);
            if ($config) {
                $config->update([
                    'is_required' => isset($fieldData['is_required']),
                    'is_enabled' => isset($fieldData['is_enabled']),
                ]);
            }
        }

        return back()->with('success', 'Configuration updated successfully.');
    }
}
