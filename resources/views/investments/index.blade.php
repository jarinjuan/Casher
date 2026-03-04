@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-black dark:text-gray-200 leading-tight">
        Investments
    </h2>
@endsection

@section('content')
    <div class="max-w-6xl mx-auto p-6 space-y-6">
        @if(session('success'))
            <div class="p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="p-3 bg-red-100 text-red-800 rounded">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow p-5">
                <p class="text-xs uppercase tracking-widest text-gray-500">Portfolio Value</p>
                <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($totalValue, 2, '.', ' ') }} {{ $currencySymbol }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $defaultCurrency }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow p-5">
                <p class="text-xs uppercase tracking-widest text-gray-500">Invested Capital</p>
                <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($totalCost, 2, '.', ' ') }} {{ $currencySymbol }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $defaultCurrency }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow p-5">
                <p class="text-xs uppercase tracking-widest text-gray-500">Total P/L</p>
                <p class="mt-2 text-2xl font-bold {{ $profit >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ number_format($profit, 2, '.', ' ') }} {{ $currencySymbol }}</p>
                <p class="text-xs mt-1 {{ $profitPct >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ number_format($profitPct, 2) }}%</p>
            </div>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow p-5">
                <p class="text-xs uppercase tracking-widest text-gray-500">Daily / Monthly</p>
                <p class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">Daily: <span class="{{ $dailyChangePct >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ number_format($dailyChangePct, 2) }}%</span></p>
                <p class="text-sm font-semibold text-gray-900 dark:text-white">Monthly: <span class="{{ $monthlyChangePct >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ number_format($monthlyChangePct, 2) }}%</span></p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Daily Performance</h3>
                    <span class="text-xs text-gray-500">Last 30 days</span>
                </div>
                <canvas id="dailyChart" height="120"></canvas>
            </div>
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Monthly Performance</h3>
                    <span class="text-xs text-gray-500">Last 12 months</span>
                </div>
                <canvas id="monthlyChart" height="120"></canvas>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Holdings</h3>
                    <form method="POST" action="{{ route('investments.refresh') }}">
                        @csrf
                        <button class="text-xs font-semibold px-3 py-2 rounded bg-indigo-600 text-white hover:bg-indigo-700 transition">Refresh prices</button>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="text-xs uppercase text-gray-500 border-b border-gray-200 dark:border-gray-700">
                            <tr>
                                <th class="text-left py-2">Asset</th>
                                <th class="text-right py-2">Qty</th>
                                <th class="text-right py-2">Avg Price</th>
                                <th class="text-right py-2">Last Price</th>
                                <th class="text-right py-2">Value</th>
                                <th class="text-right py-2">P/L</th>
                                <th class="text-right py-2"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
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
                                <tr>
                                    <td class="py-3">
                                        <div class="font-semibold text-gray-900 dark:text-white">{{ $investment->symbol }}</div>
                                        <div class="text-xs text-gray-500">{{ $investment->name ?? ucfirst($investment->type) }}</div>
                                    </td>
                                    <td class="py-3 text-right text-gray-700 dark:text-gray-300">{{ number_format($investment->quantity, 8, '.', ' ') }}</td>
                                    <td class="py-3 text-right text-gray-700 dark:text-gray-300">
                                        {{ number_format($investment->average_price, 2, '.', ' ') }} {{ $investment->currency }}
                                    </td>
                                    <td class="py-3 text-right text-gray-700 dark:text-gray-300">
                                        @if($lastPrice)
                                            <div>{{ number_format($lastPrice, 2, '.', ' ') }} {{ $lastPriceCurrency }}</div>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="py-3 text-right text-gray-700 dark:text-gray-300">
                                        @if($valueInDefault)
                                            <div class="font-semibold">{{ number_format($valueInDefault, 2, '.', ' ') }} {{ $currencySymbol }}</div>
                                            @if($lastPriceCurrency !== $defaultCurrency)
                                                <div class="text-xs text-gray-500">{{ number_format($value, 2, '.', ' ') }} {{ $lastPriceCurrency }}</div>
                                            @endif
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="py-3 text-right">
                                        @if($plInDefault !== null)
                                            <span class="font-semibold {{ $plInDefault >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                                {{ number_format($plInDefault, 2, '.', ' ') }} {{ $currencySymbol }}
                                                ({{ number_format($plPct, 2) }}%)
                                            </span>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="py-3 text-right">
                                        <div class="flex justify-end gap-3">
                                            <a href="{{ route('investments.edit', $investment) }}" class="text-xs text-indigo-600 hover:text-indigo-700">Edit</a>
                                            <form method="POST" action="{{ route('investments.destroy', $investment) }}" onsubmit="return confirm('Delete investment?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="text-xs text-red-600 hover:text-red-700">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-6 text-center text-gray-500">No investments yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Add Investment</h3>
                <form method="POST" action="{{ route('investments.store') }}" class="space-y-4" id="investmentForm">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-gray-500">Type</label>
                        <select name="type" id="investmentType" class="w-full mt-1 border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="stock">Stock</option>
                            <option value="crypto">Crypto</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500">Symbol / Zkratka</label>
                        <input name="symbol" id="symbolInput" type="text" class="w-full mt-1 border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="např. AAPL, BTC, ETH" required>
                        <p class="mt-1 text-xs text-gray-500">Zadejte zkratku akcie nebo kryptoměny</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500">Název (nepovinné)</label>
                        <input name="name" id="nameInput" type="text" class="w-full mt-1 border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="např. Apple Inc.">
                    </div>

                    <div id="externalIdRow" class="hidden">
                        <label class="block text-xs font-semibold text-gray-500">External ID (pro krypto)</label>
                        <input name="external_id" id="externalIdInput" class="w-full mt-1 border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="coingecko id, e.g. bitcoin">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500">Množství</label>
                        <input name="quantity" type="number" step="0.00000001" class="w-full mt-1 border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="např. 1.5" required>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500">Průměrná cena (nepovinné)</label>
                        <input name="average_price" type="number" step="0.00000001" class="w-full mt-1 border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="ponechte prázdné pro automatické stáhnutí">
                        <p class="mt-1 text-xs text-gray-500">Pokud necháte prázdné, stáhne se aktuální cena</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500">Měna</label>
                        <input name="currency" value="USD" class="w-full mt-1 border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                    </div>

                    <button class="w-full bg-amber-400 hover:bg-amber-500 text-white font-semibold py-2 rounded-lg">Přidat investici</button>
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

        if (dailyCtx) {
            new Chart(dailyCtx, {
                type: 'line',
                data: {
                    labels: dailySeries.labels ?? [],
                    datasets: [{
                        label: 'Portfolio value',
                        data: dailySeries.values ?? [],
                        borderColor: '#fbbf24',
                        backgroundColor: 'rgba(251, 191, 36, 0.15)',
                        fill: true,
                        tension: 0.35,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: { ticks: { color: '#9ca3af' } },
                        x: { ticks: { color: '#9ca3af' } }
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
                        backgroundColor: 'rgba(139, 92, 246, 0.15)',
                        fill: true,
                        tension: 0.35,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: { ticks: { color: '#9ca3af' } },
                        x: { ticks: { color: '#9ca3af' } }
                    }
                }
            });
        }
    </script>
@endpush
