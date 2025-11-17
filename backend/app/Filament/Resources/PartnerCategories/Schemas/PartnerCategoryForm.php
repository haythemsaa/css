<?php

namespace App\Filament\Resources\PartnerCategories\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PartnerCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name_fr')
                    ->required(),
                TextInput::make('name_ar')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                TextInput::make('icon'),
                TextInput::make('color'),
                Textarea::make('description')
                    ->columnSpanFull(),
                TextInput::make('display_order')
                    ->required()
                    ->numeric()
                    ->default(0),
                Toggle::make('is_active')
                    ->required(),
                Select::make('parent_id')
                    ->relationship('parent', 'id'),
            ]);
    }
}
