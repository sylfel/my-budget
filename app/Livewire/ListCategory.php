<?php

namespace App\Livewire;

use App\Models\Category;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Guava\FilamentIconPicker\Forms\IconPicker;
use Guava\FilamentIconPicker\Tables\IconColumn;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class ListCategory extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Category::query()->withCount('postes'))
            ->columns([
                TextColumn::make('label')
                    ->grow(),
                TextColumn::make('postes_count')
                    ->numeric()
                    ->translateLabel()
                    ->alignment(Alignment::End)
                    ->visibleFrom('sm'),
                ToggleColumn::make('credit')
                    ->translateLabel()
                    ->visibleFrom('md'),
                ToggleColumn::make('extra')
                    ->translateLabel()
                    ->visibleFrom('md'),
                ToggleColumn::make('recurrent')
                    ->translateLabel()
                    ->visibleFrom('md'),
                IconColumn::make('icon'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->form([
                        TextInput::make('label')
                            ->translateLabel()
                            ->required(),
                        Toggle::make('credit')->translateLabel(),
                        Toggle::make('extra')->translateLabel(),
                        Toggle::make('reccurent')->translateLabel(),
                        IconPicker::make('icon')->translateLabel(),
                    ]),
            ])
            ->actions([
                EditAction::make()
                    ->iconButton()
                    ->form([
                        TextInput::make('label')
                            ->translateLabel()
                            ->required(),
                        Toggle::make('credit')->translateLabel(),
                        Toggle::make('extra')->translateLabel(),
                        Toggle::make('recurrent')->translateLabel(),
                        IconPicker::make('icon')->translateLabel()
                            ->columns([
                                'default' => 1,
                                'lg' => 5,
                                '2xl' => 7,
                            ])
                            ->sets(['heroicons', 'fontawesome-solid']),
                    ]),
                DeleteAction::make()
                    ->iconButton(),
            ])
            ->heading('Categories')
            ->description('Gestion des catégories générales')
            ->defaultSort('label', 'asc')
            ->defaultPaginationPageOption(25);
    }

    public function render(): View
    {
        return view('livewire.list-category');
    }
}
