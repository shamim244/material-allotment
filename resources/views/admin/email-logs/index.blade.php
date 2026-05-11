<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-2xl text-black leading-tight flex items-center gap-2">
                <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Email Activity Log
            </h2>
            <form method="POST" action="{{ route('admin.email-logs.clear') }}" onsubmit="return confirm('Clear all email logs?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-sm px-4 py-2 rounded-xl border border-red-200 bg-red-50 text-red-600 hover:bg-red-100 transition-colors">
                    Clear Log
                </button>
            </form>
        </div>
    </x-slot>

    <div class="py-10 bg-[#f8fafc] min-h-screen relative overflow-hidden text-black">
        <div class="absolute top-0 right-0 w-[700px] h-[700px] bg-indigo-50 rounded-full blur-[120px] -translate-y-1/2 translate-x-1/3 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-slate-100 rounded-full blur-[100px] translate-y-1/3 -translate-x-1/3 pointer-events-none"></div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6 relative z-10">

            @if(session('success'))
                <div class="p-4 flex items-center gap-3 text-sm rounded-2xl bg-white/60 backdrop-blur-xl border border-white/80 shadow-sm">
                    <svg class="w-5 h-5 text-green-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    {{ session('success') }}
                </div>
            @endif

            {{-- Stats Row --}}
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-white/60 backdrop-blur-xl border border-white/80 shadow-sm rounded-2xl p-5 flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-black">{{ number_format($total) }}</div>
                        <div class="text-xs text-gray-500 mt-0.5">Total Logged</div>
                    </div>
                </div>
                <div class="bg-white/60 backdrop-blur-xl border border-white/80 shadow-sm rounded-2xl p-5 flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-black">{{ number_format($sent) }}</div>
                        <div class="text-xs text-gray-500 mt-0.5">Sent Successfully</div>
                    </div>
                </div>
                <div class="bg-white/60 backdrop-blur-xl border border-white/80 shadow-sm rounded-2xl p-5 flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-black">{{ number_format($failed) }}</div>
                        <div class="text-xs text-gray-500 mt-0.5">Failed</div>
                    </div>
                </div>
            </div>

            {{-- Filters --}}
            <form method="GET" class="flex items-center gap-3">
                <div class="relative flex-1 max-w-sm">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/></svg>
                    <input name="search" type="text" value="{{ request('search') }}" placeholder="Search by recipient, subject, type…"
                           class="pl-9 pr-4 py-2.5 w-full rounded-xl border border-gray-200 bg-white/70 backdrop-blur-sm text-sm focus:border-indigo-400 focus:ring-indigo-400 transition-colors" />
                </div>
                <select name="status" class="rounded-xl border border-gray-200 bg-white/70 backdrop-blur-sm text-sm py-2.5 px-3 focus:border-indigo-400 focus:ring-indigo-400 transition-colors">
                    <option value="">All Status</option>
                    <option value="sent"   {{ request('status') === 'sent'   ? 'selected' : '' }}>Sent</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700 transition-colors">Filter</button>
                @if(request('search') || request('status'))
                    <a href="{{ route('admin.email-logs.index') }}" class="px-4 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-600 hover:bg-gray-50 transition-colors">Reset</a>
                @endif
            </form>

            {{-- Log Table --}}
            <div class="bg-white/60 backdrop-blur-2xl border border-white/80 shadow-[0_8px_30px_rgb(0,0,0,0.04)] rounded-3xl overflow-hidden">
                @if($logs->isEmpty())
                    <div class="py-20 text-center text-gray-400">
                        <svg class="w-12 h-12 mx-auto mb-4 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <p class="font-medium text-black">No email logs yet.</p>
                        <p class="text-sm mt-1">Emails sent by the system will appear here automatically.</p>
                    </div>
                @else
                    <table class="w-full text-sm text-left">
                        <thead>
                            <tr class="border-b border-gray-100 text-xs font-semibold text-gray-400 uppercase tracking-widest">
                                <th class="px-6 py-4">#</th>
                                <th class="px-6 py-4">To</th>
                                <th class="px-6 py-4">Subject</th>
                                <th class="px-6 py-4">Type</th>
                                <th class="px-6 py-4">Driver</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4">Sent At</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach ($logs as $log)
                                <tr class="hover:bg-white/50 transition-colors">
                                    <td class="px-6 py-4 text-gray-400 text-xs">{{ $log->id }}</td>
                                    <td class="px-6 py-4 font-medium text-black max-w-[180px] truncate" title="{{ $log->to }}">
                                        {{ $log->to }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-700 max-w-[220px] truncate" title="{{ $log->subject }}">
                                        {{ $log->subject ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($log->type)
                                            <span class="inline-block px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 text-xs font-medium">{{ $log->type }}</span>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-block px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600 text-xs font-mono">{{ $log->mailer }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($log->status === 'sent')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-green-50 text-green-700 text-xs font-semibold">
                                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Sent
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-red-50 text-red-700 text-xs font-semibold" title="{{ $log->error }}">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Failed
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-gray-400 text-xs whitespace-nowrap">
                                        {{ $log->created_at->format('d M Y, h:i A') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    @if($logs->hasPages())
                        <div class="px-6 py-4 border-t border-gray-100">
                            {{ $logs->links() }}
                        </div>
                    @endif
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
