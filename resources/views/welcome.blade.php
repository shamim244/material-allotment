<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>MeterSync | Streamline Materiel & Installations</title>
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
        
        <!-- Tailwind CSS (via CDN for standalone welcome page) -->
        <script src="https://cdn.tailwindcss.com"></script>
        
        <!-- Alpine.js -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        
        <!-- Custom Tailwind Config for the Landing -->
        <script>
            tailwind.config = {
                darkMode: 'media',
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['Inter', 'sans-serif'],
                        },
                        colors: {
                            brand: {
                                50: '#f0fdf4',
                                100: '#dcfce7',
                                200: '#bbf7d0',
                                300: '#86efac',
                                400: '#4ade80',
                                500: '#22c55e',
                                600: '#16a34a',
                                700: '#15803d',
                                800: '#166534',
                                900: '#14532d',
                                950: '#052e16',
                            }
                        },
                        animation: {
                            'blob': 'blob 7s infinite',
                            'fade-in-up': 'fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards',
                            'float': 'float 6s ease-in-out infinite',
                        },
                        keyframes: {
                            blob: {
                                '0%': { transform: 'translate(0px, 0px) scale(1)' },
                                '33%': { transform: 'translate(30px, -50px) scale(1.1)' },
                                '66%': { transform: 'translate(-20px, 20px) scale(0.9)' },
                                '100%': { transform: 'translate(0px, 0px) scale(1)' },
                            },
                            fadeInUp: {
                                '0%': { opacity: '0', transform: 'translateY(30px)' },
                                '100%': { opacity: '1', transform: 'translateY(0)' },
                            },
                            float: {
                                '0%, 100%': { transform: 'translateY(0)' },
                                '50%': { transform: 'translateY(-10px)' },
                            }
                        }
                    },
                },
            }
        </script>
        
        <style>
            /* Glassmorphism utilities */
            .glass {
                background: rgba(255, 255, 255, 0.75);
                backdrop-filter: blur(16px);
                -webkit-backdrop-filter: blur(16px);
                border-bottom: 1px solid rgba(255, 255, 255, 0.3);
            }
            @media (prefers-color-scheme: dark) {
                .glass {
                    background: rgba(15, 23, 42, 0.75);
                    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
                }
            }
            
            /* Custom Scrollbar */
            ::-webkit-scrollbar {
                width: 8px;
            }
            ::-webkit-scrollbar-track {
                background: transparent;
            }
            ::-webkit-scrollbar-thumb {
                background: #cbd5e1;
                border-radius: 4px;
            }
            @media (prefers-color-scheme: dark) {
                ::-webkit-scrollbar-thumb {
                    background: #334155;
                }
            }
        </style>
    </head>
    <body class="antialiased bg-slate-50 text-slate-900 dark:bg-slate-950 dark:text-white selection:bg-brand-500 selection:text-white overflow-x-hidden" x-data="{ scrolled: false }" @scroll.window="scrolled = (window.pageYOffset > 20)">

        <!-- Atmospheric Background Globs -->
        <div class="fixed inset-0 z-0 overflow-hidden pointer-events-none">
            <!-- Top Right Emerald Blob -->
            <div class="absolute top-0 right-0 -translate-y-12 translate-x-1/3 w-96 h-96 bg-brand-400/20 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob dark:bg-brand-500/10 dark:mix-blend-lighten"></div>
            <!-- Top Left Teal Blob -->
            <div class="absolute top-0 right-1/4 w-72 h-72 bg-teal-400/20 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob animation-delay-2000 dark:bg-teal-500/10 dark:mix-blend-lighten"></div>
            <!-- Bottom Left Blue Blob -->
            <div class="absolute -bottom-32 left-1/4 w-96 h-96 bg-blue-400/20 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob animation-delay-4000 dark:bg-blue-600/10 dark:mix-blend-lighten"></div>
        </div>

        <div class="relative z-10 min-h-screen flex flex-col">
            <!-- Navigation -->
            <nav :class="{ 'glass shadow-sm': scrolled, 'bg-transparent': !scrolled }" class="fixed w-full top-0 z-50 transition-all duration-300">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center h-20 transition-all duration-300" :class="{ 'h-16': scrolled }">
                        
                        <!-- Logo -->
                        <div class="flex-shrink-0 flex items-center gap-3 group cursor-pointer" onclick="window.scrollTo(0,0)">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-400 to-teal-600 flex items-center justify-center text-white font-bold shadow-lg shadow-brand-500/30 group-hover:scale-105 transition duration-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            </div>
                            <span class="font-bold text-xl tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-slate-900 to-slate-600 dark:from-white dark:to-slate-300">
                                MeterSync
                            </span>
                        </div>
                        
                        <!-- Middle Links (Desktop) -->
                        <div class="hidden md:flex space-x-8">
                            <a href="#features" class="text-sm font-medium text-slate-600 hover:text-brand-600 dark:text-slate-300 dark:hover:text-brand-400 transition">Features</a>
                            <a href="#dashboard-preview" class="text-sm font-medium text-slate-600 hover:text-brand-600 dark:text-slate-300 dark:hover:text-brand-400 transition">App Preview</a>
                        </div>

                        <!-- Right Actions -->
                        <div class="flex items-center gap-4">
                            @if (Route::has('login'))
                                @auth
                                    <a href="{{ url('/dashboard') }}" class="text-sm font-semibold text-slate-600 hover:text-brand-600 dark:text-slate-300 dark:hover:text-brand-400 transition hidden sm:block">Dashboard</a>
                                    <a href="{{ url('/dashboard') }}" class="text-sm font-semibold bg-brand-600 text-white px-5 py-2.5 rounded-full hover:bg-brand-500 transition shadow-lg shadow-brand-600/20">
                                        Enter App
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-600 hover:text-brand-600 dark:text-slate-300 dark:hover:text-brand-400 transition hidden sm:block">Log in</a>
                                    
                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}" class="text-sm font-semibold bg-slate-900 text-white dark:bg-white dark:text-slate-900 px-5 py-2.5 rounded-full hover:bg-brand-600 dark:hover:bg-brand-50 transition shadow-lg shadow-slate-900/20 dark:shadow-white/10">
                                            Get Started
                                        </a>
                                    @endif
                                @endauth
                            @endif
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Hero Section -->
            <main class="flex-grow flex items-center justify-center pt-32 pb-16 lg:pt-40 lg:pb-24">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
                    
                    <!-- Pill Badge -->
                    <div class="opacity-0 animate-fade-in-up" style="animation-delay: 0.1s;">
                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white dark:bg-slate-900 text-brand-600 dark:text-brand-400 text-xs font-semibold tracking-wide border border-slate-200 dark:border-slate-800 shadow-sm mb-8 mx-auto">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-brand-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-brand-500"></span>
                            </span>
                            Smart Inventory & Installations
                        </span>
                    </div>
                    
                    <!-- Main Headline -->
                    <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-extrabold tracking-tight opacity-0 animate-fade-in-up mb-6 md:mb-8" style="animation-delay: 0.2s;">
                        <span class="block text-slate-900 dark:text-white leading-tight">Streamline Your</span>
                        <span class="block text-transparent bg-clip-text bg-gradient-to-r from-brand-500 to-teal-500">
                            Field Operations
                        </span>
                    </h1>
                    
                    <!-- Subheadline -->
                    <p class="max-w-2xl mx-auto text-lg md:text-xl text-slate-600 dark:text-slate-400 opacity-0 animate-fade-in-up leading-relaxed" style="animation-delay: 0.3s;">
                        The ultimate platform for modern contractors. Assign stock, track meter installations, capture on-site images, and resolve issues—all in real-time.
                    </p>
                    
                    <!-- Call to Action Buttons -->
                    <div class="mt-10 flex flex-col sm:flex-row gap-4 justify-center items-center opacity-0 animate-fade-in-up" style="animation-delay: 0.4s;">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="w-full sm:w-auto px-8 py-4 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-full font-semibold shadow-xl shadow-slate-900/20 dark:shadow-white/10 hover:-translate-y-1 hover:shadow-2xl transition transform duration-200 text-lg">
                                Open Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="w-full sm:w-auto px-8 py-4 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-full font-semibold shadow-xl shadow-slate-900/20 dark:shadow-white/10 hover:-translate-y-1 transition transform duration-200 text-lg flex items-center justify-center gap-2 group">
                                Sign In to Portal
                                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                        @endauth
                    </div>
                </div>
            </main>

            <!-- Dashboard Mockup Image Section -->
            <div id="dashboard-preview" class="w-full max-w-6xl mx-auto px-4 sm:px-6 relative z-10 pb-24 opacity-0 animate-fade-in-up" style="animation-delay: 0.6s;">
                <!-- Decorative blur behind mockup -->
                <div class="absolute inset-0 bg-gradient-to-b from-brand-100/20 to-transparent dark:from-brand-900/20 rounded-[3rem] filter blur-3xl -z-10 transform scale-95 translate-y-8"></div>
                
                <!-- Mockup Container -->
                <div class="relative rounded-2xl md:rounded-[2rem] border border-slate-200/60 dark:border-slate-800/60 bg-white/40 dark:bg-slate-900/40 backdrop-blur-xl p-2 sm:p-4 shadow-2xl animate-float">
                    
                    <!-- Browser Chrome -->
                    <div class="rounded-xl md:rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 shadow-sm relative flex flex-col h-[500px]">
                        
                        <!-- Header Bar -->
                        <div class="h-12 border-b border-slate-100 dark:border-slate-800 flex items-center px-4 justify-between bg-slate-50 dark:bg-slate-900/80">
                            <div class="flex gap-2">
                                <div class="w-3 h-3 rounded-full bg-red-400"></div>
                                <div class="w-3 h-3 rounded-full bg-amber-400"></div>
                                <div class="w-3 h-3 rounded-full bg-green-400"></div>
                            </div>
                            <div class="hidden sm:flex h-7 w-64 bg-slate-200/50 dark:bg-slate-800/80 rounded-md items-center justify-center text-[10px] text-slate-500 font-mono tracking-wider">
                                <svg class="w-3 h-3 mr-1 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                app.metersync.com
                            </div>
                            <div class="w-10"></div> <!-- Spacer -->
                        </div>

                        <!-- App Content Mockup -->
                        <div class="flex-1 flex overflow-hidden">
                            <!-- Sidebar -->
                            <div class="w-64 border-r border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/30 p-4 hidden md:flex flex-col gap-6">
                                <div class="flex items-center gap-2 mb-4">
                                    <div class="w-8 h-8 rounded-lg bg-brand-500 flex items-center justify-center"><svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg></div>
                                    <div class="h-4 w-24 bg-slate-200 dark:bg-slate-700 rounded"></div>
                                </div>
                                <div class="space-y-3">
                                    <div class="h-8 w-full bg-white dark:bg-slate-800 rounded-md border border-slate-200 dark:border-slate-700"></div>
                                    <div class="h-8 w-3/4 bg-slate-200/50 dark:bg-slate-800/50 rounded-md mt-2"></div>
                                    <div class="h-8 w-4/5 bg-slate-200/50 dark:bg-slate-800/50 rounded-md"></div>
                                    <div class="h-8 w-2/3 bg-slate-200/50 dark:bg-slate-800/50 rounded-md"></div>
                                </div>
                            </div>
                            
                            <!-- Main Area -->
                            <div class="flex-1 p-6 md:p-8 bg-white dark:bg-slate-950 flex flex-col gap-6 overflow-hidden">
                                <!-- Top row stats -->
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    <div class="h-24 rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50 p-4 flex flex-col justify-between">
                                        <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/30 mb-2"></div>
                                        <div class="h-2 w-16 bg-slate-200 dark:bg-slate-700 rounded mb-1"></div>
                                        <div class="h-4 w-10 bg-slate-300 dark:bg-slate-600 rounded"></div>
                                    </div>
                                    <div class="h-24 rounded-xl border border-brand-100 dark:border-brand-900/30 bg-brand-50 dark:bg-brand-900/10 p-4 flex flex-col justify-between">
                                        <div class="w-8 h-8 rounded-full bg-brand-200 dark:bg-brand-800/50 mb-2"></div>
                                        <div class="h-2 w-20 bg-brand-300 dark:bg-brand-700 rounded mb-1"></div>
                                        <div class="h-4 w-12 bg-brand-500 dark:bg-brand-500 rounded"></div>
                                    </div>
                                    <div class="h-24 rounded-xl border border-amber-100 dark:border-amber-900/30 bg-amber-50 dark:bg-amber-900/10 p-4 flex flex-col justify-between">
                                        <div class="w-8 h-8 rounded-full bg-amber-200 dark:bg-amber-800/50 mb-2"></div>
                                        <div class="h-2 w-14 bg-amber-300 dark:bg-amber-700 rounded mb-1"></div>
                                        <div class="h-4 w-8 bg-amber-500 dark:bg-amber-500 rounded"></div>
                                    </div>
                                    <div class="h-24 rounded-xl border border-rose-100 dark:border-rose-900/30 bg-rose-50 dark:bg-rose-900/10 p-4 flex flex-col justify-between hidden md:flex">
                                        <div class="w-8 h-8 rounded-full bg-rose-200 dark:bg-rose-800/50 mb-2"></div>
                                        <div class="h-2 w-12 bg-rose-300 dark:bg-rose-700 rounded mb-1"></div>
                                        <div class="h-4 w-6 bg-rose-500 dark:bg-rose-500 rounded"></div>
                                    </div>
                                </div>
                                
                                <!-- Table / List Area -->
                                <div class="flex-1 rounded-xl border border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900/50 overflow-hidden flex flex-col">
                                    <div class="h-12 border-b border-slate-100 dark:border-slate-800 flex items-center px-4 justify-between bg-slate-50/80 dark:bg-slate-900/80">
                                        <div class="w-1/4 h-3 bg-slate-200 dark:bg-slate-700 rounded"></div>
                                        <div class="flex gap-2">
                                            <div class="w-16 h-6 bg-slate-200 dark:bg-slate-700 rounded-md"></div>
                                            <div class="w-16 h-6 bg-brand-500 rounded-md"></div>
                                        </div>
                                    </div>
                                    <div class="p-4 flex flex-col gap-3">
                                        <!-- Rows -->
                                        @for ($i = 0; $i < 4; $i++)
                                        <div class="h-14 border border-slate-100 dark:border-slate-800 rounded-lg flex items-center px-4 justify-between">
                                            <div class="flex items-center gap-3 w-1/3">
                                                <div class="w-8 h-8 rounded bg-slate-100 dark:bg-slate-800"></div>
                                                <div class="space-y-1.5 w-full">
                                                    <div class="h-2.5 w-full max-w-[120px] bg-slate-200 dark:bg-slate-700 rounded"></div>
                                                    <div class="h-1.5 w-20 bg-slate-100 dark:bg-slate-800 rounded"></div>
                                                </div>
                                            </div>
                                            <div class="w-16 h-5 rounded-full {{ $i == 1 ? 'bg-amber-100 dark:bg-amber-900/40' : ($i == 3 ? 'bg-rose-100 dark:bg-rose-900/40' : 'bg-brand-100 dark:bg-brand-900/40') }}"></div>
                                            <div class="w-8 h-8 rounded-full bg-slate-50 dark:bg-slate-800 hidden sm:block"></div>
                                        </div>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Advanced Features Section (Interactive Toggle) -->
            <section id="features" class="py-24 bg-slate-50 dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800 relative overflow-hidden" x-data="{ activeTab: 1 }">
                <!-- Background decoration -->
                <div class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/2 w-[800px] h-[800px] bg-brand-50 dark:bg-brand-900/10 rounded-full blur-3xl -z-10"></div>
                
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                    <div class="text-center max-w-3xl mx-auto mb-16">
                        <h2 class="text-brand-600 dark:text-brand-400 font-semibold tracking-wide uppercase text-sm mb-3">Enterprise Capabilities</h2>
                        <h3 class="text-3xl md:text-5xl font-bold text-slate-900 dark:text-white mb-6">Designed for scale and precision</h3>
                        <p class="text-lg text-slate-600 dark:text-slate-400">MeterSync replaces spreadsheets and unorganized chat threads with a single source of truth for your entire hardware deployment lifecycle.</p>
                    </div>

                    <div class="flex flex-col lg:flex-row gap-12 items-start">
                        <!-- Feature Tabs Navigation -->
                        <div class="w-full lg:w-1/3 flex flex-col gap-2">
                            <button @click="activeTab = 1" :class="activeTab === 1 ? 'bg-white dark:bg-slate-800 shadow-md border-brand-200 dark:border-brand-800' : 'bg-transparent border-transparent hover:bg-white/50 dark:hover:bg-slate-800/50'" class="text-left p-6 rounded-2xl border transition-all duration-300">
                                <div class="flex items-center gap-4 mb-2">
                                    <div :class="activeTab === 1 ? 'bg-brand-100 text-brand-600 dark:bg-brand-900/50 dark:text-brand-400' : 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400'" class="w-10 h-10 rounded-xl flex items-center justify-center transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                    </div>
                                    <h4 :class="activeTab === 1 ? 'text-slate-900 dark:text-white font-semibold' : 'text-slate-600 dark:text-slate-400 font-medium'" class="text-lg">Smart Inventory Tracking</h4>
                                </div>
                                <p :class="activeTab === 1 ? 'text-slate-600 dark:text-slate-300' : 'text-slate-500 dark:text-slate-500'" class="text-sm pl-14 hidden sm:block">Upload bulk serial numbers instantly. Group stock into 'Lots' for specific regions and track exactly what is resting in your warehouse.</p>
                            </button>

                            <button @click="activeTab = 2" :class="activeTab === 2 ? 'bg-white dark:bg-slate-800 shadow-md border-amber-200 dark:border-amber-800/50' : 'bg-transparent border-transparent hover:bg-white/50 dark:hover:bg-slate-800/50'" class="text-left p-6 rounded-2xl border transition-all duration-300">
                                <div class="flex items-center gap-4 mb-2">
                                    <div :class="activeTab === 2 ? 'bg-amber-100 text-amber-600 dark:bg-amber-900/50 dark:text-amber-400' : 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400'" class="w-10 h-10 rounded-xl flex items-center justify-center transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    </div>
                                    <h4 :class="activeTab === 2 ? 'text-slate-900 dark:text-white font-semibold' : 'text-slate-600 dark:text-slate-400 font-medium'" class="text-lg">Frictionless Assignments</h4>
                                </div>
                                <p :class="activeTab === 2 ? 'text-slate-600 dark:text-slate-300' : 'text-slate-500 dark:text-slate-500'" class="text-sm pl-14 hidden sm:block">Assign individual meters or entire lots to your contractor networks. Workers see their assignments update on their mobile devices in real-time.</p>
                            </button>

                            <button @click="activeTab = 3" :class="activeTab === 3 ? 'bg-white dark:bg-slate-800 shadow-md border-emerald-200 dark:border-emerald-800/50' : 'bg-transparent border-transparent hover:bg-white/50 dark:hover:bg-slate-800/50'" class="text-left p-6 rounded-2xl border transition-all duration-300">
                                <div class="flex items-center gap-4 mb-2">
                                    <div :class="activeTab === 3 ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/50 dark:text-emerald-400' : 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400'" class="w-10 h-10 rounded-xl flex items-center justify-center transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <h4 :class="activeTab === 3 ? 'text-slate-900 dark:text-white font-semibold' : 'text-slate-600 dark:text-slate-400 font-medium'" class="text-lg">Mobile Installation Form</h4>
                                </div>
                                <p :class="activeTab === 3 ? 'text-slate-600 dark:text-slate-300' : 'text-slate-500 dark:text-slate-500'" class="text-sm pl-14 hidden sm:block">Workers log readings, upload mandatory pre/post installation photos, and submit the completed installation block seamlessly directly from the field.</p>
                            </button>
                            
                            <button @click="activeTab = 4" :class="activeTab === 4 ? 'bg-white dark:bg-slate-800 shadow-md border-rose-200 dark:border-rose-800/50' : 'bg-transparent border-transparent hover:bg-white/50 dark:hover:bg-slate-800/50'" class="text-left p-6 rounded-2xl border transition-all duration-300">
                                <div class="flex items-center gap-4 mb-2">
                                    <div :class="activeTab === 4 ? 'bg-rose-100 text-rose-600 dark:bg-rose-900/50 dark:text-rose-400' : 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400'" class="w-10 h-10 rounded-xl flex items-center justify-center transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    </div>
                                    <h4 :class="activeTab === 4 ? 'text-slate-900 dark:text-white font-semibold' : 'text-slate-600 dark:text-slate-400 font-medium'" class="text-lg">Issue Resolution</h4>
                                </div>
                                <p :class="activeTab === 4 ? 'text-slate-600 dark:text-slate-300' : 'text-slate-500 dark:text-slate-500'" class="text-sm pl-14 hidden sm:block">Automate your reverse logistics. Field workers can flag faulty meters and generate pre-filled return requests that land directly in the Action Required dashboard.</p>
                            </button>
                        </div>

                        <!-- Feature Visualizations -->
                        <div class="w-full lg:w-2/3 h-[500px] bg-slate-200 dark:bg-slate-800 rounded-[2rem] border border-slate-300 dark:border-slate-700 relative overflow-hidden shadow-2xl flex items-center justify-center">
                            
                            <!-- Tab 1 Img -->
                            <div x-show="activeTab === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="absolute inset-0 flex flex-col p-8 pb-0">
                                <div class="w-1/2 h-8 bg-slate-300/50 dark:bg-slate-700/50 rounded mb-6"></div>
                                <div class="flex gap-4 mb-6">
                                    <div class="h-10 w-32 bg-brand-500 text-white rounded-lg flex items-center justify-center font-medium text-sm shadow-lg shadow-brand-500/20">Import CSV</div>
                                    <div class="h-10 w-24 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-lg"></div>
                                </div>
                                <div class="flex-1 bg-white dark:bg-slate-900 rounded-t-2xl border-t border-x border-slate-300 dark:border-slate-700 shadow-xl overflow-hidden p-6 gap-4 flex flex-col">
                                    <div class="h-10 border-b border-slate-200 dark:border-slate-800 flex items-center gap-4">
                                        <div class="w-32 h-4 bg-slate-200 dark:bg-slate-800 rounded"></div>
                                        <div class="w-24 h-4 bg-slate-200 dark:bg-slate-800 rounded"></div>
                                    </div>
                                    @for($i=0; $i<5; $i++)
                                    <div class="flex items-center gap-4 py-2 opacity-{{ 100 - ($i * 15) }}">
                                        <div class="w-8 h-8 rounded bg-slate-100 dark:bg-slate-800"></div>
                                        <div class="w-48 h-3 bg-slate-200 dark:bg-slate-700 rounded"></div>
                                        <div class="w-16 h-5 rounded-full bg-blue-100 dark:bg-blue-900/40 border border-blue-200 dark:border-blue-800"></div>
                                    </div>
                                    @endfor
                                </div>
                            </div>
                            
                            <!-- Tab 2 Img -->
                            <div x-show="activeTab === 2" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="absolute inset-0 flex items-center justify-center bg-slate-100 dark:bg-slate-950 p-8" style="display: none;">
                                <div class="w-full max-w-sm bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-800 overflow-hidden transform rotate-2">
                                    <div class="bg-amber-50 dark:bg-amber-900/10 p-5 border-b border-amber-100 dark:border-amber-900/30">
                                        <div class="flex justify-between items-center">
                                            <div class="text-sm font-semibold text-amber-700 dark:text-amber-400">New Assignment</div>
                                            <div class="w-6 h-6 rounded-full bg-amber-200 dark:bg-amber-800/50 flex items-center justify-center text-xs">24</div>
                                        </div>
                                        <div class="text-2xl font-bold mt-2 text-slate-900 dark:text-white">Lot #A-8992</div>
                                    </div>
                                    <div class="p-5 flex flex-col gap-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-slate-200 dark:bg-slate-700 flex-shrink-0 border-2 border-white dark:border-slate-900 shadow-sm"></div>
                                            <div>
                                                <div class="h-3.5 w-32 bg-slate-200 dark:bg-slate-700 rounded mb-1.5"></div>
                                                <div class="h-2 w-20 bg-slate-100 dark:bg-slate-800 rounded"></div>
                                            </div>
                                        </div>
                                        <div class="h-10 w-full mt-2 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-lg flex items-center justify-center text-sm font-semibold shadow-md">Confirm Transfer</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Tab 3 Img & Tab 4 placeholders for aesthetics -->
                            <div x-show="activeTab === 3" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="absolute inset-x-0 bottom-0 top-12 flex justify-center" style="display: none;">
                                <div class="w-[300px] h-full bg-white dark:bg-slate-950 rounded-t-[3rem] shadow-2xl border-x border-t border-slate-300 dark:border-slate-700 p-6 flex flex-col gap-6 relative">
                                    <!-- Mobile Notch -->
                                    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-32 h-6 bg-slate-200 dark:bg-slate-800 rounded-b-xl border-x border-b border-slate-300 dark:border-slate-700"></div>
                                    <div class="mt-8 h-8 w-2/3 bg-slate-200 dark:bg-slate-800 rounded-md"></div>
                                    <div class="space-y-4">
                                        <div>
                                            <div class="h-3 w-20 bg-slate-200 dark:bg-slate-800 rounded mb-2"></div>
                                            <div class="h-12 w-full bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl"></div>
                                        </div>
                                        <div>
                                            <div class="h-3 w-24 bg-slate-200 dark:bg-slate-800 rounded mb-2"></div>
                                            <div class="h-24 w-full bg-slate-100 dark:bg-slate-900 border-2 border-dashed border-slate-300 dark:border-slate-700 rounded-xl flex items-center justify-center">
                                                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-auto mb-4 h-12 w-full bg-emerald-500 rounded-xl text-white flex items-center justify-center font-semibold shadow-lg shadow-emerald-500/30">Submit Log</div>
                                </div>
                            </div>
                            
                            <div x-show="activeTab === 4" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="absolute inset-0 flex items-center justify-center p-8 bg-rose-50/50 dark:bg-rose-950/20" style="display: none;">
                                <div class="w-full max-w-md bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-rose-200 dark:border-rose-900/50 overflow-hidden flex flex-col relative">
                                    <div class="absolute right-0 top-0 w-24 h-24 bg-rose-500/10 rounded-bl-full"></div>
                                    <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-start gap-4">
                                        <div class="w-12 h-12 rounded-full bg-rose-100 dark:bg-rose-900/30 text-rose-600 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                        <div>
                                            <h5 class="text-lg font-bold text-slate-900 dark:text-white">Action Required</h5>
                                            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Meter #99283 flagged as Faulty</p>
                                        </div>
                                    </div>
                                    <div class="p-6 bg-slate-50 dark:bg-slate-900/50 flex justify-end gap-3">
                                        <div class="px-4 py-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm font-medium">View Image</div>
                                        <div class="px-4 py-2 rounded-lg bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-sm font-medium">Initiate Return</div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </section>

            <!-- Audience Targets (Two Column Grid) -->
            <section class="py-24 bg-white dark:bg-slate-950 relative z-10">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="grid md:grid-cols-2 gap-16 items-center">
                        <div class="order-2 md:order-1 relative">
                            <div class="absolute -inset-4 bg-gradient-to-tr from-brand-100 to-teal-100 dark:from-brand-900/30 dark:to-teal-900/30 rounded-3xl blur-2xl opacity-50 -z-10"></div>
                            <img src="https://images.unsplash.com/photo-1504307651254-35680f356f67?auto=format&fit=crop&w=1000&q=80" alt="Warehouse Operations" class="rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 object-cover aspect-video md:aspect-square" loading="lazy">
                        </div>
                        <div class="order-1 md:order-2">
                            <h2 class="text-3xl md:text-5xl font-bold text-slate-900 dark:text-white leading-tight mb-6">Built for the Back Office...</h2>
                            <ul class="space-y-6">
                                <li class="flex gap-4">
                                    <div class="w-8 h-8 rounded-full bg-brand-100 dark:bg-brand-900/50 text-brand-600 dark:text-brand-400 flex items-center justify-center flex-shrink-0 mt-1">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    <div>
                                        <h4 class="text-xl font-semibold text-slate-900 dark:text-white">Seamless Logistics</h4>
                                        <p class="text-slate-600 dark:text-slate-400 mt-2">Manage returns, replace faulty components, and track warehouse to field movements without losing a single serial number.</p>
                                    </div>
                                </li>
                                <li class="flex gap-4">
                                    <div class="w-8 h-8 rounded-full bg-brand-100 dark:bg-brand-900/50 text-brand-600 dark:text-brand-400 flex items-center justify-center flex-shrink-0 mt-1">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    <div>
                                        <h4 class="text-xl font-semibold text-slate-900 dark:text-white">Live Data Exports</h4>
                                        <p class="text-slate-600 dark:text-slate-400 mt-2">Generate instant accounting and payroll reports based purely on completed installation records.</p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-16 items-center mt-32">
                        <div>
                            <h2 class="text-3xl md:text-5xl font-bold text-slate-900 dark:text-white leading-tight mb-6">...and Perfected for the Field.</h2>
                            <ul class="space-y-6">
                                <li class="flex gap-4">
                                    <div class="w-8 h-8 rounded-full bg-amber-100 dark:bg-amber-900/50 text-amber-600 dark:text-amber-400 flex items-center justify-center flex-shrink-0 mt-1">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    <div>
                                        <h4 class="text-xl font-semibold text-slate-900 dark:text-white">Zero Training Required</h4>
                                        <p class="text-slate-600 dark:text-slate-400 mt-2">A clean, mobile-first interface designed specifically for technicians wearing gloves. Big buttons, simple forms, and fast image uploads.</p>
                                    </div>
                                </li>
                                <li class="flex gap-4">
                                    <div class="w-8 h-8 rounded-full bg-amber-100 dark:bg-amber-900/50 text-amber-600 dark:text-amber-400 flex items-center justify-center flex-shrink-0 mt-1">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    <div>
                                        <h4 class="text-xl font-semibold text-slate-900 dark:text-white">Instant Syncing</h4>
                                        <p class="text-slate-600 dark:text-slate-400 mt-2">No more lost paperwork. The moment a tech hits 'Submit', the admin dashboard is updated, allowing for real-time validation.</p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="relative">
                            <div class="absolute -inset-4 bg-gradient-to-bl from-amber-100 to-rose-100 dark:from-amber-900/30 dark:to-rose-900/30 rounded-3xl blur-2xl opacity-50 -z-10"></div>
                            <img src="https://images.unsplash.com/photo-1621905252507-b35492cc74b4?auto=format&fit=crop&w=1000&q=80" alt="Field Technician" class="rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 object-cover aspect-video md:aspect-square" loading="lazy">
                        </div>
                    </div>
                </div>
            </section>

            <!-- How It Works (Stepper) -->
            <section class="py-24 bg-slate-900 text-white relative z-10 overflow-hidden">
                <div class="absolute top-0 inset-x-0 h-px bg-gradient-to-r from-transparent via-slate-700 to-transparent"></div>
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center max-w-3xl mx-auto mb-20">
                        <h2 class="text-brand-400 font-semibold tracking-wide uppercase text-sm mb-3">Workflow</h2>
                        <h3 class="text-3xl md:text-5xl font-bold mb-6">How MeterSync Works</h3>
                    </div>

                    <div class="grid md:grid-cols-4 gap-8 relative">
                        <!-- Connecting Line (Desktop) -->
                        <div class="hidden md:block absolute top-[2.5rem] left-[10%] right-[10%] h-0.5 bg-slate-800"></div>

                        <!-- Step 1 -->
                        <div class="relative text-center">
                            <div class="w-20 h-20 mx-auto bg-slate-800 border-4 border-slate-900 text-brand-400 rounded-full flex items-center justify-center text-2xl font-bold relative z-10 shadow-xl">1</div>
                            <h4 class="text-xl font-semibold mt-6 mb-2">Import Stock</h4>
                            <p class="text-slate-400 text-sm">Upload bulk CSVs to safely ingest serials into the system.</p>
                        </div>
                        <!-- Step 2 -->
                        <div class="relative text-center mt-8 md:mt-0">
                            <div class="w-20 h-20 mx-auto bg-slate-800 border-4 border-slate-900 text-brand-400 rounded-full flex items-center justify-center text-2xl font-bold relative z-10 shadow-xl">2</div>
                            <h4 class="text-xl font-semibold mt-6 mb-2">Build Lots</h4>
                            <p class="text-slate-400 text-sm">Group the ingested stock logically and securely assign them to users.</p>
                        </div>
                        <!-- Step 3 -->
                        <div class="relative text-center mt-8 md:mt-0">
                            <div class="w-20 h-20 mx-auto bg-slate-800 border-4 border-slate-900 text-brand-400 rounded-full flex items-center justify-center text-2xl font-bold relative z-10 shadow-xl">3</div>
                            <h4 class="text-xl font-semibold mt-6 mb-2">Install</h4>
                            <p class="text-slate-400 text-sm">Field techs view assignments, perform MI, and upload photo proof.</p>
                        </div>
                        <!-- Step 4 -->
                        <div class="relative text-center mt-8 md:mt-0">
                            <div class="w-20 h-20 mx-auto bg-brand-500 border-4 border-slate-900 text-white rounded-full flex items-center justify-center text-2xl font-bold relative z-10 shadow-xl shadow-brand-500/30">4</div>
                            <h4 class="text-xl font-semibold mt-6 mb-2">Reconcile</h4>
                            <p class="text-slate-400 text-sm">Run flawless reports, process returns, and prepare accounting data.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Testimonial CTA -->
            <section class="py-24 bg-white dark:bg-slate-950 relative z-10">
                <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                    <svg class="w-12 h-12 text-slate-200 dark:text-slate-800 mx-auto mb-6" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"></path></svg>
                    <blockquote class="text-2xl md:text-3xl font-semibold text-slate-900 dark:text-white leading-relaxed mb-8">
                        "Before MeterSync, tracking a simple meter swap involved three spreadsheets and a string of WhatsApp messages. Now, my dashboard shows me exactly where every piece of hardware is, live."
                    </blockquote>
                    <div class="flex items-center justify-center gap-4">
                        <div class="w-12 h-12 rounded-full border border-slate-300 dark:border-slate-700 relative overflow-hidden bg-slate-100 flex items-center justify-center">
                            <svg class="w-6 h-6 text-slate-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/></svg>
                        </div>
                        <div class="text-left">
                            <div class="font-bold text-slate-900 dark:text-white">Alex Operations</div>
                            <div class="text-sm text-slate-500 dark:text-slate-400">Head of Contractor Logistics</div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Pre-Footer CTA -->
            <div class="bg-gradient-to-br from-brand-600 to-teal-700 py-16 lg:py-24 relative overflow-hidden z-10">
                <!-- Background decor -->
                <div class="absolute inset-0 opacity-10 blur-xl">
                    <div class="absolute top-1/2 left-0 w-[40rem] h-[40rem] bg-white rounded-full -translate-y-1/2 -translate-x-1/2"></div>
                </div>
                
                <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
                    <h2 class="text-4xl md:text-5xl font-extrabold text-white mb-6">Ready to take control of your materiel?</h2>
                    <p class="text-xl text-brand-100 mb-10 max-w-2xl mx-auto">Stop guessing where your stock is. Join the platform designed exclusively for precision installation management.</p>
                    
                    @auth
                        <a href="{{ url('/dashboard') }}" class="inline-flex items-center justify-center px-8 py-4 bg-white text-slate-900 rounded-full font-bold shadow-2xl hover:bg-slate-50 hover:scale-105 transition transform duration-200 text-lg">
                            Go to Console
                        </a>
                    @else
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-8 py-4 bg-white text-slate-900 rounded-full font-bold shadow-2xl hover:bg-slate-50 hover:scale-105 transition transform duration-200 text-lg">
                                Create an Account
                            </a>
                            <p class="mt-4 text-sm text-brand-200">Or <a href="{{ route('login') }}" class="underline hover:text-white">sign in to existing team</a></p>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- Footer -->
            <footer class="bg-white dark:bg-slate-950 border-t border-slate-200 dark:border-slate-800 py-12 z-10 relative">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                        <div class="flex items-center gap-2 grayscale opacity-80 hover:grayscale-0 hover:opacity-100 transition duration-300">
                            <svg class="w-6 h-6 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            <span class="font-bold text-slate-900 dark:text-white tracking-tight">MeterSync</span>
                        </div>
                        <div class="text-sm text-slate-500 dark:text-slate-400">
                            &copy; {{ date('Y') }} Material Allotment System. All rights reserved. <span class="mx-2 text-slate-300 dark:text-slate-700">|</span> Powered by Laravel 11
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>
