<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Note;
use App\Models\Poste;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Tags\Tag;

class BudgetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return [];
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Budget $budget)
    {
        $users = User::all()->select('id', 'name');
        // $tags = Tag::all();
        $categories = Category::where('budget_id', $budget->id)->get();
        $postes = Poste::whereRelation('category', 'budget_id', $budget->id)->get();
        $notes = Note::whereRelation('category', 'budget_id', $budget->id)->get();

        return [
            'categories' => $categories,
            'notes' => $notes,
            'postes' => $postes,
            'users' => $users,
            // 'tags' => $tags
        ];
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Budget $budget)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Budget $budget)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Budget $budget)
    {
        //
    }
}
