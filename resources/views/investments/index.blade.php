@extends('layouts.app')

@section('header')
    <h2 class="font-bold text-xl t-primary leading-tight">Investments</h2>
@endsection

@section('content')
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-6 space-y-6">
        @if(session('success'))
            <div class="flash-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="flash-error">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Stats --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="card p-5">
                <p class="text-xs uppercase tracking-widest t-muted font-bold">Portfolio Value</p>
                <p class="mt-2 text-2xl font-extrabold t-primary" id="stat-portfolio-value">{{ number_format($totalValue, 2, '.', ' ') }} {{ $currencySymbol }}</p>
                <p class="text-xs t-muted mt-1">{{ $defaultCurrency }}</p>
            </div>
            <div class="card p-5">
                <p class="text-xs uppercase tracking-widest t-muted font-bold">Invested Capital</p>
                <p class="mt-2 text-2xl font-extrabold t-primary">{{ number_format($totalCost, 2, '.', ' ') }} {{ $currencySymbol }}</p>
                <p class="text-xs t-muted mt-1">{{ $defaultCurrency }}</p>
            </div>
            <div class="card p-5">
                <p class="text-xs uppercase tracking-widest t-muted font-bold">Total P/L</p>
                <p class="mt-2 text-2xl font-extrabold {{ $profit >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-500 dark:text-red-400' }}" id="stat-profit">{{ number_format($profit, 2, '.', ' ') }} {{ $currencySymbol }}</p>
                <p class="text-xs mt-1 {{ $profitPct >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-500 dark:text-red-400' }}" id="stat-profit-pct">{{ number_format($profitPct, 2) }}%</p>
            </div>
            <div class="card p-5">
                <p class="text-xs uppercase tracking-widest t-muted font-bold">Daily / Monthly</p>
                <p class="mt-2 text-sm font-semibold t-primary">Daily: <span class="{{ $dailyChangePct >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-500 dark:text-red-400' }}">{{ number_format($dailyChangePct, 2) }}%</span></p>
                <p class="text-sm font-semibold t-primary">Monthly: <span class="{{ $monthlyChangePct >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-500 dark:text-red-400' }}">{{ number_format($monthlyChangePct, 2) }}%</span></p>
            </div>
        </div>

        {{-- Charts --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="card p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold t-primary">Daily Performance</h3>
                    <span class="text-xs t-muted">Last 30 days</span>
                </div>
                <canvas id="dailyChart" height="120"></canvas>
            </div>
            <div class="card p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold t-primary">Monthly Performance</h3>
                    <span class="text-xs t-muted">Last 12 months</span>
                </div>
                <canvas id="monthlyChart" height="120"></canvas>
            </div>
        </div>

        {{-- Holdings + Add Form --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 card p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold t-primary">Holdings</h3>
                    <div class="flex items-center gap-3">
                        <span id="live-indicator" class="flex items-center gap-1.5 text-xs t-muted">
                            <span id="live-dot" class="inline-block w-2 h-2 rounded-full bg-gray-400"></span>
                            <span id="live-label" title="Auto-updating every 15 minutes to save API requests.">Auto-updates (15m)</span>
                        </span>
                        <form method="POST" action="{{ route('investments.refresh') }}">
                            @csrf
                            <button class="text-xs font-bold px-4 py-2 rounded-lg bg-[#8b5cf6] text-white hover:bg-[#7c3aed] transition shadow-lg shadow-[#8b5cf6]/10">Refresh prices</button>
                        </form>
                    </div>
                </div>

                {{-- Mobile card view --}}
                <div class="md:hidden space-y-3">
                    @forelse($investments as $investment)
                        @php
                            $lastPrice = $investment->latestPrice?->price;
                            $lastPriceCurrency = $investment->latestPrice?->currency ?? 'USD';
                            $value = $lastPrice ? $lastPrice * $investment->quantity : null;
                            $pl = $lastPrice ? ($lastPrice - $investment->average_price) * $investment->quantity : null;
                            $plPct = $investment->average_price > 0 && $lastPrice ? (($lastPrice - $investment->average_price) / $investment->average_price) * 100 : null;
                            $valueInDefault = $value ? $team->convertToDefaultCurrency($value, $lastPriceCurrency) : null;
                            $plInDefault = $pl ? $team->convertToDefaultCurrency($pl, $lastPriceCurrency) : null;
                        @endphp
                        <div class="bg-gray-50 dark:bg-white/[0.03] rounded-xl p-4 border border-gray-200 dark:border-white/5" data-inv-id="{{ $investment->id }}">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <div class="font-bold t-primary text-lg">{{ $investment->symbol }}</div>
                                    <div class="text-xs t-muted">{{ $investment->name ?? ucfirst($investment->type) }}</div>
                                </div>
                                <div class="flex gap-3">
                                    <a href="{{ route('investments.edit', $investment) }}" class="text-xs font-bold text-[#8b5cf6] hover:text-[#a78bfa] transition">Edit</a>
                                    <form method="POST" action="{{ route('investments.destroy', $investment) }}" onsubmit="return confirm('Delete investment?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-xs font-bold text-red-500 hover:text-red-400 transition">Delete</button>
                                    </form>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-sm">
                                <div>
                                    <span class="text-[10px] uppercase tracking-widest t-muted font-bold">Qty</span>
                                    <div class="t-secondary">{{ number_format($investment->quantity, 8, '.', ' ') }}</div>
                                </div>
                                <div>
                                    <span class="text-[10px] uppercase tracking-widest t-muted font-bold">Avg Price</span>
                                    <div class="t-secondary">{{ number_format($investment->average_price, 2, '.', ' ') }} {{ $investment->currency }}</div>
                                </div>
                                <div>
                                    <span class="text-[10px] uppercase tracking-widest t-muted font-bold">Last Price</span>
                                    <div class="t-secondary" id="inv-mob-last-price-{{ $investment->id }}">
                                        @if($lastPrice)
                                            {{ number_format($lastPrice, 2, '.', ' ') }} {{ $lastPriceCurrency }}
                                        @else
                                            --
                                        @endif
                                    </div>
                                </div>
                                <div>
                                    <span class="text-[10px] uppercase tracking-widest t-muted font-bold">Value</span>
                                    <div id="inv-mob-value-{{ $investment->id }}">
                                        @if($valueInDefault)
                                            <span class="font-semibold t-primary">{{ number_format($valueInDefault, 2, '.', ' ') }} {{ $currencySymbol }}</span>
                                        @else
                                            <span class="t-muted">--</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div id="inv-mob-pl-{{ $investment->id }}" class="mt-2 pt-2 border-t border-gray-200 dark:border-white/5 {{ $plInDefault === null ? 'hidden' : '' }}">
                                <span class="text-[10px] uppercase tracking-widest t-muted font-bold">P/L</span>
                                <span class="ml-2 font-semibold text-sm {{ ($plInDefault ?? 0) >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-500 dark:text-red-400' }}">
                                    {{ $plInDefault !== null ? number_format($plInDefault, 2, '.', ' ').' '.$currencySymbol.' ('.number_format($plPct, 2).'%)' : '' }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center t-muted">No investments yet.</div>
                    @endforelse
                </div>

                {{-- Desktop table view --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="text-[10px] uppercase tracking-widest t-muted border-b border-gray-200 dark:border-white/10">
                            <tr>
                                <th class="text-left py-3">Asset</th>
                                <th class="text-right py-3">Qty</th>
                                <th class="text-right py-3">Avg Price</th>
                                <th class="text-right py-3">Last Price</th>
                                <th class="text-right py-3">Value</th>
                                <th class="text-right py-3">P/L</th>
                                <th class="text-right py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                            @forelse($investments as $investment)
                                @php
                                    $lastPrice = $investment->latestPrice?->price;
                                    $lastPriceCurrency = $investment->latestPrice?->currency ?? 'USD';
                                    $value = $lastPrice ? $lastPrice * $investment->quantity : null;
                                    $pl = $lastPrice ? ($lastPrice - $investment->average_price) * $investment->quantity : null;
                                    $plPct = $investment->average_price > 0 && $lastPrice ? (($lastPrice - $investment->average_price) / $investment->average_price) * 100 : null;
                                    $valueInDefault = $value ? $team->convertToDefaultCurrency($value, $lastPriceCurrency) : null;
                                    $plInDefault = $pl ? $team->convertToDefaultCurrency($pl, $lastPriceCurrency) : null;
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition" data-inv-id="{{ $investment->id }}">
                                    <td class="py-3">
                                        <div class="font-bold t-primary">{{ $investment->symbol }}</div>
                                        <div class="text-xs t-muted">{{ $investment->name ?? ucfirst($investment->type) }}</div>
                                    </td>
                                    <td class="py-3 text-right t-secondary">{{ number_format($investment->quantity, 8, '.', ' ') }}</td>
                                    <td class="py-3 text-right t-secondary">
                                        {{ number_format($investment->average_price, 2, '.', ' ') }} {{ $investment->currency }}
                                    </td>
                                    <td class="py-3 text-right t-secondary" id="inv-last-price-{{ $investment->id }}">
                                        @if($lastPrice)
                                            <div>{{ number_format($lastPrice, 2, '.', ' ') }} {{ $lastPriceCurrency }}</div>
                                        @else
                                            <span class="t-muted">--</span>
                                        @endif
                                    </td>
                                    <td class="py-3 text-right" id="inv-value-{{ $investment->id }}">
                                        @if($valueInDefault)
                                            <div class="font-semibold t-primary">{{ number_format($valueInDefault, 2, '.', ' ') }} {{ $currencySymbol }}</div>
                                            @if($lastPriceCurrency !== $defaultCurrency)
                                                <div class="text-xs t-muted">{{ number_format($value, 2, '.', ' ') }} {{ $lastPriceCurrency }}</div>
                                            @endif
                                        @else
                                            <span class="t-muted">--</span>
                                        @endif
                                    </td>
                                    <td class="py-3 text-right" id="inv-pl-{{ $investment->id }}">
                                        @if($plInDefault !== null)
                                            <span class="font-semibold {{ $plInDefault >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-500 dark:text-red-400' }}">
                                                {{ number_format($plInDefault, 2, '.', ' ') }} {{ $currencySymbol }}
                                                ({{ number_format($plPct, 2) }}%)
                                            </span>
                                        @else
                                            <span class="t-muted">--</span>
                                        @endif
                                    </td>
                                    <td class="py-3 text-right">
                                        <div class="flex justify-end gap-3">
                                            <a href="{{ route('investments.edit', $investment) }}" class="text-xs font-bold text-[#8b5cf6] hover:text-[#a78bfa] transition">Edit</a>
                                            <form method="POST" action="{{ route('investments.destroy', $investment) }}" onsubmit="return confirm('Delete investment?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="text-xs font-bold text-red-500 hover:text-red-400 transition">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-8 text-center t-muted">No investments yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card p-6 border-t-4 border-t-[#8b5cf6]">
                <h3 class="text-lg font-bold t-primary mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-plus-circle text-[#8b5cf6]"></i> Add Investment
                </h3>
                
                <form method="POST" action="{{ route('investments.store') }}" class="space-y-4" id="investmentForm">
                    @csrf
                    
                    {{-- Basic details (Grid) --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="label-dark mb-1.5 block" for="investmentType">Type</label>
                            <select name="type" id="investmentType" class="select-dark w-full">
                                <option value="stock">Stock</option>
                                <option value="crypto">Crypto</option>
                            </select>
                        </div>

                        <div>
                            <label class="label-dark mb-1.5 block" for="symbolInput">Symbol</label>
                            <div class="relative">
                                <input name="symbol" id="symbolInput" type="text" class="input-dark w-full focus:ring-[#8b5cf6] focus:border-[#8b5cf6]" placeholder="e.g. AAPL, BTC..." autocomplete="off" required maxlength="15">
                                <div id="symbolSuggestions" class="hidden absolute top-full left-0 z-20 mt-2 w-full overflow-hidden rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#18181b] shadow-xl"></div>
                            </div>
                        </div>

                        <div>
                            <label class="label-dark mb-1.5 block" for="nameInput">Name <span class="text-gray-400 dark:text-gray-500 font-normal lowercase">(optional)</span></label>
                            <input name="name" id="nameInput" type="text" class="input-dark w-full focus:ring-[#8b5cf6] focus:border-[#8b5cf6]" placeholder="e.g. Apple Inc.">
                        </div>

                        <div id="externalIdRow" class="hidden">
                            <label class="label-dark mb-1.5 block" for="externalIdInput">External ID <span class="text-gray-400 dark:text-gray-500 font-normal lowercase">(crypto)</span></label>
                            <input name="external_id" id="externalIdInput" type="text" class="input-dark w-full focus:ring-[#8b5cf6] focus:border-[#8b5cf6]" placeholder="e.g. bitcoin">
                        </div>
                    </div>

                    {{-- Financial details panel --}}
                    <div class="p-5 rounded-xl bg-gray-50/50 dark:bg-white/[0.03] border border-gray-100 dark:border-white/5" x-data="{ buyMode: 'quantity' }">
                        
                        {{-- Segmented control for Buy Mode --}}
                        <div class="flex justify-center mb-5">
                            <div class="bg-gray-200/50 dark:bg-white/5 p-1 rounded-xl inline-flex gap-1">
                                <button type="button" @click="buyMode = 'quantity'" :class="buyMode === 'quantity' ? 'bg-white dark:bg-[#18181b] shadow-sm text-[#8b5cf6] dark:text-[#fbbf24]' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'" class="px-5 py-2 rounded-lg text-sm font-bold transition">
                                    By Quantity
                                </button>
                                <button type="button" @click="buyMode = 'amount'" :class="buyMode === 'amount' ? 'bg-white dark:bg-[#18181b] shadow-sm text-[#8b5cf6] dark:text-[#fbbf24]' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'" class="px-5 py-2 rounded-lg text-sm font-bold transition">
                                    By Amount
                                </button>
                            </div>
                        </div>

                        <input type="hidden" name="buy_mode" :value="buyMode">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Toggleable: Quantity OR Amount -->
                            <div x-show="buyMode === 'quantity'">
                                <label class="label-dark mb-1.5 block" for="quantityInput">Quantity</label>
                                <input name="quantity" type="number" step="0.00000001" min="0.00000001" max="9999999999" id="quantityInput" class="input-dark w-full focus:ring-[#8b5cf6] focus:border-[#8b5cf6]" placeholder="e.g. 1.5" :required="buyMode === 'quantity'">
                            </div>
                            <div x-show="buyMode === 'amount'" style="display: none;">
                                <label class="label-dark mb-1.5 block" for="amountInput">Amount</label>
                                <input name="amount" type="number" step="0.01" min="0.01" max="9999999999" id="amountInput" class="input-dark w-full focus:ring-[#8b5cf6] focus:border-[#8b5cf6]" placeholder="e.g. 500.00" :required="buyMode === 'amount'">
                            </div>

                            <div>
                                <label class="label-dark mb-1.5 block" for="currencyInput">Currency</label>
                                <input name="currency" id="currencyInput" value="USD" type="text" class="input-dark w-full focus:ring-[#8b5cf6] focus:border-[#8b5cf6]" required>
                            </div>
                        </div>
                        
                        <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-500/10 border border-blue-100 dark:border-blue-500/20 rounded-lg flex gap-3 text-sm">
                            <i class="fa-solid fa-circle-info text-blue-500 mt-0.5"></i>
                            <p class="t-primary">The current average market price will be fetched and assigned automatically upon adding the investment.</p>
                        </div>
                    </div>

                    <button type="submit" class="btn-primary w-full py-3 text-sm flex justify-center items-center gap-2 hover:scale-[1.02] transition-transform">
                        <i class="fa-solid fa-check"></i> Add to Portfolio
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const typeSelect = document.getElementById('investmentType');
        const externalIdRow = document.getElementById('externalIdRow');
        const symbolInput = document.getElementById('symbolInput');
        const nameInput = document.getElementById('nameInput');
        const externalIdInput = document.getElementById('externalIdInput');
        const symbolSuggestions = document.getElementById('symbolSuggestions');
        const SEARCH_URL = '{{ route('investments.search') }}';

        let symbolSearchTimer = null;
        let symbolSearchAbortController = null;

        function toggleFields() {
            const isCrypto = typeSelect.value === 'crypto';
            if (isCrypto) {
                externalIdRow.classList.remove('hidden');
            } else {
                externalIdRow.classList.add('hidden');
                externalIdInput.value = '';
            }
        }

        function clearSuggestions() {
            symbolSuggestions.innerHTML = '';
            symbolSuggestions.classList.add('hidden');
        }

        function showSuggestions(items) {
            symbolSuggestions.innerHTML = '';

            if (!items.length) {
                const empty = document.createElement('div');
                empty.className = 'px-3 py-2 text-xs t-muted';
                empty.textContent = 'No matches found';
                symbolSuggestions.appendChild(empty);
                symbolSuggestions.classList.remove('hidden');
                return;
            }

            items.forEach((item) => {
                const row = document.createElement('button');
                row.type = 'button';
                row.className = 'w-full px-3 py-2 text-left hover:bg-gray-100 dark:hover:bg-white/5 transition border-b border-gray-200 dark:border-white/5 last:border-b-0';

                const top = document.createElement('div');
                top.className = 'text-sm font-semibold t-primary';
                top.textContent = item.symbol || '';

                const meta = document.createElement('div');
                meta.className = 'text-xs t-muted';

                if (typeSelect.value === 'crypto') {
                    meta.textContent = [item.name, item.external_id].filter(Boolean).join(' · ');
                } else {
                    meta.textContent = [item.name, item.exchange].filter(Boolean).join(' · ');
                }

                row.appendChild(top);
                row.appendChild(meta);

                row.addEventListener('click', () => {
                    symbolInput.value = (item.symbol || '').toUpperCase();
                    if (item.name) {
                        nameInput.value = item.name;
                    }

                    if (typeSelect.value === 'crypto') {
                        externalIdInput.value = item.external_id || '';
                    } else {
                        externalIdInput.value = '';
                    }

                    clearSuggestions();
                });

                symbolSuggestions.appendChild(row);
            });

            symbolSuggestions.classList.remove('hidden');
        }

        function fetchSymbolSuggestions() {
            const query = symbolInput.value.trim();
            if (!query) {
                clearSuggestions();
                return;
            }

            if (symbolSearchAbortController) {
                symbolSearchAbortController.abort();
            }

            symbolSearchAbortController = new AbortController();
            const params = new URLSearchParams({
                q: query,
                type: typeSelect.value,
            });

            fetch(`${SEARCH_URL}?${params.toString()}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                signal: symbolSearchAbortController.signal,
            })
                .then((res) => res.ok ? res.json() : [])
                .then((items) => {
                    if (!Array.isArray(items)) {
                        clearSuggestions();
                        return;
                    }
                    showSuggestions(items);
                })
                .catch((err) => {
                    if (err.name === 'AbortError') {
                        return;
                    }
                    clearSuggestions();
                });
        }

        toggleFields();
        typeSelect.addEventListener('change', toggleFields);

        typeSelect.addEventListener('change', () => {
            clearSuggestions();
            if (symbolInput.value.trim()) {
                fetchSymbolSuggestions();
            }
        });

        symbolInput.addEventListener('input', () => {
            if (symbolSearchTimer) {
                clearTimeout(symbolSearchTimer);
            }
            symbolSearchTimer = setTimeout(fetchSymbolSuggestions, 250);
        });

        symbolInput.addEventListener('focus', () => {
            if (symbolInput.value.trim()) {
                fetchSymbolSuggestions();
            }
        });

        document.addEventListener('click', (event) => {
            if (!symbolSuggestions.contains(event.target) && event.target !== symbolInput) {
                clearSuggestions();
            }
        });

        const dailyCtx = document.getElementById('dailyChart');
        const monthlyCtx = document.getElementById('monthlyChart');

        const dailySeries = @json($dailySeries);
        const monthlySeries = @json($monthlySeries);

        const isDark = document.documentElement.classList.contains('dark');
        const chartGridColor = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.06)';
        const chartTickColor = isDark ? '#6b7280' : '#94a3b8';

        if (dailyCtx) {
            new Chart(dailyCtx, {
                type: 'line',
                data: {
                    labels: dailySeries.labels ?? [],
                    datasets: [{
                        label: 'Portfolio value',
                        data: dailySeries.values ?? [],
                        borderColor: '#fbbf24',
                        backgroundColor: 'rgba(251, 191, 36, 0.08)',
                        fill: true,
                        tension: 0.35,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { ticks: { color: chartTickColor }, grid: { color: chartGridColor } },
                        x: { ticks: { color: chartTickColor }, grid: { color: chartGridColor } }
                    }
                }
            });
        }

        if (monthlyCtx) {
            new Chart(monthlyCtx, {
                type: 'line',
                data: {
                    labels: monthlySeries.labels ?? [],
                    datasets: [{
                        label: 'Portfolio value',
                        data: monthlySeries.values ?? [],
                        borderColor: '#8b5cf6',
                        backgroundColor: 'rgba(139, 92, 246, 0.08)',
                        fill: true,
                        tension: 0.35,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { ticks: { color: chartTickColor }, grid: { color: chartGridColor } },
                        x: { ticks: { color: chartTickColor }, grid: { color: chartGridColor } }
                    }
                }
            });
        }

        // ── Live price auto-update ───────────────────────────────────────
        const LIVE_PRICES_URL = '{{ route('investments.live-prices') }}';
        const POLL_MS = 900000; // refresh every 15 mins (limit API usage)

        function fmtNum(num, dec = 2) {
            if (num === null || num === undefined) return '--';
            const n = parseFloat(num);
            const sign = n < 0 ? '-' : '';
            const fixed = Math.abs(n).toFixed(dec);
            const [intPart, fracPart] = fixed.split('.');
            const intFmt = intPart.replace(/\B(?=(\d{3})+(?!\d))/g, '\u00a0');
            return sign + intFmt + '.' + fracPart;
        }

        function plColorClass(val) {
            return (val ?? 0) >= 0
                ? 'font-semibold text-emerald-600 dark:text-emerald-400'
                : 'font-semibold text-red-500 dark:text-red-400';
        }

        function updateLivePrices() {
            const dot   = document.getElementById('live-dot');
            const label = document.getElementById('live-label');

            if (dot) dot.className = 'inline-block w-2 h-2 rounded-full bg-yellow-400 animate-pulse';

            fetch(LIVE_PRICES_URL, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.json())
                .then(data => {
                    const sym         = data.currency_symbol;
                    const defCurrency = data.default_currency;

                    // ── Summary stats ────────────────────────────────────
                    const pvEl = document.getElementById('stat-portfolio-value');
                    if (pvEl) pvEl.textContent = fmtNum(data.total_value) + '\u00a0' + sym;

                    const profitEl = document.getElementById('stat-profit');
                    if (profitEl) {
                        profitEl.textContent = fmtNum(data.profit) + '\u00a0' + sym;
                        profitEl.className = profitEl.className
                            .replace(/\btext-(emerald|red)-\w+\b/g, '').trim()
                            + (data.profit >= 0
                                ? ' text-emerald-600 dark:text-emerald-400'
                                : ' text-red-500 dark:text-red-400');
                    }

                    const profitPctEl = document.getElementById('stat-profit-pct');
                    if (profitPctEl) {
                        profitPctEl.textContent = fmtNum(data.profit_pct) + '%';
                        profitPctEl.className = profitPctEl.className
                            .replace(/\btext-(emerald|red)-\w+\b/g, '').trim()
                            + (data.profit_pct >= 0
                                ? ' text-emerald-600 dark:text-emerald-400'
                                : ' text-red-500 dark:text-red-400');
                    }

                    // ── Per-investment rows ──────────────────────────────
                    data.investments.forEach(inv => {
                        const id = inv.id;

                        // Desktop – last price
                        const lpEl = document.getElementById('inv-last-price-' + id);
                        if (lpEl) {
                            lpEl.innerHTML = inv.last_price !== null
                                ? '<div>' + fmtNum(inv.last_price) + '\u00a0' + inv.last_price_currency + '</div>'
                                : '<span class="t-muted">--</span>';
                        }

                        // Desktop – value
                        const valEl = document.getElementById('inv-value-' + id);
                        if (valEl) {
                            if (inv.value_in_default !== null) {
                                let html = '<div class="font-semibold t-primary">'
                                    + fmtNum(inv.value_in_default) + '\u00a0' + sym + '</div>';
                                if (inv.last_price_currency !== defCurrency && inv.value_raw !== null) {
                                    html += '<div class="text-xs t-muted">'
                                        + fmtNum(inv.value_raw) + '\u00a0' + inv.last_price_currency + '</div>';
                                }
                                valEl.innerHTML = html;
                            } else {
                                valEl.innerHTML = '<span class="t-muted">--</span>';
                            }
                        }

                        // Desktop – P/L
                        const plEl = document.getElementById('inv-pl-' + id);
                        if (plEl) {
                            plEl.innerHTML = inv.pl_in_default !== null
                                ? '<span class="' + plColorClass(inv.pl_in_default) + '">'
                                    + fmtNum(inv.pl_in_default) + '\u00a0' + sym
                                    + ' (' + fmtNum(inv.pl_pct) + '%)</span>'
                                : '<span class="t-muted">--</span>';
                        }

                        // Mobile – last price
                        const mobLpEl = document.getElementById('inv-mob-last-price-' + id);
                        if (mobLpEl) {
                            mobLpEl.textContent = inv.last_price !== null
                                ? fmtNum(inv.last_price) + '\u00a0' + inv.last_price_currency
                                : '--';
                        }

                        // Mobile – value
                        const mobValEl = document.getElementById('inv-mob-value-' + id);
                        if (mobValEl) {
                            mobValEl.innerHTML = inv.value_in_default !== null
                                ? '<span class="font-semibold t-primary">'
                                    + fmtNum(inv.value_in_default) + '\u00a0' + sym + '</span>'
                                : '<span class="t-muted">--</span>';
                        }

                        // Mobile – P/L
                        const mobPlEl = document.getElementById('inv-mob-pl-' + id);
                        if (mobPlEl) {
                            if (inv.pl_in_default !== null) {
                                mobPlEl.classList.remove('hidden');
                                const span = mobPlEl.querySelector('span.ml-2');
                                if (span) {
                                    span.className = 'ml-2 font-semibold text-sm '
                                        + (inv.pl_in_default >= 0
                                            ? 'text-emerald-600 dark:text-emerald-400'
                                            : 'text-red-500 dark:text-red-400');
                                    span.textContent = fmtNum(inv.pl_in_default) + '\u00a0' + sym
                                        + ' (' + fmtNum(inv.pl_pct) + '%)';
                                }
                            } else {
                                mobPlEl.classList.add('hidden');
                            }
                        }
                    });

                    // ── Indicator: success ──────────────────────────────
                    if (dot) dot.className = 'inline-block w-2 h-2 rounded-full bg-emerald-500';
                    if (label) {
                        const now = new Date();
                        const ts = now.getHours().toString().padStart(2,'0') + ':'
                                 + now.getMinutes().toString().padStart(2,'0') + ':'
                                 + now.getSeconds().toString().padStart(2,'0');
                        label.textContent = 'Updated ' + ts;
                    }
                })
                .catch(() => {
                    if (dot)  dot.className = 'inline-block w-2 h-2 rounded-full bg-red-500';
                    if (label) label.textContent = 'Update failed';
                });
        }

        // First poll 5 s after page load, then every 30 s
        setTimeout(updateLivePrices, 5000);
        setInterval(updateLivePrices, POLL_MS);
    </script>
@endpush
