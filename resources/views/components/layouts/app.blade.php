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
        <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-3 px-2 text-[#fff8ef]">
            <x-application-logo class="h-9 w-auto fill-current text-[#fff8ef]" />
            <div>
                <div class="text-base font-semibold leading-tight">Expense Tracker</div>
                <div class="mt-1 text-[0.68rem] font-semibold uppercase tracking-[0.22em] text-[#d8b99a]">Control Room</div>
            </div>
        </a>

        <nav class="grid gap-1.5">
            <a href="{{ route('dashboard') }}" wire:navigate @class(['expense-sidebar-link', 'expense-sidebar-link-active' => request()->routeIs('dashboard')])>
                <svg viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m3 11 9-8 9 8"/><path stroke-linecap="round" stroke-linejoin="round" d="M5 10v10h5v-6h4v6h5V10"/></svg>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('expenses.index') }}" wire:navigate @class(['expense-sidebar-link', 'expense-sidebar-link-active' => request()->routeIs('expenses.index')])>
                <svg viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="6" width="18" height="12" rx="2"/><path stroke-linecap="round" d="M7 10h.01M17 14h.01"/><circle cx="12" cy="12" r="2.2"/></svg>
                <span>Perbelanjaan</span>
            </a>
            <a href="{{ route('categories.index') }}" wire:navigate @class(['expense-sidebar-link', 'expense-sidebar-link-active' => request()->routeIs('categories.index')])>
                <svg viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13.5 13.5 20 4 10.5V4h6.5L20 13.5Z"/><path stroke-linecap="round" d="M8 8h.01"/></svg>
                <span>Kategori</span>
            </a>
            <a href="{{ route('insights') }}" wire:navigate @class(['expense-sidebar-link', 'expense-sidebar-link-active' => request()->routeIs('insights')])>
                <svg viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M5 20V10m5 10V4m5 16v-7m5 7V8"/><path stroke-linecap="round" d="M3 20h18"/></svg>
                <span>Insights</span>
            </a>
            <a href="{{ route('profile.edit') }}" wire:navigate @class(['expense-sidebar-link', 'expense-sidebar-link-active' => request()->routeIs('profile.edit')])>
                <svg viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="8" r="3.5"/><path stroke-linecap="round" stroke-linejoin="round" d="M5 20a7 7 0 0 1 14 0"/></svg>
                <span>Profile</span>
            </a>
        </nav>

        <div class="mt-auto rounded-lg border border-[#3b332c] bg-[#181410] p-3">
            <a href="{{ route('profile.edit') }}" wire:navigate class="flex min-w-0 items-center gap-3">
                <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-[#fff8ef] text-sm font-semibold text-[#9a4f39]">
                    {{ collect(explode(' ', auth()->user()->name ?? 'Guest'))->map(fn ($part) => mb_substr($part, 0, 1))->take(2)->implode('') }}
                </div>
                <div class="min-w-0">
                    <div class="truncate text-sm font-semibold text-[#fff8ef]">{{ auth()->user()->name ?? 'Guest' }}</div>
                    <div class="truncate text-xs text-[#d8b99a]">{{ auth()->user()->email ?? '' }}</div>
                </div>
            </a>
        </div>
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
