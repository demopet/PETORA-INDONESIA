<x-filament-panels::page>
    <div class="grid gap-4 md:grid-cols-2">
        @foreach($checks as $name => $check)
            <x-filament::section>
                <x-slot name="heading">{{ str($name)->headline() }}</x-slot>
                <div class="flex items-center gap-2">
                    <span class="inline-flex h-2.5 w-2.5 rounded-full {{ $check['ok'] ? 'bg-success-500' : 'bg-danger-500' }}"></span>
                    <span>{{ $check['message'] }}</span>
                </div>
            </x-filament::section>
        @endforeach
    </div>
    <p class="text-xs text-gray-500 dark:text-gray-400 mt-4">Pemeriksaan alamat publik (PUBLIC_APP_URL) dan status integrasi akan ditambahkan pada pengaturan integrasi. Pemeriksaan ini bukan uji dari jaringan penyedia webhook.</p>
</x-filament-panels::page>
