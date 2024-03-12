<?php

namespace App\Livewire;

use App\Models\Category;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class ListCategory extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Category::query())
            ->columns([
                TextColumn::make('label'),
                ToggleColumn::make('credit'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->form([
                        TextInput::make('label')
                            ->required(),
                        Toggle::make('credit'),
                    ]),
            ])
            ->actions([
                EditAction::make()
                    ->iconButton()
                    ->form([
                        TextInput::make('label')
                            ->required(),
                        Toggle::make('credit'),
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
