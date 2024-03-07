<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Note;
use Filament\Notifications\Notification;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use LivewireUI\Modal\ModalComponent;

class EditNote extends ModalComponent
{
    // External params
    public Category $category;

    public int $year;

    public int $month;

    // Forms Params
    #[Validate('required|decimal:0,2')]
    public float $price;

    #[Validate()]
    public ?int $poste = null;

    #[Validate()]
    public ?string $label = null;

    public function rules()
    {
        return [
            'price' => 'required|decimal:0,2',
            'poste' => Rule::requiredIf($this->category->postes->isNotEmpty()),
            'label' => Rule::requiredIf($this->category->postes->isEmpty()),
        ];
    }

    public function render()
    {
        return view('livewire.edit-note');
    }

    public function save()
    {
        $this->validate();

        $note = new Note();
        $note->year = $this->year;
        $note->month = $this->month;
        $note->category_id = $this->category->id;
        $note->price = $this->price;

        $note->poste_id = $this->poste;
        $note->label = $this->label;
        $note->user_id = Auth()->id();

        $note->save();
        $this->closeModalWithEvents([Dashboard::class => 'noteCreated']);

        Notification::make()
            ->title('Note ajoutée')
            ->success()
            ->send();
    }

    public static function closeModalOnClickAway(): bool
    {
        return false;
    }
}
