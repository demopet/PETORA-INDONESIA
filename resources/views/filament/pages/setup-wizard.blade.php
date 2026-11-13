<x-filament-panels::page>
    <div class="space-y-6 max-w-2xl mx-auto">
        <div class="text-center">
            <h1 class="text-2xl font-bold tracking-tight">Pengaturan Awal PETORA INDONESIA</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Buat akun Owner, profil bisnis, dan cabang pertama.</p>
        </div>

        <div class="grid gap-2 sm:grid-cols-3">
            @foreach($checks as $name => $check)
                <div class="rounded-lg border border-gray-200 dark:border-white/10 p-3 text-sm flex items-start gap-2">
                    <span class="mt-0.5 inline-flex h-2.5 w-2.5 rounded-full {{ $check['ok'] ? 'bg-success-500' : 'bg-warning-500' }}"></span>
                    <div>
                        <div class="font-medium">{{ str($name)->headline() }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $check['message'] }}</div>
                    </div>
                </div>
            @endforeach
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
