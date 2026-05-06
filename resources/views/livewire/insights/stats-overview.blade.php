<?php

use App\Models\Expense;
use Livewire\Volt\Component;

new class extends Component {
    public function with(): array
    {
        $userId = auth()->id();

        $totalSpend = (float) Expense::where('user_id', $userId)->sum('amount');

        $currentMonthSpend = (float) Expense::where('user_id', $userId)
            ->whereYear('expense_date', now()->year)
            ->whereMonth('expense_date', now()->month)
            ->sum('amount');

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

        $nonZero = $monthly->where('total', '>', 0);
        $avgMonthly = $nonZero->isNotEmpty() ? $nonZero->avg('total') : 0;
        $bestMonth = $monthly->sortByDesc('total')->first() ?? ['label' => '—', 'total' => 0];
        $worstMonth = $nonZero->sortBy('total')->first() ?? ['label' => '—', 'total' => 0];

        $topCategoryRow = auth()->user()->expenses()
            ->with('category')
            ->get()
            ->groupBy(fn($e) => $e->category?->name ?? 'Tiada Kategori')
            ->map(fn($g) => (float) $g->sum('amount'))
            ->sortDesc();

        $topCategoryName = $topCategoryRow->keys()->first() ?? '—';
        $topCategoryTotal = $topCategoryRow->first() ?? 0;

        return compact(
            'totalSpend',
            'currentMonthSpend',
            'avgMonthly',
            'bestMonth',
            'worstMonth',
            'topCategoryName',
            'topCategoryTotal'
        );
    }
}; ?>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="app-card-padded">
        <flux:subheading>Jumlah keseluruhan</flux:subheading>
        <flux:heading size="lg">RM {{ number_format($totalSpend, 2) }}</flux:heading>
        <p class="text-xs text-zinc-500 mt-1">Semua masa</p>
    </div>
    <div class="app-card-padded">
        <flux:subheading>Bulan ini</flux:subheading>
        <flux:heading size="lg">RM {{ number_format($currentMonthSpend, 2) }}</flux:heading>
        <p class="text-xs text-zinc-500 mt-1">{{ now()->format('M Y') }}</p>
    </div>
    <div class="app-card-padded">
        <flux:subheading>Purata bulanan</flux:subheading>
        <flux:heading size="lg">RM {{ number_format($avgMonthly, 2) }}</flux:heading>
        <p class="text-xs text-zinc-500 mt-1">6 bulan lepas</p>
    </div>
    <div class="app-card-padded">
        <flux:subheading>Kategori teratas</flux:subheading>
        <flux:heading size="lg" class="truncate">{{ $topCategoryName }}</flux:heading>
        <p class="text-xs text-zinc-500 mt-1">RM {{ number_format($topCategoryTotal, 2) }} keseluruhan</p>
    </div>
</div>
