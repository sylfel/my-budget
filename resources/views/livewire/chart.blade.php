<div class="p-8" wire:ignore>
    <canvas></canvas>
</div>

@assets
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endassets

@script
    <script>
        const data = {
            labels: {{ Js::from($labels) }},
            datasets: {{ Js::from($datasets) }}
        };

        const config = {
            type: 'bar',
            data: data,
            options: {
                plugins: {
                    title: {
                        display: {{ isset($title) ? 'true' : 'false' }},
                        text: {{ Js::from($title) }}
                    },
                },
                responsive: true,
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

        const ctx = $wire.$el.getElementsByTagName('canvas')[0];
        new Chart(ctx, config);
    </script>
@endscript