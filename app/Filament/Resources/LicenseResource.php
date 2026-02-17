<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LicenseResource\Pages;
use App\Models\License;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class LicenseResource extends Resource
{
    protected static ?string $model = License::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-key';

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('license_key')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->default(fn () => 'ZMIN-'.strtoupper(Str::random(4).'-'.Str::random(4).'-'.Str::random(4)))
                    ->maxLength(255)
                    ->dehydrateStateUsing(fn (?string $state) => $state && ! str_starts_with($state, 'ZMIN-') ? 'ZMIN-'.$state : $state)
                    ->suffixAction(
                        Actions\Action::make('generate')
                            ->icon('heroicon-o-arrow-path')
                            ->action(fn (\Filament\Schemas\Components\Utilities\Set $set) => $set('license_key', 'ZMIN-'.strtoupper(Str::random(4).'-'.Str::random(4).'-'.Str::random(4))))
                    ),
                Forms\Components\Select::make('license_type')
                    ->options([
                        'standard' => 'Standard',
                        'premium' => 'Premium',
                        'enterprise' => 'Enterprise',
                    ])
                    ->native(false)
                    ->required()
                    ->default('standard'),
                Forms\Components\Select::make('plan_id')
                    ->relationship('plan', 'name')
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('max_terminals')
                    ->numeric()
                    ->minValue(1)
                    ->helperText('Leave empty to use the plan default.'),
                Forms\Components\TextInput::make('business_name')
                    ->maxLength(255),
                Forms\Components\DatePicker::make('expiry_date'),
                Forms\Components\Toggle::make('is_active')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('license_key')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('business_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('license_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'enterprise' => 'success',
                        'premium' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('plan.name')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('expiry_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_active'),
                Tables\Columns\TextColumn::make('last_heartbeat_at')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Never'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('license_type')
                    ->options([
                        'standard' => 'Standard',
                        'premium' => 'Premium',
                        'enterprise' => 'Enterprise',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active'),
            ])
            ->actions([
                Actions\EditAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLicenses::route('/'),
            'create' => Pages\CreateLicense::route('/create'),
            'edit' => Pages\EditLicense::route('/{record}/edit'),
        ];
    }
}
