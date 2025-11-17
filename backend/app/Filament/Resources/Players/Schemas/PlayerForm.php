<?php

namespace App\Filament\Resources\Players\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PlayerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('first_name')
                    ->required(),
                TextInput::make('last_name')
                    ->required(),
                TextInput::make('photo'),
                TextInput::make('position')
                    ->required(),
                TextInput::make('jersey_number')
                    ->numeric(),
                TextInput::make('nationality'),
                DatePicker::make('birth_date'),
                TextInput::make('height')
                    ->numeric(),
                TextInput::make('weight')
                    ->numeric(),
                DatePicker::make('contract_expires_at'),
                TextInput::make('market_value')
                    ->numeric(),
                Textarea::make('bio')
                    ->columnSpanFull(),
                Textarea::make('statistics')
                    ->columnSpanFull(),
            ]);
    }
}
