<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                DateTimePicker::make('email_verified_at'),
                TextInput::make('password')
                    ->password()
                    ->required(),
                TextInput::make('phone')
                    ->tel(),
                TextInput::make('photo'),
                DatePicker::make('birth_date'),
                TextInput::make('city'),
                TextInput::make('governorate'),
                TextInput::make('user_type')
                    ->required()
                    ->default('free'),
                TextInput::make('socios_number'),
                Toggle::make('socios_verified')
                    ->required(),
                DateTimePicker::make('socios_verified_at'),
                TextInput::make('subscription_status'),
                DateTimePicker::make('subscription_expires_at'),
                TextInput::make('loyalty_points')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('loyalty_level')
                    ->required()
                    ->default('bronze'),
                Textarea::make('preferences')
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->required(),
                DateTimePicker::make('last_login_at'),
            ]);
    }
}
