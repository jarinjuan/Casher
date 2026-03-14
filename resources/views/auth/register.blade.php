<!DOCTYPE html>
<html lang="en" class="dark scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Account | Casher</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,600,800&display=swap" rel="stylesheet" />

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

    <nav class="fixed top-0 z-50 w-full border-b border-gray-200 dark:border-white/10 bg-white/60 dark:bg-black/60 backdrop-blur-md">
        <div class="container mx-auto flex items-center justify-between px-6 py-4">
            <a href="/" class="flex items-center gap-2">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#fbbf24] font-bold text-black shadow-[0_0_20px_rgba(251,191,36,0.3)]">
                    C$
                </div>
                <span class="text-xl font-extrabold tracking-tight t-primary italic">CASHER</span>
            </a>

            <div class="flex items-center gap-4">
                <a href="{{ route('login') }}" class="text-sm font-semibold t-secondary hover:text-[#fbbf24] transition">Sign In</a>
            </div>
        </div>
    </nav>

    <div class="relative min-h-screen flex items-center justify-center pt-20 pb-20 px-6">
        <div class="hidden dark:block absolute top-0 right-0 -z-10 h-[600px] w-[600px] rounded-full bg-[#8b5cf6]/10 blur-[120px]"></div>
        <div class="hidden dark:block absolute bottom-0 left-0 -z-10 h-[500px] w-[500px] rounded-full bg-[#fbbf24]/5 blur-[100px]"></div>

        <div class="w-full max-w-md relative">
            <div class="hidden dark:block absolute -inset-1 rounded-3xl bg-gradient-to-r from-[#fbbf24] to-[#8b5cf6] opacity-20 blur"></div>
            <div class="relative card rounded-3xl shadow-2xl p-8 md:p-10">

                <div class="text-center mb-8">
                    <h1 class="text-3xl font-extrabold t-primary mb-2">Create Account</h1>
                    <p class="t-secondary">Join Casher and start managing your finances</p>
                </div>

                <form method="POST" action="{{ route('register') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="name" class="label-dark">Full Name</label>
                        <input
                            id="name"
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            required
                            autofocus
                            autocomplete="name"
                            class="input-dark rounded-xl py-3"
                            placeholder="David Uncle"
                        />
                        @error('name')
                            <p class="mt-2 text-red-500 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="label-dark">Email</label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autocomplete="username"
                            class="input-dark rounded-xl py-3"
                            placeholder="your@email.com"
                        />
                        @error('email')
                            <p class="mt-2 text-red-500 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="label-dark">Password</label>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="new-password"
                            class="input-dark rounded-xl py-3"
                            placeholder="********"
                        />
                        @error('password')
                            <p class="mt-2 text-red-500 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="label-dark">Confirm Password</label>
                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            required
                            autocomplete="new-password"
                            class="input-dark rounded-xl py-3"
                            placeholder="********"
                        />
                        @error('password_confirmation')
                            <p class="mt-2 text-red-500 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <button
                        type="submit"
                        class="btn-primary w-full py-4 rounded-xl shadow-lg shadow-[#fbbf24]/20 hover:shadow-[#fbbf24]/40"
                    >
                        Create Account
                    </button>
                </form>

                <div class="mt-8 pt-6 border-t border-gray-200 dark:border-white/5 text-center">
                    <p class="t-secondary text-sm">
                        Already have an account?
                        <a href="{{ route('login') }}" class="text-[#fbbf24] hover:text-[#f59e0b] font-semibold transition">
                            Sign in here
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
