<?php

use Carbon\Carbon;

use function Livewire\Volt\computed;
use function Livewire\Volt\state;

state(['total' => 0, 'month', 'year'])->reactive();

$currentDate = computed(function () {
    return Carbon::createMidnightDate($this->year, $this->month + 1, 1)->locale('fr');
});

$changeDate = function (int $offset) {
    $date = $this->currentDate->copy()->addMonths($offset);
    $this->dispatch('update-filters', date: $date);
};

?>
<x-section>
    <x-slot:header>
        <h2 class="text-center grow">{{ __('Filters') }}</h2>
        <span class="sm:hidden text-center font-bold {{ $total >= 0 ? 'text-green-600' : 'text-red-600' }}">
            {{ Number::currency(($total ?? 0) / 100, 'EUR', 'fr') }}
        </span>
    </x-slot:header>
    <form class="gap-4">
        <div class="flex items-center ">
            <x-filament::icon-button icon="heroicon-m-arrow-left" wire:click="changeDate(-1)" label="Précédent" />

            {{-- todo : afficher calendrier pour changer rapidement --}}
            <span class="mx-4">{{ Str::ucfirst($this->currentDate->isoFormat('MMMM Y')) }}</span>

            <x-filament::icon-button icon="heroicon-m-arrow-right" wire:click="changeDate(+1)" label="Suivant" />

            {{-- <div class="ml-2">
                <x-filament-actions::group :actions="[$this->initMonthAction]" />
            </div> --}}

        </div>
    </form>
</x-section>
