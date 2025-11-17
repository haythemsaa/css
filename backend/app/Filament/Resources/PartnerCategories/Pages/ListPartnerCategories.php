<?php

namespace App\Filament\Resources\PartnerCategories\Pages;

use App\Filament\Resources\PartnerCategories\PartnerCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPartnerCategories extends ListRecords
{
    protected static string $resource = PartnerCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
