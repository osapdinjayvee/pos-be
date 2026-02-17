<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TerminalResource\Pages;
use App\Models\Terminal;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class TerminalResource extends Resource
{
    protected static ?string $model = Terminal::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-device-tablet';

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Forms\Components\Select::make('license_id')
                    ->relationship('license', 'license_key')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('device_identifier')
                    ->required()
                    ->maxLength(255)
                    ->readOnly(fn (?Terminal $record) => $record !== null),
                Forms\Components\TextInput::make('device_name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('device_type')
                    ->maxLength(255),
                Forms\Components\Toggle::make('is_active')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('device_identifier')
                    ->searchable(),
                Tables\Columns\TextColumn::make('device_name')
                    ->searchable()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('license.license_key')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('device_type')
                    ->badge()
                    ->placeholder('—'),
                Tables\Columns\ToggleColumn::make('is_active'),
                Tables\Columns\TextColumn::make('activated_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_seen_at')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Never'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active'),
                Tables\Filters\SelectFilter::make('device_type')
                    ->options(fn () => Terminal::query()
                        ->whereNotNull('device_type')
                        ->distinct()
                        ->pluck('device_type', 'device_type')
                        ->toArray()),
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
            'index' => Pages\ListTerminals::route('/'),
            'create' => Pages\CreateTerminal::route('/create'),
            'edit' => Pages\EditTerminal::route('/{record}/edit'),
        ];
    }
}
