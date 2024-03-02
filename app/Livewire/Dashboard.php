<?php

namespace App\Livewire;

use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Livewire\Attributes\Url;
use Livewire\Component;

class Dashboard extends Component
{
    public $years;

    public $months;

    #[Url(keep: true)]
    public int $year;

    #[Url(keep: true)]
    public int $month;

    public $listeners = [
        'noteCreated' => 'render',
    ];

    public function mount()
    {
        $this->year = $this->year ?? now()->year;
        $this->month = $this->month ?? now()->month - 1;
    }

    public function render()
    {
        $categories = Category::withSum(['notes' => function (Builder $query) {
            $query->where('notes.year', $this->year)
                ->where('notes.month', $this->month);
        }], 'price')
            ->with(['notes' => function (HasMany $query) {
                $query->where('notes.year', $this->year)
                    ->where('notes.month', $this->month)
                    ->orderBy('created_at', 'desc');
            }, 'notes.poste'])
            ->get();

        return view('livewire.dashboard', compact('categories'));
    }
}
