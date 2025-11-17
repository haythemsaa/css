<?php

namespace App\Filament\Resources\PartnerOffers;

use App\Filament\Resources\PartnerOffers\Pages\CreatePartnerOffer;
use App\Filament\Resources\PartnerOffers\Pages\EditPartnerOffer;
use App\Filament\Resources\PartnerOffers\Pages\ListPartnerOffers;
use App\Filament\Resources\PartnerOffers\Schemas\PartnerOfferForm;
use App\Filament\Resources\PartnerOffers\Tables\PartnerOffersTable;
use App\Models\PartnerOffer;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PartnerOfferResource extends Resource
{
    protected static ?string $model = PartnerOffer::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return PartnerOfferForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PartnerOffersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPartnerOffers::route('/'),
            'create' => CreatePartnerOffer::route('/create'),
            'edit' => EditPartnerOffer::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
