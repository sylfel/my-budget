<?php

use App\Livewire\Dashboard;
use Livewire\Livewire;

$years = [2023, 2024];
$months = ['Jan', 'Fev', 'Mar', 'Avr', 'Mai', 'Jui', 'Jul', 'Aou', 'Sep', 'Oct', 'Nov', 'Dec'];

it('renders successfully', function () use ($years, $months) {
    Livewire::test(Dashboard::class, ['years' => $years, 'months' => $months])
        ->assertStatus(200);
});

it('can init months', function () use ($years, $months) {
    Livewire::withQueryParams(['year' => '2023', 'month' => 2])
        ->test(Dashboard::class, ['years' => $years, 'months' => $months])
        ->callAction('initMonthAction');
});

it('can show', function () use ($years, $months) {
    $this->seed();

    Livewire::withQueryParams(['year' => '2024', 'month' => 3])
        ->test(Dashboard::class, ['years' => $years, 'months' => $months])
        ->assertSee('Alimentation');
});
