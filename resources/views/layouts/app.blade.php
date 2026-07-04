<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Material Allotment') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        sidebar: {
                            bg: '#0f172a',
                            hover: '#1e293b',
                            active: '#1d4ed8',
                            text: '#94a3b8',
                            heading: '#475569',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        .sidebar-link {
            display: flex; align-items: center; gap: 10px;
            padding: 8px 12px; border-radius: 8px;
            font-size: 0.875rem; font-weight: 500;
            color: #94a3b8; transition: all 0.15s;
            text-decoration: none;
        }
        .sidebar-link:hover { background: #1e293b; color: #e2e8f0; }
        .sidebar-link.active { background: #1d4ed8; color: #fff; }
        .sidebar-link svg { flex-shrink: 0; width: 18px; height: 18px; }
        .sidebar-group-label {
            font-size: 0.65rem; font-weight: 700; letter-spacing: 0.08em;
            text-transform: uppercase; color: #475569;
            padding: 16px 12px 6px; margin: 0;
        }
    </style>

    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-50">

<div x-data="{ sidebarOpen: true, mobileSidebarOpen: false }" class="flex h-screen overflow-hidden">

    {{-- ============================================================ --}}
    {{-- SIDEBAR --}}
    {{-- ============================================================ --}}
    <!-- Desktop Sidebar -->
    <aside
        :class="sidebarOpen ? 'w-56' : 'w-16'"
        class="hidden lg:flex flex-col bg-[#0f172a] transition-all duration-300 ease-in-out shrink-0 overflow-hidden"
        style="min-height:100vh">

        <!-- Logo + Toggle -->
        <div class="flex items-center justify-between px-3 py-4 border-b border-white/10 shrink-0">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 min-w-0 overflow-hidden">
                <div class="w-7 h-7 rounded-lg bg-indigo-600 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <span x-show="sidebarOpen" x-transition:enter="transition-opacity duration-200" x-transition:enter-end="opacity-100" x-transition:leave-end="opacity-0" x-cloak
                    class="text-sm font-bold text-white truncate">Material</span>
            </a>
            <button @click="sidebarOpen = !sidebarOpen"
                class="shrink-0 p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-white/10 transition">
                <svg x-show="sidebarOpen" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7M18 19l-7-7 7-7"/>
                </svg>
                <svg x-show="!sidebarOpen" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M6 5l7 7-7 7"/>
                </svg>
            </button>
        </div>

        <!-- Nav Items -->
        <nav class="flex-1 overflow-y-auto py-3 px-2 space-y-0.5">

            {{-- Dashboard --}}
            <a href="{{ route('dashboard') }}"
               class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
               title="Dashboard">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span x-show="sidebarOpen" x-cloak class="truncate">Dashboard</span>
            </a>

            @if(Auth::user()->role === 'contractor')

                {{-- STOCK --}}
                <p class="sidebar-group-label" x-show="sidebarOpen" x-cloak>Inventory</p>
                <a href="{{ route('stock.index') }}"
                   class="sidebar-link {{ request()->routeIs('stock.*') ? 'active' : '' }}"
                   title="Stock">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    <span x-show="sidebarOpen" x-cloak class="truncate">Stock</span>
                </a>
                <a href="{{ route('lots.index') }}"
                   class="sidebar-link {{ request()->routeIs('lots.*') ? 'active' : '' }}"
                   title="Lots">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    <span x-show="sidebarOpen" x-cloak class="truncate">Lots</span>
                </a>

                {{-- OPERATIONS --}}
                <p class="sidebar-group-label" x-show="sidebarOpen" x-cloak>Operations</p>
                <a href="{{ route('assignments.create') }}"
                   class="sidebar-link {{ request()->routeIs('assignments.*') ? 'active' : '' }}"
                   title="Assign Meters">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    <span x-show="sidebarOpen" x-cloak class="truncate">Assign Meters</span>
                </a>
                <a href="{{ route('worker.dashboard') }}"
                   class="sidebar-link {{ request()->routeIs('worker.dashboard') ? 'active' : '' }}"
                   title="Manage Meters">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span x-show="sidebarOpen" x-cloak class="truncate">Manage Meters</span>
                </a>
                <a href="{{ route('returns.index') }}"
                   class="sidebar-link {{ request()->routeIs('returns.*') ? 'active' : '' }}"
                   title="Returns">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                    <span x-show="sidebarOpen" x-cloak class="truncate">Returns</span>
                </a>

                {{-- REPORTS --}}
                <p class="sidebar-group-label" x-show="sidebarOpen" x-cloak>Analytics</p>
                <a href="{{ route('reports.index') }}"
                   class="sidebar-link {{ request()->routeIs('reports.index') ? 'active' : '' }}"
                   title="Reports">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span x-show="sidebarOpen" x-cloak class="truncate">Reports</span>
                </a>
                <a href="{{ route('reports.custom') }}"
                   class="sidebar-link {{ request()->routeIs('reports.custom*') ? 'active' : '' }}"
                   title="Custom Report">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <span x-show="sidebarOpen" x-cloak class="truncate">Custom Report</span>
                </a>
                <a href="{{ route('accounting.index') }}"
                   class="sidebar-link {{ request()->routeIs('accounting.*') ? 'active' : '' }}"
                   title="Accounting">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span x-show="sidebarOpen" x-cloak class="truncate">Accounting</span>
                </a>
                <a href="{{ route('admin.map.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.map.*') ? 'active' : '' }}"
                   title="Installation Map">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                    <span x-show="sidebarOpen" x-cloak class="truncate">Map</span>
                </a>

                {{-- ADMIN --}}
                <p class="sidebar-group-label" x-show="sidebarOpen" x-cloak>Administration</p>
                <a href="{{ route('admin.users.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                   title="Users">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span x-show="sidebarOpen" x-cloak class="truncate">Users</span>
                </a>
                <a href="{{ route('admin.installation-config') }}"
                   class="sidebar-link {{ request()->routeIs('admin.installation-config') ? 'active' : '' }}"
                   title="Installation Config">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span x-show="sidebarOpen" x-cloak class="truncate">Config</span>
                </a>
                <a href="{{ route('admin.mi-issue-types') }}"
                   class="sidebar-link {{ request()->routeIs('admin.mi-issue-types') ? 'active' : '' }}"
                   title="MI Issue Types">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span x-show="sidebarOpen" x-cloak class="truncate">MI Issues</span>
                </a>

                {{-- SYSTEM --}}
                <p class="sidebar-group-label" x-show="sidebarOpen" x-cloak>System</p>
                <a href="{{ route('admin.settings.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}"
                   title="Email Settings">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span x-show="sidebarOpen" x-cloak class="truncate">Email Settings</span>
                </a>
                <a href="{{ route('admin.email-logs.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.email-logs.*') ? 'active' : '' }}"
                   title="Email Logs">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <span x-show="sidebarOpen" x-cloak class="truncate">Email Logs</span>
                </a>

            @endif

            @if(Auth::user()->role === 'worker')
                <p class="sidebar-group-label" x-show="sidebarOpen" x-cloak>My Work</p>
                <a href="{{ route('worker.dashboard') }}"
                   class="sidebar-link {{ request()->routeIs('worker.dashboard') ? 'active' : '' }}"
                   title="My Meters">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/></svg>
                    <span x-show="sidebarOpen" x-cloak class="truncate">My Meters</span>
                </a>
            @endif

            {{-- Both roles --}}
            <p class="sidebar-group-label" x-show="sidebarOpen" x-cloak>Field</p>
            <a href="{{ route('mi.index') }}"
               class="sidebar-link {{ request()->routeIs('mi.*') ? 'active' : '' }}"
               title="MI (Meter Installation)">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                <span x-show="sidebarOpen" x-cloak class="truncate">MI</span>
            </a>
            <a href="{{ route('action-required') }}"
               class="sidebar-link {{ request()->routeIs('action-required') ? 'active' : '' }}"
               title="Action Required">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span x-show="sidebarOpen" x-cloak class="truncate">Action Required</span>
            </a>

        </nav>

        <!-- User section at bottom -->
        <div class="shrink-0 border-t border-white/10 p-3">
            <div x-data="{ profileOpen: false }" class="relative">
                <button @click="profileOpen = !profileOpen"
                    class="w-full flex items-center gap-2.5 p-2 rounded-lg hover:bg-white/10 transition text-left">
                    <div class="w-7 h-7 rounded-full bg-indigo-600 flex items-center justify-center text-white text-xs font-bold shrink-0">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div x-show="sidebarOpen" x-cloak class="flex-1 min-w-0">
                        <div class="text-sm font-semibold text-white truncate">{{ Auth::user()->name }}</div>
                        <div class="text-xs text-slate-400 truncate capitalize">{{ Auth::user()->role }}</div>
                    </div>
                    <svg x-show="sidebarOpen" x-cloak class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/></svg>
                </button>

                <!-- Profile popup -->
                <div x-show="profileOpen" @click.away="profileOpen = false" x-cloak
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="opacity-0 translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="absolute bottom-full left-0 right-0 mb-1 bg-white rounded-xl shadow-2xl border border-gray-100 overflow-hidden z-50">
                    <div class="px-4 py-3 border-b border-gray-100">
                        <div class="text-sm font-semibold text-gray-900">{{ Auth::user()->name }}</div>
                        <div class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</div>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Profile Settings
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            Log Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    {{-- Mobile Sidebar Overlay --}}
    <div x-show="mobileSidebarOpen" x-cloak @click="mobileSidebarOpen = false"
         class="lg:hidden fixed inset-0 bg-black/60 z-40 backdrop-blur-sm"></div>

    <aside x-show="mobileSidebarOpen" x-cloak x-transition:enter="transition ease-out duration-200"
           x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
           x-transition:leave="transition ease-in duration-150" x-transition:leave-end="-translate-x-full"
           class="lg:hidden fixed inset-y-0 left-0 w-64 bg-[#0f172a] z-50 flex flex-col overflow-y-auto">
        <div class="flex items-center justify-between px-4 py-4 border-b border-white/10">
            <span class="text-sm font-bold text-white">Material Allotment</span>
            <button @click="mobileSidebarOpen = false" class="text-slate-400 hover:text-white p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        {{-- Same nav, always expanded --}}
        <nav class="flex-1 py-3 px-3 space-y-0.5">
            <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Dashboard
            </a>
            @if(Auth::user()->role === 'contractor')
                <p class="sidebar-group-label">Inventory</p>
                <a href="{{ route('stock.index') }}" class="sidebar-link {{ request()->routeIs('stock.*') ? 'active' : '' }}"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg> Stock</a>
                <a href="{{ route('lots.index') }}" class="sidebar-link {{ request()->routeIs('lots.*') ? 'active' : '' }}"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg> Lots</a>
                <p class="sidebar-group-label">Operations</p>
                <a href="{{ route('assignments.create') }}" class="sidebar-link {{ request()->routeIs('assignments.*') ? 'active' : '' }}"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg> Assign Meters</a>
                <a href="{{ route('worker.dashboard') }}" class="sidebar-link {{ request()->routeIs('worker.dashboard') ? 'active' : '' }}"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg> Manage Meters</a>
                <a href="{{ route('returns.index') }}" class="sidebar-link {{ request()->routeIs('returns.*') ? 'active' : '' }}"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg> Returns</a>
                <p class="sidebar-group-label">Analytics</p>
                <a href="{{ route('reports.index') }}" class="sidebar-link {{ request()->routeIs('reports.index') ? 'active' : '' }}"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg> Reports</a>
                <a href="{{ route('reports.custom') }}" class="sidebar-link {{ request()->routeIs('reports.custom*') ? 'active' : '' }}"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg> Custom Report</a>
                <a href="{{ route('accounting.index') }}" class="sidebar-link {{ request()->routeIs('accounting.*') ? 'active' : '' }}"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Accounting</a>
                <a href="{{ route('admin.map.index') }}" class="sidebar-link {{ request()->routeIs('admin.map.*') ? 'active' : '' }}"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg> Map</a>
                <p class="sidebar-group-label">Administration</p>
                <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg> Users</a>
                <a href="{{ route('admin.installation-config') }}" class="sidebar-link {{ request()->routeIs('admin.installation-config') ? 'active' : '' }}"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg> Config</a>
                <a href="{{ route('admin.mi-issue-types') }}" class="sidebar-link {{ request()->routeIs('admin.mi-issue-types') ? 'active' : '' }}"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg> MI Issues</a>
                <p class="sidebar-group-label">System</p>
                <a href="{{ route('admin.settings.index') }}" class="sidebar-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg> Email Settings</a>
                <a href="{{ route('admin.email-logs.index') }}" class="sidebar-link {{ request()->routeIs('admin.email-logs.*') ? 'active' : '' }}"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg> Email Logs</a>
            @endif
            @if(Auth::user()->role === 'worker')
                <p class="sidebar-group-label">My Work</p>
                <a href="{{ route('worker.dashboard') }}" class="sidebar-link {{ request()->routeIs('worker.dashboard') ? 'active' : '' }}"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/></svg> My Meters</a>
            @endif
            <p class="sidebar-group-label">Field</p>
            <a href="{{ route('mi.index') }}" class="sidebar-link {{ request()->routeIs('mi.*') ? 'active' : '' }}"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg> MI</a>
            <a href="{{ route('action-required') }}" class="sidebar-link {{ request()->routeIs('action-required') ? 'active' : '' }}"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg> Action Required</a>
        </nav>
        <div class="border-t border-white/10 p-3">
            <div class="px-2 py-1 text-sm text-slate-400 truncate">{{ Auth::user()->name }}</div>
            <form method="POST" action="{{ route('logout') }}" class="mt-1">
                @csrf
                <button type="submit" class="sidebar-link w-full text-red-400 hover:text-red-300 hover:bg-red-900/30">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Log Out
                </button>
            </form>
        </div>
    </aside>

    {{-- ============================================================ --}}
    {{-- MAIN CONTENT AREA --}}
    {{-- ============================================================ --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

        <!-- Top bar -->
        <header class="bg-white border-b border-gray-200 shrink-0 h-14 flex items-center px-4 gap-3">
            <!-- Mobile hamburger -->
            <button @click="mobileSidebarOpen = true"
                class="lg:hidden p-1.5 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            <!-- Page title / breadcrumb -->
            <div class="flex-1 min-w-0">
                @isset($header)
                    <div class="text-base font-semibold text-gray-900 truncate">{{ $header }}</div>
                @endisset
            </div>

            <!-- Global Search -->
            <div class="hidden sm:block" x-data="{
                query: '', results: [], open: false, loading: false, selectedIdx: -1, debounceTimer: null,
                search() {
                    clearTimeout(this.debounceTimer);
                    if (this.query.length < 2) { this.results = []; this.open = false; return; }
                    this.loading = true;
                    this.debounceTimer = setTimeout(() => {
                        fetch('/search?q=' + encodeURIComponent(this.query))
                            .then(r => r.json())
                            .then(data => { this.results = data; this.open = true; this.loading = false; this.selectedIdx = -1; })
                            .catch(() => { this.loading = false; });
                    }, 250);
                },
                go(url) { window.location.href = url; this.open = false; this.query = ''; },
                handleKey(e) {
                    if (e.key === 'ArrowDown') { e.preventDefault(); this.selectedIdx = Math.min(this.selectedIdx + 1, this.results.length - 1); }
                    else if (e.key === 'ArrowUp') { e.preventDefault(); this.selectedIdx = Math.max(this.selectedIdx - 1, 0); }
                    else if (e.key === 'Enter' && this.selectedIdx >= 0) { e.preventDefault(); this.go(this.results[this.selectedIdx].url); }
                    else if (e.key === 'Escape') { this.open = false; this.$refs.searchInput.blur(); }
                }
            }" @keydown.window.prevent.ctrl.k="$refs.searchInput.focus()"
               @keydown.window.prevent.meta.k="$refs.searchInput.focus()"
               @click.away="open = false">
                <div class="relative">
                    <svg class="absolute left-2.5 top-2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" x-ref="searchInput" x-model="query" @input="search()" @keydown="handleKey($event)" @focus="if(results.length) open = true"
                        class="w-48 pl-8 pr-3 py-1.5 text-sm rounded-lg border border-gray-200 bg-gray-50 focus:bg-white focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400 focus:w-64 transition-all placeholder:text-gray-400"
                        placeholder="Search… (Ctrl+K)">
                    <div x-show="loading" class="absolute right-2.5 top-2">
                        <svg class="animate-spin h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    </div>
                    <div x-show="open && results.length > 0" x-transition x-cloak
                         class="absolute right-0 mt-1 w-80 bg-white border border-gray-200 rounded-xl shadow-xl z-50 max-h-96 overflow-y-auto">
                        <template x-for="(item, idx) in results" :key="idx">
                            <a :href="item.url" @click.prevent="go(item.url)"
                               class="flex items-center gap-3 px-4 py-2.5 hover:bg-indigo-50 transition cursor-pointer border-b border-gray-50 last:border-0"
                               :class="{ 'bg-indigo-50': selectedIdx === idx }">
                                <span class="text-lg" x-text="item.icon"></span>
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-medium text-gray-900 truncate" x-text="item.label"></div>
                                    <div class="text-xs text-gray-500 truncate" x-text="item.sublabel"></div>
                                </div>
                                <span class="text-xs px-1.5 py-0.5 rounded-full bg-gray-100 text-gray-500 uppercase" x-text="item.type"></span>
                            </a>
                        </template>
                    </div>
                    <div x-show="open && results.length === 0 && query.length >= 2 && !loading" x-transition x-cloak
                         class="absolute right-0 mt-1 w-72 bg-white border border-gray-200 rounded-xl shadow-xl z-50 p-5 text-center text-sm text-gray-400">
                        No results for "<span x-text="query" class="font-medium text-gray-600"></span>"
                    </div>
                </div>
            </div>

            <!-- Impersonation warning -->
            @if(session()->has('impersonator_id'))
            <div class="hidden sm:flex items-center gap-2 bg-amber-50 border border-amber-200 rounded-lg px-3 py-1.5">
                <span class="text-xs text-amber-700 font-medium">Impersonating <strong>{{ Auth::user()->name }}</strong></span>
                <form method="POST" action="{{ route('admin.stop-impersonating') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-xs bg-amber-600 text-white px-2 py-0.5 rounded font-semibold hover:bg-amber-700 transition">Exit</button>
                </form>
            </div>
            @endif
        </header>

        <!-- Impersonation banner (mobile) -->
        @if(session()->has('impersonator_id'))
        <div class="sm:hidden bg-amber-500 text-white text-center py-2 px-4 text-xs font-medium flex items-center justify-center gap-3">
            <span>Impersonating <strong>{{ Auth::user()->name }}</strong></span>
            <form method="POST" action="{{ route('admin.stop-impersonating') }}" class="inline">
                @csrf
                <button type="submit" class="bg-white text-amber-700 px-2 py-0.5 rounded text-xs font-semibold">Exit</button>
            </form>
        </div>
        @endif

        <!-- Scrollable page content -->
        <main class="flex-1 overflow-y-auto">
            {{ $slot }}
        </main>

    </div>
</div>

@stack('scripts')
</body>
</html>
