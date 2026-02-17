<?php

namespace App\Filament\Widgets;

use App\Models\AppVersion;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestAppVersionsTable extends TableWidget
{
    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Latest App Versions')
            ->query(AppVersion::query()->latest()->limit(5))
            ->paginated(false)
            ->columns([
                TextColumn::make('version')
                    ->label('Version'),

                TextColumn::make('platform')
                    ->label('Platform')
                    ->badge(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                TextColumn::make('released_at')
                    ->label('Released')
                    ->since(),
            ]);
    }
}
