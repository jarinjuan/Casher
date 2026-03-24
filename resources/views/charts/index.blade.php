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

    @php
        $monthLabels = [];
        $incomeData = [];
        $expenseData = [];
        $teamId = Auth::user()->currentTeam->id;

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthLabels[] = $date->format('M Y');

            $income = \App\Models\Transaction::where('team_id', $teamId)
                ->where('type', 'income')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('amount');

            $expense = \App\Models\Transaction::where('team_id', $teamId)
                ->where('type', 'expense')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('amount');

            $incomeData[] = $income;
            $expenseData[] = $expense;
        }
    @endphp

    <div class="card p-6">
        <h3 class="font-bold text-lg t-primary mb-4">{{ __('Income vs expenses trend (12 months)') }}</h3>
        <div class="chart-h-lg">
            <canvas id="trendLineChart"></canvas>
        </div>
    </div>

    @php
        $last6Months = [];
        $last6IncomeData = [];
        $last6ExpenseData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $last6Months[] = $date->format('M Y');

            $income = \App\Models\Transaction::where('team_id', $teamId)
                ->where('type', 'income')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('amount');

            $expense = \App\Models\Transaction::where('team_id', $teamId)
                ->where('type', 'expense')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('amount');

            $last6IncomeData[] = $income;
            $last6ExpenseData[] = $expense;
        }
    @endphp

    <div class="card p-6">
        <h3 class="font-bold text-lg t-primary mb-4">{{ __('Monthly cash flow (last 6 months)') }}</h3>
        <div class="chart-h-lg">
            <canvas id="monthlyBarChart"></canvas>
        </div>
    </div>

    @php
        $incomeSources = \App\Models\Transaction::where('team_id', $teamId)
            ->where('type', 'income')
            ->groupBy('category_id')
            ->selectRaw('category_id, SUM(amount) as total')
            ->orderByDesc('total')
            ->with('category')
            ->get()
            ->map(function($tx) {
                return [
                    'name' => $tx->category->name ?? __('Uncategorized'),
                    'total' => $tx->total
                ];
            });
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="card p-6">
            <h3 class="font-bold text-lg t-primary mb-4">{{ __('Income sources') }}</h3>
            <div class="chart-h-sm">
                <canvas id="incomeSourcePie"></canvas>
            </div>
        </div>

        @php
            $topCategories = \App\Models\Transaction::where('team_id', $teamId)
                ->where('type', 'expense')
                ->groupBy('category_id')
                ->selectRaw('category_id, SUM(amount) as total')
                ->orderByDesc('total')
                ->with('category')
                ->limit(5)
                ->get()
                ->map(function($tx) {
                    return [
                        'name' => $tx->category->name ?? __('Uncategorized'),
                        'total' => $tx->total
                    ];
                });
        @endphp

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

            const labels = @json($labels);
            const data = @json($data);
            const colors = @json($colors ?? []);

            new Chart(document.getElementById('categoryPie').getContext('2d'), {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: colors.length ? colors : labels.map((_,i)=>['#fbbf24','#8b5cf6','#06b6d4','#ef4444','#10b981','#f97316','#60a5fa','#ec4899'][i%8]),
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom', labels: { color: legendColor } } }
                }
            });

            new Chart(document.getElementById('trendLineChart').getContext('2d'), {
                type: 'line',
                data: {
                    labels: @json($monthLabels),
                    datasets: [
                        { label: '{{ __('Income') }}', data: @json($incomeData), borderColor: '#fbbf24', backgroundColor: 'rgba(251,191,36,0.08)', tension: 0.4, fill: true },
                        { label: '{{ __('Expenses') }}', data: @json($expenseData), borderColor: '#8b5cf6', backgroundColor: 'rgba(139,92,246,0.08)', tension: 0.4, fill: true }
                    ]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { position: 'top', labels: { color: legendColor } } },
                    scales: { y: { beginAtZero: true, ticks: { color: tickColor }, grid: { color: gridColor } }, x: { ticks: { color: tickColor }, grid: { color: gridColor } } }
                }
            });

            new Chart(document.getElementById('monthlyBarChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: @json($last6Months),
                    datasets: [
                        { label: '{{ __('Income') }}', data: @json($last6IncomeData), backgroundColor: '#fbbf24', borderRadius: 4 },
                        { label: '{{ __('Expenses') }}', data: @json($last6ExpenseData), backgroundColor: '#8b5cf6', borderRadius: 4 }
                    ]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { position: 'top', labels: { color: legendColor } } },
                    scales: { y: { beginAtZero: true, ticks: { color: tickColor }, grid: { color: gridColor } }, x: { ticks: { color: tickColor }, grid: { color: gridColor } } }
                }
            });

            const incomeSourcesData = @json($incomeSources);
            new Chart(document.getElementById('incomeSourcePie').getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: incomeSourcesData.map(c => c.name),
                    datasets: [{ 
                        data: incomeSourcesData.map(c => c.total), 
                        backgroundColor: ['#fbbf24', '#8b5cf6', '#06b6d4', '#ef4444', '#10b981', '#f97316', '#ec4899', '#3b82f6'], 
                        borderWidth: 0 
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { color: legendColor } } } }
            });

            const topCatsData = @json($topCategories);
            new Chart(document.getElementById('topCategoriesBar').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: topCatsData.map(c => c.name),
                    datasets: [{ label: '{{ __('Total Spent') }}', data: topCatsData.map(c => c.total), backgroundColor: ['#ef4444','#f97316','#fbbf24','#8b5cf6','#06b6d4'], borderRadius: 4 }]
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
