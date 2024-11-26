<?php

namespace App\Filament\Seller\Resources\SellerDataResource\Pages;

use App\Filament\Seller\Resources\SellerDataResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateSellerData extends CreateRecord
{
    protected static string $resource = SellerDataResource::class;

    public static function canCreateAnother(): bool
    {
        return false;
    }

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->label('Save');
    }
}
