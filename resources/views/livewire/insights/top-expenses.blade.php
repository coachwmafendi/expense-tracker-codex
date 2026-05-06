<?php

use Livewire\Volt\Component;

new class extends Component {
    public function with(): array
    {
        $topExpenses = auth()->user()->expenses()
            ->with('category')
            ->orderByDesc('amount')
            ->limit(5)
            ->get();

        return ['topExpenses' => $topExpenses];
    }
}; ?>

<div class="app-card-padded">
    <flux:heading size="md" class="mb-4">5 Perbelanjaan Terbesar</flux:heading>
    <div class="space-y-4">
        @foreach($topExpenses as $i => $exp)
        <div class="flex items-center gap-3">
            <span class="w-7 h-7 rounded-full bg-zinc-100 dark:bg-zinc-700 text-xs font-bold flex items-center justify-center text-zinc-500 shrink-0">
                {{ $i + 1 }}
            </span>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium truncate">{{ $exp->title }}</p>
                <p class="text-xs text-zinc-400">
                    {{ $exp->expense_date->format('d/m/Y') }}
                    @if($exp->category)
                        &middot; <span style="color:{{ $exp->category->color }}">{{ $exp->category->name }}</span>
                    @endif
                </p>
            </div>
            <span class="font-mono font-semibold text-sm shrink-0">RM {{ number_format($exp->amount, 2) }}</span>
        </div>
        @endforeach
    </div>
</div>
