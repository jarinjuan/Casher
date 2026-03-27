@extends('layouts.app')

@section('header')
    <h2 class="font-bold text-xl t-primary leading-tight">{{ __('Investments') }}</h2>
@endsection

@section('content')
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-6 space-y-6">
        @if($errors->has('refresh'))
            <div class="flash-error mb-6 flex items-center gap-3">
                <i class="fa-solid fa-circle-exclamation"></i>
                <div class="text-sm font-medium leading-tight">{{ $errors->first('refresh') }}</div>
            </div>
        @endif
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="card p-5">
                <p class="text-xs uppercase tracking-widest t-muted font-bold">{{ __('Portfolio value') }}</p>
                <p class="mt-2 text-2xl font-extrabold t-primary" id="stat-portfolio-value">@money($totalValue) {{ $currencySymbol }}</p>
                <p class="text-xs t-muted mt-1">{{ $defaultCurrency }}</p>
            </div>
            <div class="card p-5">
                <p class="text-xs uppercase tracking-widest t-muted font-bold">{{ __('Invested capital') }}</p>
                <p class="mt-2 text-2xl font-extrabold t-primary">@money($totalCost) {{ $currencySymbol }}</p>
                <p class="text-xs t-muted mt-1">{{ $defaultCurrency }}</p>
            </div>
            <div class="card p-5">
                <p class="text-xs uppercase tracking-widest t-muted font-bold">{{ __('Total P/L') }}</p>
                <p class="mt-2 text-2xl font-extrabold {{ $profit >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-500 dark:text-red-400' }}" id="stat-profit">@money($profit) {{ $currencySymbol }}</p>
                <p class="text-xs mt-1 {{ $profitPct >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-500 dark:text-red-400' }}" id="stat-profit-pct">@money($profitPct)%</p>
            </div>
            <div class="card p-5">
                <p class="text-xs uppercase tracking-widest t-muted font-bold">{{ __('Daily / Monthly') }}</p>
                <p class="mt-2 text-sm font-semibold t-primary">{{ __('Daily') }}: <span class="{{ $dailyChangePct >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-500 dark:text-red-400' }}">@money($dailyChangePct)%</span></p>
                <p class="text-sm font-semibold t-primary">{{ __('Monthly') }}: <span class="{{ $monthlyChangePct >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-500 dark:text-red-400' }}">@money($monthlyChangePct)%</span></p>
            </div>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="card p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold t-primary">{{ __('Daily performance') }}</h3>
                    <span class="text-xs t-muted">{{ __('Last 30 days') }}</span>
                </div>
                <canvas id="dailyChart" height="120"></canvas>
            </div>
            <div class="card p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold t-primary">{{ __('Monthly performance') }}</h3>
                    <span class="text-xs t-muted">{{ __('Last 12 months') }}</span>
                </div>
                <canvas id="monthlyChart" height="120"></canvas>
            </div>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 card p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold t-primary">{{ __('Holdings') }}</h3>
                    <div class="flex items-center gap-3">
                        <span id="live-indicator" class="flex items-center gap-1.5 text-xs t-muted">
                            <span id="live-dot" class="inline-block w-2 h-2 rounded-full bg-gray-400"></span>
                            <span id="live-label" title="Auto-updating every 15 minutes to save API requests.">{{ __('Auto-updates (15m)') }}</span>
                        </span>
                        @can('create', \App\Models\Investment::class)
                            <form method="POST" action="{{ route('investments.refresh') }}">
                                @csrf
                                <button class="text-xs font-bold px-4 py-2 rounded-lg bg-[#8b5cf6] text-white hover:bg-[#7c3aed] transition shadow-lg shadow-[#8b5cf6]/10">{{ __('Refresh prices') }}</button>
                            </form>
                        @endcan
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse($investments as $investment)
                        @php
                            $lastPrice = $investment->latestPrice?->price;
                            $lastPriceCurrency = $investment->latestPrice?->currency ?? 'USD';
                            $value = $lastPrice ? $lastPrice * $investment->quantity : null;
                            $valueInDefault = $value ? $team->convertToDefaultCurrency($value, $lastPriceCurrency) : null;

                            // Convert cost to default currency from investment's stored currency
                            $costInOriginal = $investment->average_price * $investment->quantity;
                            $costInDefault = $team->convertToDefaultCurrency($costInOriginal, $investment->currency);

                            // P/L computed in default currency to avoid mixing currencies
                            $plInDefault = $valueInDefault !== null ? $valueInDefault - $costInDefault : null;
                            $plPct = $costInDefault > 0 && $valueInDefault !== null
                                ? (($valueInDefault - $costInDefault) / $costInDefault) * 100 : null;
                        @endphp
                        <div class="bg-gray-50 dark:bg-white/[0.03] rounded-xl p-5 flex flex-col gap-2 hover:border-[#fbbf24]/30 transition group relative" data-inv-id="{{ $investment->id }}">
                            <div class="flex items-center gap-3 mb-1">
                                <div class="flex items-center justify-center w-9 h-9 rounded-lg shrink-0 bg-[#8b5cf6]/10 text-[#8b5cf6]">
                                    @if($investment->type === 'crypto')
                                        <i class="fa-brands fa-bitcoin"></i>
                                    @else
                                        <i class="fa-solid fa-building"></i>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="truncate font-bold t-primary text-lg">{{ $investment->symbol }}</div>
                                    <div class="truncate text-xs t-muted">{{ $investment->name ?? ucfirst($investment->type) }}</div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4 text-sm mt-2">
                                <div>
                                    <div class="text-[10px] uppercase tracking-widest t-muted font-bold">{{ __('Qty') }}</div>
                                    <div class="font-semibold t-secondary">@money($investment->quantity, 6)</div>
                                </div>
                                <div>
                                    <div class="text-[10px] uppercase tracking-widest t-muted font-bold">{{ __('Avg price') }}</div>
                                    <div class="font-semibold t-secondary">@money($investment->average_price) {{ $investment->currency }}</div>
                                </div>
                                <div>
                                    <div class="text-[10px] uppercase tracking-widest t-muted font-bold">{{ __('Last Price') }}</div>
                                    <div class="font-semibold t-secondary" id="inv-last-price-{{ $investment->id }}">
                                        @if($lastPrice)
                                            @money($lastPrice) {{ $lastPriceCurrency }}
                                        @else
                                            --
                                        @endif
                                    </div>
                                </div>
                                <div>
                                    <div class="text-[10px] uppercase tracking-widest t-muted font-bold">{{ __('Value') }}</div>
                                    <div id="inv-value-{{ $investment->id }}">
                                        @if($valueInDefault)
                                            <span class="font-semibold t-primary">@money($valueInDefault) {{ $currencySymbol }}</span>
                                        @else
                                            <span class="t-muted">--</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div id="inv-pl-{{ $investment->id }}" class="mt-4 pt-3 border-t border-gray-200 dark:border-white/5 {{ $plInDefault === null ? 'hidden' : '' }}">
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] uppercase tracking-widest t-muted font-bold">{{ __('Profit / Loss') }}</span>
                                    <span class="font-bold {{ ($plInDefault ?? 0) >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-500 dark:text-red-400' }}">
                                        {{ $plInDefault !== null ? ($plInDefault >= 0 ? '+' : '').\App\Helpers\Number::format($plInDefault).' '.$currencySymbol : '' }} 
                                        @if($plPct !== null)
                                            <span class="text-xs ml-1">(@money($plPct)%)</span>
                                        @endif
                                    </span>
                                </div>
                            </div>

                            @if(auth()->user()->canEdit($investment->team_id))
                                <div class="mt-3 pt-3 border-t border-gray-100 dark:border-white/5 flex gap-2 w-full appearance-none">
                                    <a href="{{ route('investments.edit', $investment) }}" class="flex-1 flex justify-center items-center rounded-lg px-2 py-2 text-xs font-bold text-[#8b5cf6] bg-[#8b5cf6]/10 hover:bg-[#8b5cf6]/20 transition">{{ __('Edit') }}</a>
                                    <form method="POST" action="{{ route('investments.destroy', $investment) }}" 
                                        x-data
                                        @submit.prevent="$dispatch('confirm', {
                                            title: '{{ __('Delete investment?') }}',
                                            message: '{{ __('Are you sure you want to delete this investment? All historical price data for this holding will rest in peace.') }}',
                                            confirmText: '{{ __('Delete') }}',
                                            variant: 'danger',
                                            onConfirm: () => $el.submit()
                                        })"
                                        class="flex-1 flex">
                                        @csrf
                                        @method('DELETE')
                                        <button class="w-full flex justify-center items-center rounded-lg px-2 py-2 text-xs font-bold text-red-500 bg-red-500/10 hover:bg-red-500/20 transition">{{ __('Delete') }}</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="col-span-1 md:col-span-2 py-8 text-center t-secondary text-sm border border-dashed border-gray-300 dark:border-white/10 rounded-xl">{{ __('No investments yet.') }}</div>
                    @endforelse
                </div>
            </div>

            @can('create', \App\Models\Investment::class)
                <div class="card p-6">
                    <h3 class="text-lg font-bold t-primary mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-plus-circle text-[#8b5cf6]"></i> {{ __('Add investment') }}
                    </h3>
                    
                    <form method="POST" action="{{ route('investments.store') }}" class="space-y-4" id="investmentForm">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="label-dark mb-1.5 block" for="investmentType">{{ __('Type') }}</label>
                                <select name="type" id="investmentType" class="select-dark w-full">
                                    <option value="stock">{{ __('Stock') }}</option>
                                    <option value="crypto">{{ __('Crypto') }}</option>
                                </select>
                            </div>

                            <div>
                                <label class="label-dark mb-1.5 block" for="symbolInput">{{ __('Symbol') }}</label>
                                <div class="relative">
                                    <input name="symbol" id="symbolInput" type="text" class="input-dark w-full focus:ring-[#8b5cf6] focus:border-[#8b5cf6]" placeholder="ex. AAPL, BTC..." autocomplete="off" required maxlength="15" value="{{ old('symbol') }}">
                                    <div id="symbolSuggestions" class="hidden absolute top-full left-0 z-20 mt-2 w-full overflow-hidden rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#18181b] shadow-xl"></div>
                                    @error('symbol')
                                        <div class="mt-1.5 text-xs text-red-500 font-bold flex items-center gap-1 backdrop-blur-sm bg-red-500/5 p-2 rounded-lg border border-red-500/20">
                                            <i class="fa-solid fa-circle-exclamation"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <input type="hidden" name="name" id="nameInput">
                            <input type="hidden" name="external_id" id="externalIdInput">
                        </div>
                        <div class="p-5 rounded-xl bg-gray-50/50 dark:bg-white/[0.03] border border-gray-100 dark:border-white/5" x-data="{ buyMode: 'quantity' }">
                            <div class="flex justify-center mb-5">
                                <div class="bg-gray-200/50 dark:bg-white/5 p-1 rounded-xl inline-flex gap-1">
                                    <button type="button" @click="buyMode = 'quantity'" :class="buyMode === 'quantity' ? 'bg-white dark:bg-[#18181b] shadow-sm text-[#8b5cf6] dark:text-[#fbbf24]' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'" class="px-5 py-2 rounded-lg text-sm font-bold transition">
                                        {{ __('By quantity') }}
                                    </button>
                                    <button type="button" @click="buyMode = 'amount'" :class="buyMode === 'amount' ? 'bg-white dark:bg-[#18181b] shadow-sm text-[#8b5cf6] dark:text-[#fbbf24]' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'" class="px-5 py-2 rounded-lg text-sm font-bold transition">
                                        {{ __('By amount') }}
                                    </button>
                                </div>
                            </div>

                            <input type="hidden" name="buy_mode" :value="buyMode">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div x-show="buyMode === 'quantity'">
                                    <label class="label-dark mb-1.5 block" for="quantityInput">{{ __('Quantity') }}</label>
                                    <input name="quantity" type="number" step="0.00000001" min="0.00000001" max="9999999999" id="quantityInput" class="input-dark w-full focus:ring-[#8b5cf6] focus:border-[#8b5cf6]" :required="buyMode === 'quantity'">
                                </div>
                                <div x-show="buyMode === 'amount'" style="display: none;">
                                    <label class="label-dark mb-1.5 flex justify-between items-center" for="amountInput">
                                        <span>{{ __('Amount') }}</span>
                                        <span class="text-xs px-2 py-0.5 rounded bg-[#8b5cf6]/20 text-[#8b5cf6] font-bold">{{ $defaultCurrency }}</span>
                                    </label>
                                    <input name="amount" type="number" step="0.01" min="0.01" max="9999999999" id="amountInput" class="input-dark w-full focus:ring-[#8b5cf6] focus:border-[#8b5cf6]" :required="buyMode === 'amount'">
                                </div>
                            </div>

                            
                        </div>

                        <button type="submit" class="btn-primary w-full py-3 text-sm flex justify-center items-center gap-2 hover:scale-[1.02] transition-transform">
                            <i class="fa-solid fa-check"></i> {{ __('Add to portfolio') }}
                        </button>
                    </form>
                </div>
            @endcan
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const typeSelect = document.getElementById('investmentType');
        const symbolInput = document.getElementById('symbolInput');
        const nameInput = document.getElementById('nameInput');
        const externalIdInput = document.getElementById('externalIdInput');
        const symbolSuggestions = document.getElementById('symbolSuggestions');
        const SEARCH_URL = '{{ route('investments.search') }}';
        const PRICE_URL = '{{ route('investments.price') }}';

        function fmtNum(num, dec = 2) {
            if (num === null || num === undefined) return '--';
            const n = parseFloat(num);
            const sign = n < 0 ? '-' : '';
            const fixed = Math.abs(n).toFixed(dec);
            const [intPart, fracPart] = fixed.split('.');
            const intFmt = intPart.replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
            return sign + intFmt + '.' + fracPart;
        }

        let symbolSearchTimer = null;
        let symbolSearchAbortController = null;
        let currentDefaultCurrency = '{{ $defaultCurrency }}';
        
        const quantityInput = document.getElementById('quantityInput');
        const amountInput = document.getElementById('amountInput');

        function toggleFields() {
            const isCrypto = typeSelect.value === 'crypto';
            if (!isCrypto) {
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

                        // Unified Card – last price
                        const lpEl = document.getElementById('inv-last-price-' + id);
                        if (lpEl) {
                            lpEl.textContent = inv.last_price !== null
                                ? fmtNum(inv.last_price) + ' ' + inv.last_price_currency
                                : '--';
                        }

                        // Unified Card – value
                        const valEl = document.getElementById('inv-value-' + id);
                        if (valEl) {
                            if (inv.value_in_default !== null) {
                                valEl.innerHTML = '<span class="font-semibold t-primary">'
                                    + fmtNum(inv.value_in_default) + ' ' + sym + '</span>';
                            } else {
                                valEl.innerHTML = '<span class="t-muted">--</span>';
                            }
                        }

                        // Unified Card – P/L
                        const plEl = document.getElementById('inv-pl-' + id);
                        if (plEl) {
                            if (inv.pl_in_default !== null) {
                                plEl.classList.remove('hidden');
                                plEl.innerHTML = `
                                    <div class="flex items-center justify-between">
                                        <span class="text-[10px] uppercase tracking-widest t-muted font-bold">Profit / Loss</span>
                                        <span class="font-bold ${plColorClass(inv.pl_in_default)}">
                                            ${inv.pl_in_default >= 0 ? '+' : ''}${fmtNum(inv.pl_in_default)} ${sym}
                                            <span class="text-xs ml-1">(${fmtNum(inv.pl_pct)}%)</span>
                                        </span>
                                    </div>
                                `;
                            } else {
                                plEl.classList.add('hidden');
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
                        label.textContent = '{{ __('Updated') }} ' + ts;
                    }
                })
                .catch(() => {
                    if (dot)  dot.className = 'inline-block w-2 h-2 rounded-full bg-red-500';
                    if (label) label.textContent = '{{ __('Update failed') }}';
                });
        }

        // First poll 5 s after page load, then every 30 s
        setTimeout(updateLivePrices, 5000);
        setInterval(updateLivePrices, POLL_MS);
    </script>
@endpush
