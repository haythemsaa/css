<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('phone')
                    ->searchable(),
                TextColumn::make('photo')
                    ->searchable(),
                TextColumn::make('birth_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('city')
                    ->searchable(),
                TextColumn::make('governorate')
                    ->searchable(),
                TextColumn::make('user_type')
                    ->searchable(),
                TextColumn::make('socios_number')
                    ->searchable(),
                IconColumn::make('socios_verified')
                    ->boolean(),
                TextColumn::make('socios_verified_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('subscription_status')
                    ->searchable(),
                TextColumn::make('subscription_expires_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('loyalty_points')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('loyalty_level')
                    ->searchable(),
                IconColumn::make('is_active')
                    ->boolean(),
                TextColumn::make('last_login_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
