<x-filament-panels::page>
    <div class="space-y-6 max-w-2xl mx-auto">
        <div class="text-center">
            <h1 class="text-2xl font-bold tracking-tight">Pengaturan Awal PETORA INDONESIA</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Buat akun Owner, profil bisnis, dan cabang pertama.</p>
        </div>

        <form wire:submit="submit" class="space-y-6">
            {{ $this->form }}

            <x-filament::button type="submit" class="w-full">
                Selesaikan Pengaturan Awal
            </x-filament::button>
        </form>

        <p class="text-center text-xs text-gray-500 dark:text-gray-400">
            {{ __('app.attribution') }}
        </p>
    </div>
</x-filament-panels::page>
