<x-jet-action-section>
    <x-slot name="title">
        {{ __('Domains') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Manage your application\'s domains.') }}
    </x-slot>

    <x-slot name="content">
        @if (count($this->domains) > 0)
            <div class="space-y-6">
                <!-- Domains -->
                @foreach ($this->domains as $domain)
                    <div class="flex justify-between">
                        <div class="flex items-center">
                            <div>
                                <svg class="w-8 h-8 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>

                            <div class="ml-3">
                                <div class="text-sm text-gray-600">
                                    {{ $domain->name }} @if($domain->is_primary) - <span class="text-green-500 font-semibold">{{ __('Primary') }}</span> @endif
                                </div>

                                <div>
                                    <div class="text-xs text-gray-500">
                                        {{ $domain->type }},

                                        {{ __('Added on') }} {{ $domain->created_at }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if(! $domain->is_primary)
                            <div class="flex items-between space-x-2">
                                <x-jet-secondary-button type="button">
                                    {{ __('Make primary') }}
                                </x-jet-secondary-button>
                                @if ('Domain' === $domain->type)
                                    <x-jet-secondary-button type="button" class="text-red-600">
                                        {{ __('Delete') }}
                                    </x-jet-secondary-button>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </x-slot>

</x-jet-action-section>
