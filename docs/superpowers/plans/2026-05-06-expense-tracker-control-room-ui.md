# Expense Tracker Control Room UI Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Redesign the authenticated Expense Tracker UI to closely match the warm editorial control-room style from `/Users/wmafendi/Herd/notion-blog-automation`.

**Architecture:** Keep the existing Laravel, Livewire Volt, Blade, Flux, Tailwind, and Chart.js behavior intact. Add shared CSS component utilities first, then apply them page-by-page to the existing layout, navigation, dashboard, expenses, categories, insights, and visible profile surfaces.

**Tech Stack:** Laravel Blade, Livewire Volt, Flux UI, Tailwind CSS, Chart.js, Vite, PHPUnit.

---

## File Map

- Modify `resources/css/app.css`: add the warm control-room utility classes and Flux contrast overrides.
- Modify `resources/views/components/layouts/app.blade.php`: wrap authenticated pages in the new workspace shell.
- Modify `resources/views/livewire/layout/navigation.blade.php`: replace the Breeze-style white navigation with espresso navigation.
- Modify `resources/views/pages/dashboard.blade.php`: add page hero, metric cards, app-card chart wrappers, and chart colors.
- Modify `resources/views/pages/expenses/index.blade.php`: restyle hero, filters, table, empty state, pagination, and modal content.
- Modify `resources/views/pages/categories/index.blade.php`: restyle hero, category rows, empty state, and modal content.
- Modify `resources/views/pages/insights.blade.php`: restyle hero, metric cards, and month-over-month banner.
- Modify visible insight components if they contain starter-kit card wrappers:
  - `resources/views/livewire/insights/category-breakdown.blade.php`
  - `resources/views/livewire/insights/stats-overview.blade.php`
  - `resources/views/livewire/insights/top-expenses.blade.php`
  - `resources/views/livewire/insights/trend-chart.blade.php`
  - `resources/views/livewire/insights/dow-chart.blade.php`
  - `resources/views/livewire/pages/insights/trend-chart.blade.php`
  - `resources/views/livewire/pages/insights/dow-chart.blade.php`
- Modify `resources/views/profile.blade.php` if it uses generic panels inside the authenticated layout.

## Task 1: Shared Control-Room CSS

**Files:**
- Modify: `resources/css/app.css`

- [ ] **Step 1: Add shared CSS utilities**

Append a Tailwind components layer to `resources/css/app.css` with these utilities:

```css
@layer components {
    .app-workspace {
        @apply min-h-screen bg-[#f6f1e8] text-zinc-950 antialiased dark:bg-[#171411] dark:text-zinc-100;
    }

    .app-main {
        @apply px-4 py-5 sm:px-6 lg:px-8 lg:py-7;
    }

    .expense-nav {
        @apply border-b border-[#2f2922] bg-[#1f1b16] text-[#fff8ef] shadow-sm dark:border-[#2f2922] dark:bg-[#1f1b16];
    }

    .expense-nav-link {
        @apply inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-[#f7eadb] transition hover:bg-white/10 hover:text-white;
    }

    .expense-nav-link-active {
        @apply bg-[#fff8ef] text-[#9a4f39] shadow-sm shadow-black/20 hover:bg-[#fff8ef] hover:text-[#9a4f39];
    }

    .expense-mobile-menu {
        @apply border-t border-[#332b23] bg-[#1f1b16] px-4 py-3 text-[#fff8ef];
    }

    .page-stack {
        @apply w-full space-y-6;
    }

    .page-hero {
        @apply overflow-hidden rounded-lg border border-[#e6dccb] bg-[#fffaf2] p-5 shadow-sm sm:p-6 dark:border-[#3c342b] dark:bg-[#211d18];
    }

    .page-hero-kicker {
        @apply mb-3 inline-flex items-center rounded-full border border-[#e4d5bf] bg-[#f7eddf] px-3 py-1 text-xs font-semibold uppercase tracking-wide text-[#8c5b38] dark:border-[#574636] dark:bg-[#2b251e] dark:text-[#f0b38f];
    }

    .page-hero-title {
        @apply text-2xl font-semibold tracking-tight text-zinc-950 sm:text-3xl dark:text-white;
    }

    .page-hero-copy {
        @apply mt-2 max-w-3xl text-sm leading-6 text-zinc-600 dark:text-zinc-300;
    }

    .metric-card {
        @apply rounded-lg border border-[#e6dccb] bg-white p-4 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-[#d5c4ad] hover:shadow-md dark:border-[#3c342b] dark:bg-[#211d18] dark:hover:border-[#5c4d3e];
    }

    .metric-label {
        @apply text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400;
    }

    .metric-value {
        @apply mt-2 text-3xl font-semibold tracking-tight text-zinc-950 dark:text-white;
    }

    .app-card {
        @apply overflow-hidden rounded-lg border border-[#e6dccb] bg-white shadow-sm dark:border-[#3c342b] dark:bg-[#211d18];
    }

    .app-card-padded {
        @apply rounded-lg border border-[#e6dccb] bg-white p-5 shadow-sm dark:border-[#3c342b] dark:bg-[#211d18];
    }

    .app-card-header {
        @apply border-b border-[#eee3d2] px-5 py-4 dark:border-[#3c342b];
    }

    .app-soft-panel {
        @apply rounded-lg border border-[#eadfce] bg-[#fbf5ec] p-4 dark:border-[#3c342b] dark:bg-[#1b1713];
    }

    .app-table {
        @apply min-w-full divide-y divide-[#eee3d2] text-sm dark:divide-[#3c342b];
    }

    .app-table-head {
        @apply bg-[#fbf5ec] text-left text-xs uppercase tracking-wide text-zinc-500 dark:bg-[#1b1713] dark:text-zinc-400;
    }

    .app-table-th {
        @apply px-5 py-3 font-semibold;
    }

    .app-table-body {
        @apply divide-y divide-[#f0e7da] dark:divide-[#332b23];
    }

    .app-table-row {
        @apply transition hover:bg-[#fbf5ec] dark:hover:bg-[#2a241e];
    }

    .empty-state {
        @apply flex flex-col items-center justify-center gap-3 px-5 py-12 text-center;
    }

    .empty-state-icon {
        @apply rounded-full border border-[#eadfce] bg-[#fbf5ec] p-3 text-[#c26b50] dark:border-[#3c342b] dark:bg-[#1b1713] dark:text-[#f0b38f];
    }
}
```

- [ ] **Step 2: Run frontend build**

Run: `npm run build`

Expected: Vite completes successfully. If Tailwind rejects a class, replace that class with the closest compatible utility in `resources/css/app.css` and rerun the build.

## Task 2: Authenticated App Shell And Navigation

**Files:**
- Modify: `resources/views/components/layouts/app.blade.php`
- Modify: `resources/views/livewire/layout/navigation.blade.php`

- [ ] **Step 1: Update the layout wrapper**

In `resources/views/components/layouts/app.blade.php`, ensure the body content uses:

```blade
<body class="font-sans antialiased">
    <div class="app-workspace">
        <livewire:layout.navigation />

        <main class="app-main">
            {{ $slot }}
        </main>
    </div>
</body>
```

Keep the existing `<head>`, metadata, and `@vite` lines intact.

- [ ] **Step 2: Replace navigation markup**

In `resources/views/livewire/layout/navigation.blade.php`, keep the PHP Volt class at the top and replace only the HTML below it with an espresso top navigation:

```blade
<nav x-data="{ open: false }" class="expense-nav">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex min-h-16 items-center justify-between gap-4">
            <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-3 text-[#fff8ef]">
                <x-application-logo class="block h-9 w-auto fill-current text-[#fff8ef]" />
                <div class="leading-tight">
                    <div class="text-sm font-semibold tracking-wide">Expense Tracker</div>
                    <div class="text-xs font-medium uppercase tracking-wide text-[#d8b99a]">Control Room</div>
                </div>
            </a>

            <div class="hidden items-center gap-2 md:flex">
                <a href="{{ route('dashboard') }}" wire:navigate @class(['expense-nav-link', 'expense-nav-link-active' => request()->routeIs('dashboard')])>Dashboard</a>
                <a href="{{ route('expenses.index') }}" wire:navigate @class(['expense-nav-link', 'expense-nav-link-active' => request()->routeIs('expenses.index')])>Expenses</a>
                <a href="{{ route('categories.index') }}" wire:navigate @class(['expense-nav-link', 'expense-nav-link-active' => request()->routeIs('categories.index')])>Categories</a>
                <a href="{{ route('insights') }}" wire:navigate @class(['expense-nav-link', 'expense-nav-link-active' => request()->routeIs('insights')])>Insights</a>
            </div>

            <div class="hidden items-center gap-3 md:flex">
                <a href="{{ route('profile.edit') }}" wire:navigate class="rounded-lg px-3 py-2 text-sm font-medium text-[#f7eadb] transition hover:bg-white/10 hover:text-white">
                    <span x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></span>
                </a>
                <button wire:click="logout" class="rounded-lg border border-[#5b4b3f] px-3 py-2 text-sm font-medium text-[#f7eadb] transition hover:border-[#d8b99a] hover:bg-white/10 hover:text-white">
                    Log Out
                </button>
            </div>

            <button @click="open = ! open" class="inline-flex items-center justify-center rounded-lg border border-[#5b4b3f] p-2 text-[#f7eadb] transition hover:bg-white/10 hover:text-white md:hidden">
                <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <div x-show="open" x-cloak class="expense-mobile-menu md:hidden">
        <div class="grid gap-2">
            <a href="{{ route('dashboard') }}" wire:navigate @class(['expense-nav-link', 'expense-nav-link-active' => request()->routeIs('dashboard')])>Dashboard</a>
            <a href="{{ route('expenses.index') }}" wire:navigate @class(['expense-nav-link', 'expense-nav-link-active' => request()->routeIs('expenses.index')])>Expenses</a>
            <a href="{{ route('categories.index') }}" wire:navigate @class(['expense-nav-link', 'expense-nav-link-active' => request()->routeIs('categories.index')])>Categories</a>
            <a href="{{ route('insights') }}" wire:navigate @class(['expense-nav-link', 'expense-nav-link-active' => request()->routeIs('insights')])>Insights</a>
            <a href="{{ route('profile.edit') }}" wire:navigate class="expense-nav-link">Profile</a>
            <button wire:click="logout" class="expense-nav-link text-left">Log Out</button>
        </div>
    </div>
</nav>
```

- [ ] **Step 3: Run a build**

Run: `npm run build`

Expected: Build succeeds and no Blade syntax error is reported by Vite/Tailwind.

## Task 3: Dashboard Page

**Files:**
- Modify: `resources/views/pages/dashboard.blade.php`

- [ ] **Step 1: Replace the outer starter-kit markup**

Wrap dashboard content in:

```blade
<div class="mx-auto max-w-7xl page-stack">
```

Add a `page-hero`, three `metric-card` blocks, and two `app-card-padded` chart wrappers using the existing `$todayTotal`, `$monthTotal`, `$totalEntries`, `$monthly`, and `$byCategory` data.

- [ ] **Step 2: Update chart colors**

In the existing Chart.js script, use:

```js
backgroundColor: '#c26b50',
```

for the monthly bar chart. Keep category colors from saved categories for the doughnut chart.

- [ ] **Step 3: Run focused syntax check**

Run: `php artisan test --filter=ExampleTest`

Expected: command completes without Blade parse errors. If the test environment reports database/app setup failures unrelated to Blade, run `php artisan view:clear` and continue to the full verification task later.

## Task 4: Expenses Page

**Files:**
- Modify: `resources/views/pages/expenses/index.blade.php`

- [ ] **Step 1: Restyle the page structure**

Wrap content in:

```blade
<div class="mx-auto max-w-7xl page-stack">
```

Replace the plain heading area with a `page-hero` that includes:

- Kicker: `Expense ledger`
- Title: `Perbelanjaan`
- Copy: `Track daily spending, filter by category or month, and keep the month total easy to scan.`
- Primary add button, keeping `wire:click="openCreate"`.

- [ ] **Step 2: Restyle filters and table**

Move search, category filter, and month input into an `app-soft-panel`.

Replace the table wrapper with `app-card`, table class `app-table`, header class `app-table-head`, header cells `app-table-th`, body class `app-table-body`, and rows `app-table-row`.

For the empty row, use:

```blade
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
```

- [ ] **Step 3: Restyle the modal content**

Inside the existing `flux:modal`, keep the same model bindings and save/cancel actions, but use a compact header and `space-y-4` form rhythm consistent with the app cards.

- [ ] **Step 4: Run focused route check**

Run: `php artisan route:list --name=expenses`

Expected: `/expenses` route remains present with name `expenses.index`.

## Task 5: Categories Page

**Files:**
- Modify: `resources/views/pages/categories/index.blade.php`

- [ ] **Step 1: Restyle hero and list**

Wrap content in:

```blade
<div class="mx-auto max-w-5xl page-stack">
```

Use a `page-hero` with kicker `Spending taxonomy`, title `Kategori`, and the existing add button.

Use standalone `app-card-padded` rows for categories with swatch, name, count badge, and actions.

- [ ] **Step 2: Restyle empty state**

Use:

```blade
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
```

- [ ] **Step 3: Run focused route check**

Run: `php artisan route:list --name=categories`

Expected: `/categories` route remains present with name `categories.index`.

## Task 6: Insights Page And Insight Components

**Files:**
- Modify: `resources/views/pages/insights.blade.php`
- Modify visible insight component wrappers listed in the File Map if they use generic `bg-white dark:bg-zinc-800 rounded-lg border` panels.

- [ ] **Step 1: Restyle insights shell**

Wrap content in:

```blade
<div class="mx-auto max-w-7xl page-stack">
```

Add a `page-hero` with kicker `Spending intelligence`, title `Insights`, and a short sentence about trends, categories, and month-over-month movement.

- [ ] **Step 2: Convert stat cards and banner**

Convert the four stat cards to `metric-card` blocks.

Convert the month-over-month banner to a bordered soft panel using warm-compatible status colors:

```blade
'border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-900/60 dark:bg-emerald-950/30 dark:text-emerald-300' => $momChange <= 0,
'border-rose-200 bg-rose-50 text-rose-700 dark:border-rose-900/60 dark:bg-rose-950/30 dark:text-rose-300' => $momChange > 0,
```

- [ ] **Step 3: Update component wrappers**

For each visible insight component wrapper, replace generic card classes with `app-card-padded` or `app-card` plus `app-card-header` where the component has a heading and body.

- [ ] **Step 4: Run focused route check**

Run: `php artisan route:list --name=insights`

Expected: `/insights` route remains present with name `insights`.

## Task 7: Profile Surface

**Files:**
- Modify: `resources/views/profile.blade.php`
- Modify child profile partials only if they hard-code generic white card wrappers:
  - `resources/views/livewire/profile/update-profile-information-form.blade.php`
  - `resources/views/livewire/profile/update-password-form.blade.php`
  - `resources/views/livewire/profile/delete-user-form.blade.php`

- [ ] **Step 1: Restyle visible profile wrapper**

Use `page-stack`, `page-hero`, and `app-card-padded` around profile sections while keeping all Livewire profile forms intact.

- [ ] **Step 2: Run focused route check**

Run: `php artisan route:list --name=profile`

Expected: `/profile` route remains present with name `profile.edit`.

## Task 8: Full Verification

**Files:**
- No code modifications unless verification reveals a concrete issue.

- [ ] **Step 1: Clear cached views**

Run: `php artisan view:clear`

Expected: `Compiled views cleared successfully.`

- [ ] **Step 2: Build frontend assets**

Run: `npm run build`

Expected: Vite build succeeds.

- [ ] **Step 3: Run Laravel tests**

Run: `php artisan test`

Expected: Tests pass, or failures are documented if caused by existing unrelated environment/database state.

- [ ] **Step 4: Start the dev server**

Run: `php artisan serve --host=127.0.0.1 --port=8000`

Expected: Server starts at `http://127.0.0.1:8000`.

- [ ] **Step 5: Inspect in browser**

Open `http://127.0.0.1:8000/dashboard` while authenticated or log in with an existing local account.

Check desktop and mobile widths for:

- Navigation contrast and active states.
- Dashboard hero, metric cards, and charts.
- Expenses filters, table, modal, and empty state.
- Categories list, modal, and empty state.
- Insights cards, banner, and chart panels.
- No overlapping text or broken chart canvases.

## Self-Review

- Spec coverage: The plan covers the app shell, dashboard, expenses, categories, insights, profile, shared utilities, empty states, and verification.
- Placeholder scan: No TBD/TODO/fill-in instructions are present.
- Type consistency: No new PHP methods, properties, routes, models, or validation rules are introduced.
- Scope check: The plan does not add budget logic, database fields, new chart types, or unrelated public landing changes.
