@props(['total' => 0, 'year', 'month', 'actions' => '', 'form' => ''])

@php
    use Carbon\Carbon;
    $currentDate = Carbon::createMidnightDate($year, $month + 1, 1)->locale('fr');
    $nextDate = $currentDate->copy()->addMonth();
    $prevDate = $currentDate->copy()->subMonth();
@endphp

<x-section {{ $attributes }}>
    <x-slot:header>
        <h2 class="text-center grow">{{ __('Filters') }}</h2>
        <span class="sm:hidden text-center font-bold {{ $total >= 0 ? 'text-green-600' : 'text-red-600' }}">
            {{ Number::currency($total / 100, 'EUR', 'fr') }}
        </span>
    </x-slot:header>
    <div>
        <div class="flex justify-center mb-4">
            <x-filament::button size="xs" outlined icon="heroicon-m-arrow-left"
                wire:click="$dispatch('update-filters',{ date: '{{ $prevDate->toDateString() }}'})">
                {{ Str::ucfirst($prevDate->isoFormat('MMM')) }}
            </x-filament::button>

            {{ $actions }}

            <x-filament::button size="xs" outlined icon="heroicon-m-arrow-right" icon-position="after"
                wire:click="$dispatch('update-filters',{ date: '{{ $nextDate->toDateString() }}'})">
                {{ Str::ucfirst($nextDate->isoFormat('MMM')) }}
            </x-filament::button>
        </div>
        <div>
            {{ $form }}
        </div>
    </div>
</x-section>
