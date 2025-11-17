<?php

namespace App\Filament\Resources\PartnerOffers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class PartnerOffersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('partner.name')
                    ->searchable(),
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('slug')
                    ->searchable(),
                TextColumn::make('offer_type')
                    ->searchable(),
                TextColumn::make('reduction_value')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('reduction_type')
                    ->searchable(),
                TextColumn::make('min_purchase_amount')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('max_discount_amount')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('valid_from')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('valid_until')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('stock_available')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('stock_used')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('user_limit_per_day')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('user_limit_per_month')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('membership_required')
                    ->searchable(),
                ImageColumn::make('image_url'),
                IconColumn::make('is_featured')
                    ->boolean(),
                TextColumn::make('display_order')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('notification_sent_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('status')
                    ->searchable(),
                TextColumn::make('views_count')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('clicks_count')
                    ->numeric()
                    ->sortable(),
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
