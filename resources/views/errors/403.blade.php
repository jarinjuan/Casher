<!DOCTYPE html>
<html lang="en" class="dark scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 | Casher</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,600,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="antialiased bg-[#09090b] text-gray-200 overflow-x-hidden">
    <div class="relative min-h-screen flex items-center justify-center px-4 sm:px-6 overflow-hidden">
        <div class="absolute top-0 right-0 -z-10 h-[600px] w-[600px] rounded-full bg-[#8b5cf6]/10 blur-[120px]"></div>
        <div class="absolute bottom-0 left-0 -z-10 h-[500px] w-[500px] rounded-full bg-[#fbbf24]/5 blur-[100px]"></div>
        <div class="w-full max-w-md relative">
            <div class="absolute -inset-1 rounded-3xl bg-gradient-to-r from-[#fbbf24] to-[#8b5cf6] opacity-20 blur"></div>
            <div class="relative card rounded-3xl shadow-2xl p-6 sm:p-8 md:p-10 text-center">
                <div class="flex items-center justify-center mb-6">
                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-red-500/10 text-red-400">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                    </div>
                </div>
                <h1 class="text-2xl font-extrabold t-primary mb-3">{{ __('Access denied') }}</h1>
                <p class="t-secondary text-sm leading-relaxed mb-8">
                    {{ __('You do not have permission to access this page.') }}
                </p>
                <a href="{{ url()->previous() }}" class="btn-primary inline-block w-full py-4 rounded-xl shadow-lg shadow-[#fbbf24]/20 hover:shadow-[#fbbf24]/40">
                    {{ __('Go back') }}
                </a>
            </div>
        </div>
    </div>
</body>
</html>
