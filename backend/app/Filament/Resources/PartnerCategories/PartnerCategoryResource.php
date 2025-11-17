<?php

namespace App\Filament\Resources\PartnerCategories;

use App\Filament\Resources\PartnerCategories\Pages\CreatePartnerCategory;
use App\Filament\Resources\PartnerCategories\Pages\EditPartnerCategory;
use App\Filament\Resources\PartnerCategories\Pages\ListPartnerCategories;
use App\Filament\Resources\PartnerCategories\Schemas\PartnerCategoryForm;
use App\Filament\Resources\PartnerCategories\Tables\PartnerCategoriesTable;
use App\Models\PartnerCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PartnerCategoryResource extends Resource
{
    protected static ?string $model = PartnerCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return PartnerCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PartnerCategoriesTable::configure($table);
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
            'index' => ListPartnerCategories::route('/'),
            'create' => CreatePartnerCategory::route('/create'),
            'edit' => EditPartnerCategory::route('/{record}/edit'),
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
