<?php

namespace App\Filament\Resources\FootballMatches\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class FootballMatchForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('opponent')
                    ->required(),
                TextInput::make('competition')
                    ->required(),
                TextInput::make('stadium'),
                DatePicker::make('match_date')
                    ->required(),
                TimePicker::make('kick_off_time')
                    ->required(),
                TextInput::make('home_away')
                    ->required()
                    ->default('home'),
                TextInput::make('css_score')
                    ->numeric(),
                TextInput::make('opponent_score')
                    ->numeric(),
                TextInput::make('status')
                    ->required()
                    ->default('scheduled'),
                TextInput::make('attendance')
                    ->numeric(),
                TextInput::make('referee'),
            ]);
    }
}
