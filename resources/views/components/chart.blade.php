@props([
    'id' => 'chart-' . uniqid(),
    'title' => null,
    'type' => 'bar',
    'labels' => [],
    'datasets' => [],
    'height' => 80,
])

<div class="mt-6 bg-white dark:bg-gray-800 rounded-lg shadow p-6">
    @if($title)
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">{{ $title }}</h3>
    @endif
    <canvas id="{{ $id }}" height="{{ $height }}"></canvas>
</div>

<script>
    (function() {
        const chartId = '{{ $id }}';
        let chartInstance = null;

        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById(chartId);
            if (!ctx) return;
            
            const canvasCtx = ctx.getContext('2d');
            
            function getChartOptions() {
                const isDark = document.documentElement.classList.contains('dark');
                const textColor = isDark ? '#9ca3af' : '#6b7280';
                const labelColor = isDark ? '#e5e7eb' : '#374151';
                const gridColor = isDark ? '#374151' : '#e5e7eb';
                
                return {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            labels: {
                                color: labelColor
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: textColor
                            },
                            grid: {
                                color: gridColor
                            }
                        },
                        x: {
                            ticks: {
                                color: textColor
                            },
                            grid: {
                                color: gridColor
                            }
                        }
                    }
                };
            }

            chartInstance = new Chart(canvasCtx, {
                type: '{{ $type }}',
                data: {
                    labels: {!! json_encode($labels) !!},
                    datasets: {!! json_encode($datasets) !!}
                },
                options: getChartOptions()
            });

            // Sledování změny dark modu
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.attributeName === 'class' && chartInstance) {
                        const isDark = document.documentElement.classList.contains('dark');
                        const textColor = isDark ? '#9ca3af' : '#6b7280';
                        const labelColor = isDark ? '#e5e7eb' : '#374151';
                        const gridColor = isDark ? '#374151' : '#e5e7eb';
                        
                        chartInstance.options.plugins.legend.labels.color = labelColor;
                        chartInstance.options.scales.y.ticks.color = textColor;
                        chartInstance.options.scales.y.grid.color = gridColor;
                        chartInstance.options.scales.x.ticks.color = textColor;
                        chartInstance.options.scales.x.grid.color = gridColor;
                        chartInstance.update();
                    }
                });
            });

            observer.observe(document.documentElement, { attributes: true });
        });
    })();
</script>
