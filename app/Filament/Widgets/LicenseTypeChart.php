<?php

namespace App\Filament\Widgets;

use App\Models\License;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;

class LicenseTypeChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Licenses by Type';

    protected int | string | array $columnSpan = 1;

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $standard = License::where('license_type', 'standard')->count();
        $premium = License::where('license_type', 'premium')->count();
        $enterprise = License::where('license_type', 'enterprise')->count();

        return [
            'datasets' => [
                [
                    'data' => [$standard, $premium, $enterprise],
                    'backgroundColor' => [
                        'rgb(156, 163, 175)', // gray
                        'rgb(245, 158, 11)',  // amber
                        'rgb(34, 197, 94)',   // green
                    ],
                ],
            ],
            'labels' => ['Standard', 'Premium', 'Enterprise'],
        ];
    }

    protected function getOptions(): array | RawJs | null
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
            ],
        ];
    }
}
