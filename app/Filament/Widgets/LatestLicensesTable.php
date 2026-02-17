<?php

namespace App\Filament\Widgets;

use App\Models\License;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestLicensesTable extends TableWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Latest Licenses')
            ->query(License::query()->latest()->limit(5))
            ->paginated(false)
            ->columns([
                TextColumn::make('license_key')
                    ->label('License Key')
                    ->copyable(),

                TextColumn::make('business_name')
                    ->label('Business'),

                TextColumn::make('license_type')
                    ->label('Type')
                    ->badge(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                TextColumn::make('last_heartbeat_at')
                    ->label('Last Heartbeat')
                    ->since(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->since(),
            ]);
    }
}
