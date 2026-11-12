<x-filament-panels::page>
    <form wire:submit="save" class="space-y-6 max-w-2xl">
        {{ $this->form }}

        <x-filament::button type="submit">
            Simpan
        </x-filament::button>
    </form>
</x-filament-panels::page>
