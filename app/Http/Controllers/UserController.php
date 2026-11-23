<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    public function show(Budget $budget)
    {
        Gate::authorize('view', $budget);

        return User::select('users.id', 'users.name')
            ->whereIn('users.id', function ($query) use ($budget) {
                $query->select('budget_user.user_id')
                    ->from('budget_user')
                    ->where('budget_user.budget_id', $budget->id)
                    ->union(function ($query) use ($budget) {
                        $query->select('notes.user_id')
                            ->from('notes')
                            ->join('categories', 'notes.category_id', '=', 'categories.id')
                            ->where('categories.budget_id', $budget->id);
                    });
            })
            ->get();
    }
}
