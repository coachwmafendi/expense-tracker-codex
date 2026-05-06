
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

    <flux:modal name="category-form" wire:model.self="showModal" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $editingId ? 'Edit' : 'Tambah' }} Kategori</flux:heading>
                <flux:text class="text-sm text-zinc-500">Pilih nama dan warna yang mudah dikenali.</flux:text>
            </div>

            <flux:input wire:model="name" label="Nama" placeholder="cth: Makanan" />
            <flux:input wire:model="color" type="color" label="Warna" />

            <div class="flex gap-2 justify-end">
                <flux:button variant="ghost" wire:click="$set('showModal', false)">Batal</flux:button>
                <flux:button variant="primary" wire:click="save">Simpan</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
