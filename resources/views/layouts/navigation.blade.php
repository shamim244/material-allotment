<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 shadow-sm">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-14">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-8 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-1 sm:-my-px sm:ms-8 sm:flex items-center">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    @if(Auth::user()->role === 'contractor')
                        <x-nav-link :href="route('stock.index')" :active="request()->routeIs('stock.*')">
                            {{ __('Stock') }}
                        </x-nav-link>
                        <x-nav-link :href="route('lots.index')" :active="request()->routeIs('lots.*')">
                            {{ __('Lots') }}
                        </x-nav-link>

                        <!-- Operations Dropdown -->
                        <div class="relative" x-data="{ dropOpen: false }" @mouseenter="dropOpen = true" @mouseleave="dropOpen = false">
                            <button @click="dropOpen = !dropOpen"
                                class="inline-flex items-center gap-1 px-3 py-2 text-sm font-medium leading-5 transition
                                       {{ request()->routeIs('assignments.*') || request()->routeIs('returns.*') || request()->routeIs('worker.dashboard') ? 'text-indigo-700 border-b-2 border-indigo-400' : 'text-gray-500 hover:text-gray-700 border-b-2 border-transparent hover:border-gray-300' }}">
                                Operations
                                <svg class="w-3.5 h-3.5 transition-transform" :class="{ 'rotate-180': dropOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="dropOpen" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                                 x-cloak class="absolute left-0 mt-0 w-52 bg-white rounded-xl shadow-xl border border-gray-100 py-2 z-50">
                                <a href="{{ route('assignments.create') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm {{ request()->routeIs('assignments.*') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700 hover:bg-gray-50' }} transition">
                                    <span class="text-base">📋</span> Assign Meters
                                </a>
                                <a href="{{ route('worker.dashboard') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm {{ request()->routeIs('worker.dashboard') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700 hover:bg-gray-50' }} transition">
                                    <span class="text-base">⚙️</span> Manage Meters
                                </a>
                                <a href="{{ route('returns.index') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm {{ request()->routeIs('returns.*') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700 hover:bg-gray-50' }} transition">
                                    <span class="text-base">↩️</span> Returns
                                </a>
                            </div>
                        </div>

                        <!-- Admin Dropdown -->
                        <div class="relative" x-data="{ dropOpen: false }" @mouseenter="dropOpen = true" @mouseleave="dropOpen = false">
                            <button @click="dropOpen = !dropOpen"
                                class="inline-flex items-center gap-1 px-3 py-2 text-sm font-medium leading-5 transition
                                       {{ request()->routeIs('reports.*') || request()->routeIs('accounting.*') || request()->routeIs('admin.*') ? 'text-indigo-700 border-b-2 border-indigo-400' : 'text-gray-500 hover:text-gray-700 border-b-2 border-transparent hover:border-gray-300' }}">
                                Admin
                                <svg class="w-3.5 h-3.5 transition-transform" :class="{ 'rotate-180': dropOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="dropOpen" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                                 x-cloak class="absolute left-0 mt-0 w-52 bg-white rounded-xl shadow-xl border border-gray-100 py-2 z-50">
                                <a href="{{ route('reports.index') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm {{ request()->routeIs('reports.index') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700 hover:bg-gray-50' }} transition">
                                    <span class="text-base">📊</span> Reports
                                </a>
                                <a href="{{ route('reports.custom') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm {{ request()->routeIs('reports.custom*') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700 hover:bg-gray-50' }} transition">
                                    <span class="text-base">🔎</span> Custom Report
                                </a>
                                <a href="{{ route('accounting.index') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm {{ request()->routeIs('accounting.*') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700 hover:bg-gray-50' }} transition">
                                    <span class="text-base">💰</span> Accounting
                                </a>
                                <a href="{{ route('admin.map.index') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm {{ request()->routeIs('admin.map.*') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700 hover:bg-gray-50' }} transition">
                                    <span class="text-base">🗺️</span> Installation Map
                                </a>
                                <div class="border-t border-gray-100 my-1"></div>
                                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm {{ request()->routeIs('admin.users.*') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700 hover:bg-gray-50' }} transition">
                                    <span class="text-base">👥</span> Users
                                </a>
                                <a href="{{ route('admin.installation-config') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm {{ request()->routeIs('admin.installation-config') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700 hover:bg-gray-50' }} transition">
                                    <span class="text-base">⚙️</span> Settings
                                </a>
                                <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm {{ request()->routeIs('admin.settings.index') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700 hover:bg-gray-50' }} transition">
                                    <span class="text-base">📧</span> SMTP Setup
                                </a>
                                <a href="{{ route('admin.mi-issue-types') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm {{ request()->routeIs('admin.mi-issue-types') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700 hover:bg-gray-50' }} transition">
                                    <span class="text-base">🔧</span> MI Issues
                                </a>
                            </div>
                        </div>
                    @endif

                    @if(Auth::user()->role === 'worker')
                        <x-nav-link :href="route('worker.dashboard')" :active="request()->routeIs('worker.dashboard')">
                            {{ __('My Meters') }}
                        </x-nav-link>
                    @endif

                    <!-- MI: Both roles -->
                    <x-nav-link :href="route('mi.index')" :active="request()->routeIs('mi.*')">
                        {{ __('MI') }}
                    </x-nav-link>

                    <!-- Action Required: Both roles -->
                    <x-nav-link :href="route('action-required')" :active="request()->routeIs('action-required')">
                        <span class="flex items-center gap-1">🚨 {{ __('Action') }}</span>
                    </x-nav-link>
                </div>
            </div>

            <!-- Global Search + User Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-4 sm:gap-2">

                <!-- Search Bar -->
                <div class="relative" x-data="{
                    query: '',
                    results: [],
                    open: false,
                    loading: false,
                    selectedIdx: -1,
                    debounceTimer: null,
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
                            class="w-48 pl-8 pr-8 py-1.5 text-sm rounded-lg border border-gray-200 bg-gray-50 focus:bg-white focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400 focus:w-64 transition-all placeholder:text-gray-400"
                            placeholder="Search... (Ctrl+K)">
                        <div x-show="loading" class="absolute right-2.5 top-2">
                            <svg class="animate-spin h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        </div>
                    </div>

                    <!-- Results Dropdown -->
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

                    <!-- No Results -->
                    <div x-show="open && results.length === 0 && query.length >= 2 && !loading" x-transition x-cloak
                         class="absolute right-0 mt-1 w-80 bg-white border border-gray-200 rounded-xl shadow-xl z-50 p-6 text-center">
                        <div class="text-gray-400 text-sm">No results for "<span x-text="query" class="font-medium text-gray-600"></span>"</div>
                    </div>
                </div>

                <!-- User Dropdown -->
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-1 px-2 py-1.5 border border-transparent text-sm font-medium rounded-lg text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition">
                            <div class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs font-bold">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <span class="hidden lg:inline">{{ Auth::user()->name }}</span>
                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu (Mobile) -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                📊 {{ __('Dashboard') }}
            </x-responsive-nav-link>

            @if(Auth::user()->role === 'contractor')
                <x-responsive-nav-link :href="route('stock.index')" :active="request()->routeIs('stock.*')">
                    📦 {{ __('Stock') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('lots.index')" :active="request()->routeIs('lots.*')">
                    🏷️ {{ __('Lots') }}
                </x-responsive-nav-link>

                <div class="px-4 pt-3 pb-1"><div class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Operations</div></div>
                <x-responsive-nav-link :href="route('assignments.create')" :active="request()->routeIs('assignments.create')">
                    📋 {{ __('Assign Meters') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('worker.dashboard')" :active="request()->routeIs('worker.dashboard')">
                    ⚙️ {{ __('Manage Meters') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('returns.index')" :active="request()->routeIs('returns.index')">
                    ↩️ {{ __('Returns') }}
                </x-responsive-nav-link>

                <div class="px-4 pt-3 pb-1"><div class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Admin</div></div>
                <x-responsive-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.index')">
                    📊 {{ __('Reports') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('reports.custom')" :active="request()->routeIs('reports.custom*')">
                    🔎 {{ __('Custom Report') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('accounting.index')" :active="request()->routeIs('accounting.index')">
                    💰 {{ __('Accounting') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.map.index')" :active="request()->routeIs('admin.map.*')">
                    🗺️ {{ __('Installation Map') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                    👥 {{ __('Users') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.installation-config')" :active="request()->routeIs('admin.installation-config')">
                    ⚙️ {{ __('Settings') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.settings.index')" :active="request()->routeIs('admin.settings.index')">
                    📧 {{ __('SMTP Setup') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.mi-issue-types')" :active="request()->routeIs('admin.mi-issue-types')">
                    🔧 {{ __('MI Issues') }}
                </x-responsive-nav-link>
            @endif

            @if(Auth::user()->role === 'worker')
                <x-responsive-nav-link :href="route('worker.dashboard')" :active="request()->routeIs('worker.dashboard')">
                    📟 {{ __('My Meters') }}
                </x-responsive-nav-link>
            @endif

            <!-- MI: Both roles -->
            <x-responsive-nav-link :href="route('mi.index')" :active="request()->routeIs('mi.*')">
                ⚡ {{ __('MI') }}
            </x-responsive-nav-link>

            <!-- Action Required: Both roles -->
            <x-responsive-nav-link :href="route('action-required')" :active="request()->routeIs('action-required')">
                🚨 {{ __('Action Required') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
