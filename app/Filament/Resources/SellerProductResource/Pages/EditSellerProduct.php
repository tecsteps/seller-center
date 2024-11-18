<?php

namespace App\Filament\Resources\SellerProductResource\Pages;

use App\Filament\Resources\SellerProductResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Traits\RedirectsToIndex;

class EditSellerProduct extends EditRecord
{
    use RedirectsToIndex;

    protected static string $resource = SellerProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
