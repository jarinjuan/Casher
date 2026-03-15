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
                <p class="mt-2 text-2xl font-extrabold t-primary">{{ number_format($totalValue, 2, '.', ' ') }} {{ $currencySymbol }}</p>
                <p class="text-xs t-muted mt-1">{{ $defaultCurrency }}</p>
            </div>
            <div class="card p-5">
                <p class="text-xs uppercase tracking-widest t-muted font-bold">Invested Capital</p>
                <p class="mt-2 text-2xl font-extrabold t-primary">{{ number_format($totalCost, 2, '.', ' ') }} {{ $currencySymbol }}</p>
                <p class="text-xs t-muted mt-1">{{ $defaultCurrency }}</p>
            </div>
            <div class="card p-5">
                <p class="text-xs uppercase tracking-widest t-muted font-bold">Total P/L</p>
                <p class="mt-2 text-2xl font-extrabold {{ $profit >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-500 dark:text-red-400' }}">{{ number_format($profit, 2, '.', ' ') }} {{ $currencySymbol }}</p>
                <p class="text-xs mt-1 {{ $profitPct >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-500 dark:text-red-400' }}">{{ number_format($profitPct, 2) }}%</p>
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
                    <form method="POST" action="{{ route('investments.refresh') }}">
                        @csrf
                        <button class="text-xs font-bold px-4 py-2 rounded-lg bg-[#8b5cf6] text-white hover:bg-[#7c3aed] transition shadow-lg shadow-[#8b5cf6]/10">Refresh prices</button>
                    </form>
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
                        <div class="bg-gray-50 dark:bg-white/[0.03] rounded-xl p-4 border border-gray-200 dark:border-white/5">
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
                                    <div class="t-secondary">
                                        @if($lastPrice)
                                            {{ number_format($lastPrice, 2, '.', ' ') }} {{ $lastPriceCurrency }}
                                        @else
                                            --
                                        @endif
                                    </div>
                                </div>
                                <div>
                                    <span class="text-[10px] uppercase tracking-widest t-muted font-bold">Value</span>
                                    <div>
                                        @if($valueInDefault)
                                            <span class="font-semibold t-primary">{{ number_format($valueInDefault, 2, '.', ' ') }} {{ $currencySymbol }}</span>
                                        @else
                                            <span class="t-muted">--</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @if($plInDefault !== null)
                                <div class="mt-2 pt-2 border-t border-gray-200 dark:border-white/5">
                                    <span class="text-[10px] uppercase tracking-widest t-muted font-bold">P/L</span>
                                    <span class="ml-2 font-semibold text-sm {{ $plInDefault >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-500 dark:text-red-400' }}">
                                        {{ number_format($plInDefault, 2, '.', ' ') }} {{ $currencySymbol }}
                                        ({{ number_format($plPct, 2) }}%)
                                    </span>
                                </div>
                            @endif
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
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02] transition">
                                    <td class="py-3">
                                        <div class="font-bold t-primary">{{ $investment->symbol }}</div>
                                        <div class="text-xs t-muted">{{ $investment->name ?? ucfirst($investment->type) }}</div>
                                    </td>
                                    <td class="py-3 text-right t-secondary">{{ number_format($investment->quantity, 8, '.', ' ') }}</td>
                                    <td class="py-3 text-right t-secondary">
                                        {{ number_format($investment->average_price, 2, '.', ' ') }} {{ $investment->currency }}
                                    </td>
                                    <td class="py-3 text-right t-secondary">
                                        @if($lastPrice)
                                            <div>{{ number_format($lastPrice, 2, '.', ' ') }} {{ $lastPriceCurrency }}</div>
                                        @else
                                            <span class="t-muted">--</span>
                                        @endif
                                    </td>
                                    <td class="py-3 text-right">
                                        @if($valueInDefault)
                                            <div class="font-semibold t-primary">{{ number_format($valueInDefault, 2, '.', ' ') }} {{ $currencySymbol }}</div>
                                            @if($lastPriceCurrency !== $defaultCurrency)
                                                <div class="text-xs t-muted">{{ number_format($value, 2, '.', ' ') }} {{ $lastPriceCurrency }}</div>
                                            @endif
                                        @else
                                            <span class="t-muted">--</span>
                                        @endif
                                    </td>
                                    <td class="py-3 text-right">
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

            <div class="card p-6">
                <h3 class="text-lg font-bold t-primary mb-4">Add Investment</h3>
                <form method="POST" action="{{ route('investments.store') }}" class="space-y-3" id="investmentForm">
                    @csrf
                    <div>
                        <label class="label-dark">Type</label>
                        <select name="type" id="investmentType" class="select-dark">
                            <option value="stock">Stock</option>
                            <option value="crypto">Crypto</option>
                        </select>
                    </div>
                    <div>
                        <label class="label-dark">Symbol</label>
                        <input name="symbol" id="symbolInput" type="text" class="input-dark" placeholder="e.g. AAPL, BTC, ETH" required>
                    </div>
                    <div>
                        <label class="label-dark">Name (optional)</label>
                        <input name="name" id="nameInput" type="text" class="input-dark" placeholder="e.g. Apple Inc.">
                    </div>
                    <div id="externalIdRow" class="hidden">
                        <label class="label-dark">External ID (crypto)</label>
                        <input name="external_id" id="externalIdInput" class="input-dark" placeholder="coingecko id, e.g. bitcoin">
                    </div>
                    <div>
                        <label class="label-dark">Quantity</label>
                        <input name="quantity" type="number" step="0.00000001" class="input-dark" placeholder="e.g. 1.5" required>
                    </div>
                    <div>
                        <label class="label-dark">Average price (optional)</label>
                        <input name="average_price" type="number" step="0.00000001" class="input-dark" placeholder="Leave empty for auto-fetch">
                        <p class="mt-1 text-xs t-muted">If left empty, current price will be fetched</p>
                    </div>
                    <div>
                        <label class="label-dark">Currency</label>
                        <input name="currency" value="USD" class="input-dark" required>
                    </div>
                    <button class="btn-primary w-full text-sm">Add investment</button>
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

        function toggleFields() {
            const isCrypto = typeSelect.value === 'crypto';
            if (isCrypto) {
                externalIdRow.classList.remove('hidden');
            } else {
                externalIdRow.classList.add('hidden');
            }
        }

        toggleFields();
        typeSelect.addEventListener('change', toggleFields);

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
    </script>
@endpush
