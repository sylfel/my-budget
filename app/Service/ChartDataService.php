<?php

namespace App\Service;

use App\Models\Category;
use App\Models\Note;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ChartDataService
{
    /**
     * returns $nbMonths next or last month from $from
     * in ascending order
     *
     * getMonths('2024-04-01',2) return ['2024-04-01','2024-05-01','2024-06-01']
     * getMonths('2024-04-01',-2) return ['2024-02-01','2024-03-01','2024-04-01']
     */
    public function getMonths(Carbon $from, int $nbMonths)
    {
        $range = collect()->range(min(0, $nbMonths), max(0, $nbMonths));
        $dates = $range->map(fn ($i) => $from->copy()->addMonths($i));

        return $dates;
    }

    public function getLabels(Carbon $from, int $nbMonths, string $format = 'MMM YYYY')
    {
        $range = $this->getMonths($from, $nbMonths);

        return $range->map(fn ($dt) => $dt->isoFormat($format));
    }

    // TODO : découper et ajouter des tests
    public function getChartLastMonths(Carbon $dt_base, int $nbMonths)
    {
        // Calcul libellé
        $labels = $this->getLabels($dt_base, -$nbMonths);

        $name = 'lastMonths';

        // Création du titre : TODO : mettre en lang.json
        $title = $nbMonths + 1 .' derniers mois';

        // Récupération données

        // Query
        $queryNotes = Note::select('price', 'category_id')
            ->addSelect(DB::raw("concat(notes.year, lpad(notes.month + 1, 2,'0')) as period"))
            ->whereBetween(
                DB::raw("cast(concat(notes.year,'-', notes.month + 1,'-1') as date)"),
                [DB::raw("add_months('{$dt_base->toDateString()}', -$nbMonths)"), "{$dt_base->toDateString()}"]
            );

        $query = Category::select(DB::raw("IF(categories.extra, 'Extra', categories.label) as _label"))
            ->addSelect(DB::raw("IF(credit, 'Credit','Debit') as _stack"))
            ->addSelect(DB::raw('sum(all_notes.price) / 100 as _data'))
            ->addSelect(DB::raw("$nbMonths - PERIOD_DIFF('{$dt_base->format('Ym')}',period) as _idx_data"))
            ->joinSub($queryNotes, 'all_notes', function (JoinClause $join) {
                $join->on('categories.id', '=', 'all_notes.category_id');
            })
            ->groupBy('_label', '_stack', '_idx_data');

        $queryResult = $query->get();

        // Retraitement des données
        $datasets = $queryResult->reduce(function ($carry, $record) use ($nbMonths) {
            $item = $carry->get($record->getAttribute('_label'), collect([
                'label' => $record->getAttribute('_label'),
                'stack' => $record->getAttribute('_stack'),
                'data' => array_fill(0, $nbMonths + 1, 0),
            ]));
            $data = $item->get('data');
            $data[$record->getAttribute('_idx_data')] = $record->getAttribute('_data');
            $item->put('data', $data);

            return $carry->put($record->getAttribute('_label'), $item);
        }, collect())
            ->values();

        return compact('labels', 'datasets', 'title', 'name');
    }

    public function getChartLastMonthsByUser(Carbon $dt_base, int $nbMonths)
    {
        // Calcul libellé
        $labels = $this->getLabels($dt_base, -$nbMonths);

        $name = 'lastMonthsByUser';

        // Création du titre : TODO : mettre en lang.json
        $title = 'Répartition par utilisateur sur les '.($nbMonths + 1).' derniers mois';

        // Récupération données

        // Query
        $query = Note::select(DB::raw('sum(price) / 100 as _data'), 'credit', 'users.name as _label')
            ->addSelect(DB::raw("concat(notes.year, lpad(notes.month + 1, 2,'0')) as _period"))
            ->addSelect(DB::raw("$nbMonths - PERIOD_DIFF('{$dt_base->format('Ym')}', concat(notes.year, lpad(notes.month + 1, 2,'0'))) as _idx_data"))
            ->join('users', 'users.id', '=', 'notes.user_id')
            ->join('categories', 'categories.id', '=', 'notes.category_id')
            ->whereBetween(
                DB::raw("cast(concat(notes.year,'-', notes.month + 1,'-1') as date)"),
                [DB::raw("add_months('{$dt_base->toDateString()}', -$nbMonths)"), "{$dt_base->toDateString()}"]
            )
            ->groupBy('_label', 'credit', '_period', '_idx_data');

        $queryResult = $query->get();

        // Retraitement des données
        $datasets = $queryResult->reduce(function ($carry, $record) use ($nbMonths) {
            $key = $record->getAttribute('_label').'-'.$record->getAttribute('credit');
            $item = $carry->get($key, collect([
                'label' => $record->getAttribute('_label').($record->getAttribute('credit') ? ' (revenue)' : ' (dépense)'),
                'data' => array_fill(0, $nbMonths + 1, null),
                'type' => $record->getAttribute('credit') ? 'bar' : 'line',
                'order' => $record->getAttribute('credit') ? 1 : 0,
            ]));
            $data = $item->get('data');
            $data[$record->getAttribute('_idx_data')] = $record->getAttribute('_data');
            $item->put('data', $data);

            return $carry->put($key, $item);
        }, collect())
            ->values();

        return compact('labels', 'datasets', 'title', 'name');
    }

    public function getTableByCategory(Carbon $dt_base, int $nbMonths)
    {
        // Calcul libellé
        $labels = $this->getLabels($dt_base, -$nbMonths, 'YYYYMM');
        $initialPeriod = $labels->reduce(fn ($carry, $label) => $carry->put($label, 0), collect());
        $labels = $this->getLabels($dt_base, -$nbMonths);

        $name = 'evolutions';

        $query = Category::select(DB::raw('sum(price) / 100 as price'))
            ->addSelect('categories.label')
            ->addSelect(DB::raw("concat(notes.year, lpad(notes.month + 1, 2,'0')) as _period"))
            ->leftJoin('notes', 'categories.id', '=', 'notes.category_id')
            ->whereBetween(
                DB::raw("cast(concat(notes.year,'-', notes.month + 1,'-1') as date)"),
                [DB::raw("add_months('{$dt_base->toDateString()}', -$nbMonths)"), "{$dt_base->toDateString()}"]
            )
            ->groupBy('categories.label', '_period');

        $queryResult = $query->get();

        $datasets = $queryResult->reduce(function ($carry, $record) use ($initialPeriod) {
            $key = $record->getAttribute('label');
            $category = $carry->get($key, collect($initialPeriod));
            $category->put($record->getAttribute('_period'), $record->getAttribute('price'));

            return $carry->put($key, $category);
        }, collect());

        return compact('labels', 'name', 'datasets');
    }
}
