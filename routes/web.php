<?php

use App\Http\Controllers\BudgetController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\PosteController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [WelcomeController::class, 'index'])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/budget/{budget}', [BudgetController::class, 'show'])->name('budget');
});

Route::get('/budget/{budget}/categories', [CategoryController::class, 'show']);
Route::get('/budget/{budget}/postes', [PosteController::class, 'show']);
Route::get('/budget/{budget}/notes', [NoteController::class, 'show']);
Route::post('/budget/{budget}/notes', [NoteController::class, 'store']);
Route::delete('/budget/{budget}/notes/{note}', [NoteController::class, 'remove']);
Route::patch('/budget/{budget}/notes/{note}', [NoteController::class, 'update']);

require __DIR__.'/settings.php';
