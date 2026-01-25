<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
            
        </h2>

        
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg">
                        {{ __("Welcome back, ") }} <strong>{{ Auth::user()->name }}</strong>!
                    </h3>
                    
                    <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        Current workspace: 
                        <span class="font-bold text-indigo-500 text-base">
                            {{ Auth::user()->currentTeam->name }}
                        </span>
                    </div>
                </div>
            </div>

            @php
                $user = Auth::user();
                $month = now()->month;
                $year = now()->year;
                
                // Celkový zůstatek
                $allTransactions = $user->transactions()->get();
                $totalBalance = $allTransactions->sum(function($t) {
                    return $t->type === 'income' ? $t->amount : -$t->amount;
                });
                
                // Měsíční výdaje
                $monthlyExpenses = $user->transactions()
                    ->where('type', 'expense')
                    ->whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->sum('amount');
                
                // Měsíční příjmy
                $monthlyIncome = $user->transactions()
                    ->where('type', 'income')
                    ->whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->sum('amount');
                
                
                
                // Trend výdajů - minulý měsíc
                $lastMonthExpenses = $user->transactions()
                    ->where('type', 'expense')
                    ->whereYear('created_at', now()->subMonth()->year)
                    ->whereMonth('created_at', now()->subMonth()->month)
                    ->sum('amount');
                
                $expenseTrend = $lastMonthExpenses > 0 ? (($monthlyExpenses - $lastMonthExpenses) / $lastMonthExpenses * 100) : 0;
                
                // Trend příjmů - minulý měsíc
                $lastMonthIncome = $user->transactions()
                    ->where('type', 'income')
                    ->whereYear('created_at', now()->subMonth()->year)
                    ->whereMonth('created_at', now()->subMonth()->month)
                    ->sum('amount');
                
                $incomeTrend = $lastMonthIncome > 0 ? (($monthlyIncome - $lastMonthIncome) / $lastMonthIncome * 100) : 0;
            @endphp

            <!-- Stats Cards Grid -->
            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <p class="text-sm text-gray-500">Overall balance</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">{{ number_format($totalBalance, 0) }} Kč</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <p class="text-sm text-gray-500">Monthly expenses</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">{{ number_format($monthlyExpenses, 0) }} Kč</p>
                    @if($expenseTrend !== 0)
                        <p class="text-sm font-semibold mt-2 {{ $expenseTrend >= 0 ? 'text-red-600' : 'text-green-600' }}">
                            {{ $expenseTrend >= 0 ? '▲' : '▼' }} {{ abs($expenseTrend) > 0.1 ? number_format(abs($expenseTrend), 1) : '0.0' }}% vs. minulý měsíc
                        </p>
                    @endif
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <p class="text-sm text-gray-500">Monthly income</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">{{ number_format($monthlyIncome, 0) }} Kč</p>
                    @if($incomeTrend !== 0)
                        <p class="text-sm font-semibold mt-2 {{ $incomeTrend >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $incomeTrend >= 0 ? '▲' : '▼' }} {{ abs($incomeTrend) > 0.1 ? number_format(abs($incomeTrend), 1) : '0.0' }}% vs. minulý měsíc
                        </p>
                    @endif
                </div>
            </div>

            <!-- Expenses vs Income Chart (6 months) -->
            @php
                $months = [];
                $expenseData = [];
                $incomeData = [];
                
                for ($i = 5; $i >= 0; $i--) {
                    $date = now()->subMonths($i);
                    $months[] = $date->format('M Y');
                    
                    $expenses = Auth::user()->transactions()
                        ->where('type', 'expense')
                        ->whereYear('created_at', $date->year)
                        ->whereMonth('created_at', $date->month)
                        ->sum('amount');
                    
                    $income = Auth::user()->transactions()
                        ->where('type', 'income')
                        ->whereYear('created_at', $date->year)
                        ->whereMonth('created_at', $date->month)
                        ->sum('amount');
                    
                    $expenseData[] = $expenses;
                    $incomeData[] = $income;
                }
            @endphp

            <div class="mt-6 bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Expenses vs Income</h3>
                <canvas id="expensesVsIncomeChart" height="80"></canvas>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const ctx = document.getElementById('expensesVsIncomeChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: {!! json_encode($months) !!},
                            datasets: [
                                {
                                    label: 'Expenses',
                                    data: {!! json_encode($expenseData) !!},
                                    backgroundColor: '#3b82f6',
                                    borderColor: '#1e40af',
                                    borderWidth: 1
                                },
                                {
                                    label: 'Income',
                                    data: {!! json_encode($incomeData) !!},
                                    backgroundColor: '#eab308',
                                    borderColor: '#ca8a04',
                                    borderWidth: 1
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            plugins: {
                                legend: {
                                    labels: {
                                        color: document.documentElement.classList.contains('dark') ? '#e5e7eb' : '#374151'
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        color: document.documentElement.classList.contains('dark') ? '#9ca3af' : '#6b7280'
                                    },
                                    grid: {
                                        color: document.documentElement.classList.contains('dark') ? '#374151' : '#e5e7eb'
                                    }
                                },
                                x: {
                                    ticks: {
                                        color: document.documentElement.classList.contains('dark') ? '#9ca3af' : '#6b7280'
                                    },
                                    grid: {
                                        color: document.documentElement.classList.contains('dark') ? '#374151' : '#e5e7eb'
                                    }
                                }
                            }
                        }
                    });
                });
            </script>

            @php
                $categories = Auth::user()->categories()->where('monthly_budget', '>', 0)->get();
                $currencySymbols = ['CZK' => 'Kč', 'EUR' => '€', 'USD' => '$'];
            @endphp

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
                            $symbol = $currencySymbols[$cat->budget_currency] ?? $cat->budget_currency;
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