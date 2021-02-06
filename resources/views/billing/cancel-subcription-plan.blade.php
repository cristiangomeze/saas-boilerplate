<x-jet-action-section>
    <x-slot name="title">
        {{ __('Cancel Subcription') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Lorem ipsum dolor sit amet.') }}
    </x-slot>

    <x-slot name="content">
        @if(Auth::user()?->subscription('default')?->onGracePeriod())
        <div class="max-w-xl text-sm text-gray-600">
            {{ __('Your subscription is active until: '.Auth::user()?->subscription('default')?->ends_at->format('d-m-Y').', I will be able to continue using the application until it ends. once finished you will not be able to resume the subscription') }}
        </div>
        @else
        <div class="max-w-xl text-sm text-gray-600">
            {{ __('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam fringilla feugiat diam, at volutpat massa venenatis non. Integer nec pharetra odio. Fusce sagittis libero eu ipsum dictum, vel pretium mi tincidunt.') }}
        </div>
        @endif

        <div class="mt-5">
            @unless($cancelingSubcription)
            <x-jet-danger-button wire:click="cancelSubcription" wire:loading.attr="disabled">
                {{ __('Cancel Subcription') }}
            </x-jet-danger-button>
            @endunless

            @if(Auth::user()?->subscription('default')?->onGracePeriod())
            <x-jet-button wire:click="resumeSubcription" wire:loading.attr="disabled">
                {{ __('Resume Subcription') }}
            </x-jet-button>
            @endif

            <x-jet-action-message class="ml-3" on="chosenPlan">
                {{ __('Done.') }}
            </x-jet-action-message>
        </div>
    </x-slot>
</x-jet-action-section>