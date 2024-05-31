@props(['total'])

<x-section class="hidden sm:block">
    <x-slot:header>
        <h2 class="text-center grow">{{ __('Total') }}</h2>
    </x-slot:header>

    <div class="text-center font-bold {{ $total >= 0 ? 'text-green-600' : 'text-red-600' }}">
        {{ Number::currency(($total ?? 0) / 100, 'EUR', 'fr') }}
    </div>
</x-section>
