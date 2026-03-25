<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Casher') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
        <link rel="shortcut icon" href="{{ asset('favicon.svg') }}">

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body { font-family: 'Plus Jakarta Sans', sans-serif; }
        </style>
        <script>
            if (localStorage.getItem('theme') === 'light') {
                document.documentElement.classList.remove('dark');
            }
        </script>
    </head>

    <body class="antialiased bg-slate-50 dark:bg-[#09090b] text-gray-800 dark:text-gray-200">
        <div class="min-h-screen relative">

            {{-- Background ambient glow (dark mode only) --}}
            <div class="hidden dark:block fixed top-0 right-0 -z-10 h-[600px] w-[600px] rounded-full bg-[#8b5cf6]/5 blur-[150px]"></div>
            <div class="hidden dark:block fixed bottom-0 left-0 -z-10 h-[500px] w-[500px] rounded-full bg-[#fbbf24]/3 blur-[120px]"></div>

@php
    $menuItems = [
        [
            'label' => 'Dashboard',
            'route' => route('dashboard'),
            'icon' => 'fa-solid fa-chart-line',
            'active' => request()->routeIs('dashboard'),
        ],
        [
            'label' => 'Transactions',
            'route' => route('transactions.index'),
            'icon' => 'fa-solid fa-wallet',
            'active' => request()->routeIs('transactions.*'),
        ],
        [
            'label' => 'Categories',
            'route' => route('categories.index'),
            'icon' => 'fa-solid fa-tags',
            'active' => request()->routeIs('categories.*'),
        ],
        [
            'label' => 'Data',
            'route' => route('charts.index'),
            'icon' => 'fa-solid fa-chart-pie',
            'active' => request()->routeIs('charts.*'),
        ],
        [
            'label' => 'Investments',
            'route' => route('investments.index'),
            'icon' => 'fa-solid fa-coins',
            'active' => request()->routeIs('investments.*'),
        ],
        [
            'label' => 'Import / export',
            'route' => route('data.index'),
            'icon' => 'fa-solid fa-exchange',
            'active' => request()->routeIs('data.*'),
        ],
    ];
@endphp

            <x-sidebar :menu-items="$menuItems" />

            {{-- Main content area --}}
            <div class="flex-1 lg:ml-64 transition-all">

                {{-- Top header bar --}}
                <header class="sticky top-0 z-30 border-b border-gray-200 dark:border-white/5 bg-white/80 dark:bg-[#09090b]/80 backdrop-blur-xl">
                    <div class="flex items-center justify-between max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                        {{-- Mobile menu toggle --}}
                        <button onclick="document.getElementById('mobile-sidebar').classList.toggle('hidden')" class="lg:hidden flex items-center justify-center w-10 h-10 rounded-lg bg-gray-100 dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition">
                            <i class="fa-solid fa-bars"></i>
                        </button>

                        <div>
                            @if(isset($header))
                                {{ $header }}
                            @elseif(View::hasSection('header'))
                                @yield('header')
                            @endif
                        </div>

                        <div class="flex gap-3 items-center">
                            <x-darkmode-toggle />
                        </div>
                    </div>
                </header>

                {{-- Page content --}}
                <main class="pb-8">
                    @isset($slot)
                        {{ $slot }}
                    @else
                        @yield('content')
                    @endisset
                </main>

            </div>
        </div>

        <x-confirm-dialog />
        <x-toast />
    </body>

    @stack('scripts')
</html>
