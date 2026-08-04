<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Poste;

class PosteController extends Controller
{
    public function show(Budget $budget)
    {
        return Poste::whereRelation('category', 'budget_id', $budget->id)->get();
    }
}
