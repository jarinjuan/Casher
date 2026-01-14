@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-black dark:text-gray-200 leading-tight">Charts</h2>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white p-6 rounded shadow">
        <h3 class="font-medium mb-4">Expenses by Category</h3>
        <div class="flex justify-center">
            <canvas id="categoryPie" style="width:150px;height:150px;"></canvas>
        </div>
    </div>
    <div class="mt-4 text-sm text-gray-600">Data are aggregated for `expenses` per category.</div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        (function(){
            const labels = @json($labels);
            const data = @json($data);
            const colors = @json($colors ?? []);

            const ctx = document.getElementById('categoryPie').getContext('2d');
            new Chart(ctx, {
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
        })();
    </script>
@endpush

@endsection
