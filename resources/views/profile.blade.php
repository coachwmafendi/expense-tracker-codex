<x-app-layout>
    <div class="mx-auto max-w-7xl page-stack">
        <section class="page-hero">
            <div class="page-hero-kicker">Account settings</div>
            <h1 class="page-hero-title">{{ __('Profile') }}</h1>
            <p class="page-hero-copy">
                Manage account details, password updates, and account removal from one focused settings surface.
            </p>
        </section>

        <div class="space-y-6">
            <div class="app-card-padded">
                <div class="max-w-xl">
                    <livewire:profile.update-profile-information-form />
                </div>
            </div>

            <div class="app-card-padded">
                <div class="max-w-xl">
                    <livewire:profile.update-password-form />
                </div>
            </div>

            <div class="app-card-padded">
                <div class="max-w-xl">
                    <livewire:profile.delete-user-form />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
