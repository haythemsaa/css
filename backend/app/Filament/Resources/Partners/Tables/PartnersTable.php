<?php

namespace App\Filament\Resources\Partners\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class PartnersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo')
                    ->label('Logo')
                    ->circular()
                    ->size(40),

                TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('category.name_fr')
                    ->label('Catégorie')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('city')
                    ->label('Ville')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('reduction_value_premium')
                    ->label('Réduc. Premium')
                    ->formatStateUsing(fn ($record) =>
                        $record->reduction_type === 'percentage'
                            ? "-{$record->reduction_value_premium}%"
                            : "-{$record->reduction_value_premium} TND"
                    )
                    ->badge()
                    ->color('success')
                    ->sortable(),

                TextColumn::make('reduction_value_socios')
                    ->label('Réduc. Socios')
                    ->formatStateUsing(fn ($record) =>
                        $record->reduction_type === 'percentage'
                            ? "-{$record->reduction_value_socios}%"
                            : "-{$record->reduction_value_socios} TND"
                    )
                    ->badge()
                    ->color('warning')
                    ->sortable(),

                BadgeColumn::make('status')
                    ->label('Statut')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'pending',
                        'danger' => 'inactive',
                        'secondary' => 'suspended',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Actif',
                        'inactive' => 'Inactif',
                        'pending' => 'En attente',
                        'suspended' => 'Suspendu',
                        default => $state,
                    })
                    ->sortable(),

                IconColumn::make('is_online')
                    ->label('En ligne')
                    ->boolean()
                    ->toggleable(),

                TextColumn::make('featured_order')
                    ->label('Vedette')
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'warning' : 'gray')
                    ->formatStateUsing(fn ($state) => $state > 0 ? "★ #{$state}" : '-')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('phone')
                    ->label('Téléphone')
                    ->icon('heroicon-o-phone')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('email')
                    ->label('Email')
                    ->icon('heroicon-o-envelope')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('views_count')
                    ->label('Vues')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('contract_end_date')
                    ->label('Fin contrat')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Modifié le')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'active' => 'Actif',
                        'inactive' => 'Inactif',
                        'pending' => 'En attente',
                        'suspended' => 'Suspendu',
                    ])
                    ->multiple(),

                SelectFilter::make('category')
                    ->label('Catégorie')
                    ->relationship('category', 'name_fr')
                    ->multiple()
                    ->preload(),

                SelectFilter::make('city')
                    ->label('Ville')
                    ->options(fn () => \App\Models\Partner::distinct()->pluck('city', 'city')->toArray())
                    ->multiple(),

                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
