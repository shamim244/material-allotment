<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        <!-- Tailwind CSS via CDN (No NPM required) -->
        <script src="https://cdn.tailwindcss.com"></script>
        <!-- Alpine.js via CDN (Required for interactive navigation) -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['Figtree', 'sans-serif'],
                        },
                    },
                },
            }
        </script>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            @if(session()->has('impersonator_id'))
            <div class="bg-amber-500 text-white text-center py-2 px-4 text-sm font-medium flex items-center justify-center space-x-3">
                <span>⚠️ You are logged in as <strong>{{ Auth::user()->name }}</strong> (impersonating)</span>
                <form method="POST" action="{{ route('admin.stop-impersonating') }}" class="inline">
                    @csrf
                    <button type="submit" class="bg-white text-amber-700 px-3 py-1 rounded-md text-xs font-semibold hover:bg-amber-50 transition">
                        ← Return to Admin
                    </button>
                </form>
            </div>
            @endif

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
