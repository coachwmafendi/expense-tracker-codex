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
    <canvas id="dowChart" height="220"></canvas>
</div>

@script
<script>
    new Chart(document.getElementById('dowChart'), {
        type: 'bar',
        data: {
            labels: @json($dowLabels),
            datasets: [{
                label: 'RM',
                data: @json($dowData),
                backgroundColor: ['#c26b50','#2f7d75','#8d6b3f','#ef4444','#eab308','#22c55e','#c26b50'],
                borderRadius: 4,
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
