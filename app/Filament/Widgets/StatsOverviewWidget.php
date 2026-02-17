<?php

namespace App\Filament\Widgets;

use App\Models\AppVersion;
use App\Models\License;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $totalLicenses = License::count();
        $activeLicenses = License::where('is_active', true)->count();
        $activePercentage = $totalLicenses > 0
            ? round(($activeLicenses / $totalLicenses) * 100)
            : 0;

        $expiredLicenses = License::where('is_active', true)
            ->where('expiry_date', '<', now())
            ->count();

        $enterpriseLicenses = License::where('license_type', 'enterprise')->count();

        $onlineLast24h = License::where('last_heartbeat_at', '>=', now()->subDay())->count();

        $activeAppVersions = AppVersion::where('is_active', true)->count();

        return [
            Stat::make('Total Licenses', $totalLicenses)
                ->color('primary'),

            Stat::make('Active Licenses', $activeLicenses)
                ->description("{$activePercentage}% of total")
                ->color('success'),

            Stat::make('Expired Licenses', $expiredLicenses)
                ->color($expiredLicenses > 0 ? 'danger' : 'success'),

            Stat::make('Enterprise', $enterpriseLicenses)
                ->color('warning'),

            Stat::make('Online (24h)', $onlineLast24h)
                ->color('success'),

            Stat::make('App Versions', $activeAppVersions)
                ->color('gray'),
        ];
    }
}
