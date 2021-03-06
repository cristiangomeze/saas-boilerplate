<div class="md:grid md:grid-cols-3 md:gap-6" x-data="{ shown: @entangle('choosingSubcriptionPlan'), hidden: @entangle('hideWithEvents') }" x-show="!hidden">
    <x-jet-section-title>
        <x-slot name="title">Subcriptions</x-slot>
        <x-slot name="description">Change your plan subscription among those available</x-slot>
    </x-jet-section-title>

    <div x-show="false === shown" class="mt-5 md:mt-0 md:col-span-2">
        <div class="px-4 py-5 sm:p-6 bg-white shadow sm:rounded-lg">
            <div class="mt-5">
                <x-jet-button wire:click="$toggle('choosingSubcriptionPlan')" wire:loading.attr="disabled">
                    {{ __('Change Subcription Plan') }}
                </x-jet-button>
            </div>
        </div>
    </div>

    <div x-show="shown" class="mt-5 md:mt-0 md:col-span-2">
        <div class="mb-4 inline-flex items-center" x-data="{toggleActive: @entangle('choosingYearlyPlan')}" wire:click="$toggle('choosingYearlyPlan')">
            <div class="font-semibold text-gray-700 truncate mr-2">{{ __('Monthly') }}</div>
            <div class="w-14 h-7 flex items-center bg-gray-300 rounded-full p-1 duration-300 ease-in-out" :class="{ 'bg-gray-900': toggleActive}">
              <div class="bg-white w-6 h-6 rounded-full shadow-md transform duration-300 ease-in-out" :class="{ 'translate-x-6': toggleActive,}"></div>
            </div>
            <div class="font-semibold text-gray-700 truncate ml-2">{{ __('Yearly') }}</div>
        </div>
   
        @foreach ($plans as $plan)
        <form class="mb-6" wire:submit.prevent="chosenPlan({{ $choosingYearlyPlan ? $plan['yearly_id'] : $plan['monthly_id'] }})">
            <div class="px-4 py-5 bg-white sm:p-6 shadow sm:rounded-tl-md sm:rounded-tr-md">
                <h3 class="text-lg font-bold text-gray-900">{{ $plan['name'] }}</h3>
                <h4 class="mt-3 text-md font-semibold text-gray-900">$99.00 / {{ $choosingYearlyPlan ? __('Yearly') : __('Monthly') }}</h4>
                <div class="mt-3 max-w-xl text-sm text-gray-600">
                    <p>{{ $plan['short_description'] }}</p>
                </div>
                <div class="mt-3 max-w-xl text-sm text-gray-600">
                   <ul class="space-y-2">
                       @foreach ($plan['features'] as $feature)
                       <li>
                            <div class="inline-flex items-center">
                                <svg class="text-green-500 w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                               {{ $feature }}
                            </div>
                        </li>
                       @endforeach
                   </ul>
                </div>
            </div>
            <div class="flex items-center justify-end px-4 py-3 bg-gray-50 text-right sm:px-6 shadow sm:rounded-bl-md sm:rounded-br-md">
                <x-jet-action-message class="mr-3" on="saved">
                    {{ __('Saved.') }}
                </x-jet-action-message>
        
                <x-jet-button wire:loading.attr="disabled">
                    {{ __('Subcribe') }}
                </x-jet-button>
            </div>
        </form>
        @endforeach
    </div>

    <!-- Subscription purchase Confirmation Modal -->
    <x-jet-dialog-modal wire:model="confirmingSubcriptionPurchase" maxWidth="sm">
        <x-slot name="title">
            <div class="text-center">{{ __('Purchase') }} <span class="font-bold">{{ config('app.name') }} Hobby</span></div>
        </x-slot>

        <x-slot name="content">
            <div class="text-center">
                <p class="text-lg font-semibold">{{ __('Your total is $12.00') }}</p>
                <p>{{ __('then $12.00/month') }}</p>
            </div>

            {{-- <div class="mt-4" x-data="{}" x-on:confirming-logout-other-browser-sessions.window="setTimeout(() => $refs.coupon.focus(), 250)">
                <x-jet-input type="text" class="mt-1 block w-3/4"
                            placeholder="{{ __('Coupon') }}"
                            x-ref="coupon"
                            wire:model.defer="coupon"
                            wire:keydown.enter="subcribeNow" />

                <x-jet-input-error for="coupon" class="mt-2" />
            </div> --}}
        </x-slot>

        <x-slot name="footer">
            <div class="flex justify-between">
                <x-jet-secondary-button wire:click="$toggle('confirmingSubcriptionPurchase')" wire:loading.attr="disabled">
                    {{ __('Cancel') }}
                </x-jet-secondary-button>
    
                <x-jet-button class="ml-2"
                            wire:click="subcribeNow"
                            wire:loading.attr="disabled">
                    {{ __('Subcribe Now') }}
                </x-jet-button>
            </div>
        </x-slot>
    </x-jet-dialog-modal>
</div>