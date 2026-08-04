<?php

use App\Http\Controllers\BudgetController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\PosteController;
use Illuminate\Support\Facades\Route;

// Route::inertia('/', 'Welcome')->name('home');

// Route::middleware(['auth', 'verified'])->group(function () {
//     Route::inertia('dashboard', 'Dashboard')->name('dashboard');
// });
Route::inertia('/', 'Dashboard')->name('home');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::resource('budget', BudgetController::class);

Route::get('/budget/{budget}/categories', [CategoryController::class, 'show']);
Route::get('/budget/{budget}/postes', [PosteController::class, 'show']);
Route::get('/budget/{budget}/notes', [NoteController::class, 'show']);

require __DIR__.'/settings.php';
