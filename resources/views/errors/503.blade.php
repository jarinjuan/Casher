<!DOCTYPE html>
<html lang="en" class="dark scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>503 | Casher</title>
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
                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-[#fbbf24]/10 text-[#fbbf24]">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17l-5.47-3.17a2.25 2.25 0 010-3.9L11.42 4.83a2.25 2.25 0 012.16 0l5.47 3.17a2.25 2.25 0 010 3.9l-5.47 3.17a2.25 2.25 0 01-2.16 0z" />
                        </svg>
                    </div>
                </div>
                <h1 class="text-2xl font-extrabold t-primary mb-3">{{ __('Maintenance in progress') }}</h1>
                <p class="t-secondary text-sm leading-relaxed mb-8">
                    {{ __('We are currently performing maintenance. Please check back in a few minutes.') }}
                </p>
            </div>
        </div>
    </div>
</body>
</html>
