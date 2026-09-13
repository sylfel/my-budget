<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateNoteRequest;
use App\Models\Budget;
use App\Models\Note;

class NoteController extends Controller
{
    public function show(Budget $budget)
    {
        return Note::whereRelation('category', 'budget_id', $budget->id)->get();
    }

    public function remove(Budget $budget, Note $note)
    {
        $note->delete();

        return response()->noContent();
    }

    public function update(UpdateNoteRequest $request, Budget $budget, Note $note)
    {
        $validated = $request->validated();
        $note->update($validated);

        return response()->json($note);
    }
}
