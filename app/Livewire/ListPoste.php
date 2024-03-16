<?php

namespace App\Livewire;

use App\Models\Poste;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class ListPoste extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Poste::query())
            ->columns([
                TextColumn::make('label'),
                TextColumn::make('category.label'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->form([
                        TextInput::make('label')
                            ->translateLabel()
                            ->required(),
                        Select::make('category_id')
                            ->translateLabel()
                            ->relationship(name: 'category', titleAttribute: 'label')
                            ->required(),
                    ]),
            ])
            ->actions([
                EditAction::make()
                    ->iconButton()
                    ->form([
                        TextInput::make('label')
                            ->translateLabel()
                            ->required(),
                        Select::make('category_id')
                            ->translateLabel()
                            ->relationship(name: 'category', titleAttribute: 'label')
                            ->required(),
                    ]),
                DeleteAction::make()
                    ->iconButton(),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->relationship('category', 'label')
                    ->searchable()
                    ->preload(),
            ], layout: FiltersLayout::AboveContent)
            ->heading('Postes')
            ->description('Gestion des postes. Un poste est lié à une catégorie.')
            ->defaultPaginationPageOption(25)
            ->defaultGroup(Group::make('category.label')
                ->label('Catégorie'));
    }

    public function render(): View
    {
        return view('livewire.list-poste');
    }
}
