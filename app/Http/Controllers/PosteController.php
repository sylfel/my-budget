<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Poste;
use Illuminate\Support\Facades\Gate;

class PosteController extends Controller
{
    public function show(Budget $budget)
    {
        Gate::authorize('view', $budget);
        
        return Poste::whereRelation('category', 'budget_id', $budget->id)->get();
    }
}
