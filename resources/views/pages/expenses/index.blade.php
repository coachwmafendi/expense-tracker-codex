
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
                <flux:button variant="primary" icon="plus" wire:click="openCreate">
                    Tambah
                </flux:button>
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

    <flux:modal name="expense-form" wire:model.self="showModal" class="md:w-[500px]">
        <div class="space-y-4">
            <div>
                <flux:heading size="lg">{{ $editingId ? 'Edit' : 'Tambah' }} Perbelanjaan</flux:heading>
                <flux:text class="text-sm text-zinc-500">Simpan butiran ledger dengan cepat dan kemas.</flux:text>
            </div>

            <flux:input wire:model="title" label="Tajuk" placeholder="cth: Petrol" />
            <flux:input wire:model="amount" label="Jumlah (RM)" type="number" step="0.01" />
            <flux:select wire:model="category_id" label="Kategori">
                <flux:select.option value="">— Tiada —</flux:select.option>
                @foreach($categories as $cat)
                    <flux:select.option value="{{ $cat->id }}">{{ $cat->name }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:input wire:model="expense_date" type="date" label="Tarikh" />
            <flux:textarea wire:model="notes" label="Nota (optional)" rows="3" />

            <div class="flex gap-2 justify-end">
                <flux:button variant="ghost" wire:click="$set('showModal', false)">Batal</flux:button>
                <flux:button variant="primary" wire:click="save">Simpan</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
