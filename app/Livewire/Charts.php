<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Note;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Livewire;

#[Layout('layouts.app')]
class Charts extends Component
{
    public int $nbMonths = 6;

    // TODO : découper et déporter dans une classe à part
    protected function getChartLastMonths()
    {
        // Calcul X derniers mois
        $dt_base = today()->day(1);

        // Calcul libellé
        $range = collect()->range($this->nbMonths, 0)->map(fn ($i) => $dt_base->copy()->subMonths($i));
        $labels = $range->map(fn ($dt) => $dt->isoFormat('MMM YYYY'));

        $name = 'lastMonths';

        // Création du titre : TODO : mettre en lang.json
        $title = $this->nbMonths.' derniers mois';

        // Récupération données

        // Query
        $queryNotes = Note::select('price', 'category_id')
            ->addSelect(DB::raw("concat(notes.year, lpad(notes.month + 1, 2,'0')) as period"))
            ->whereBetween(
                DB::raw("cast(concat(notes.year,'-', notes.month + 1,'-1') as date)"),
                [DB::raw("add_months('{$dt_base->toDateString()}', -$this->nbMonths)"), "{$dt_base->toDateString()}"]
            );

        $query = Category::select(DB::raw("IF(categories.extra, 'Extra', categories.label) as _label"))
            ->addSelect(DB::raw("IF(credit, 'Credit','Debit') as _stack"))
            ->addSelect(DB::raw('sum(all_notes.price) / 100 as _data'))
            ->addSelect(DB::raw("$this->nbMonths - PERIOD_DIFF('{$dt_base->format('Ym')}',period) as _idx_data"))
            ->joinSub($queryNotes, 'all_notes', function (JoinClause $join) {
                $join->on('categories.id', '=', 'all_notes.category_id');
            })
            ->groupBy('_label', '_stack', '_idx_data');

        $queryResult = $query->get();

        // Retraitement des données
        $datasets = $queryResult->reduce(function ($carry, $record) {
            $item = $carry->get($record->getAttribute('_label'), collect([
                'label' => $record->getAttribute('_label'),
                'stack' => $record->getAttribute('_stack'),
                'data' => array_fill(0, $this->nbMonths + 1, 0),
            ]));
            $data = $item->get('data');
            $data[$record->getAttribute('_idx_data')] = $record->getAttribute('_data');
            $item->put('data', $data);

            return $carry->put($record->getAttribute('_label'), $item);
        }, collect())
            ->values();

        return compact('labels', 'datasets', 'title', 'name');
    }

    public function render()
    {
        $charts = [$this->getChartLastMonths()];

        if (Livewire::isLivewireRequest()) {
            foreach ($charts as $chart) {
                $this->dispatch(sprintf('charts-%s-update', $chart['name']), ...$chart);
            }
        }

        return view('livewire.charts', compact('charts'));
    }
}
