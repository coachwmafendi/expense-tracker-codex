<?php

use Livewire\Volt\Component;

new class extends Component {
    public function with(): array
    {
        $byDow = auth()->user()->expenses()
            ->selectRaw('CAST(strftime("%w", expense_date) AS INTEGER) as dow, SUM(amount) as total')
            ->groupByRaw('strftime("%w", expense_date)')
            ->pluck('total', 'dow');

        $dowLabels = ['Ahad', 'Isnin', 'Selasa', 'Rabu', 'Khamis', 'Jumaat', 'Sabtu'];
        $dowData   = collect(range(0, 6))->map(fn($i) => (float) ($byDow[$i] ?? 0));

        return compact('dowLabels', 'dowData');
    }
}; ?>

<div class="app-card-padded">
    <flux:heading size="md" class="mb-4">Perbelanjaan Mengikut Hari</flux:heading>
    <div id="dowChart"></div>
</div>

@script
<script>
    const options = {
        series: [{
            name: 'Perbelanjaan',
            data: @json($dowData)
        }],
        chart: {
            type: 'bar',
            height: 220,
            toolbar: { show: false },
            fontFamily: 'inherit'
        },
        plotOptions: {
            bar: {
                borderRadius: 4,
                columnWidth: '60%',
            }
        },
        dataLabels: { enabled: false },
        colors: ['#c26b50'],
        xaxis: {
            categories: @json($dowLabels),
            axisBorder: { show: false },
            axisTicks: { show: false },
            labels: {
                style: {
                    colors: 'var(--zinc-500)'
                }
            }
        },
        yaxis: {
            labels: {
                formatter: (val) => `RM ${val.toFixed(0)}`,
                style: {
                    colors: 'var(--zinc-500)'
                }
            }
        },
        grid: {
            borderColor: 'var(--zinc-200)',
            strokeDashArray: 4,
        },
        tooltip: {
            y: {
                formatter: (val) => `RM ${val.toFixed(2)}`
            }
        },
        theme: {
            mode: document.documentElement.classList.contains('dark') ? 'dark' : 'light'
        }
    };

    const chart = new ApexCharts(document.querySelector("#dowChart"), options);
    chart.render();
</script>
@endscript
