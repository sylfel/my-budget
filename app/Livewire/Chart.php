<?php

namespace App\Livewire;

use Illuminate\Support\Collection;
use Livewire\Component;

class Chart extends Component
{
    public Collection $labels;

    public Collection $datasets;

    public $title;

    public $name;

    public function render()
    {
        return view('livewire.chart');
    }
}
