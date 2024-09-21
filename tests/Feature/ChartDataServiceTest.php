<?php

use App\Service\ChartDataService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

it('can resolve ChartDataService', function () {
    $chartDateService = $this->app->make(ChartDataService::class);
    expect($chartDateService)->toBeObject();
});

it('get only 1 month', function () {
    $chartDateService = $this->app->make(ChartDataService::class);

    $from = Carbon::createMidnightDate(2024, 4, 1);
    $data = $chartDateService->getMonths($from, 0);

    expect($data)->toHaveLength(1);
    $date = $data->get(0);
    expect($date)->toEqual($from);
});

it('get 2 next months', function () {
    $chartDateService = $this->app->make(ChartDataService::class);

    $from = Carbon::createMidnightDate(2024, 4, 1);
    $data = $chartDateService->getMonths($from, 2);

    expect($data)->toHaveLength(3);
    $date = $data->get(0);
    expect($date)->toEqual($from);
    $date = $data->get(1);
    expect($date)->toEqual($from->copy()->addMonths(1));
    $date = $data->get(2);
    expect($date)->toEqual($from->copy()->addMonths(2));
});

it('get 2 last months', function () {
    $chartDateService = $this->app->make(ChartDataService::class);

    $from = Carbon::createMidnightDate(2024, 4, 1);
    $data = $chartDateService->getMonths($from, -2);

    expect($data)->toHaveLength(3);
    $date = $data->get(0);
    expect($date)->toEqual($from->copy()->addMonths(-2));
    $date = $data->get(1);
    expect($date)->toEqual($from->copy()->addMonths(-1));
    $date = $data->get(2);
    expect($date)->toEqual($from);
});

it('get only 1 month label', function () {
    $chartDateService = $this->app->make(ChartDataService::class);

    $from = Carbon::createMidnightDate(2024, 4, 1);
    $data = $chartDateService->getLabels($from, 0);
    expect($data)->toHaveLength(1);
    expect($data)->toMatchArray(['avr. 2024']);
});

it('get 3 next months label', function () {
    $chartDateService = $this->app->make(ChartDataService::class);

    $from = Carbon::createMidnightDate(2024, 4, 1);
    $data = $chartDateService->getLabels($from, 2);
    expect($data)->toHaveLength(3);
    expect($data)->toMatchArray(['avr. 2024', 'mai 2024', 'juin 2024']);
});

it('get 3 last months label', function () {
    $chartDateService = $this->app->make(ChartDataService::class);

    $from = Carbon::createMidnightDate(2024, 4, 1);
    $data = $chartDateService->getLabels($from, -2);
    expect($data)->toHaveLength(3);
    expect($data)->toMatchArray(['févr. 2024', 'mars 2024', 'avr. 2024']);
});

describe('Last Months data', function () {
    it('has getChartLastMonths function', function () {
        $chartDateService = $this->app->make(ChartDataService::class);
        expect($chartDateService::class)->toHaveMethod(['getChartLastMonths']);
    });

    it('get data for two last months', function () {
        $this->seed();

        $chartDateService = $this->app->make(ChartDataService::class);
        $dt = Carbon::createMidnightDate(2024, 4, 1); // it's a joke
        $data = $chartDateService->getChartLastMonths($dt, 1);
        expect($data)->toBeArray();
        expect($data)->toHaveKeys(['labels', 'datasets', 'title', 'name']);

        $labels = $data['labels'];
        expect($labels)->toHaveLength(2);
        expect($labels)->toMatchArray(['mars 2024', 'avr. 2024']);

        $title = $data['title'];
        expect($title)->toBe('2 derniers mois');

        $name = $data['name'];
        expect($name)->toBe('lastMonths');

        $datasets = $data['datasets'];
        expect($datasets)->toBeInstanceOf(Collection::class);
        expect($datasets->count())->toBe(1);
        $dataset = $datasets->get(0);
        expect($dataset['label'])->toBe('Alimentation');
        expect($dataset['stack'])->toBe('Debit');
        expect($dataset['data'])->toHaveLength(2);
        expect($dataset['data'])->toMatchArray([0 => 45, 1 => 50]);
    });

    it('get data for 6 last months', function () {
        $this->seed();

        $chartDateService = $this->app->make(ChartDataService::class);
        $dt = Carbon::createMidnightDate(2024, 5, 1); // it's a joke
        $data = $chartDateService->getChartLastMonths($dt, 5);

        $labels = $data['labels'];
        expect($labels)->toHaveLength(6);
        expect($labels)->toMatchArray(['déc. 2023', 'janv. 2024', 'févr. 2024', 'mars 2024', 'avr. 2024', 'mai 2024']);

        $datasets = $data['datasets'];
        expect($datasets)->toBeInstanceOf(Collection::class);
        expect($datasets->count())->toBe(1);
        $dataset = $datasets->get(0);
        expect($dataset['data'])->toHaveLength(6);
        expect($dataset['data'])->toMatchArray([0 => 30, 1 => 35, 2 => 40, 3 => 45, 4 => 50, 5 => 0]);
    });

    it('get data for 3 last months', function () {
        $this->seed();

        $chartDateService = $this->app->make(ChartDataService::class);
        $dt = Carbon::createMidnightDate(2024, 4, 1); // it's a joke
        $data = $chartDateService->getChartLastMonths($dt, 2);

        $labels = $data['labels'];
        expect($labels)->toHaveLength(3);
        expect($labels)->toMatchArray(['févr. 2024', 'mars 2024', 'avr. 2024']);

        $datasets = $data['datasets'];
        expect($datasets)->toBeInstanceOf(Collection::class);
        expect($datasets->count())->toBe(1);
        $dataset = $datasets->get(0);
        expect($dataset['data'])->toHaveLength(3);
        expect($dataset['data'])->toMatchArray([0 => 40, 1 => 45, 2 => 50]);
    });
});
