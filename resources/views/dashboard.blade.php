<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Stats Cards Grid -->
            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <p class="text-sm text-gray-500">Overall balance</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">{{ number_format($totalBalance, 0) }} {{ $currencySymbol }}</p>
                    <p class="text-xs text-gray-500 mt-1">In {{ $defaultCurrency }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <p class="text-sm text-gray-500">Monthly expenses</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">{{ number_format($monthlyExpenses, 0) }} {{ $currencySymbol }}</p>
                    @if($expenseTrend !== 0)
                        <p class="text-sm font-semibold mt-2 {{ $expenseTrend >= 0 ? 'text-red-600' : 'text-green-600' }}">
                            {{ $expenseTrend >= 0 ? '▲' : '▼' }} {{ abs($expenseTrend) > 0.1 ? number_format(abs($expenseTrend), 1) : '0.0' }}% vs. minulý měsíc
                        </p>
                    @endif
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <p class="text-sm text-gray-500">Monthly income</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">{{ number_format($monthlyIncome, 0) }} {{ $currencySymbol }}</p>
                    @if($incomeTrend !== 0)
                        <p class="text-sm font-semibold mt-2 {{ $incomeTrend >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $incomeTrend >= 0 ? '▲' : '▼' }} {{ abs($incomeTrend) > 0.1 ? number_format(abs($incomeTrend), 1) : '0.0' }}% vs. minulý měsíc
                        </p>
                    @endif
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                    <div>
                        <p class="text-sm text-gray-500">Expense forecast</p>
                        <p class="text-2xl font-bold text-gray-900 mt-2 dark:text-white">{{ number_format($forecast, 0) }} {{ $currencySymbol }}</p>
                        <p class="mt-2 text-xs text-slate-500">Průměr za posledních 6 měsíců</p>
                    </div>
                </div>
            </div>

            <!-- Expenses vs Income Chart (6 months) -->
            <x-chart 
                title="Expenses vs Income ({{ $defaultCurrency }})"
                type="bar"
                :labels="$months"
                :datasets="$chartDatasets"
                height="80"
            />

            @if($categories->count() > 0)
            <div class="mt-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Monthly budget</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($categories as $cat)
                        @php
                            $spent = $cat->getMonthlySpent();
                            $budget = $cat->monthly_budget;
                            $percentage = $cat->getMonthlyBudgetPercentage();
                            $isExceeded = $spent > $budget;
                            $symbol = match($cat->budget_currency) {
                                'CZK' => 'Kč',
                                'EUR' => '€',
                                'USD' => '$',
                                'GBP' => '£',
                                'JPY' => '¥',
                                default => $cat->budget_currency
                            };
                        @endphp
                        <div class="bg-white dark:bg-gray-800 rounded shadow p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <div style="width:12px;height:12px;background:{{ $cat->color ?? '#4f46e5' }};border-radius:3px"></div>
                                <h4 class="font-medium text-gray-800 dark:text-gray-200">{{ $cat->name }}</h4>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3 mb-2">
                                <div class="bg-indigo-600 h-3 rounded-full transition-all" style="width: {{ min($percentage, 100) }}%; background-color: {{ $percentage >= 100 ? '#ef4444' : '#4f46e5' }}"></div>
                            </div>
                            <div class="text-sm">
                                <span class="font-semibold {{ $isExceeded ? 'text-red-600' : 'text-gray-700 dark:text-gray-300' }}">
                                    {{ number_format($spent, 2, ',', ' ') }}{{ $symbol }}
                                </span>
                                <span class="text-gray-500">/</span>
                                <span class="text-gray-700 dark:text-gray-300">
                                    {{ number_format($budget, 2, ',', ' ') }}{{ $symbol }}
                                </span>
                            </div>
                            @if($isExceeded)
                                <div class="mt-2 text-xs text-red-600 font-semibold">Překročeno o {{ number_format($spent - $budget, 2, ',', ' ') }}{{ $symbol }}</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>