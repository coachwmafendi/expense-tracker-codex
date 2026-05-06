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
    <flux:heading size="md" class="mb-4">Trend 6 Bulan</flux:heading>
    <canvas id="trendChart" height="220"></canvas>
</div>

@script
<script>
    new Chart(document.getElementById('trendChart'), {
        type: 'line',
        data: {
            labels: @json($monthly->pluck('label')),
            datasets: [{
                label: 'RM',
                data: @json($monthly->pluck('total')),
                borderColor: '#c26b50',
                backgroundColor: 'rgba(194,107,80,0.12)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#c26b50',
                pointRadius: 4,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });
</script>
@endscript
