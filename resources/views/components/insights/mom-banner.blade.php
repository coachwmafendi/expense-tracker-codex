@props([
    'thisMonthTotal' => 0,
    'lastMonthTotal' => 0,
    'momChange' => null,
])

@if($momChange !== null)
<div @class([
    'flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium',
    'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400' => $momChange <= 0,
    'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400'         => $momChange > 0,
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
