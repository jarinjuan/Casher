<!DOCTYPE html>
<html lang="en" class="dark scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign In | Casher</title>

    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.svg') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,600,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="antialiased bg-[#09090b] text-gray-200 overflow-x-hidden">

    <nav class="fixed top-0 z-50 w-full border-b border-white/10 bg-black/60 backdrop-blur-md">
        <div class="container mx-auto flex items-center justify-between px-6 py-4">
            <a href="/" class="flex items-center gap-2">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#fbbf24] font-bold text-black shadow-[0_0_20px_rgba(251,191,36,0.3)]">
                    C$
                </div>
                <span class="text-xl font-extrabold tracking-tight t-primary italic">CASHER</span>
            </a>

            <div class="flex items-center gap-4">
                <a href="{{ route('register') }}" class="text-sm font-semibold t-secondary hover:text-[#fbbf24] transition">Create Account</a>
            </div>
        </div>
    </nav>

    <div class="relative min-h-screen flex items-center justify-center pt-24 pb-20 px-4 sm:px-6 overflow-hidden">
        <div class="absolute top-0 right-0 -z-10 h-[600px] w-[600px] rounded-full bg-[#8b5cf6]/10 blur-[120px]"></div>
        <div class="absolute bottom-0 left-0 -z-10 h-[500px] w-[500px] rounded-full bg-[#fbbf24]/5 blur-[100px]"></div>

        <div class="w-full max-w-md relative">
            <div class="absolute -inset-1 rounded-3xl bg-gradient-to-r from-[#fbbf24] to-[#8b5cf6] opacity-20 blur"></div>
            <div class="relative card rounded-3xl shadow-2xl p-6 sm:p-8 md:p-10">

                <div class="text-center mb-8">
                    <h1 class="text-3xl font-extrabold t-primary mb-2">Welcome Back</h1>
                    <p class="t-secondary">Sign in to access your dashboard</p>
                </div>

                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="email" class="label-dark">Email</label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="username"
                            class="input-dark rounded-xl py-3"
                            placeholder="your@email.com"
                        />
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500 text-sm" />
                    </div>

                    <div>
                        <label for="password" class="label-dark">Password</label>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            class="input-dark rounded-xl py-3"
                            placeholder="********"
                        />
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500 text-sm" />
                    </div>

                    <div class="flex items-center justify-between">
                        <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                            <input
                                id="remember_me"
                                type="checkbox"
                                name="remember"
                                class="rounded bg-black/40 border-white/20 text-[#fbbf24] focus:ring-[#fbbf24] focus:ring-offset-0 cursor-pointer"
                            />
                            <span class="ml-2 text-sm t-secondary group-hover:text-[#fbbf24] transition">Remember me</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-sm t-secondary hover:text-[#fbbf24] transition font-semibold">
                                Forgot password?
                            </a>
                        @endif
                    </div>

                    <button
                        type="submit"
                        class="btn-primary w-full py-4 rounded-xl shadow-lg shadow-[#fbbf24]/20 hover:shadow-[#fbbf24]/40"
                    >
                        Sign In
                    </button>
                </form>

                <div class="mt-8 pt-6 border-t border-white/5 text-center">
                    <p class="t-secondary text-sm">
                        Don't have an account?
                        <a href="{{ route('register') }}" class="text-[#fbbf24] hover:text-[#f59e0b] font-semibold transition">
                            Create one now
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>

</body>
</html>