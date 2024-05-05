<div class="p-8 h-[min(30rem,60svh)]" x-data="chartData({
    'labels': {{ Js::from($labels) }},
    'datasets': {{ Js::from($datasets) }},
    'title': {{ Js::from($title) }},
    'name': {{ Js::from($name) }}
})" wire:ignore>
    <canvas x-ref="canvas"></canvas>
</div>

@script
    <script>
        Alpine.data('chartData', (config) => ({
            init() {
                const configChart = {
                    type: 'bar',
                    data: {
                        labels: config.labels,
                        datasets: config.datasets
                    },
                    options: {
                        plugins: {
                            title: {
                                display: true,
                                text: config.title
                            },
                        },
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            intersect: false,
                            mode: 'index',
                        },
                        scales: {
                            x: {
                                stacked: true,
                            },
                            y: {
                                stacked: true
                            }
                        }

                    }
                };

                const chart = new Chart(this.$refs.canvas, configChart);

                Livewire.on(`charts-${config.name}-update`, ({
                    title,
                    labels,
                    datasets
                }) => {
                    chart.options.plugins.title.text = title
                    chart.data.labels = labels;
                    chart.data.datasets = datasets
                    chart.update();
                })
            }
        }))
    </script>
@endscript
