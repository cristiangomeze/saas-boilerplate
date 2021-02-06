<x-jet-action-section>
    <x-slot name="title">
        {{ __('Current Subcription Plan') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Change your plan subscription among those available') }}
    </x-slot>

    <x-slot name="content">
        <div x-data="{ shown: @entangle('choosingSubcriptionPlan') }">
            <div x-show="true === shown" 
                class="grid grid-cols-1 divide-y">
                
                <div class="flex justify-between items-center pb-4">
                   
                    @if($this->currentPlan)
                    <div class="max-w-xl text-sm text-gray-600">
                        {{ __('You are currently subcribed to the Basic (Monthly) plan.') }}
                    </div>
                    @endif

                    <div class="inline-flex items-center" x-data="{toggleActive: @entangle('choosingYearlyPlan')}" wire:click="$toggle('choosingYearlyPlan')">
                        <div class="font-semibold text-gray-700 truncate mr-2">{{ __('Monthly') }}</div>
                        <div class="w-10 h-5 flex items-center bg-gray-300 rounded-full p-1 duration-300 ease-in-out" :class="{ 'bg-gray-900': toggleActive}">
                          <div class="bg-white w-4 h-4 rounded-full shadow-md transform duration-300 ease-in-out" :class="{ 'translate-x-4': toggleActive,}"></div>
                        </div>
                        <div class="font-semibold text-gray-700 truncate ml-2">{{ __('Yearly') }}</div>
                    </div>
                </div>

                @foreach ($plans as $plan)
                <div class="grid grid-cols-3 gap-2 py-4 items-center">
                    <div>
                        <label class="inline-flex items-center">
                            <input 
                                wire:model="plan"
                                type="radio" 
                                class="rounded-full border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                name="plan"
                                value="{{ $plan->braintree_plan }}">
                            <span class="ml-2">{{ $plan->name }}</span>
                        </label>
                    </div>
                    <div class="flex justify-center">
                        <button class="inline-flex items-center px-4 py-2 bg-white border shadow rounded-md font-semibold text-xs text-gray-900 tracking-widest hover:bg-gray-100 focus:outline-none disabled:opacity-25 transition ease-in-out duration-150">
                            {{ __('Features') }}
                        <button>
                    </div>
                    <div class="flex justify-end">
                        <span class="font-bold">${{ number_format($plan->price, 2) }}</span>&nbsp;/ Monthly
                    </div>
                </div>
                @endforeach

                <x-jet-input-error for="plan" class="pt-4" />
            </div>
            <div x-show="false === shown">
                <div class="text-lg font-semibold pb-3">
                    {{ __($this->currentPlan?->name) }}
                </div>

                <div class="font-semibold pb-3">
                    ${{ number_format($this->currentPlan?->price, 2) }} / {{ __('Monthly')}}
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
            </div>
        </div>
        
        <div class="flex items-center mt-5">
            @if($this->choosingSubcriptionPlan)
            <x-jet-button wire:click="chooseSubcriptionPlan" wire:loading.attr="disabled">
                {{ __('Choose Subcription Plan') }}
            </x-jet-button>
            @else
            <x-jet-button wire:click="$toggle('choosingSubcriptionPlan')" wire:loading.attr="disabled">
                {{ __('Change Subcription Plan') }}
            </x-jet-button>
            @endif

            <x-jet-action-message class="ml-3" on="chosenPlan">
                {{ __('Done.') }}
            </x-jet-action-message>
        </div>
    </x-slot>
</x-jet-action-section>
