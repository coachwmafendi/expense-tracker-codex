
<?php

use App\Models\Category;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new
#[Layout('components.layouts.app')]
#[Title('Kategori')]
class extends Component {
    public bool $showModal = false;
    public ?int $editingId = null;

    public string $name = '';
    public string $color = '#c26b50';

    public function openCreate(): void
    {
        $this->reset(['editingId', 'name', 'color']);
        $this->color = '#c26b50';
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $category = auth()->user()->categories()->findOrFail($id);
        $this->editingId = $id;
        $this->name = $category->name;
        $this->color = $category->color;
        $this->showModal = true;
    }

    public function save(): void
    {
        $data = $this->validate([
            'name' => 'required|string|max:50',
            'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        if ($this->editingId) {
            auth()->user()->categories()->findOrFail($this->editingId)->update($data);
        } else {
            auth()->user()->categories()->create($data);
        }

        $this->showModal = false;
        $this->reset(['editingId', 'name', 'color']);
        Flux::toast('Kategori disimpan', variant: 'success');
    }

    public function delete(int $id): void
    {
        auth()->user()->categories()->findOrFail($id)->delete();
        Flux::toast('Kategori dipadam', variant: 'success');
    }

    public function with(): array
    {
        return [
            'categories' => auth()->user()->categories()->latest()->get(),
        ];
    }
};

?>

<div class="mx-auto max-w-5xl page-stack">
    <section class="page-hero">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <div class="page-hero-kicker">Spending taxonomy</div>
                <h1 class="page-hero-title">Kategori</h1>
                <p class="page-hero-copy">
                    Keep spending groups tidy so the ledger and insights stay easy to read.
                </p>
            </div>
            <button type="button" wire:click="openCreate" class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#c26b50] px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-[#a85a43] focus:outline-none focus:ring-2 focus:ring-[#c26b50] focus:ring-offset-2 focus:ring-offset-[#fffaf2] dark:focus:ring-offset-[#211d18]">
                <span class="text-base leading-none">+</span>
                Tambah Kategori
            </button>
        </div>
    </section>

    @if($categories->isEmpty())
        <div class="app-card">
            <div class="empty-state">
                <div class="empty-state-icon">
                    <flux:icon.swatch class="size-5" />
                </div>
                <div>
                    <div class="font-medium text-zinc-800 dark:text-zinc-100">Belum ada kategori.</div>
                    <div class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Tambah kategori pertama untuk susun perbelanjaan dengan lebih jelas.</div>
                </div>
            </div>
        </div>
    @else
        <div class="grid gap-3">
            @foreach($categories as $cat)
                <div class="app-card-padded flex items-center justify-between gap-4">
                    <div class="flex min-w-0 items-center gap-3">
                        <div class="size-4 shrink-0 rounded-full ring-4 ring-[#fbf5ec] dark:ring-[#1b1713]" style="background-color: {{ $cat->color }}"></div>
                        <flux:text class="truncate font-medium">{{ $cat->name }}</flux:text>
                        <flux:badge size="sm" color="zinc">{{ $cat->expenses()->count() }} entries</flux:badge>
                    </div>
                    <div class="flex shrink-0 gap-2">
                        <flux:button size="sm" variant="ghost" icon="pencil" wire:click="openEdit({{ $cat->id }})" />
                        <flux:button size="sm" variant="ghost" icon="trash"
                            wire:click="delete({{ $cat->id }})"
                            wire:confirm="Padam kategori ni?" />
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-4 py-6 backdrop-blur-sm" role="dialog" aria-modal="true">
            <div class="w-full max-w-lg overflow-hidden rounded-lg border border-[#3c342b] bg-[#211d18] text-zinc-100 shadow-2xl shadow-black/40">
                <div class="flex items-start justify-between gap-4 border-b border-[#3c342b] px-5 py-4">
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wide text-[#f0b38f]">
                            Category setup
                        </div>
                        <h2 class="mt-1 text-xl font-semibold text-white">{{ $editingId ? 'Edit' : 'Tambah' }} Kategori</h2>
                        <p class="mt-1 text-sm text-zinc-400">Pilih nama dan warna yang mudah dikenali.</p>
                    </div>
                    <button type="button" wire:click="$set('showModal', false)" class="rounded-lg border border-[#4a3d32] p-2 text-zinc-400 transition hover:bg-[#2a241e] hover:text-white" aria-label="Tutup">
                        <svg viewBox="0 0 20 20" class="size-4" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" d="m5 5 10 10M15 5 5 15" />
                        </svg>
                    </button>
                </div>

                <div class="grid gap-4 px-5 py-5">
                    <div>
                        <label for="category-name" class="mb-1.5 block text-sm font-medium text-zinc-300">Nama</label>
                        <input id="category-name" wire:model="name" type="text" placeholder="cth: Makanan" class="w-full rounded-lg border-[#4a3d32] bg-[#171411] text-zinc-100 placeholder:text-zinc-500 focus:border-[#c26b50] focus:ring-[#c26b50]" />
                        @error('name') <p class="mt-1 text-sm text-rose-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="category-color" class="mb-1.5 block text-sm font-medium text-zinc-300">Warna</label>
                        <div class="flex items-center gap-3">
                            <input id="category-color" wire:model="color" type="color" class="h-11 w-16 rounded-lg border border-[#4a3d32] bg-[#171411] p-1" />
                            <input wire:model="color" type="text" class="min-w-0 flex-1 rounded-lg border-[#4a3d32] bg-[#171411] font-mono text-zinc-100 placeholder:text-zinc-500 focus:border-[#c26b50] focus:ring-[#c26b50]" />
                        </div>
                        @error('color') <p class="mt-1 text-sm text-rose-400">{{ $message }}</p> @enderror
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
