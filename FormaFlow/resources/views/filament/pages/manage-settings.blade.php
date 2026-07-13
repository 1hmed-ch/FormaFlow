<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <div class="mt-10 flex justify-end">
            <x-filament::button
                type="submit"
                size="lg"
                wire:loading.attr="disabled"
                wire:target="save"
            >
                <span wire:loading.remove wire:target="save">
                    Enregistrer
                </span>

                <span wire:loading wire:target="save">
                    Enregistrement...
                </span>
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>