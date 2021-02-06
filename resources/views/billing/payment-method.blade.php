<x-jet-action-section>
    <x-slot name="title">
        {{ __('Payment Method') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Lorem ipsum dolor sit amet.') }}
    </x-slot>

    <x-slot name="content">
        <div id="payment-container"></div>

        <input id="client-token" type="hidden" value="{{ $this->token }}">

        <div class="mt-5">
            <x-jet-button id="submit-button">
                {{ __('Update Payment Method') }}
            </x-jet-button>
        </div>
    </x-slot>
</x-jet-action-section>