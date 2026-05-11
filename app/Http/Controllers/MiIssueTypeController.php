<?php

namespace App\Http\Controllers;

use App\Models\MiIssueType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MiIssueTypeController extends Controller
{
    public function index()
    {
        if (Auth::user()->role !== 'contractor') {
            abort(403);
        }

        $issueTypes = MiIssueType::withCount('activations')->orderBy('sort_order')->orderBy('name')->get();

        return view('admin.mi-issue-types', compact('issueTypes'));
    }

    public function store(Request $request)
    {
        if (Auth::user()->role !== 'contractor') {
            abort(403);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:mi_issue_types,name'],
        ]);

        $maxOrder = MiIssueType::max('sort_order') ?? 0;

        MiIssueType::create([
            'name' => $request->name,
            'is_active' => true,
            'sort_order' => $maxOrder + 1,
        ]);

        return back()->with('success', "Issue type \"{$request->name}\" added.");
    }

    public function toggleActive(MiIssueType $type)
    {
        if (Auth::user()->role !== 'contractor') {
            abort(403);
        }

        $type->update(['is_active' => !$type->is_active]);

        return back()->with('success', "\"{$type->name}\" " . ($type->is_active ? 'activated' : 'deactivated') . ".");
    }

    public function updateOrder(Request $request)
    {
        if (Auth::user()->role !== 'contractor') {
            abort(403);
        }

        $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer', 'exists:mi_issue_types,id'],
        ]);

        foreach ($request->order as $index => $id) {
            MiIssueType::where('id', $id)->update(['sort_order' => $index + 1]);
        }

        return back()->with('success', 'Order updated.');
    }

    public function destroy(MiIssueType $type)
    {
        if (Auth::user()->role !== 'contractor') {
            abort(403);
        }

        // Check if used
        if ($type->activations()->exists()) {
            return back()->with('error', "Cannot delete \"{$type->name}\" — it's used by " . $type->activations()->count() . " MI record(s). Deactivate it instead.");
        }

        $name = $type->name;
        $type->delete();

        return back()->with('success', "Issue type \"{$name}\" deleted.");
    }
}
