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
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieTagsInput;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\IconPosition;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * @property-read Form $form
 */
#[Layout('layouts.app')]
class Dashboard extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    #[Url(keep: true)]
    public int $year;

    #[Url(keep: true)]
    public int $month;

    public int $userCount;

    public ?array $filters = [];

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
                return $this->getNoteForm($arguments['category']);
            })
            ->translateLabel()
            ->labeledFrom('md')
            ->button()
            ->outlined()
            ->size(ActionSize::Small)
            ->icon('heroicon-m-plus-circle')
            ->mutateFormDataUsing(function (array $data): array {
                $data['user_id'] = $data['user_id'] ?? auth()->id();
                $data['year'] = $this->year;
                $data['month'] = $this->month;

                return $data;
            })
            ->createAnother(false)
            ->modalFooterActions(fn (Action $action) => [
                $action->getModalCancelAction()
                    ->extraAttributes([
                        'class' => 'ms-auto',
                    ]),
                $action->getModalSubmitAction(),
            ]);
    }

    public function getNoteForm(int $category_id): array
    {
        $categories = Category::with('postes')->get();
        $category = $categories->find($category_id);

        return [
            Tabs::make('Tabs')
                ->tabs([
                    Tabs\Tab::make('Général')->schema([
                        Grid::make()
                            ->schema([
                                Select::make('category_id')
                                    ->label('Category')
                                    ->translateLabel()
                                    ->default($category_id)
                                    ->options($categories->pluck('label', 'id')->sort())
                                    ->required()
                                    ->live(),
                                Select::make('poste_id')
                                    ->required(fn (Get $get): bool => $categories->find($get('category_id'))->postes->count() > 0)
                                    ->disabled(fn (Get $get): bool => $categories->find($get('category_id'))->postes->count() <= 0)
                                    ->label('Post')
                                    ->translateLabel()
                                    ->options(fn (Get $get) => $categories->find($get('category_id'))->postes->pluck('label', 'id')->sort()),
                                TextInput::make('price')
                                    ->translateLabel()
                                    ->numeric()
                                    ->inputMode('decimal')
                                    ->required(),
                                TextInput::make('label')
                                    ->translateLabel()
                                    ->required($category->postes->count() == 0)
                                    ->maxLength(255),
                            ]),
                    ]),
                    Tabs\Tab::make('Options')->schema([
                        SpatieTagsInput::make('tags'),
                        Select::make('user_id')
                            ->translateLabel()
                            ->relationship(name: 'user', titleAttribute: 'name')
                            ->default(Auth::id())
                            ->required(),
                    ]),
                ]),
        ];
    }

    public function editNoteAction(): Action
    {
        return EditAction::make('editNote')
            ->record(fn (array $arguments) => Note::find($arguments['note']))
            ->form(function (Note $record) {
                return $this->getNoteForm($record->category_id);
            })
            ->icon('heroicon-m-pencil-square')
            ->iconButton()
            ->mutateFormDataUsing(function (array $data): array {
                $data['poste_id'] = Arr::get($data, 'poste_id');

                return $data;
            })
            ->modalFooterActions(fn (Note $record, Action $action) => [
                DeleteAction::make()
                    ->requiresConfirmation()
                    ->record($record)
                    ->cancelParentActions(),
                $action->getModalCancelAction()
                    ->extraAttributes([
                        'class' => 'ms-auto',
                    ]),
                $action->getModalSubmitAction(),
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
        $currentDate = Carbon::createMidnightDate($this->year, $this->month + 1, 1)->locale('fr');

        return ActionGroup::make([
            $this->initMonthAction(),
            $this->clearMonthAction(),
        ])
            ->label(ucfirst($currentDate->isoFormat('MMMM Y')))
            ->outlined()
            ->size(ActionSize::Small)
            ->color('primary')
            ->button()
            ->icon('heroicon-o-cog-6-tooth')
            ->iconPosition(IconPosition::After)
            ->extraAttributes([
                'class' => 'mx-4',
            ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        CheckboxList::make('userId')
                            ->label('')
                            ->searchable(false)
                            ->options(User::query()->pluck('name', 'id'))
                            ->columns(2),
                    ])
                    ->compact(),
            ])
            ->statePath('filters')
            ->live();
    }

    public function mount()
    {
        $users = User::query()->pluck('id')->toArray();
        $this->userCount = count($users);
        $this->form->fill([
            'userId' => $users,
        ]);
        $this->year = $this->year ?? now()->year;
        $this->month = $this->month ?? now()->month - 1;
    }

    private function addCommonCondition(Builder|Relation $query)
    {
        $query->where('notes.year', $this->year)
            ->where('notes.month', $this->month);
        if (count($this->filters['userId']) != $this->userCount) {
            $query->whereIn('notes.user_id', $this->filters['userId']);
        }

        return $query;
    }

    public function render()
    {
        $categories = Category::withSum(['notes' => function (Builder $query) {
            $this->addCommonCondition($query);
        }], 'price')
            ->orderBy('label')
            // @phpstan-ignore argument.type
            ->with(['postes' => function (HasMany $query) {
                $query->withSum(['notes' => function (Builder $query) {
                    $this->addCommonCondition($query);
                }], 'price')
                    ->withCount(['notes' => function (Builder $query) {
                        $this->addCommonCondition($query);
                    }])
                    ->orderBy('label')
                    // @phpstan-ignore argument.type
                    ->with(['notes' => function (HasMany $query) {
                        $this->addCommonCondition($query)
                            ->orderBy('created_at', 'desc');
                    }]);
            }, 'notes' => function (HasMany $query) {
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
