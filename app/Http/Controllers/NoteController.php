<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNoteRequest;
use App\Http\Requests\UpdateNoteRequest;
use App\Models\Budget;
use App\Models\Note;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function show(Budget $budget)
    {
        return Note::whereRelation('category', 'budget_id', $budget->id)->get();
    }

    public function store(StoreNoteRequest $request, Budget $budget)
    {
        $validated = $request->validated();
        $validated['user_id'] = $request->user()->id;

        Note::create($validated);

        return to_route('budget', ['budget' => $budget]);
    }

    public function remove(Budget $budget, Note $note)
    {
        $note->delete();

        return to_route('budget', ['budget' => $budget]);
    }

    public function update(UpdateNoteRequest $request, Budget $budget, Note $note)
    {
        $validated = $request->validated();
        $note->update($validated);

        return to_route('budget', ['budget' => $budget]);
    }
}
