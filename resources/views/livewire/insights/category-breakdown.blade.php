<?php

use Livewire\Volt\Component;

new class extends Component {
    public function with(): array
    {
        $byCategory = auth()->user()->expenses()
            ->with('category')
            ->get()
            ->groupBy(fn($e) => $e->category?->name ?? 'Tiada Kategori')
            ->map(fn($g) => [
                'total' => (float) $g->sum('amount'),
                'color' => $g->first()->category?->color ?? '#9ca3af',
                'count' => $g->count(),
            ])
            ->sortByDesc('total');

        return ['byCategory' => $byCategory];
    }
}; ?>

<div class="app-card-padded">
    <flux:heading size="md" class="mb-4">Mengikut Kategori (keseluruhan)</flux:heading>
    @php $catTotal = $byCategory->sum('total'); @endphp
    <div class="space-y-4">
        @foreach($byCategory as $name => $data)
        <div>
            <div class="flex justify-between text-sm mb-1">
                <span class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background:{{ $data['color'] }}"></span>
                    {{ $name }}
                </span>
                <span class="font-mono font-medium">RM {{ number_format($data['total'], 2) }}</span>
            </div>
            <div class="w-full bg-zinc-100 dark:bg-zinc-700 rounded-full h-1.5">
                <div class="h-1.5 rounded-full transition-all"
                     style="width:{{ $catTotal > 0 ? round(($data['total'] / $catTotal) * 100, 1) : 0 }}%; background:{{ $data['color'] }}">
                </div>
            </div>
            <p class="text-xs text-zinc-400 mt-0.5">
                {{ $data['count'] }} entri &middot; {{ $catTotal > 0 ? round(($data['total'] / $catTotal) * 100, 1) : 0 }}%
            </p>
        </div>
        @endforeach
    </div>
</div>
