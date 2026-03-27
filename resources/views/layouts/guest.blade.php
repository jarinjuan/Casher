<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Casher</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,600,800&display=swap" rel="stylesheet" />
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
    <body class="font-sans antialiased bg-slate-50 dark:bg-[#09090b] text-gray-800 dark:text-gray-200">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 px-6">

            {{-- Background Effects (dark only) --}}
            <div class="hidden dark:block absolute top-0 right-0 -z-10 h-[600px] w-[600px] rounded-full bg-[#8b5cf6]/10 blur-[120px]"></div>
            <div class="hidden dark:block absolute bottom-0 left-0 -z-10 h-[500px] w-[500px] rounded-full bg-[#fbbf24]/5 blur-[100px]"></div>

            {{-- Logo --}}
            <div class="mb-8">
                <a href="/" class="flex items-center gap-2">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-[#fbbf24] font-bold text-black shadow-[0_0_20px_rgba(251,191,36,0.3)] text-lg">
                        C$
                    </div>
                    <span class="text-2xl font-extrabold tracking-tight t-primary italic">CASHER</span>
                </a>
            </div>

            {{-- Content Card --}}
            <div class="w-full sm:max-w-md relative">
                <div class="hidden dark:block absolute -inset-1 rounded-3xl bg-gradient-to-r from-[#fbbf24] to-[#8b5cf6] opacity-20 blur"></div>
                <div class="relative card rounded-3xl shadow-2xl p-8 md:p-10">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
