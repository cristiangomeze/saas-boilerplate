<x-jet-action-section>
    <x-slot name="title">
        {{ __('Invoices') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Lorem ipsum dolor sit amet.') }}
    </x-slot>

    <x-slot name="content">
        <div class="max-w-xl text-sm text-gray-600">
            {{ __('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin vehicula suscipit imperdiet. Vestibulum ut tortor a arcu ornare scelerisque. Ut vel nibh eu leo ultrices elementum vel eget tortor.') }}
        </div>
        
        @foreach ($invoices as $invoice)
        <div class="mt-5 space-y-6">
            <div class="flex items-center">
                <a class="focus:outline-none" href="{{ route('invoice', $invoice->id) }}">
                    <svg class="w-8 h-8 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                </a>

                <div class="ml-3">
                    <div class="text-sm text-gray-600">
                        {{ __(config('app.name') . ' Service')}} - {{ $invoice->total() }}
                    </div>

                    <div>
                        <div class="text-xs text-gray-500">
                            {{ $invoice->date()->toFormattedDateString() }},

                            <span class="text-green-500 font-semibold">{{ __('Last Invoice') }}</span>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        @endforeach
    </x-slot>
</x-jet-action-section>