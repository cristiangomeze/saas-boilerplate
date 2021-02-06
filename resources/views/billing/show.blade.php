<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Billing') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
        @livewire('billing.current-subcription-plan')
        
        <x-jet-section-border />

        @livewire('billing.payment-method')

        <x-jet-section-border />

        @livewire('billing.invoice-subcription')

        <x-jet-section-border />

        @livewire('billing.cancel-subcription-plan')       
    </div>
</x-app-layout>