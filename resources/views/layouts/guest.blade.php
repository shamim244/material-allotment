<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'MeterSync') }} - Authentication</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Tailwind CSS & Alpine -->
        <script src="https://cdn.tailwindcss.com"></script>
        <!-- Include Forms Plugin for inputs -->
        <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>

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
                            }
                        }
                    },
                },
            }
        </script>
        <style>
            .glass-panel {
                background: rgba(255, 255, 255, 0.4) !important;
                backdrop-filter: blur(24px) !important;
                -webkit-backdrop-filter: blur(24px) !important;
                border: 1px solid rgba(255, 255, 255, 0.6) !important;
                box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07) !important;
            }
            @media (prefers-color-scheme: dark) {
                .glass-panel {
                    background: rgba(15, 23, 42, 0.4) !important;
                    border: 1px solid rgba(255, 255, 255, 0.1) !important;
                    box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3) !important;
                }
            }
        </style>
    </head>
    <body class="font-sans antialiased bg-slate-50 text-slate-900 dark:bg-slate-950 dark:text-white selection:bg-brand-500 selection:text-white relative">
        
        <!-- Abstract Background -->
        <div class="fixed inset-0 z-0 overflow-hidden pointer-events-none">
            <div class="absolute top-0 right-0 w-[500px] md:w-[700px] h-[500px] md:h-[700px] bg-brand-400/30 rounded-full blur-[80px] md:blur-[120px] -translate-y-1/3 translate-x-1/3 dark:bg-brand-500/20"></div>
            <div class="absolute bottom-0 left-0 w-[500px] md:w-[700px] h-[500px] md:h-[700px] bg-teal-400/30 rounded-full blur-[80px] md:blur-[120px] translate-y-1/3 -translate-x-1/3 dark:bg-teal-500/20"></div>
            <div class="absolute top-1/2 left-1/2 w-[300px] md:w-[400px] h-[300px] md:h-[400px] bg-rose-400/20 rounded-full blur-[60px] md:blur-[80px] -translate-y-1/2 -translate-x-1/2 dark:bg-rose-500/10"></div>
        </div>

        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 relative z-10 px-4">
            
            <!-- Logo -->
            <div class="mb-8">
                <a href="/" class="flex flex-col items-center gap-3 group">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-brand-400 to-teal-600 flex items-center justify-center text-white shadow-xl shadow-brand-500/20 group-hover:scale-105 transition-transform duration-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <span class="font-bold text-2xl tracking-tight text-slate-900 dark:text-white">
                        MeterSync
                    </span>
                </a>
            </div>

            <!-- Auth Card -->
            <div class="w-full sm:max-w-md px-6 sm:px-8 py-8 sm:py-10 glass-panel shadow-2xl rounded-3xl sm:rounded-[2rem]">
                {{ $slot }}
            </div>
            
            <!-- Footer Link -->
            <div class="mt-8 text-sm text-slate-500 dark:text-slate-400">
                &copy; {{ date('Y') }} MeterSync Logistics
            </div>
        </div>
    </body>
</html>
