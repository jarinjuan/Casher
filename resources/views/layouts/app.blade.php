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
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body class="font-sans antialiased">
        <div class="min-h-screen bg-purple-50 dark:bg-gray-900 flex">

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
            'label' => 'Charts',
            'route' => route('charts.index'),
            'icon' => 'fa-solid fa-chart-pie',
            'active' => request()->routeIs('charts.*'),
        ],
    ];
@endphp


            {{-- SIDEBAR --}}
            <x-sidebar :menu-items="$menuItems" />

            {{-- CONTENT --}}
            <div class="flex-1 ml-64">

                {{-- PAGE HEADING --}}
                @if(isset($header) || View::hasSection('header'))
                    <header class="bg-white dark:bg-gray-800 shadow">
                        <div class="flex flex-row justify-between items-center max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            <div>
                                @if(isset($header))
                                    {{ $header }}
                                @else
                                    @yield('header')
                                @endif
                            </div>
                            <x-darkmode-toggle></x-darkmode-toggle>
                        </div>
                    </header>
                @else
                    <header class="bg-white dark:bg-gray-800 shadow">
                        <div class="flex flex-row justify-end items-center max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            <x-darkmode-toggle></x-darkmode-toggle>
                        </div>
                    </header>
                @endif

                {{-- PAGE CONTENT --}}
                <main>
                    @isset($slot)
                        {{ $slot }}
                    @else
                        @yield('content')
                    @endisset
                </main>

            </div>
        </div>
    </body>

    @stack('scripts')
</html>
