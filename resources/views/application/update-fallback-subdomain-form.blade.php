<x-jet-form-section submit="updateFallbackSubdomain">
    <x-slot name="title">
        {{ __('Update Fallback subdomain') }}
    </x-slot>

    <x-slot name="description">
        {{ __('update fallback subdomains.') }}
    </x-slot>

    <x-slot name="form">
        <div class="col-span-6 sm:col-span-4">
            <x-jet-label for="subdomain" value="{{ __('Subdomain') }}" />
            <x-jet-input id="subdomain" type="text" class="mt-1 block w-full" wire:model.defer="subdomain" autocomplete="subdomain" />
            <x-jet-input-error for="subdomain" class="mt-2" />
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-jet-action-message class="mr-3" on="saved">
            {{ __('Saved.') }}
        </x-jet-action-message>

        <x-jet-button>
            {{ __('Save') }}
        </x-jet-button>
    </x-slot>
</x-jet-form-section>
