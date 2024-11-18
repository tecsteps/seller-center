<?php

namespace App\Filament\Resources\SellerVariantResource\Pages;

use App\Filament\Resources\SellerVariantResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSellerVariants extends ListRecords
{
    protected static string $resource = SellerVariantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
