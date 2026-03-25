@extends('layouts.app')

@section('header')
    <h2 class="font-bold text-xl t-primary leading-tight">{{ __('Charts & analytics') }}</h2>
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 space-y-6">
    <div class="card p-6">
        <h3 class="font-bold text-lg t-primary mb-4">{{ __('Expenses by category') }}</h3>
        <div class="flex justify-center chart-h-sm">
            <canvas id="categoryPie"></canvas>
        </div>
    </div>

    <div class="card p-6">
        <h3 class="font-bold text-lg t-primary mb-4">{{ __('Income vs expenses trend (12 months)') }}</h3>
        <div class="chart-h-lg">
            <canvas id="trendLineChart"></canvas>
        </div>
    </div>

    <div class="card p-6">
        <h3 class="font-bold text-lg t-primary mb-4">{{ __('Monthly cash flow (last 6 months)') }}</h3>
        <div class="chart-h-lg">
            <canvas id="monthlyBarChart"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="card p-6">
            <h3 class="font-bold text-lg t-primary mb-4">{{ __('Income sources') }}</h3>
            <div class="chart-h-sm">
                <canvas id="incomeSourcePie"></canvas>
            </div>
        </div>

        <div class="card p-6">
            <h3 class="font-bold text-lg t-primary mb-4">{{ __('Top spending categories') }}</h3>
            <div class="chart-h-sm">
                <canvas id="topCategoriesBar"></canvas>
            </div>
        </div>
    </div>

</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        (function(){
            const isDark = document.documentElement.classList.contains('dark');
            const gridColor = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.06)';
            const tickColor = isDark ? '#6b7280' : '#94a3b8';
            const legendColor = isDark ? '#9ca3af' : '#475569';

            // 1. Expenses by Category
            new Chart(document.getElementById('categoryPie').getContext('2d'), {
                type: 'pie',
                data: {
                    labels: @json($expenseLabels),
                    datasets: [{
                        data: @json($expenseData),
                        backgroundColor: @json($expenseColors),
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom', labels: { color: legendColor } } }
                }
            });

            // 2. Trend Chart
            new Chart(document.getElementById('trendLineChart').getContext('2d'), {
                type: 'line',
                data: {
                    labels: @json($trendLabels),
                    datasets: [
                        { label: '{{ __('Income') }}', data: @json($trendIncome), borderColor: '#fbbf24', backgroundColor: 'rgba(251,191,36,0.08)', tension: 0.4, fill: true },
                        { label: '{{ __('Expenses') }}', data: @json($trendExpense), borderColor: '#8b5cf6', backgroundColor: 'rgba(139,92,246,0.08)', tension: 0.4, fill: true }
                    ]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { position: 'top', labels: { color: legendColor } } },
                    scales: { y: { beginAtZero: true, ticks: { color: tickColor }, grid: { color: gridColor } }, x: { ticks: { color: tickColor }, grid: { color: gridColor } } }
                }
            });

            // 3. Monthly Bar Chart
            new Chart(document.getElementById('monthlyBarChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: @json($last6Labels),
                    datasets: [
                        { label: '{{ __('Income') }}', data: @json($last6Income), backgroundColor: '#fbbf24', borderRadius: 4 },
                        { label: '{{ __('Expenses') }}', data: @json($last6Expense), backgroundColor: '#8b5cf6', borderRadius: 4 }
                    ]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { position: 'top', labels: { color: legendColor } } },
                    scales: { y: { beginAtZero: true, ticks: { color: tickColor }, grid: { color: gridColor } }, x: { ticks: { color: tickColor }, grid: { color: gridColor } } }
                }
            });

            // 4. Income Source Pie
            new Chart(document.getElementById('incomeSourcePie').getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: @json($incomeSourceLabels),
                    datasets: [{ 
                        data: @json($incomeSourceData), 
                        backgroundColor: @json($incomeSourceColors), 
                        borderWidth: 0 
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { color: legendColor } } } }
            });

            // 5. Top Categories Bar
            new Chart(document.getElementById('topCategoriesBar').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: @json($topCategoryLabels),
                    datasets: [{ 
                        label: '{{ __('Total Spent') }}', 
                        data: @json($topCategoryData), 
                        backgroundColor: @json($topCategoryColors), 
                        borderRadius: 4 
                    }]
                },
                options: {
                    indexAxis: 'y', responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { x: { beginAtZero: true, ticks: { color: tickColor }, grid: { color: gridColor } }, y: { ticks: { color: tickColor }, grid: { color: gridColor } } }
                }
            });
        })();
    </script>
@endpush

@endsection
