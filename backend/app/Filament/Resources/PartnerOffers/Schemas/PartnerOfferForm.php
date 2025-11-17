<?php

namespace App\Filament\Resources\PartnerOffers\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PartnerOfferForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('partner_id')
                    ->relationship('partner', 'name')
                    ->required(),
                TextInput::make('title')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                TextInput::make('offer_type')
                    ->required()
                    ->default('standard'),
                TextInput::make('reduction_value')
                    ->required()
                    ->numeric(),
                TextInput::make('reduction_type')
                    ->required()
                    ->default('percentage'),
                TextInput::make('min_purchase_amount')
                    ->numeric(),
                TextInput::make('max_discount_amount')
                    ->numeric(),
                DateTimePicker::make('valid_from'),
                DateTimePicker::make('valid_until'),
                Textarea::make('days_of_week')
                    ->columnSpanFull(),
                Textarea::make('time_slots')
                    ->columnSpanFull(),
                TextInput::make('stock_available')
                    ->numeric(),
                TextInput::make('stock_used')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('user_limit_per_day')
                    ->numeric(),
                TextInput::make('user_limit_per_month')
                    ->numeric(),
                TextInput::make('membership_required')
                    ->required()
                    ->default('both'),
                Textarea::make('terms_and_conditions')
                    ->columnSpanFull(),
                FileUpload::make('image_url')
                    ->image(),
                Textarea::make('images')
                    ->columnSpanFull(),
                Toggle::make('is_featured')
                    ->required(),
                TextInput::make('display_order')
                    ->required()
                    ->numeric()
                    ->default(0),
                DateTimePicker::make('notification_sent_at'),
                TextInput::make('status')
                    ->required()
                    ->default('draft'),
                TextInput::make('views_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('clicks_count')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
