<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(#[CurrentUser] User $user)
    {
        return Inertia::render('Dashboard', [
            'budgets' => $user->budgets()->get(),
        ]);
    }
}
