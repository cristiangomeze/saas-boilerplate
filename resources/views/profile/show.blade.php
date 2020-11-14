<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            @livewire('update-profile-information-form')

            <x-section-border />

            <div class="mt-10 sm:mt-0">
                @livewire('update-password-form')
            </div>

            <x-section-border />

            <div class="mt-10 sm:mt-0">
                @livewire('logout-other-browser-sessions-form')
            </div>

            <x-section-border />

            <div class="mt-10 sm:mt-0">
                @livewire('delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
