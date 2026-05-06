<?php

use App\Models\Expense;
use Livewire\Volt\Component;

new class extends Component {
    public function with(): array
    {
        $userId = auth()->id();

        $monthly = collect(range(5, 0))->map(function ($i) use ($userId) {
            $date = now()->subMonths($i);
            return [
                'label' => $date->format('M Y'),
                'total' => (float) Expense::where('user_id', $userId)
                    ->whereYear('expense_date', $date->year)
                    ->whereMonth('expense_date', $date->month)
                    ->sum('amount'),
            ];
        });

        return ['monthly' => $monthly];
    }
}; ?>

<div class="app-card-padded">
    <flux:heading size="md" class="mb-4">Trend 6 Bulan Ini</flux:heading>
    <div id="trendChartAlt"></div>
</div>

@script
<script>
    new ApexCharts(document.getElementById('trendChartAlt'), {
        series: [{ name: 'RM', data: @json($monthly->pluck('total')) }],
        chart: {
            type: 'area',
            height: 250,
            toolbar: { show: false },
            background: 'transparent',
        },
        theme: { mode: 'dark' },
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 2 },
        fill: {
            type: 'gradient',
            gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0, stops: [0, 100] },
        },
        colors: ['#c26b50'],
        xaxis: {
            categories: @json($monthly->pluck('label')),
            labels: { style: { colors: '#a1a1aa' } },
            axisBorder: { show: false },
            axisTicks: { show: false },
        },
        yaxis: {
            labels: {
                style: { colors: '#a1a1aa' },
                formatter: val => 'RM ' + val.toFixed(0),
            },
        },
        grid: { borderColor: '#3f3f46' },
        tooltip: { y: { formatter: val => 'RM ' + val.toFixed(2) } },
    }).render();
</script>
@endscript
