<x-jet-action-section>
    <x-slot name="title">
        {{ __('Current Subcription Plan') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Change your plan subscription among those available') }}
    </x-slot>

    <x-slot name="content">
        <div class="text-lg font-semibold pb-3">
            {{ __('Hobby') }}
        </div>

        <div class="font-semibold pb-3">
          {{ __('$14.00 / monthly') }}
        </div>
        <div class="max-w-xl text-sm text-gray-600">
            {{ __('This is a short plain-text description of the plan describes its features that it makes available to end users. It may be a long sentence similar to this one.') }}
        </div>

        <ul class="list-none pl-0 space-y-3 my-5">
            <li class="flex items-baseline space-x-2">
                <div class="text-sm text-gray-600 flex-none">5 Servers</span>
            </li>
            <li class="flex items-start space-x-2">
                <span class="text-sm text-gray-600">Shell Access</span>
            </li>
            <li class="flex items-start space-x-2">
                <span class="text-sm text-gray-600">Email Support</span>
            </li>
        </ul>
        
        <div class="flex items-center mt-5">
            <x-jet-button wire:click="confirmLogout" wire:loading.attr="disabled">
                {{ __('Change Subcription Plan') }}
            </x-jet-button>

            <x-jet-action-message class="ml-3" on="loggedOut">
                {{ __('Done.') }}
            </x-jet-action-message>
        </div>
    </x-slot>
</x-jet-action-section>
