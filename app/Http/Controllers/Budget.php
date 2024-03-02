<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Note;
use Carbon\Translator;

class Budget extends Controller
{
    public function index()
    {
        $years = Note::select('year')->distinct()->pluck('year', 'year')->union([now()->year => now()->year]);
        $months = Translator::get('fr')->getMessages('fr')['months'];

        //$categories = Category::withSum('notes', 'price')->with('notes.poste')->get();

        return view('budget', compact('years', 'months'));
    }
}
