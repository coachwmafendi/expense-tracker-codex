
<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new
#[Layout('components.layouts.app')]
#[Title('Perbelanjaan')]
class extends Component {
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $categoryFilter = '';

    #[Url]
    public string $month = '';

    public bool $showModal = false;
    public ?int $editingId = null;

    public string $title = '';
    public string $amount = '';
    public ?int $category_id = null;
    public string $expense_date = '';
    public string $notes = '';

    public function mount(): void
    {
        $this->expense_date = now()->toDateString();
        $this->month = now()->format('Y-m');
    }

    public function openCreate(): void
    {
        $this->reset(['editingId', 'title', 'amount', 'category_id', 'notes']);
        $this->expense_date = now()->toDateString();
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $expense = auth()->user()->expenses()->findOrFail($id);
        $this->editingId = $id;
        $this->title = $expense->title;
        $this->amount = (string) $expense->amount;
        $this->category_id = $expense->category_id;
        $this->expense_date = $expense->expense_date->toDateString();
        $this->notes = $expense->notes ?? '';
        $this->showModal = true;
    }

    public function save(): void
    {
        $data = $this->validate([
            'title' => 'required|string|max:100',
            'amount' => 'required|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'expense_date' => 'required|date',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($this->editingId) {
            auth()->user()->expenses()->findOrFail($this->editingId)->update($data);
        } else {
            auth()->user()->expenses()->create($data);
        }

        $this->showModal = false;
        Flux::toast('Perbelanjaan disimpan', variant: 'success');
    }

    public function delete(int $id): void
    {
        auth()->user()->expenses()->findOrFail($id)->delete();
        Flux::toast('Dipadam', variant: 'success');
    }

    public function with(): array
    {
        $expensesQuery = auth()->user()->expenses()
            ->with('category')
            ->when($this->search, fn($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->when($this->categoryFilter, fn($q) => $q->where('category_id', $this->categoryFilter))
            ->when($this->month, function ($q) {
                [$year, $month] = explode('-', $this->month);
                $q->whereYear('expense_date', $year)->whereMonth('expense_date', $month);
            });

        return [
            'expenses' => $expensesQuery->latest('expense_date')->paginate(15),
            'categories' => auth()->user()->categories()->orderBy('name')->get(),
            'monthTotal' => auth()->user()->expenses()
                ->when($this->month, function ($q) {
                    [$year, $month] = explode('-', $this->month);
                    $q->whereYear('expense_date', $year)->whereMonth('expense_date', $month);
                })
                ->sum('amount'),
        ];
    }
};

?>

<div class="mx-auto max-w-7xl page-stack">
    <section class="page-hero">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <div class="page-hero-kicker">Expense ledger</div>
                <h1 class="page-hero-title">Perbelanjaan</h1>
                <p class="page-hero-copy">
                    Track daily spending, filter by category or month, and keep the month total easy to scan.
                </p>
            </div>
            <div class="flex flex-col gap-3 sm:items-end">
                <div class="app-soft-panel">
                    <div class="metric-label">Jumlah bulan ni</div>
                    <div class="mt-2 text-lg font-semibold text-zinc-950 dark:text-white">RM {{ number_format($monthTotal, 2) }}</div>
                </div>
                <button type="button" wire:click="openCreate" class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#c26b50] px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-[#a85a43] focus:outline-none focus:ring-2 focus:ring-[#c26b50] focus:ring-offset-2 focus:ring-offset-[#fffaf2] dark:focus:ring-offset-[#211d18]">
                    <span class="text-base leading-none">+</span>
                    Tambah
                </button>
            </div>
        </div>
    </section>

    <div class="app-soft-panel grid grid-cols-1 gap-3 md:grid-cols-3">
        <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Cari..." />
        <flux:select wire:model.live="categoryFilter" placeholder="Semua kategori">
            <flux:select.option value="">Semua kategori</flux:select.option>
            @foreach($categories as $cat)
                <flux:select.option value="{{ $cat->id }}">{{ $cat->name }}</flux:select.option>
            @endforeach
        </flux:select>
        <flux:input wire:model.live="month" type="month" />
    </div>

    <div class="app-card">
        <div class="overflow-x-auto">
        <table class="app-table">
            <thead class="app-table-head">
                <tr>
                    <th class="app-table-th">Tarikh</th>
                    <th class="app-table-th">Tajuk</th>
                    <th class="app-table-th">Kategori</th>
                    <th class="app-table-th text-right">Jumlah (RM)</th>
                    <th class="app-table-th text-right">Tindakan</th>
                </tr>
            </thead>
            <tbody class="app-table-body">
                @forelse($expenses as $exp)
                    <tr class="app-table-row">
                        <td class="whitespace-nowrap px-5 py-4 text-zinc-500">{{ $exp->expense_date->format('d/m/Y') }}</td>
                        <td class="px-5 py-4 font-medium text-zinc-800 dark:text-zinc-100">{{ $exp->title }}</td>
                        <td class="px-5 py-4">
                            @if($exp->category)
                                <span class="inline-flex items-center gap-2 text-zinc-700 dark:text-zinc-200">
                                    <span class="size-2 rounded-full" style="background:{{ $exp->category->color }}"></span>
                                    {{ $exp->category->name }}
                                </span>
                            @else
                                <span class="text-zinc-400">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right font-mono font-semibold text-zinc-900 dark:text-zinc-100">{{ number_format($exp->amount, 2) }}</td>
                        <td class="whitespace-nowrap px-5 py-4 text-right">
                            <flux:button size="xs" variant="ghost" icon="pencil" wire:click="openEdit({{ $exp->id }})" />
                            <flux:button size="xs" variant="ghost" icon="trash"
                                wire:click="delete({{ $exp->id }})"
                                wire:confirm="Padam entry ni?" />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12">
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <flux:icon.receipt-percent class="size-5" />
                                </div>
                                <div>
                                    <div class="font-medium text-zinc-800 dark:text-zinc-100">Tiada data.</div>
                                    <div class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Tambah perbelanjaan pertama untuk mula melihat ledger bulan ini.</div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    <div>
        {{ $expenses->links() }}
    </div>

    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-4 py-6 backdrop-blur-sm" role="dialog" aria-modal="true">
            <div class="w-full max-w-2xl overflow-hidden rounded-lg border border-[#3c342b] bg-[#211d18] text-zinc-100 shadow-2xl shadow-black/40">
                <div class="flex items-start justify-between gap-4 border-b border-[#3c342b] px-5 py-4">
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wide text-[#f0b38f]">
                            Expense entry
                        </div>
                        <h2 class="mt-1 text-xl font-semibold text-white">{{ $editingId ? 'Edit' : 'Tambah' }} Perbelanjaan</h2>
                        <p class="mt-1 text-sm text-zinc-400">Simpan butiran ledger dengan cepat dan kemas.</p>
                    </div>
                    <button type="button" wire:click="$set('showModal', false)" class="rounded-lg border border-[#4a3d32] p-2 text-zinc-400 transition hover:bg-[#2a241e] hover:text-white" aria-label="Tutup">
                        <svg viewBox="0 0 20 20" class="size-4" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" d="m5 5 10 10M15 5 5 15" />
                        </svg>
                    </button>
                </div>

                <div class="grid max-h-[72vh] gap-4 overflow-y-auto px-5 py-5">
                    <div>
                        <label for="expense-title" class="mb-1.5 block text-sm font-medium text-zinc-300">Tajuk</label>
                        <input id="expense-title" wire:model="title" type="text" placeholder="cth: Petrol" class="w-full rounded-lg border-[#4a3d32] bg-[#171411] text-zinc-100 placeholder:text-zinc-500 focus:border-[#c26b50] focus:ring-[#c26b50]" />
                        @error('title') <p class="mt-1 text-sm text-rose-400">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="expense-amount" class="mb-1.5 block text-sm font-medium text-zinc-300">Jumlah (RM)</label>
                            <input id="expense-amount" wire:model="amount" type="number" step="0.01" class="w-full rounded-lg border-[#4a3d32] bg-[#171411] text-zinc-100 placeholder:text-zinc-500 focus:border-[#c26b50] focus:ring-[#c26b50]" />
                            @error('amount') <p class="mt-1 text-sm text-rose-400">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="expense-date" class="mb-1.5 block text-sm font-medium text-zinc-300">Tarikh</label>
                            <input id="expense-date" wire:model="expense_date" type="date" class="w-full rounded-lg border-[#4a3d32] bg-[#171411] text-zinc-100 focus:border-[#c26b50] focus:ring-[#c26b50]" />
                            @error('expense_date') <p class="mt-1 text-sm text-rose-400">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="expense-category" class="mb-1.5 block text-sm font-medium text-zinc-300">Kategori</label>
                        <select id="expense-category" wire:model="category_id" class="w-full rounded-lg border-[#4a3d32] bg-[#171411] text-zinc-100 focus:border-[#c26b50] focus:ring-[#c26b50]">
                            <option value="">Tiada kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <p class="mt-1 text-sm text-rose-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="expense-notes" class="mb-1.5 block text-sm font-medium text-zinc-300">Nota <span class="text-zinc-500">(optional)</span></label>
                        <textarea id="expense-notes" wire:model="notes" rows="3" class="w-full rounded-lg border-[#4a3d32] bg-[#171411] text-zinc-100 placeholder:text-zinc-500 focus:border-[#c26b50] focus:ring-[#c26b50]"></textarea>
                        @error('notes') <p class="mt-1 text-sm text-rose-400">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 border-t border-[#3c342b] bg-[#1b1713] px-5 py-4">
                    <button type="button" wire:click="$set('showModal', false)" class="rounded-lg px-4 py-2 text-sm font-semibold text-zinc-300 transition hover:bg-white/10 hover:text-white">
                        Batal
                    </button>
                    <button type="button" wire:click="save" class="rounded-lg bg-[#c26b50] px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-[#a85a43] focus:outline-none focus:ring-2 focus:ring-[#c26b50] focus:ring-offset-2 focus:ring-offset-[#1b1713]">
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
