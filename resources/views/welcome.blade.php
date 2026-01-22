<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Casher | Master Your Wealth</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,600,800&display=swap" rel="stylesheet" />

    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="antialiased bg-[#09090b] text-gray-200">

    <nav class="fixed top-0 z-50 w-full border-b border-white/10 bg-black/60 backdrop-blur-md">
        <div class="container mx-auto flex items-center justify-between px-6 py-4">
            <div class="flex items-center gap-2">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#fbbf24] font-bold text-black shadow-[0_0_20px_rgba(251,191,36,0.3)]">
                    C$
                </div>
                <span class="text-xl font-extrabold tracking-tight text-white italic">CASHER</span>
            </div>

            <div class="hidden md:flex items-center gap-8 text-sm font-medium text-gray-400">
                <a href="#features" class="hover:text-[#fbbf24] transition">Features</a>
                <a href="#investments" class="hover:text-[#fbbf24] transition">Investments</a>
                <a href="#security" class="hover:text-[#fbbf24] transition">Security</a>
            </div>

            <div class="flex items-center gap-4">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="rounded-full bg-white/10 px-6 py-2 text-sm font-semibold text-white hover:bg-white/20 transition">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="hidden sm:block text-sm font-semibold hover:text-[#fbbf24] transition">Sign In</a>
                        <a href="{{ route('register') }}" class="rounded-full bg-[#fbbf24] px-6 py-2 text-sm font-bold text-black hover:bg-[#f59e0b] transition shadow-lg shadow-[#fbbf24]/20">Get Started</a>
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    <header class="relative overflow-hidden pt-20 pb-20 lg:pt-32 lg:pb-32">
        <div class="absolute top-0 right-0 -z-10 h-[600px] w-[600px] rounded-full bg-[#8b5cf6]/10 blur-[120px]"></div>
        <div class="absolute bottom-0 left-0 -z-10 h-[500px] w-[500px] rounded-full bg-[#fbbf24]/5 blur-[100px]"></div>

        <div class="container mx-auto px-6 text-center">
            
            <h1 class="mx-auto max-w-5xl text-5xl font-extrabold leading-[1.1] text-white md:text-7xl lg:text-8xl">
                Expense tracking <br> 
                <span class="bg-gradient-to-r from-[#fbbf24] to-[#a78bfa] bg-clip-text text-transparent">done right.</span>
            </h1>
            
            <p class="mx-auto mt-8 max-w-2xl text-lg text-gray-400 md:text-xl">
                The ultimate platform for monitoring, managing, and analyzing your personal cash flow. Scale your assets with professional tools.
            </p>

            <div class="mt-12 flex flex-col sm:flex-row items-center justify-center gap-4">
                 <a href="{{ route('register') }}" class="w-full sm:w-auto rounded-full bg-white px-8 py-4 text-black font-bold hover:bg-gray-200 transition">Get Started Now</a>
                 <a href="#features" class="w-full sm:w-auto rounded-full bg-white/5 border border-white/10 px-8 py-4 text-white font-semibold hover:bg-white/10 transition">View Features</a>
            </div>

            <div class="mt-20 relative mx-auto max-w-4xl group">
                <div class="absolute -inset-1 rounded-2xl bg-gradient-to-r from-[#fbbf24] to-[#8b5cf6] opacity-20 blur group-hover:opacity-30 transition duration-1000"></div>
                <div class="relative overflow-hidden rounded-2xl border border-white/10 bg-[#18181b] shadow-2xl">
                    <div class="flex items-center justify-between border-b border-white/5 bg-white/5 px-4 py-3 text-gray-500">
                        <div class="flex gap-1.5">
                            <div class="h-3 w-3 rounded-full bg-[#ef4444]/40"></div>
                            <div class="h-3 w-3 rounded-full bg-[#fbbf24]/40"></div>
                            <div class="h-3 w-3 rounded-full bg-[#22c55e]/40"></div>
                        </div>
                        <span class="text-[9px] font-mono uppercase tracking-widest">Casher Terminal // Active Session</span>
                        <div class="w-10"></div>
                    </div>
                    
                    <div class="p-8 text-left">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                            <div class="space-y-1">
                                <span class="text-[10px] text-gray-500 uppercase font-bold tracking-widest">Net Liquidity</span>
                                <div class="text-3xl font-extrabold text-white">$12,450.00</div>
                                <div class="text-xs text-[#22c55e] font-medium">+14.2% this month</div>
                            </div>
                            <div class="space-y-1">
                                <span class="text-[10px] text-gray-500 uppercase font-bold tracking-widest">Invested Assets</span>
                                <div class="text-3xl font-extrabold text-[#fbbf24]">$8,205.40</div>
                                <div class="flex gap-2 text-[10px] text-gray-500 font-mono italic">
                                    <span>BTC</span><span>ETH</span><span>NASDAQ</span>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <span class="text-[10px] text-gray-500 uppercase font-bold tracking-widest">Monthly Limit</span>
                                <div class="h-2 w-full bg-gray-800 rounded-full overflow-hidden">
                                    <div class="h-full bg-[#8b5cf6] w-[68%] shadow-[0_0_15px_#8b5cf6]"></div>
                                </div>
                                <div class="flex justify-between text-[10px] font-bold">
                                    <span class="text-gray-400">EXPENDED: 68%</span>
                                    <span class="text-[#8b5cf6]">SAFE</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <section id="features" class="py-32 bg-[#09090b]">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
                <div>
                    <h2 class="text-4xl font-extrabold text-white mb-8 leading-tight">Advanced tools for <br> <span class="text-[#fbbf24]">modern investors.</span></h2>
                    
                    <div class="space-y-10">
                        <div class="flex gap-6">
                            <div class="shrink-0 flex h-12 w-12 items-center justify-center rounded-xl bg-[#8b5cf6]/10 text-[#8b5cf6] border border-[#8b5cf6]/20">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-white">Family Multitenancy</h4>
                                <p class="text-gray-400 mt-2">Isolated workspaces allowing you to invite family members while maintaining strict access control.</p>
                            </div>
                        </div>

                        <div class="flex gap-6">
                            <div class="shrink-0 flex h-12 w-12 items-center justify-center rounded-xl bg-[#fbbf24]/10 text-[#fbbf24] border border-[#fbbf24]/20">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-white">Investment Portfolio</h4>
                                <p class="text-gray-400 mt-2">Real-time tracking of crypto and stocks with automated valuation via external market APIs.</p>
                            </div>
                        </div>

                        <div class="flex gap-6">
                            <div class="shrink-0 flex h-12 w-12 items-center justify-center rounded-xl bg-[#8b5cf6]/10 text-[#8b5cf6] border border-[#8b5cf6]/20">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m-3 9c0 4.97 1.343 9 3 9m-3-9c1.657 0-3-4.03-3-9s1.343-9 3-9m-3 9H3"></path></svg>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-white">Automated FX Rates</h4>
                                <p class="text-gray-400 mt-2">Global transaction support with currency conversion powered by central bank exchange rate data.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-[#18181b] p-10 rounded-[2.5rem] border border-white/5 relative overflow-hidden shadow-2xl">
                    <div class="absolute -right-20 -top-20 h-64 w-64 bg-[#8b5cf6]/10 rounded-full blur-3xl"></div>
                    <div class="relative">
                        <h3 class="text-2xl font-extrabold text-white mb-6">Master Your Data</h3>
                        <p class="text-gray-400 mb-10 leading-relaxed">Full control over your financial history. Import external data or export professional reports for your accounting.</p>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-black/40 p-6 rounded-2xl border border-white/5 group hover:border-[#fbbf24]/50 transition cursor-default">
                                <div class="text-[#fbbf24] font-black text-2xl mb-1">PDF</div>
                                <div class="text-[10px] uppercase font-bold text-gray-500 tracking-wider">Reports</div>
                            </div>
                            <div class="bg-black/40 p-6 rounded-2xl border border-white/5 group hover:border-[#8b5cf6]/50 transition cursor-default">
                                <div class="text-[#8b5cf6] font-black text-2xl mb-1">XLSX</div>
                                <div class="text-[10px] uppercase font-bold text-gray-500 tracking-wider">Spreadsheets</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="py-20 border-t border-white/5 bg-black">
        <div class="container mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-8">
            <div class="flex items-center gap-2">
                <div class="h-8 w-8 rounded-md bg-[#fbbf24] flex items-center justify-center text-black font-black text-xs">C</div>
                <span class="font-extrabold tracking-tight text-white italic">CASHER</span>
            </div>
            
            <p class="text-xs text-gray-600 font-medium uppercase tracking-[0.3em]">
                &copy; 2026 Jaroslav Rašovský. All rights reserved.
            </p>
        </div>
    </footer>

</body>
</html>