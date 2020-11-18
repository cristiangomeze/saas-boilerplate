<x-jet-action-section>
    <x-slot name="title">
        {{ __('Create Application') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Create application with fallback subdomain.') }}
    </x-slot>

    <x-slot name="content">
        <div class="max-w-xl text-sm text-gray-600">
            {{ __('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce vehicula aliquam iaculis. Proin tortor justo, maximus pellentesque volutpat eget, posuere et massa. Aenean ornare, ipsum sit amet porta mattis, enim nisl ullamcorper ligula, eget suscipit lectus magna et felis. ') }}
        </div>

        <div class="flex items-center mt-5">
            <x-jet-button wire:click="confirmApplicationCreation" wire:loading.attr="disabled">
                {{ __('Create') }}
            </x-jet-button>

            <x-jet-action-message class="ml-3" on="created" x-on:confirming-redirect.window="setTimeout(() => window.location.href='{{route('user.application')}}', 350)">
                {{ __('Done.') }}
            </x-jet-action-message>
        </div>

        <!-- Create Application Confirmation Modal -->
        <x-jet-dialog-modal wire:model="confirmingApplicationCreate">
            <x-slot name="title">
                {{ __('Create Application') }}
            </x-slot>

            <x-slot name="content">
                {{ __('Please enter your company name and the domain name you want to use.') }}

                <div class="mt-4" x-data="{}" x-on:confirming-create-application.window="setTimeout(() => $refs.name.focus(), 250)">
                    <x-jet-input type="text" class="mt-1 block w-3/4" placeholder="{{ __('name') }}"
                                x-ref="name"
                                wire:model.defer="name" />

                    <x-jet-input-error for="name" class="mt-2" />

                    <div class="flex flex-wrap items-stretch w-3/4 mt-8 relative">
                        <x-jet-input class="flex-shrink flex-grow flex-auto leading-normal w-px flex-1 border h-10 border-grey-light rounded rounded-r-none px-3 relative" 
                            type="text" 
                            placeholder="{{ __('domain') }}"
                            wire:model.defer="domain" />
                        <div class="flex -mr-px">
                            <span class="flex items-center leading-normal bg-grey-lighter rounded rounded-l-none border border-l-0 border-grey-light px-3 whitespace-no-wrap text-grey-dark text-sm">{{ config('app.url') }}</span>
                        </div>	
                    </div>
                    <x-jet-input-error for="domain" class="mt-2" />
                </div>
            </x-slot>

            <x-slot name="footer">
                <x-jet-secondary-button wire:click="$toggle('confirmingApplicationCreate')" wire:loading.attr="disabled">
                    {{ __('Nevermind') }}
                </x-jet-secondary-button>

                <x-jet-button class="ml-2" wire:click="createApplication" wire:loading.attr="disabled">
                    {{ __('Create Application') }}
                </x-jet-button>
            </x-slot>
        </x-jet-dialog-modal>
    </x-slot>
</x-jet-action-section>