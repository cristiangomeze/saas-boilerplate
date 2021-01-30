<x-jet-action-section>
    <x-slot name="title">
        {{ __('Cancel Subcription') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Lorem ipsum dolor sit amet.') }}
    </x-slot>

    <x-slot name="content">
        <div class="max-w-xl text-sm text-gray-600">
            {{ __('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam fringilla feugiat diam, at volutpat massa venenatis non. Integer nec pharetra odio. Fusce sagittis libero eu ipsum dictum, vel pretium mi tincidunt.') }}
        </div>

        <div class="mt-5">
            <x-jet-danger-button>
                {{ __('Cancel Subcription') }}
            </x-jet-danger-button>
        </div>
    </x-slot>
</x-jet-action-section>