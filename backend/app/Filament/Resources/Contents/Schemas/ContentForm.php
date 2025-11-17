<?php

namespace App\Filament\Resources\Contents\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ContentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                Textarea::make('body')
                    ->columnSpanFull(),
                Textarea::make('excerpt')
                    ->columnSpanFull(),
                TextInput::make('type')
                    ->required()
                    ->default('article'),
                TextInput::make('category_id')
                    ->numeric(),
                Select::make('author_id')
                    ->relationship('author', 'name'),
                Toggle::make('is_premium')
                    ->required(),
                Toggle::make('is_featured')
                    ->required(),
                TextInput::make('views_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('likes_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                DateTimePicker::make('published_at'),
            ]);
    }
}
