<?php

use App\Livewire\Chart;
use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test(Chart::class)
        ->assertStatus(200);
});
