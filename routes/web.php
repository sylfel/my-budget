<?php

use App\Http\Controllers\BudgetController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\PosteController;
use Illuminate\Support\Facades\Route;

// Route::inertia('/', 'Welcome')->name('home');

//     Route::inertia('dashboard', 'Dashboard')->name('dashboard');
Route::inertia('/home', 'Dashboard')->name('home');
Route::middleware(['auth', 'verified'])->group(function () {
    // Route::inertia('/budget/{budgetId}', 'Budget')->name('budget');

    Route::get('/budget/{budget}', [BudgetController::class, 'show'])->name('budget');

});

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Route::resource('budget', BudgetController::class);

Route::get('/budget/{budget}/categories', [CategoryController::class, 'show']);
Route::get('/budget/{budget}/postes', [PosteController::class, 'show']);
Route::get('/budget/{budget}/notes', [NoteController::class, 'show']);
Route::post('/budget/{budget}/notes', [NoteController::class, 'store']);
Route::delete('/budget/{budget}/notes/{note}', [NoteController::class, 'remove']);
Route::patch('/budget/{budget}/notes/{note}', [NoteController::class, 'update']);

require __DIR__.'/settings.php';
