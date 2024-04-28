<?php

namespace App\Livewire;

use App\Service\ChartDataService;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Livewire;

#[Layout('layouts.app')]
class Charts extends Component
{
    public int $nbMonths = 6;

    protected ChartDataService $chartDataService;

    public function boot(
        ChartDataService $chartDataService
    ) {
        $this->chartDataService = $chartDataService;
    }

    protected function getChartLastMonths()
    {
        $dt_base = today()->day(1);

        return $this->chartDataService->getChartLastMonths($dt_base, $this->nbMonths);
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
