<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Expense Tracker' }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    @fluxAppearance
</head>
<body class="font-sans antialiased">
<div class="app-workspace">

    <aside class="expense-sidebar hidden lg:flex">
        <flux:brand href="/dashboard" name="Expense Tracker" class="px-2" />
        <div class="px-2 text-xs font-semibold uppercase tracking-wide text-[#d8b99a]">
            Control Room
        </div>

        <flux:navlist variant="outline">
            <flux:navlist.item icon="home" href="{{ route('dashboard') }}" wire:navigate>
                Dashboard
            </flux:navlist.item>
            <flux:navlist.item icon="banknotes" href="{{ route('expenses.index') }}" wire:navigate>
                Perbelanjaan
            </flux:navlist.item>
            <flux:navlist.item icon="tag" href="{{ route('categories.index') }}" wire:navigate>
                Kategori
            </flux:navlist.item>
            <flux:navlist.item icon="chart-bar" href="{{ route('insights') }}" wire:navigate>
                Insights
            </flux:navlist.item>
            <flux:navlist.item icon="cog-6-tooth" href="{{ route('profile.edit') }}" wire:navigate>
                Profile
            </flux:navlist.item>
        </flux:navlist>

        <flux:spacer />

        <flux:dropdown position="top" align="start">
            <flux:profile name="{{ auth()->user()->name ?? 'Guest' }}" />
            <flux:menu>
                <flux:menu.item href="{{ route('profile.edit') }}" icon="cog-6-tooth" wire:navigate>
                    Settings
                </flux:menu.item>
                <flux:menu.separator />
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle">
                        Log keluar
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </aside>

    <div class="lg:hidden">
        <livewire:layout.navigation />
    </div>

    <main class="app-main lg:ml-72">
        {{ $slot }}
    </main>

    @fluxScripts
</div>
</body>
</html>
