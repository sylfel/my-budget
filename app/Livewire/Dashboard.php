<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Note;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\Enums\ActionSize;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
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
                    Select::make('user_id')
                        ->relationship(name: 'user', titleAttribute: 'name')
                        ->default(Auth::id())
                        ->required(),
                ];
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

    public function editNoteAction(): Action
    {
        return EditAction::make('editNote')
            ->record(fn (array $arguments) => Note::find($arguments['note']))
            ->form(function (array $arguments, Note $record) {
                $category = Category::with('postes')->find($record->category_id);
                $fields = [
                    TextInput::make('price')
                        ->numeric()
                        ->inputMode('decimal')
                        ->required(),
                    TextInput::make('label')
                        ->required($category->postes->count() == 0)
                        ->maxLength(255),
                    Select::make('user_id')
                        ->relationship(name: 'user', titleAttribute: 'name')
                        ->required(),
                ];
                if ($category->postes->count() > 0) {
                    $select = Select::make('poste_id')
                        ->required()
                        ->label('Poste')
                        ->options($category->postes->pluck('label', 'id'));
                    array_splice($fields, 1, 0, [$select]);
                }

                return $fields;
            })
            ->icon('heroicon-m-pencil-square')
            ->iconButton()
            ->modalFooterActions(fn (Note $record, Action $action) => [
                $action->getModalSubmitAction(),
                $action->getModalCancelAction(),
                DeleteAction::make()
                    ->requiresConfirmation()
                    ->record($record)
                    ->cancelParentActions()
                    ->extraAttributes([
                        'class' => 'ms-auto',
                    ]),
            ]);
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

        $total = $categories->sum(function (Category $category) {
            return $category->notes_sum_price * ($category->credit ? 1 : -1);
        });

        return view('livewire.dashboard', compact('categories', 'total'));
    }
}
