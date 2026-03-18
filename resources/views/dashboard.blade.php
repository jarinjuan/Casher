<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl t-primary leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            
            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="card p-6">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-xs uppercase tracking-widest t-muted font-bold">Overall balance (Cash + Investments)</p>
                        
                    </div>
                    <p id="overall-balance-value" class="text-2xl font-extrabold t-primary mt-2">{{ number_format($totalBalance, 0) }} {{ $currencySymbol }}</p>
                    <p class="text-xs t-muted mt-1">In {{ $defaultCurrency }}</p>
                    <p id="overall-balance-cash" class="text-xs t-muted mt-1">Cash: {{ number_format($cashBalance, 0) }} {{ $currencySymbol }}</p>
                    <p id="overall-balance-investments" class="text-xs t-muted">Investments: {{ number_format($investmentPortfolioValue, 0) }} {{ $currencySymbol }}</p>
                </div>
                <div class="card p-6">
                    <p class="text-xs uppercase tracking-widest t-muted font-bold">Monthly expenses</p>
                    <p class="text-2xl font-extrabold t-primary mt-2">{{ number_format($monthlyExpenses, 0) }} {{ $currencySymbol }}</p>
                    @if($expenseTrend !== 0)
                        <p class="text-sm font-semibold mt-2 {{ $expenseTrend >= 0 ? 'text-red-500 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                            {{ $expenseTrend >= 0 ? '▲' : '▼' }} {{ abs($expenseTrend) > 0.1 ? number_format(abs($expenseTrend), 1) : '0.0' }}% vs. last month
                        </p>
                    @endif
                </div>
                <div class="card p-6">
                    <p class="text-xs uppercase tracking-widest t-muted font-bold">Monthly income</p>
                    <p class="text-2xl font-extrabold t-primary mt-2">{{ number_format($monthlyIncome, 0) }} {{ $currencySymbol }}</p>
                    @if($incomeTrend !== 0)
                        <p class="text-sm font-semibold mt-2 {{ $incomeTrend >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-500 dark:text-red-400' }}">
                            {{ $incomeTrend >= 0 ? '▲' : '▼' }} {{ abs($incomeTrend) > 0.1 ? number_format(abs($incomeTrend), 1) : '0.0' }}% vs. last month
                        </p>
                    @endif
                </div>
                <div class="card p-6">
                    <p class="text-xs uppercase tracking-widest t-muted font-bold">Expense forecast</p>
                    <p class="text-2xl font-extrabold t-primary mt-2">{{ number_format($forecast, 0) }} {{ $currencySymbol }}</p>
                    <p class="mt-2 text-xs t-muted">6 last months average</p>
                </div>
            </div>

            
            <x-chart
                title="Expenses vs Income ({{ $defaultCurrency }})"
                type="bar"
                :labels="$months"
                :datasets="$chartDatasets"
                wrapperClass="dashboard-income-expense-chart"
                height="80"
            />

        
            @if($categories->count() > 0)
            <div class="mt-6">
                <h3 class="text-lg font-bold t-primary mb-4">Monthly budget</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($categories as $cat)
                        @php
                            $spent = $cat->getMonthlySpent();
                            $budget = $cat->monthly_budget;
                            $percentage = $cat->getMonthlyBudgetPercentage();
                            $isExceeded = $spent > $budget;
                            $symbol = match($cat->budget_currency) {
                                'CZK' => 'CZK',
                                'EUR' => '€',
                                'USD' => '$',
                                'GBP' => '£',
                                'JPY' => '¥',
                                'CHF' => '₣',
                                'PLN' => 'zł',
                                'SEK' => 'kr',
                                'NOK' => 'kr',
                                'DKK' => 'kr',
                                default => $cat->budget_currency
                            };
                        @endphp
                        <div class="card p-5">
                            <div class="flex items-center gap-2 mb-3">
                                <div style="width:10px;height:10px;background:{{ $cat->color ?? '#fbbf24' }};border-radius:3px"></div>
                                <h4 class="font-semibold t-primary text-sm">{{ $cat->name }}</h4>
                            </div>
                            <div class="w-full bg-gray-100 dark:bg-white/5 rounded-full h-2 mb-2 overflow-hidden">
                                <div class="h-full rounded-full transition-all {{ $percentage >= 100 ? 'bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.5)]' : 'bg-[#8b5cf6] shadow-[0_0_8px_rgba(139,92,246,0.3)]' }}" style="width: {{ min($percentage, 100) }}%"></div>
                            </div>
                            <div class="text-sm">
                                <span class="font-semibold {{ $isExceeded ? 'text-red-500 dark:text-red-400' : 't-primary' }}">
                                    {{ number_format($spent, 2, ',', ' ') }} {{ $symbol }}
                                </span>
                                <span class="t-muted">/</span>
                                <span class="t-secondary">
                                    {{ number_format($budget, 2, ',', ' ') }} {{ $symbol }}
                                </span>
                            </div>
                            @if($isExceeded)
                                <div class="mt-2 text-xs text-red-500 dark:text-red-400 font-semibold">Exceeded by {{ number_format($spent - $budget, 2, ',', ' ') }} {{ $symbol }}</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>

@push('scripts')
    <script>
        const DASHBOARD_LIVE_BALANCE_URL = '{{ route('dashboard.live-balance') }}';
        const DASHBOARD_POLL_MS = 30000;

        function fmtWhole(value) {
            const num = Number(value ?? 0);
            return num.toLocaleString('en-US').replace(/,/g, ' ');
        }

        function updateDashboardBalance() {
            const dot = document.getElementById('balance-live-dot');
            const label = document.getElementById('balance-live-label');

            if (dot) {
                dot.className = 'inline-block w-2 h-2 rounded-full bg-yellow-400 animate-pulse';
            }

            fetch(DASHBOARD_LIVE_BALANCE_URL, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(response => response.json())
                .then(data => {
                    const symbol = data.currency_symbol;

                    const totalEl = document.getElementById('overall-balance-value');
                    if (totalEl) {
                        totalEl.textContent = fmtWhole(data.total_balance) + ' ' + symbol;
                    }

                    const cashEl = document.getElementById('overall-balance-cash');
                    if (cashEl) {
                        cashEl.textContent = 'Cash: ' + fmtWhole(data.cash_balance) + ' ' + symbol;
                    }

                    const invEl = document.getElementById('overall-balance-investments');
                    if (invEl) {
                        invEl.textContent = 'Investments: ' + fmtWhole(data.investment_portfolio_value) + ' ' + symbol;
                    }

                    if (dot) {
                        dot.className = 'inline-block w-2 h-2 rounded-full bg-emerald-500';
                    }

                    if (label) {
                        const now = new Date();
                        const ts = now.getHours().toString().padStart(2, '0') + ':'
                            + now.getMinutes().toString().padStart(2, '0') + ':'
                            + now.getSeconds().toString().padStart(2, '0');
                        label.textContent = 'Updated ' + ts;
                    }
                })
                .catch(() => {
                    if (dot) {
                        dot.className = 'inline-block w-2 h-2 rounded-full bg-red-500';
                    }
                    if (label) {
                        label.textContent = 'Update failed';
                    }
                });
        }

        setTimeout(updateDashboardBalance, 5000);
        setInterval(updateDashboardBalance, DASHBOARD_POLL_MS);
    </script>
@endpush