<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Note;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieTagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\Enums\ActionSize;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
class Dashboard extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    #[Url(keep: true)]
    public int $year;

    #[Url(keep: true)]
    public int $month;

    public array $users;

    public int $userCount;

    #[On('update-filters')]
    public function onUpdateFilters(string $date)
    {
        $newDate = Carbon::parse($date);
        $this->month = $newDate->month - 1;
        $this->year = $newDate->year;
    }

    public function addToCategoryAction(): Action
    {
        return CreateAction::make('addToCategory')
            ->model(Note::class)
            ->form(function (array $arguments) {
                $category = Category::with('postes')->find($arguments['category']);
                $fields = [
                    TextInput::make('price')
                        ->translateLabel()
                        ->numeric()
                        ->inputMode('decimal')
                        ->required(),
                    TextInput::make('label')
                        ->translateLabel()
                        ->required($category->postes->count() == 0)
                        ->maxLength(255),
                    SpatieTagsInput::make('tags'),
                    Select::make('user_id')
                        ->translateLabel()
                        ->relationship(name: 'user', titleAttribute: 'name')
                        ->default(Auth::id())
                        ->required(),
                ];
                if ($category->postes->count() > 0) {
                    $select = Select::make('poste_id')
                        ->translateLabel()
                        ->label('Post')
                        ->required()
                        ->options($category->postes->pluck('label', 'id'));
                    array_splice($fields, 1, 0, [$select]);
                }

                return $fields;
            })
            ->translateLabel()
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
                        ->translateLabel()
                        ->numeric()
                        ->inputMode('decimal')
                        ->required(),
                    TextInput::make('label')
                        ->translateLabel()
                        ->required($category->postes->count() == 0)
                        ->maxLength(255),
                    SpatieTagsInput::make('tags'),
                    Select::make('user_id')
                        ->translateLabel()
                        ->relationship(name: 'user', titleAttribute: 'name')
                        ->required(),
                ];
                if ($category->postes->count() > 0) {
                    $select = Select::make('poste_id')
                        ->required()
                        ->label('Post')
                        ->translateLabel()
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

    public function initMonthAction(): Action
    {
        return Action::make('initMonth')
            ->label('Initialize month')
            ->translateLabel()
            ->requiresConfirmation()
            ->modalDescription('Les catégories récurrentes vont être initialisées/remplacées par celle du mois précédente. Êtes vous sûr de vouloir faire ceci ?')
            ->icon('heroicon-m-arrow-path-rounded-square')
            ->modalIcon('heroicon-m-arrow-path-rounded-square')
            ->action(function () {
                $prevMonth = $this->month == 0 ? 11 : $this->month - 1;
                $prevYear = $this->year - ($this->month == 0 ? 1 : 0);

                $categories = Category::where('recurrent', true)->pluck('id');

                DB::table('notes')
                    ->where('month', $this->month)
                    ->where('year', $this->year)
                    ->whereIn('category_id', $categories)
                    ->delete();

                DB::table('notes')->insertUsing(
                    [
                        'label',
                        'price',
                        'month',
                        'year',
                        'category_id',
                        'poste_id',
                        'user_id',
                    ],
                    DB::table('notes')->select(
                        'label',
                        'price',
                        DB::raw($this->month),
                        DB::raw($this->year),
                        'category_id',
                        'poste_id',
                        'user_id'
                    )->where('month', $prevMonth)
                        ->where('year', $prevYear)
                        ->whereIn('category_id', $categories)
                );
            });
    }

    public function clearMonthAction(): Action
    {
        return Action::make('clearMonth')
            ->label('Clear month')
            ->translateLabel()
            ->requiresConfirmation()
            ->modalDescription('Toutes les notes du mois vont être supprimés, êtes-vous sûr ?')
            ->icon('heroicon-o-trash')
            ->modalIcon('heroicon-o-trash')
            ->color('danger')
            ->action(function () {
                DB::table('notes')
                    ->where('month', $this->month)
                    ->where('year', $this->year)
                    ->delete();
            });
    }

    public function groupedAction(): ActionGroup
    {
        return ActionGroup::make([
            $this->initMonthAction(),
            $this->clearMonthAction(),
        ])
            ->icon('heroicon-o-cog-6-tooth');
    }

    public function filtersAction()
    {
        return Action::make('filters')
            ->icon('heroicon-o-adjustments-horizontal')
            ->translateLabel()
            ->iconButton()
            ->extraAttributes([
                'class' => 'ml-auto',
            ])
            ->fillForm(fn (): array => [
                'userId' => $this->users,
            ])
            ->form([
                CheckboxList::make('userId')
                    ->label('Choose users')
                    ->translateLabel()
                    ->options(User::query()->pluck('name', 'id'))
                    ->columns(3)
                    ->bulkToggleable(),
            ])
            ->action(function (array $data): void {
                $this->users = $data['userId'];
            });
    }

    public function mount()
    {
        $this->users = User::query()->pluck('id')->toArray();
        $this->userCount = count($this->users);
        $this->year = $this->year ?? now()->year;
        $this->month = $this->month ?? now()->month - 1;
    }

    private function addCommonCondition(Builder|Relation $query)
    {
        $query->where('notes.year', $this->year)
            ->where('notes.month', $this->month);
        if (count($this->users) != $this->userCount) {
            $query->whereIn('notes.user_id', $this->users);
        }

        return $query;
    }

    public function render()
    {
        $categories = Category::withSum(['notes' => function (Builder $query) {
            $this->addCommonCondition($query);
        }], 'price')
            ->orderBy('label')
            ->with(['postes' => function (HasMany $query) {
                $query->withSum(['notes' => function (Builder $query) {
                    $this->addCommonCondition($query);
                }], 'price')
                    ->withCount(['notes' => function (Builder $query) {
                        $this->addCommonCondition($query);
                    }])
                    ->orderBy('label')
                    ->with(['notes' => function (HasMany $query) {
                        $this->addCommonCondition($query)
                            ->orderBy('created_at', 'desc');
                    }]);
            }])
            ->with(['notes' => function (HasMany $query) {
                $this->addCommonCondition($query)
                    ->whereNull('notes.poste_id')
                    ->orderBy('created_at', 'desc');
            }, 'notes.poste'])
            ->get();

        $summaries = collect(['Extra' => 0]);
        $total = 0;

        $categories->each(function (Category $category) use (&$summaries, &$total) {
            $montant = $category->notes_sum_price * ($category->credit ? 1 : -1);
            if ($category->extra) {
                $summaries->put('Extra', $summaries->get('Extra', 0) + $montant);
            } else {
                $summaries->put($category->label, $montant);
            }
            $total = $total + $montant;
        });

        $summaries = $summaries->sortKeys();

        return view('livewire.dashboard', compact('categories', 'total', 'summaries'));
    }
}
