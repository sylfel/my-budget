<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Note;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Charts extends Component
{
    public function render()
    {
        $nbMonth = 5;
        // Calcul X derniers mois
        $dt_base = today()->day(1);
        $range = collect()->range($nbMonth, 0)->map(fn ($i) => $dt_base->copy()->subMonths($i));

        // Calcul libellé
        $labels = $range->map(fn ($dt) => $dt->isoFormat('MMM YYYY'));

        // Récupération données

        // Query
        $queryNotes = Note::select('price', 'category_id')
            ->addSelect(DB::raw("concat(notes.year, lpad(notes.month + 1, 2,'0')) as period"))
            ->whereBetween(
                DB::raw("cast(concat(notes.year,'-', notes.month + 1,'-1') as date)"),
                [DB::raw("add_months('{$dt_base->toDateString()}', -$nbMonth)"), "{$dt_base->toDateString()}"]
            );

        $query = Category::select(DB::raw("IF(categories.extra, 'Extra', categories.label) as _label"))
            ->addSelect(DB::raw("IF(credit, 'Credit','Debit') as _stack"))
            ->addSelect(DB::raw('sum(all_notes.price) / 100 as _data'))
            ->addSelect(DB::raw("$nbMonth - PERIOD_DIFF('{$dt_base->format('Ym')}',period) as _idx_data"))
            ->joinSub($queryNotes, 'all_notes', function (JoinClause $join) {
                $join->on('categories.id', '=', 'all_notes.category_id');
            })
            ->groupBy('_label', '_stack', '_idx_data');

        $queryResult = $query->get();

        $datasets1 = $queryResult->reduce(function ($carry, $record) use ($nbMonth) {
            $item = $carry->get($record->_label, collect([
                'label' => $record->_label,
                'stack' => $record->_stack,
                'data' => array_fill(0, $nbMonth + 1, 0),
            ]));
            $data = $item->get('data');
            $data[$record->_idx_data] = $record->_data;
            $item->put('data', $data);

            return $carry->put($record->_label, $item);
        }, collect())
            ->values();

        return view('livewire.charts', compact('labels', 'datasets1'));
    }
}
