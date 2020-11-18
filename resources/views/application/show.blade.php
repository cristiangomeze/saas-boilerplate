<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Application') }}
        </h2>
    </x-slot>

    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            @if($hasApplicationCreated)
                @livewire('update-application-information-form')

                <x-jet-section-border />

                @livewire('update-domains-form')
            @else
                @livewire('create-application-form')
            @endif
        </div>
    </div>
</x-app-layout>
