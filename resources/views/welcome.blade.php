<!DOCTYPE html>
<html lang="en" class="scroll-smooth overflow-x-hidden">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Casher | Master Your Wealth</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,600,800&display=swap" rel="stylesheet" />
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.svg') }}">

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="antialiased bg-[#09090b] text-gray-200 overflow-x-hidden">

    <nav x-data="{ open: false }" class="fixed top-0 z-50 w-full border-b border-white/10 bg-black/60 backdrop-blur-md">
        <div class="container mx-auto flex items-center justify-between px-4 sm:px-6 py-4">
            <div class="flex items-center gap-2">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#fbbf24] font-bold text-black shadow-[0_0_20px_rgba(251,191,36,0.3)]">
                    C$
                </div>
                <span class="text-xl font-extrabold tracking-tight text-white italic">CASHER</span>
            </div>

            <button @click="open = !open" class="md:hidden flex items-center px-3 py-2 border rounded text-gray-400 border-gray-600 hover:text-white hover:border-white focus:outline-none" aria-label="Open Menu">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <div class="hidden md:flex items-center gap-8 text-sm font-medium text-gray-400">
                <a href="#features" class="hover:text-[#fbbf24] transition">Features</a>
                <a href="#investments" class="hover:text-[#fbbf24] transition">Investments</a>
                <a href="#data" class="hover:text-[#fbbf24] transition">Data & export</a>
            </div>

            <div class="hidden sm:flex items-center gap-4">
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
        <!-- Mobile menu -->
        <div x-show="open" class="md:hidden bg-black/95 border-t border-white/10 px-4 pb-4 pt-2">
            <a href="#features" class="block py-2 text-gray-300 hover:text-[#fbbf24]">Features</a>
            <a href="#investments" class="block py-2 text-gray-300 hover:text-[#fbbf24]">Investments</a>
            <a href="#data" class="block py-2 text-gray-300 hover:text-[#fbbf24]">Data & export</a>
            <div class="mt-2 flex flex-col gap-2">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="rounded-full bg-white/10 px-6 py-2 text-sm font-semibold text-white hover:bg-white/20 transition">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-semibold hover:text-[#fbbf24] transition">Sign In</a>
                        <a href="{{ route('register') }}" class="rounded-full bg-[#fbbf24] px-6 py-2 text-sm font-bold text-black hover:bg-[#f59e0b] transition shadow-lg shadow-[#fbbf24]/20">Get Started</a>
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    {{-- HERO --}}
    <header class="relative overflow-hidden pt-24 pb-16 sm:pt-28 sm:pb-20 lg:pt-32 lg:pb-24">
        <div class="absolute top-0 right-0 -z-10 h-[600px] w-[600px] rounded-full bg-[#8b5cf6]/10 blur-[120px]"></div>
        <div class="absolute bottom-0 left-0 -z-10 h-[500px] w-[500px] rounded-full bg-[#fbbf24]/5 blur-[100px]"></div>

        <div class="container mx-auto px-4 sm:px-6 text-center">
            
            <h1 class="mx-auto max-w-5xl text-3xl sm:text-5xl font-extrabold leading-[1.1] text-white md:text-7xl lg:text-8xl">
                Expense tracking <br> 
                <span class="bg-gradient-to-r from-[#fbbf24] to-[#a78bfa] bg-clip-text text-transparent">done right.</span>
            </h1>
            
            <p class="mx-auto mt-6 max-w-2xl text-base sm:text-lg text-gray-400 md:text-xl">
                Dashboard, transactions, categories, charts, investments and import / export in one workflow. Simple, intuitive and powerful.
            </p>

              <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4 w-full max-w-md mx-auto">
                  <a href="{{ route('register') }}" class="w-full sm:w-auto rounded-full bg-white px-8 py-4 text-black font-bold hover:bg-gray-200 transition">Get Started Free</a>
                  <a href="#features" class="w-full sm:w-auto rounded-full bg-white/5 border border-white/10 px-8 py-4 text-white font-semibold hover:bg-white/10 transition">Explore Features</a>
              </div>

            <div class="mt-12 relative mx-auto max-w-full sm:max-w-5xl px-2">
                <div class="relative overflow-hidden rounded-2xl border border-white/10 bg-[#18181b] shadow-2xl">
                    <div class="flex items-center justify-between border-b border-white/5 bg-[#141418] px-4 py-3 text-gray-400">
                        <span class="text-[10px] font-semibold uppercase tracking-[0.2em]">Casher dashboard preview</span>
                        <span class="rounded-md border border-white/10 bg-white/5 px-2 py-1 text-[9px] font-medium uppercase tracking-wider text-gray-500">App UI</span>
                    </div>
                    <div class="p-4 sm:p-6 text-left">
                        <div class="rounded-xl border border-white/10 bg-[#111114] overflow-hidden">
                            <div class="grid grid-cols-12">
                                <aside class="col-span-3 hidden sm:block border-r border-white/5 bg-[#0d0d10] p-4">
                                    <div class="text-[10px] uppercase tracking-widest text-gray-500 font-bold mb-3">Workspace</div>
                                    <div class="text-sm font-bold text-white mb-4">Family budget</div>
                                    <ul class="space-y-2 text-xs">
                                        <li class="text-[#fbbf24] font-bold">Dashboard</li>
                                        <li class="text-gray-400">Transactions</li>
                                        <li class="text-gray-400">Categories</li>
                                        <li class="text-gray-400">Data</li>
                                        <li class="text-gray-400">Investments</li>
                                        <li class="text-gray-400">Import / Export</li>
                                    </ul>
                                </aside>

                                <div class="col-span-12 sm:col-span-9 p-4 sm:p-5">
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="text-lg font-extrabold text-white">Dashboard</div>
                                        <div class="flex items-center gap-2 text-[10px]">
                                            <span class="px-2 py-1 rounded-md bg-white/5 text-gray-400">Logout</span>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-3 mb-4">
                                        <div class="rounded-lg border border-white/5 bg-white/[0.02] p-3">
                                            <div class="text-[9px] uppercase tracking-widest text-gray-500 font-bold">Overall balance (Cash + Investments)</div>
                                            <div class="mt-1 text-sm font-extrabold text-white">24 830 €</div>
                                            <div class="text-[10px] text-gray-500">In EUR</div>
                                            <div class="text-[10px] text-gray-500 mt-1">Cash: 19 420 €</div>
                                            <div class="text-[10px] text-gray-500">Investments: 5 410 €</div>
                                        </div>
                                        <div class="rounded-lg border border-white/5 bg-white/[0.02] p-3">
                                            <div class="text-[9px] uppercase tracking-widest text-gray-500 font-bold">Monthly expenses</div>
                                            <div class="mt-1 text-sm font-extrabold text-white">2 450 €</div>
                                            <div class="text-[10px] font-semibold text-red-400">▲ 8.3% vs. last month</div>
                                        </div>
                                        <div class="rounded-lg border border-white/5 bg-white/[0.02] p-3">
                                            <div class="text-[9px] uppercase tracking-widest text-gray-500 font-bold">Monthly income</div>
                                            <div class="mt-1 text-sm font-extrabold text-white">3 610 €</div>
                                            <div class="text-[10px] font-semibold text-emerald-400">▲ 4.1% vs. last month</div>
                                        </div>
                                        <div class="rounded-lg border border-white/5 bg-white/[0.02] p-3">
                                            <div class="text-[9px] uppercase tracking-widest text-gray-500 font-bold">Expense forecast</div>
                                            <div class="mt-1 text-sm font-extrabold text-white">2 520 €</div>
                                            <div class="text-[10px] text-gray-500">6 last months average</div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
                                        <div class="lg:col-span-2 rounded-lg border border-white/5 bg-white/[0.02] p-3">
                                            <div class="text-[10px] uppercase tracking-widest text-gray-500 font-bold mb-2">Expenses vs income (EUR)</div>
                                            <div class="h-20 flex items-end gap-2">
                                                <div class="h-8 w-4 rounded-sm bg-[#8b5cf6]/80"></div>
                                                <div class="h-12 w-4 rounded-sm bg-[#fbbf24]/80"></div>
                                                <div class="h-10 w-4 rounded-sm bg-[#8b5cf6]/80"></div>
                                                <div class="h-14 w-4 rounded-sm bg-[#fbbf24]/80"></div>
                                                <div class="h-9 w-4 rounded-sm bg-[#8b5cf6]/80"></div>
                                                <div class="h-[3.75rem] w-4 rounded-sm bg-[#fbbf24]/80"></div>
                                            </div>
                                        </div>

                                        <div class="rounded-lg border border-white/5 bg-white/[0.02] p-3">
                                            <div class="text-[10px] uppercase tracking-widest text-gray-500 font-bold mb-2">Monthly budget</div>
                                            <div class="space-y-2 text-[10px]">
                                                <div>
                                                    <div class="flex justify-between text-gray-400"><span>Food</span><span>68%</span></div>
                                                    <div class="h-1.5 rounded-full bg-gray-800 mt-1 overflow-hidden"><div class="h-full w-[68%] bg-[#8b5cf6]"></div></div>
                                                </div>
                                                <div>
                                                    <div class="flex justify-between text-gray-400"><span>Transport</span><span>41%</span></div>
                                                    <div class="h-1.5 rounded-full bg-gray-800 mt-1 overflow-hidden"><div class="h-full w-[41%] bg-[#8b5cf6]"></div></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <section id="features" class="py-14 sm:py-20 bg-[#09090b]">
        <div class="container mx-auto px-4 sm:px-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 sm:gap-20 items-center">
                <div>
                    <h2 class="text-2xl sm:text-4xl font-extrabold text-white mb-8 leading-tight">Everything you need <br> <span class="text-[#fbbf24]">to manage money.</span></h2>
                    
                    <div class="space-y-8 sm:space-y-10">

                        <div class="flex gap-4 sm:gap-6">
                            <div class="shrink-0 flex h-12 w-12 items-center justify-center rounded-xl bg-[#fbbf24]/10 text-[#fbbf24] border border-[#fbbf24]/20">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-white">Income & expense tracking</h4>
                                <p class="text-gray-400 mt-2">Track income and expenses in the Transactions page, convert values to your workspace default currency, and keep every record under the active workspace.</p>
                            </div>
                        </div>

                        <div class="flex gap-4 sm:gap-6">
                            <div class="shrink-0 flex h-12 w-12 items-center justify-center rounded-xl bg-[#8b5cf6]/10 text-[#8b5cf6] border border-[#8b5cf6]/20">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-white">Budget management</h4>
                                <p class="text-gray-400 mt-2">Create categories with custom colors and monthly budgets, then monitor progress in Dashboard and category detail views with spent vs budget indicators.</p>
                            </div>
                        </div>

                        <div class="flex gap-4 sm:gap-6">
                            <div class="shrink-0 flex h-12 w-12 items-center justify-center rounded-xl bg-[#06b6d4]/10 text-[#06b6d4] border border-[#06b6d4]/20">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path></svg>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-white">Charts & analytics</h4>
                                <p class="text-gray-400 mt-2">Use charts & analytics for category pie, 12-month trend, 6-month cash flow, income sources, and top spending categories.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Stats Infographic --}}
                <div class="bg-[#18181b] p-4 sm:p-10 rounded-[2.5rem] border border-white/5 relative overflow-hidden shadow-2xl mt-10 sm:mt-0">
                    <div class="absolute -right-20 -top-20 h-64 w-64 bg-[#8b5cf6]/10 rounded-full blur-3xl"></div>
                    <div class="relative">
                        <h3 class="text-2xl font-extrabold text-white mb-6">Built around real screens</h3>
                        <p class="text-gray-400 mb-10 leading-relaxed">The welcome page mirrors the actual app modules: Dashboard cards, forecast logic, chart pages, and workspace-based financial organization.</p>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-black/40 p-6 rounded-2xl border border-white/5 group hover:border-[#22c55e]/50 transition cursor-default">
                                <div class="font-black text-2xl mb-1"><span class="text-[#22c55e]">▲</span><span class="text-[#ef4444]">▼</span></div>
                                <div class="text-[10px] uppercase font-bold text-gray-500 tracking-wider">Monthly Trends</div>
                            </div>
                            <div class="bg-black/40 p-6 rounded-2xl border border-white/5 group hover:border-[#fbbf24]/50 transition cursor-default">
                                <div class="text-[#fbbf24] font-black text-2xl mb-1">Forecast</div>
                                <div class="text-[10px] uppercase font-bold text-gray-500 tracking-wider">6-month average of expenses</div>
                            </div>
                            <div class="bg-black/40 p-6 rounded-2xl border border-white/5 group hover:border-[#8b5cf6]/50 transition cursor-default">
                                <div class="text-[#8b5cf6] font-black text-2xl mb-1">Visualizations</div>
                                <div class="text-[10px] uppercase font-bold text-gray-500 tracking-wider">Analytic graphs</div>
                            </div>
                            <div class="bg-black/40 p-6 rounded-2xl border border-white/5 group hover:border-[#06b6d4]/50 transition cursor-default">
                                <div class="text-[#06b6d4] font-black text-2xl mb-1">Team</div>
                                <div class="text-[10px] uppercase font-bold text-gray-500 tracking-wider">User-generated workspaces</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- INVESTMENTS --}}
    <section id="investments" class="py-14 sm:py-20 bg-[#0a0a0c]">
        <div class="container mx-auto px-4 sm:px-6">
            <div class="text-center mb-10">
                <h2 class="text-2xl sm:text-4xl font-extrabold text-white mb-4">Investment Portfolio <span class="text-[#fbbf24]">Tracking</span></h2>
                <p class="text-gray-400 max-w-2xl mx-auto">Monitor stocks and crypto holdings, refresh prices from external APIs, and track P/L with daily and monthly performance charts in your workspace currency.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-5xl mx-auto">
                <div class="bg-[#18181b] p-6 rounded-2xl border border-white/5 hover:border-[#fbbf24]/30 transition">
                    <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-[#fbbf24]/10 mb-4">
                        <svg class="w-6 h-6 text-[#fbbf24]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    </div>
                    <h4 class="font-bold text-white mb-2">Stocks & Crypto Holdings</h4>
                    <p class="text-sm text-gray-400">Add any stock symbol (AAPL, MSFT) or crypto (BTC, ETH) with automatic price fetching. Duplicate investments are smartly merged with weighted average pricing.</p>
                </div>
                <div class="bg-[#18181b] p-6 rounded-2xl border border-white/5 hover:border-[#8b5cf6]/30 transition">
                    <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-[#8b5cf6]/10 mb-4">
                        <svg class="w-6 h-6 text-[#8b5cf6]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    </div>
                    <h4 class="font-bold text-white mb-2">Price Refresh</h4>
                    <p class="text-sm text-gray-400">Prices refresh automatically each hour via scheduled jobs, with one-click manual refresh for all holdings. Track daily and monthly portfolio performance trends.</p>
                </div>
                <div class="bg-[#18181b] p-6 rounded-2xl border border-white/5 hover:border-[#22c55e]/30 transition">
                    <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-[#22c55e]/10 mb-4">
                        <svg class="w-6 h-6 text-[#22c55e]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                    <h4 class="font-bold text-white mb-2">P/L & Analytics</h4>
                    <p class="text-sm text-gray-400">See profit/loss per holding and total portfolio. Interactive 30-day daily and 12-month monthly performance charts with automatic currency conversion.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- MULTI-CURRENCY + WORKSPACES --}}
    <section class="py-14 sm:py-20 bg-[#09090b]">
        <div class="container mx-auto px-4 sm:px-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 sm:gap-16">
                
                <div class="bg-[#18181b] p-6 sm:p-10 rounded-[2rem] border border-white/5 relative overflow-hidden">
                    <div class="absolute -left-20 -bottom-20 h-64 w-64 bg-[#fbbf24]/5 rounded-full blur-3xl"></div>
                    <div class="relative">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-[#fbbf24]/10">
                                <svg class="w-6 h-6 text-[#fbbf24]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                            </div>
                            <h3 class="text-2xl font-extrabold text-white">Multi-Currency</h3>
                        </div>
                        <p class="text-gray-400 mb-8 leading-relaxed">Log transactions in CZK, EUR, USD, GBP and more. Amounts are converted to the workspace default currency configured in Workspace Settings using ECB exchange rates.</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach(['CZK', 'EUR', 'USD', 'GBP', 'JPY', 'CHF', 'PLN', 'SEK'] as $cur)
                                <span class="px-3 py-1.5 rounded-full bg-white/5 border border-white/10 text-xs font-bold text-gray-300">{{ $cur }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="bg-[#18181b] p-6 sm:p-10 rounded-[2rem] border border-white/5 relative overflow-hidden">
                    <div class="absolute -right-20 -top-20 h-64 w-64 bg-[#8b5cf6]/5 rounded-full blur-3xl"></div>
                    <div class="relative">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-[#8b5cf6]/10">
                                <svg class="w-6 h-6 text-[#8b5cf6]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            </div>
                            <h3 class="text-2xl font-extrabold text-white">Family Workspaces</h3>
                        </div>
                        <p class="text-gray-400 mb-8 leading-relaxed">Create isolated workspaces for your household, generate invite codes, and switch workspace context. Each workspace has its own transactions, categories, budgets, and investments.</p>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-black/40 p-4 rounded-xl border border-white/5">
                                <div class="text-[#8b5cf6] font-black text-lg mb-0.5">Invite Codes</div>
                                <div class="text-[10px] uppercase font-bold text-gray-500 tracking-wider">Share & Join</div>
                            </div>
                            <div class="bg-black/40 p-4 rounded-xl border border-white/5">
                                <div class="text-[#fbbf24] font-black text-lg mb-0.5">Roles</div>
                                <div class="text-[10px] uppercase font-bold text-gray-500 tracking-wider">Owner & Member</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- DATA IMPORT / EXPORT --}}
    <section id="data" class="py-14 sm:py-20 bg-[#0a0a0c]">
        <div class="container mx-auto px-4 sm:px-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 sm:gap-20 items-center">
                <div class="bg-[#18181b] p-4 sm:p-10 rounded-[2.5rem] border border-white/5 relative overflow-hidden shadow-2xl">
                    <div class="absolute -right-20 -top-20 h-64 w-64 bg-[#8b5cf6]/10 rounded-full blur-3xl"></div>
                    <div class="relative">
                        <h3 class="text-2xl font-extrabold text-white mb-6">Export Data / Import Data</h3>
                        <p class="text-gray-400 mb-10 leading-relaxed">Export Transactions, Categories and Investments to XLSX, CSV or PDF. Import XLSX/CSV files and merge them into your current workspace.</p>
                        
                        <div class="grid grid-cols-3 gap-4">
                            <div class="bg-black/40 p-5 rounded-2xl border border-white/5 group hover:border-[#ef4444]/50 transition cursor-default text-center">
                                <div class="text-[#ef4444] font-black text-2xl mb-1">PDF</div>
                                <div class="text-[10px] uppercase font-bold text-gray-500 tracking-wider">Reports</div>
                            </div>
                            <div class="bg-black/40 p-5 rounded-2xl border border-white/5 group hover:border-[#22c55e]/50 transition cursor-default text-center">
                                <div class="text-[#22c55e] font-black text-2xl mb-1">XLSX</div>
                                <div class="text-[10px] uppercase font-bold text-gray-500 tracking-wider">Excel</div>
                            </div>
                            <div class="bg-black/40 p-5 rounded-2xl border border-white/5 group hover:border-[#06b6d4]/50 transition cursor-default text-center">
                                <div class="text-[#06b6d4] font-black text-2xl mb-1">CSV</div>
                                <div class="text-[10px] uppercase font-bold text-gray-500 tracking-wider">Import</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <h2 class="text-2xl sm:text-4xl font-extrabold text-white mb-8 leading-tight">Structured exports. <br> <span class="text-[#fbbf24]">Practical imports.</span></h2>
                    
                    <div class="space-y-8 sm:space-y-10">
                        <div class="flex gap-4 sm:gap-6">
                            <div class="shrink-0 flex h-12 w-12 items-center justify-center rounded-xl bg-[#ef4444]/10 text-[#ef4444] border border-[#ef4444]/20">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-white">PDF Reports</h4>
                                <p class="text-gray-400 mt-2">Generate PDF reports from the same Export Data flow used for XLSX and CSV outputs.</p>
                            </div>
                        </div>

                        <div class="flex gap-4 sm:gap-6">
                            <div class="shrink-0 flex h-12 w-12 items-center justify-center rounded-xl bg-[#22c55e]/10 text-[#22c55e] border border-[#22c55e]/20">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-white">Spreadsheet Import</h4>
                                <p class="text-gray-400 mt-2">Upload XLSX/CSV files for transactions, categories and investments. Imported records are merged into the current workspace.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="pt-8 pb-14 sm:pt-12 sm:pb-20 bg-[#09090b] relative overflow-hidden">
        <div class="absolute inset-0 -z-10">
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 h-[400px] w-[600px] rounded-full bg-[#fbbf24]/5 blur-[150px]"></div>
        </div>
        <div class="container mx-auto px-4 sm:px-6 text-center">
            <h2 class="text-2xl sm:text-4xl font-extrabold text-white mb-4">Ready to take control?</h2>
            <p class="text-gray-400 max-w-lg mx-auto mb-10">Join Casher and start tracking your finances with professional-grade tools. Free to get started, dark mode included.</p>
            <a href="{{ route('register') }}" class="inline-block rounded-full bg-[#fbbf24] px-10 py-4 text-black font-bold hover:bg-[#f59e0b] transition shadow-lg shadow-[#fbbf24]/20 text-lg">Create Your Account</a>
        </div>
    </section>

    <footer class="py-8 sm:py-12 border-t border-white/5 bg-black">
        <div class="container mx-auto px-4 sm:px-6 flex flex-col md:flex-row justify-between items-center gap-4 sm:gap-8">
            <div class="flex items-center gap-2">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#fbbf24] font-bold text-black shadow-[0_0_20px_rgba(251,191,36,0.3)]">C$</div>
                <span class="font-extrabold tracking-tight text-white italic">CASHER</span>
            </div>
            
            <p class="text-xs text-gray-600 font-medium uppercase tracking-[0.3em]">
                &copy; 2026 Jaroslav Rašovský. All rights reserved.
            </p>
        </div>
    </footer>

</body>
</html>