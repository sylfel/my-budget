<?php

use App\Livewire\Charts;
use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test(Charts::class)
        ->assertStatus(200);
});
