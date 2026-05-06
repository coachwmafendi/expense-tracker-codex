<?php

use App\Models\Expense;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new
#[Layout('components.layouts.app')]
#[Title('Dashboard')]
class extends Component {
    public function with(): array
    {
        $userId = auth()->id();
        $now = now();

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

        $byCategory = auth()->user()->expenses()
            ->whereYear('expense_date', $now->year)
            ->whereMonth('expense_date', $now->month)
            ->with('category')
            ->get()
            ->groupBy(fn($e) => $e->category?->name ?? 'Tiada Kategori')
            ->map(fn($g) => [
                'total' => $g->sum('amount'),
                'color' => $g->first()->category?->color ?? '#9ca3af',
            ]);

        return [
            'monthly' => $monthly,
            'byCategory' => $byCategory,
            'todayTotal' => auth()->user()->expenses()->whereDate('expense_date', today())->sum('amount'),
            'monthTotal' => auth()->user()->expenses()
                ->whereYear('expense_date', $now->year)
                ->whereMonth('expense_date', $now->month)
                ->sum('amount'),
            'totalEntries' => auth()->user()->expenses()->count(),
        ];
    }
};

?>

<div class="mx-auto max-w-7xl page-stack">
    <section class="page-hero">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <div class="page-hero-kicker">Finance overview</div>
                <h1 class="page-hero-title">Spending Control Room</h1>
                <p class="page-hero-copy">
                    Track daily spending, monthly movement, category rhythm, and ledger activity from one calm workspace.
                </p>
            </div>

            <div class="grid gap-3 sm:grid-cols-3 lg:min-w-[28rem]">
                <div class="app-soft-panel">
                    <div class="metric-label">Today</div>
                    <div class="mt-2 text-lg font-semibold text-zinc-950 dark:text-white">RM {{ number_format($todayTotal, 2) }}</div>
                </div>
                <div class="app-soft-panel">
                    <div class="metric-label">Month</div>
                    <div class="mt-2 text-lg font-semibold text-zinc-950 dark:text-white">RM {{ number_format($monthTotal, 2) }}</div>
                </div>
                <div class="app-soft-panel">
                    <div class="metric-label">Entries</div>
                    <div class="mt-2 text-lg font-semibold text-zinc-950 dark:text-white">{{ $totalEntries }}</div>
                </div>
            </div>
        </div>
    </section>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <div class="metric-card">
            <div class="metric-label">Hari ini</div>
            <div class="metric-value">RM {{ number_format($todayTotal, 2) }}</div>
            <p class="mt-3 text-sm text-zinc-500 dark:text-zinc-400">Perbelanjaan direkodkan hari ini.</p>
        </div>
        <div class="metric-card">
            <div class="metric-label">Bulan ini</div>
            <div class="metric-value">RM {{ number_format($monthTotal, 2) }}</div>
            <p class="mt-3 text-sm text-zinc-500 dark:text-zinc-400">{{ now()->format('F Y') }} ledger total.</p>
        </div>
        <div class="metric-card">
            <div class="metric-label">Total entries</div>
            <div class="metric-value">{{ $totalEntries }}</div>
            <p class="mt-3 text-sm text-zinc-500 dark:text-zinc-400">Semua rekod perbelanjaan.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
        <div class="app-card-padded">
            <div class="mb-4">
                <flux:heading size="md">Trend 6 Bulan</flux:heading>
                <flux:text class="text-sm text-zinc-500">Monthly spend movement.</flux:text>
            </div>
            <canvas id="monthlyChart" height="200"></canvas>
        </div>
        <div class="app-card-padded">
            <div class="mb-4">
                <flux:heading size="md">Bulan Ini Mengikut Kategori</flux:heading>
                <flux:text class="text-sm text-zinc-500">Current month category mix.</flux:text>
            </div>
            @if($byCategory->isNotEmpty())
                <canvas id="categoryChart" height="200"></canvas>
            @else
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <flux:icon.chart-pie class="size-5" />
                    </div>
                    <div class="text-sm text-zinc-500 dark:text-zinc-400">Data kategori akan muncul selepas perbelanjaan direkodkan.</div>
                </div>
            @endif
        </div>
    </div>
</div>

@script
<script>
    new Chart(document.getElementById('monthlyChart'), {
        type: 'bar',
        data: {
            labels: @json($monthly->pluck('label')),
            datasets: [{
                label: 'RM',
                data: @json($monthly->pluck('total')),
                backgroundColor: '#c26b50',
                borderRadius: 4,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } }
        }
    });

    @if($byCategory->isNotEmpty())
    new Chart(document.getElementById('categoryChart'), {
        type: 'doughnut',
        data: {
            labels: @json($byCategory->keys()),
            datasets: [{
                data: @json($byCategory->pluck('total')),
                backgroundColor: @json($byCategory->pluck('color')),
            }]
        },
        options: { responsive: true }
    });
    @endif
</script>
@endscript
