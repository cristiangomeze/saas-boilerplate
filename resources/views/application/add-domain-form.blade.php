<x-jet-form-section submit="addDomain">
    <x-slot name="title">
        {{ __('Add Custom Domain') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Add the custom domains you need.') }}
    </x-slot>

    <x-slot name="form">
        <div class="col-span-6 sm:col-span-4">
            <x-jet-label for="domain" value="{{ __('Domain') }}" />
            <x-jet-input id="domain" type="text" class="mt-1 block w-full" wire:model.defer="domain" autocomplete="domain" />
            <x-jet-input-error for="domain" class="mt-2" />
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-jet-action-message class="mr-3" on="Added">
            {{ __('Added.') }}
        </x-jet-action-message>

        <x-jet-button>
            {{ __('Add') }}
        </x-jet-button>
    </x-slot>
</x-jet-form-section>
