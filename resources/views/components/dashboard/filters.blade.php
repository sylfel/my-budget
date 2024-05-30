@props(['total' => 0, 'year', 'month', 'actions'])

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
        <div class="ml-2">
            <x-filament-actions::group icon="heroicon-o-cog-6-tooth" :actions="$actions" />
        </div>
    </x-slot:header>
    <form class="gap-4">
        <div class="flex items-center justify-center">
            <x-filament::icon-button icon="heroicon-m-arrow-left" label="Précédent"
                wire:click="$dispatch('update-filters',{ date: '{{ $prevDate }}'})" />

            {{-- todo : afficher calendrier pour changer rapidement --}}
            <span class="mx-4">{{ Str::ucfirst($currentDate->isoFormat('MMMM Y')) }}</span>

            <x-filament::icon-button icon="heroicon-m-arrow-right" label="Suivant"
                wire:click="$dispatch('update-filters',{ date: '{{ $nextDate }}'})" />

            {{-- <div class="ml-2">
                <x-filament-actions::group :actions="[$this->initMonthAction]" />
            </div> --}}


        </div>
    </form>
</x-section>
