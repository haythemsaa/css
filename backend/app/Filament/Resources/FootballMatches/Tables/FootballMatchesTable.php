<?php

namespace App\Filament\Resources\FootballMatches\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class FootballMatchesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('opponent')
                    ->searchable(),
                TextColumn::make('competition')
                    ->searchable(),
                TextColumn::make('stadium')
                    ->searchable(),
                TextColumn::make('match_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('kick_off_time')
                    ->time()
                    ->sortable(),
                TextColumn::make('home_away')
                    ->searchable(),
                TextColumn::make('css_score')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('opponent_score')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->searchable(),
                TextColumn::make('attendance')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('referee')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
