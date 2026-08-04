<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Category;

class CategoryController extends Controller
{
    public function show(Budget $budget)
    {
        return Category::where('budget_id', $budget->id)->get();
    }
}
