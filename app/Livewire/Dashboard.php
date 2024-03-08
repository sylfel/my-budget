<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Note;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\Enums\ActionSize;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Livewire\Attributes\Url;
use Livewire\Component;

class Dashboard extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    public $years;

    public $months;

    #[Url(keep: true)]
    public int $year;

    #[Url(keep: true)]
    public int $month;

    public function addToCategoryAction(): Action
    {
        return CreateAction::make('addToCategory')
            ->model(Note::class)
            ->form(function (array $arguments) {
                $category = Category::with('postes')->find($arguments['category']);
                $fields = [
                    TextInput::make('price')
                        ->numeric()
                        ->inputMode('decimal')
                        ->required(),
                    TextInput::make('label')
                        ->required($category->postes->count() == 0)
                        ->maxLength(255),
                ];
                // TODO : insert in 2nd place
                if ($category->postes->count() > 0) {
                    $select = Select::make('poste_id')
                        ->required()
                        ->label('Poste')
                        ->options($category->postes->pluck('label', 'id'));
                    array_splice($fields, 1, 0, [$select]);
                }

                return $fields;
            })
            ->label('Add note')
            ->labeledFrom('md')
            ->button()
            ->outlined()
            ->size(ActionSize::Small)
            ->icon('heroicon-m-plus-circle')
            ->mutateFormDataUsing(function (array $data, array $arguments): array {
                $data['user_id'] = auth()->id();
                $data['year'] = $this->year;
                $data['month'] = $this->month;
                $data['category_id'] = $arguments['category'];

                return $data;
            });
    }

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
