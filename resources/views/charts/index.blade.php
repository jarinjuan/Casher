@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-black dark:text-gray-200 leading-tight">Charts & Analytics</h2>
@endsection

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Expenses by Category (Pie) -->
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
        <h3 class="font-semibold text-lg text-gray-900 dark:text-gray-100 mb-4">Expenses by Category</h3>
        <div class="flex justify-center" style="height: 300px;">
            <canvas id="categoryPie"></canvas>
        </div>
    </div>

    <!-- Income vs Expenses (Line Chart) -->
    @php
        $monthLabels = [];
        $incomeData = [];
        $expenseData = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthLabels[] = $date->format('M Y');
            
            $income = Auth::user()->transactions()
                ->where('type', 'income')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('amount');
            
            $expense = Auth::user()->transactions()
                ->where('type', 'expense')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('amount');
            
            $incomeData[] = $income;
            $expenseData[] = $expense;
        }
    @endphp
    
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
        <h3 class="font-semibold text-lg text-gray-900 dark:text-gray-100 mb-4">Income vs Expenses Trend (12 Months)</h3>
        <div style="height: 350px;">
            <canvas id="trendLineChart"></canvas>
        </div>
    </div>

    <!-- Monthly Comparison (Bar Chart) -->
    @php
        $last6Months = [];
        $last6IncomeData = [];
        $last6ExpenseData = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $last6Months[] = $date->format('M Y');
            
            $income = Auth::user()->transactions()
                ->where('type', 'income')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('amount');
            
            $expense = Auth::user()->transactions()
                ->where('type', 'expense')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('amount');
            
            $last6IncomeData[] = $income;
            $last6ExpenseData[] = $expense;
        }
    @endphp

    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
        <h3 class="font-semibold text-lg text-gray-900 dark:text-gray-100 mb-4">Monthly Cash Flow (Last 6 Months)</h3>
        <div style="height: 350px;">
            <canvas id="monthlyBarChart"></canvas>
        </div>
    </div>

    <!-- Income by Type Pie -->
    @php
        $salaryIncome = Auth::user()->transactions()
            ->where('type', 'income')
            ->where('title', 'like', '%salary%')
            ->sum('amount');
        
        $otherIncome = Auth::user()->transactions()
            ->where('type', 'income')
            ->whereNotLike('title', '%salary%')
            ->sum('amount');
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <h3 class="font-semibold text-lg text-gray-900 dark:text-gray-100 mb-4">Income Sources</h3>
            <div style="height: 300px;">
                <canvas id="incomeSourcePie"></canvas>
            </div>
        </div>

        <!-- Top Spending Categories -->
        @php
            $topCategories = Auth::user()->categories()
                ->with(['transactions' => function($q) {
                    $q->where('type', 'expense');
                }])
                ->get()
                ->map(function($cat) {
                    return [
                        'name' => $cat->name,
                        'total' => $cat->transactions->sum('amount')
                    ];
                })
                ->sortByDesc('total')
                ->take(5)
                ->values();
        @endphp

        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <h3 class="font-semibold text-lg text-gray-900 dark:text-gray-100 mb-4">Top Spending Categories</h3>
            <div style="height: 300px;">
                <canvas id="topCategoriesBar"></canvas>
            </div>
        </div>
    </div>
    
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        (function(){
            // Pie Chart - Expenses by Category
            const labels = @json($labels);
            const data = @json($data);
            const colors = @json($colors ?? []);

            const ctx1 = document.getElementById('categoryPie').getContext('2d');
            new Chart(ctx1, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: colors.length ? colors : labels.map((_,i)=>['#4f46e5','#06b6d4','#f59e0b','#ef4444','#10b981','#8b5cf6','#f97316','#60a5fa'][i%8])
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });

            // Line Chart - Income vs Expenses Trend (12 months)
            const trendMonths = @json($monthLabels);
            const trendIncomeData = @json($incomeData);
            const trendExpenseData = @json($expenseData);

            const ctx2 = document.getElementById('trendLineChart').getContext('2d');
            new Chart(ctx2, {
                type: 'line',
                data: {
                    labels: trendMonths,
                    datasets: [
                        {
                            label: 'Income',
                            data: trendIncomeData,
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            tension: 0.4,
                            fill: true
                        },
                        {
                            label: 'Expenses',
                            data: trendExpenseData,
                            borderColor: '#ef4444',
                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
                            tension: 0.4,
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top' }
                    },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });

            // Bar Chart - Monthly Cash Flow (Last 6 months)
            const monthlyLabels = @json($last6Months);
            const monthlyIncome = @json($last6IncomeData);
            const monthlyExpense = @json($last6ExpenseData);

            const ctx3 = document.getElementById('monthlyBarChart').getContext('2d');
            new Chart(ctx3, {
                type: 'bar',
                data: {
                    labels: monthlyLabels,
                    datasets: [
                        {
                            label: 'Income',
                            data: monthlyIncome,
                            backgroundColor: '#eab308'
                        },
                        {
                            label: 'Expenses',
                            data: monthlyExpense,
                            backgroundColor: '#3b82f6'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top' }
                    },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });

            // Pie Chart - Income Sources
            const incomeSources = ['Salary', 'Other'];
            const incomeValues = [@json($salaryIncome), @json($otherIncome)];

            const ctx4 = document.getElementById('incomeSourcePie').getContext('2d');
            new Chart(ctx4, {
                type: 'doughnut',
                data: {
                    labels: incomeSources,
                    datasets: [{
                        data: incomeValues,
                        backgroundColor: ['#10b981', '#6366f1']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });

            // Bar Chart - Top Spending Categories
            const topCatsData = @json($topCategories);
            const topCatLabels = topCatsData.map(c => c.name);
            const topCatValues = topCatsData.map(c => c.total);

            const ctx5 = document.getElementById('topCategoriesBar').getContext('2d');
            new Chart(ctx5, {
                type: 'bar',
                data: {
                    labels: topCatLabels,
                    datasets: [{
                        label: 'Total Spent',
                        data: topCatValues,
                        backgroundColor: ['#ef4444', '#f97316', '#f59e0b', '#eab308', '#06b6d4']
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: { beginAtZero: true }
                    }
                }
            });
        })();
    </script>
@endpush

@endsection
