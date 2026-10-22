<x-filament-panels::page>
    <form wire:submit="save" class="space-y-6">
        {{ $this->form }}

        <div class="flex items-center gap-3">
            <x-filament::button type="submit">Simpan pengaturan</x-filament::button>
            <x-filament::link :href="url('/')" target="_blank" icon="heroicon-m-arrow-top-right-on-square">Lihat website</x-filament::link>
        </div>
    </form>
</x-filament-panels::page>
