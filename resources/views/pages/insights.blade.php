<?php

use App\Models\Expense;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new
#[Layout('components.layouts.app')]
#[Title('Insights')]
class extends Component {
    public function with(): array
    {
        $userId = auth()->id();
        $now    = now();

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

        $nonZero    = $monthly->where('total', '>', 0);
        $avgMonthly = $nonZero->isNotEmpty() ? $nonZero->avg('total') : 0;
        $bestMonth  = $monthly->sortByDesc('total')->first();
        $worstMonth = $nonZero->sortBy('total')->first() ?? ['label' => '—', 'total' => 0];

        $topCategoryRow = auth()->user()->expenses()
            ->with('category')
            ->get()
            ->groupBy(fn($e) => $e->category?->name ?? 'Tiada Kategori')
            ->map(fn($g) => (float) $g->sum('amount'))
            ->sortDesc();

        $topCategoryName  = $topCategoryRow->keys()->first() ?? '—';
        $topCategoryTotal = $topCategoryRow->first() ?? 0;

        $lastMonthDate  = $now->copy()->subMonth();
        $thisMonthTotal = (float) Expense::where('user_id', $userId)
            ->whereYear('expense_date', $now->year)
            ->whereMonth('expense_date', $now->month)
            ->sum('amount');
        $lastMonthTotal = (float) Expense::where('user_id', $userId)
            ->whereYear('expense_date', $lastMonthDate->year)
            ->whereMonth('expense_date', $lastMonthDate->month)
            ->sum('amount');
        $momChange = $lastMonthTotal > 0
            ? round((($thisMonthTotal - $lastMonthTotal) / $lastMonthTotal) * 100, 1)
            : null;

        return compact(
            'avgMonthly', 'bestMonth', 'worstMonth',
            'topCategoryName', 'topCategoryTotal',
            'thisMonthTotal', 'lastMonthTotal', 'momChange'
        );
    }
};
?>

<div class="mx-auto max-w-7xl page-stack">
    <section class="page-hero">
        <div class="page-hero-kicker">Spending intelligence</div>
        <h1 class="page-hero-title">Insights</h1>
        <p class="page-hero-copy">
            Review spending patterns, category pressure, high months, and month-over-month movement.
        </p>
    </section>

    {{-- Stat cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="metric-card">
            <div class="metric-label">Purata bulanan</div>
            <div class="metric-value text-2xl">RM {{ number_format($avgMonthly, 2) }}</div>
            <p class="text-xs text-zinc-500 mt-1">6 bulan lepas</p>
        </div>
        <div class="metric-card">
            <div class="metric-label">Bulan tertinggi</div>
            <div class="metric-value text-2xl">RM {{ number_format($bestMonth['total'], 2) }}</div>
            <p class="text-xs text-zinc-500 mt-1">{{ $bestMonth['label'] }}</p>
        </div>
        <div class="metric-card">
            <div class="metric-label">Bulan terendah</div>
            <div class="metric-value text-2xl">RM {{ number_format($worstMonth['total'], 2) }}</div>
            <p class="text-xs text-zinc-500 mt-1">{{ $worstMonth['label'] }}</p>
        </div>
        <div class="metric-card">
            <div class="metric-label">Kategori teratas</div>
            <div class="metric-value truncate text-2xl">{{ $topCategoryName }}</div>
            <p class="text-xs text-zinc-500 mt-1">RM {{ number_format($topCategoryTotal, 2) }} keseluruhan</p>
        </div>
    </div>

    {{-- Month-over-month banner --}}
    @if($momChange !== null)
    <div @class([
        'flex items-center gap-3 rounded-lg border px-4 py-3 text-sm font-medium shadow-sm',
        'border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-900/60 dark:bg-emerald-950/30 dark:text-emerald-300' => $momChange <= 0,
        'border-rose-200 bg-rose-50 text-rose-700 dark:border-rose-900/60 dark:bg-rose-950/30 dark:text-rose-300' => $momChange > 0,
    ])>
        @if($momChange <= 0)
            <flux:icon.arrow-trending-down class="w-5 h-5 shrink-0" />
            <span>Bulan ini <strong>{{ abs($momChange) }}% lebih rendah</strong> berbanding bulan lepas — RM {{ number_format($thisMonthTotal, 2) }} vs RM {{ number_format($lastMonthTotal, 2) }}</span>
        @else
            <flux:icon.arrow-trending-up class="w-5 h-5 shrink-0" />
            <span>Bulan ini <strong>{{ $momChange }}% lebih tinggi</strong> berbanding bulan lepas — RM {{ number_format($thisMonthTotal, 2) }} vs RM {{ number_format($lastMonthTotal, 2) }}</span>
        @endif
    </div>
    @endif

    {{-- Charts --}}
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
        <livewire:insights.trend-chart />
        <livewire:pages.insights.trend-chart />
        <livewire:insights.dow-chart />
    </div>

    {{-- Category breakdown + Top expenses --}}
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
        <livewire:insights.category-breakdown />
        <livewire:insights.top-expenses />
    </div>
</div>
