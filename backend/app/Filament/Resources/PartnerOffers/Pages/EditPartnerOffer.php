<?php

namespace App\Filament\Resources\PartnerOffers\Pages;

use App\Filament\Resources\PartnerOffers\PartnerOfferResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditPartnerOffer extends EditRecord
{
    protected static string $resource = PartnerOfferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
