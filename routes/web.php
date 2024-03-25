<?php

use App\Livewire\Charts;
use App\Livewire\Dashboard;
use App\Livewire\Settings;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::view('/', 'welcome');

Route::get('budget', Dashboard::class)
    ->middleware(['auth', 'verified'])
    ->name('budget');

Route::get('settings', Settings::class)
    ->middleware(['auth', 'verified'])
    ->name('settings');

Route::get('stats', Charts::class)
    ->middleware(['auth', 'verified'])
    ->name('stats');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
