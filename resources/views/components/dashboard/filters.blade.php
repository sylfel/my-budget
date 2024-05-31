@props(['total' => 0, 'year', 'month', 'actions' => '', 'filters' => ''])

@php
    use Carbon\Carbon;
    $currentDate = Carbon::createMidnightDate($year, $month + 1, 1)->locale('fr');
    $nextDate = $currentDate->copy()->addMonth()->toDateString();
    $prevDate = $currentDate->copy()->subMonth()->toDateString();
@endphp

<x-section {{ $attributes }}>
    <x-slot:header>
        <h2 class="text-center grow">{{ __('Filters') }}</h2>
        <span class="sm:hidden text-center font-bold {{ $total >= 0 ? 'text-green-600' : 'text-red-600' }}">
            {{ Number::currency($total / 100, 'EUR', 'fr') }}
        </span>
        {{ $actions }}
    </x-slot:header>
    <div>
        <div class="flex items-center justify-center mb-2">
            <x-filament::icon-button icon="heroicon-m-arrow-left" label="Précédent"
                wire:click="$dispatch('update-filters',{ date: '{{ $prevDate }}'})" />

            {{-- todo : afficher calendrier pour changer rapidement --}}
            <span class="mx-4">{{ Str::ucfirst($currentDate->isoFormat('MMMM Y')) }}</span>

            <x-filament::icon-button icon="heroicon-m-arrow-right" label="Suivant"
                wire:click="$dispatch('update-filters',{ date: '{{ $nextDate }}'})" />

            {{ $filters }}
        </div>
    </div>
</x-section>
