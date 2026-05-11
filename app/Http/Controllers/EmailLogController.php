<?php

namespace App\Http\Controllers;

use App\Models\EmailLog;
use Illuminate\Http\Request;

class EmailLogController extends Controller
{
    public function index(Request $request)
    {
        $query = EmailLog::latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('to', 'like', '%' . $request->search . '%')
                  ->orWhere('subject', 'like', '%' . $request->search . '%')
                  ->orWhere('type', 'like', '%' . $request->search . '%');
            });
        }

        $logs    = $query->paginate(50)->withQueryString();
        $total   = EmailLog::count();
        $sent    = EmailLog::where('status', 'sent')->count();
        $failed  = EmailLog::where('status', 'failed')->count();

        return view('admin.email-logs.index', compact('logs', 'total', 'sent', 'failed'));
    }

    public function clear()
    {
        EmailLog::truncate();
        return back()->with('success', 'Email log cleared successfully.');
    }
}
