<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Category;
use Illuminate\Support\Facades\Gate;

class CategoryController extends Controller
{
    public function show(Budget $budget)
    {
        Gate::authorize('view', $budget);
        
        return Category::where('budget_id', $budget->id)->get();
    }
}
