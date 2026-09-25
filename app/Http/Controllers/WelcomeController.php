<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Inertia\Inertia;

class WelcomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(#[CurrentUser] ?User $user = null)
    {
        return Inertia::render('Welcome', [
            'budgets' => $user?->budgets()->get() ?? [],
        ]);
    }
}
