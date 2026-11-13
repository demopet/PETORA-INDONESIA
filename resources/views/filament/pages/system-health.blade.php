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

    <x-filament::section class="mt-4">
        <x-slot name="heading">Pemeriksaan Alamat Publik</x-slot>
        <p class="text-sm text-gray-600 dark:text-gray-300 mb-3">Memeriksa endpoint kesehatan melalui APP_URL/PUBLIC_APP_URL yang dikonfigurasi. Pemeriksaan ini bukan uji dari jaringan penyedia webhook.</p>
        <x-filament::button wire:click="runPublicUrlCheck" size="sm">Jalankan Pemeriksaan</x-filament::button>
        @if(!empty($publicCheck))
            <div class="mt-3 text-sm">
                <div class="flex items-center gap-2">
                    <span class="inline-flex h-2.5 w-2.5 rounded-full {{ ($publicCheck['ok'] ?? false) ? 'bg-success-500' : 'bg-danger-500' }}"></span>
                    <span>{{ $publicCheck['message'] ?? '' }}</span>
                </div>
                @if(!empty($publicCheck['url']))
                    <div class="text-xs text-gray-500 mt-1">URL: {{ $publicCheck['url'] }}</div>
                @endif
                <div class="text-xs text-gray-500 mt-1">{{ $publicCheck['note'] ?? '' }}</div>
            </div>
        @endif
    </x-filament::section>

    <x-filament::section class="mt-4">
        <x-slot name="heading">Data Contoh</x-slot>
        <p class="text-sm text-gray-600 dark:text-gray-300 mb-3">Muat data contoh untuk mencoba sistem setelah pengaturan awal. Data contoh dapat dihapus.</p>
        <div class="flex gap-2">
            @if($sampleLoaded)
                <x-filament::button wire:click="removeSampleData" color="danger" size="sm">Hapus Data Contoh</x-filament::button>
                <span class="text-sm text-success-600 dark:text-success-400 self-center">Data contoh aktif.</span>
            @else
                <x-filament::button wire:click="loadSampleData" size="sm">Muat Data Contoh</x-filament::button>
            @endif
        </div>
    </x-filament::section>

    <p class="text-xs text-gray-500 dark:text-gray-400 mt-4">Status integrasi dan detail antrean/penjadwal/backup akan dilengkapi bersama modul Integrasi dan Operasional.</p>
</x-filament-panels::page>
