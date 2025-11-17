<?php

namespace App\Filament\Resources\PartnerOffers\Pages;

use App\Filament\Resources\PartnerOffers\PartnerOfferResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPartnerOffers extends ListRecords
{
    protected static string $resource = PartnerOfferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
