<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Account | Casher</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,600,800&display=swap" rel="stylesheet" />

    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="antialiased bg-[#09090b] text-gray-200">

    <!-- Navigation -->
    <nav class="fixed top-0 z-50 w-full border-b border-white/10 bg-black/60 backdrop-blur-md">
        <div class="container mx-auto flex items-center justify-between px-6 py-4">
            <a href="/" class="flex items-center gap-2">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#fbbf24] font-bold text-black shadow-[0_0_20px_rgba(251,191,36,0.3)]">
                    C$
                </div>
                <span class="text-xl font-extrabold tracking-tight text-white italic">CASHER</span>
            </a>

            <div class="flex items-center gap-4">
                <a href="{{ route('login') }}" class="text-sm font-semibold text-gray-400 hover:text-[#fbbf24] transition">Sign In</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="relative min-h-screen flex items-center justify-center pt-20 pb-20 px-6">
        <!-- Background Effects -->
        <div class="absolute top-0 right-0 -z-10 h-[600px] w-[600px] rounded-full bg-[#8b5cf6]/10 blur-[120px]"></div>
        <div class="absolute bottom-0 left-0 -z-10 h-[500px] w-[500px] rounded-full bg-[#fbbf24]/5 blur-[100px]"></div>

        <!-- Register Card -->
        <div class="w-full max-w-md relative">
            <div class="absolute -inset-1 rounded-3xl bg-gradient-to-r from-[#fbbf24] to-[#8b5cf6] opacity-20 blur"></div>
            <div class="relative bg-[#18181b] border border-white/10 rounded-3xl shadow-2xl p-8 md:p-10">
                
                <!-- Header -->
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-extrabold text-white mb-2">Create Account</h1>
                    <p class="text-gray-400">Join Casher and start managing your finances</p>
                </div>

                <!-- Form -->
                <form method="POST" action="{{ route('register') }}" class="space-y-5">
                    @csrf

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-bold text-gray-300 mb-2 uppercase tracking-wider">Full Name</label>
                        <input 
                            id="name" 
                            type="text" 
                            name="name" 
                            value="{{ old('name') }}"
                            required 
                            autofocus 
                            autocomplete="name"
                            class="w-full px-4 py-3 bg-black/40 border border-white/10 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#fbbf24] focus:border-transparent transition"
                            placeholder="David Uncle"
                        />
                        @error('name')
                            <p class="mt-2 text-red-400 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-sm font-bold text-gray-300 mb-2 uppercase tracking-wider">Email</label>
                        <input 
                            id="email" 
                            type="email" 
                            name="email" 
                            value="{{ old('email') }}"
                            required 
                            autocomplete="username"
                            class="w-full px-4 py-3 bg-black/40 border border-white/10 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#fbbf24] focus:border-transparent transition"
                            placeholder="your@email.com"
                        />
                        @error('email')
                            <p class="mt-2 text-red-400 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-bold text-gray-300 mb-2 uppercase tracking-wider">Password</label>
                        <input 
                            id="password" 
                            type="password" 
                            name="password" 
                            required 
                            autocomplete="new-password"
                            class="w-full px-4 py-3 bg-black/40 border border-white/10 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#fbbf24] focus:border-transparent transition"
                            placeholder="••••••••"
                        />
                        @error('password')
                            <p class="mt-2 text-red-400 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-bold text-gray-300 mb-2 uppercase tracking-wider">Confirm Password</label>
                        <input 
                            id="password_confirmation" 
                            type="password" 
                            name="password_confirmation" 
                            required 
                            autocomplete="new-password"
                            class="w-full px-4 py-3 bg-black/40 border border-white/10 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#fbbf24] focus:border-transparent transition"
                            placeholder="••••••••"
                        />
                        @error('password_confirmation')
                            <p class="mt-2 text-red-400 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit"
                        class="w-full py-4 px-6 bg-[#fbbf24] hover:bg-[#f59e0b] text-black font-bold rounded-xl transition shadow-lg shadow-[#fbbf24]/20 hover:shadow-[#fbbf24]/40"
                    >
                        Create Account
                    </button>
                </form>

                <!-- Login Link -->
                <div class="mt-8 pt-6 border-t border-white/5 text-center">
                    <p class="text-gray-400 text-sm">
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
