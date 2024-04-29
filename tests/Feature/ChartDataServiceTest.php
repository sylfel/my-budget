<?php

use App\Service\ChartDataService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

it('can resolve ChartDataService', function () {
    $chartDateService = $this->app->make(ChartDataService::class);
    expect($chartDateService)->toBeObject();
});

describe('Last Months data', function () {
    it('has getChartLastMonths function', function () {
        $chartDateService = $this->app->make(ChartDataService::class);
        expect($chartDateService)->toHaveMethods(['getChartLastMonths']);
    });

    it('get data for two last months', function () {
        $this->seed();

        $chartDateService = $this->app->make(ChartDataService::class);
        $dt = Carbon::createFromDate(2024, 4, 1); // it's a joke
        $data = $chartDateService->getChartLastMonths($dt, 2);
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
        expect($dataset['data'])->toMatchArray([45, 50]);
    });
});
