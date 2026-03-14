@props([
    'id' => 'chart-' . uniqid(),
    'title' => null,
    'type' => 'bar',
    'labels' => [],
    'datasets' => [],
    'height' => 80,
])

<div class="mt-6 card p-6">
    @if($title)
        <h3 class="text-lg font-bold t-primary mb-4">{{ $title }}</h3>
    @endif
    <canvas id="{{ $id }}" height="{{ $height }}"></canvas>
</div>

<script>
    (function() {
        const chartId = '{{ $id }}';

        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById(chartId);
            if (!ctx) return;

            const isDark = document.documentElement.classList.contains('dark');
            const tickColor = isDark ? '#6b7280' : '#94a3b8';
            const gridColor = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.06)';
            const legendColor = isDark ? '#9ca3af' : '#475569';

            new Chart(ctx.getContext('2d'), {
                type: '{{ $type }}',
                data: {
                    labels: {!! json_encode($labels) !!},
                    datasets: {!! json_encode($datasets) !!}
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { labels: { color: legendColor } }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { color: tickColor },
                            grid: { color: gridColor }
                        },
                        x: {
                            ticks: { color: tickColor },
                            grid: { color: gridColor }
                        }
                    }
                }
            });
        });
    })();
</script>
