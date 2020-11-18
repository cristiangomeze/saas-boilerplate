<x-jet-form-section submit="updateApplicationInformation">
    <x-slot name="title">
        {{ __('Company Information') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Update your company\'s logo and information.') }}
    </x-slot>

    <x-slot name="form">
        <!-- Application Logo -->
        <div x-data="{logoName: null, logoPreview: null}" class="col-span-6 sm:col-span-4">
                <!-- Application logo File Input -->
                <input type="file" class="hidden"
                            wire:model="logo"
                            x-ref="logo"
                            x-on:change="
                                    logoName = $refs.logo.files[0].name;
                                    const reader = new FileReader();
                                    reader.onload = (e) => {
                                        logoPreview = e.target.result;
                                    };
                                    reader.readAsDataURL($refs.logo.files[0]);
                            " />

                <x-jet-label for="logo" value="{{ __('Company Logo') }}" />

                <!-- Current Profile logo -->
                <div class="mt-2" x-show="! logoPreview">
                    <img src="{{ $this->tenant->application_logo_url }}" alt="{{ $this->tenant->name }}" class="rounded-full h-20 w-20 object-cover">
                </div>

                <!-- New Profile logo Preview -->
                <div class="mt-2" x-show="logoPreview">
                    <span class="block rounded-full w-20 h-20"
                          x-bind:style="'background-size: cover; background-repeat: no-repeat; background-position: center center; background-image: url(\'' + logoPreview + '\');'">
                    </span>
                </div>

                <x-jet-secondary-button class="mt-2 mr-2" type="button" x-on:click.prevent="$refs.logo.click()">
                    {{ __('Select A New logo') }}
                </x-jet-secondary-button>

                @if ($this->tenant->application_logo_path)
                    <x-jet-secondary-button type="button" class="mt-2" wire:click="deleteApplicationLogo">
                        {{ __('Remove logo') }}
                    </x-jet-secondary-button>
                @endif

                <x-jet-input-error for="logo" class="mt-2" />
            </div>

        <!-- Name -->
        <div class="col-span-6 sm:col-span-4">
            <x-jet-label for="name" value="{{ __('Name') }}" />
            <x-jet-input id="name" type="text" class="mt-1 block w-full" wire:model.defer="state.name" autocomplete="name" />
            <x-jet-input-error for="name" class="mt-2" />
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-jet-action-message class="mr-3" on="saved">
            {{ __('Saved.') }}
        </x-jet-action-message>

        <x-jet-button wire:loading.attr="disabled" wire:target="logo">
            {{ __('Save') }}
        </x-jet-button>
    </x-slot>
</x-jet-form-section>
