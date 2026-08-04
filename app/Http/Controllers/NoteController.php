<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Note;

class NoteController extends Controller
{
    public function show(Budget $budget)
    {
        return Note::whereRelation('category', 'budget_id', $budget->id)->get();
    }
}
