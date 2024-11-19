<?php

namespace App\Filament\Seller\Resources\SellerVariantResource\Pages;

use App\Filament\Seller\Resources\SellerVariantResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Traits\RedirectsToIndex;

class EditSellerVariant extends EditRecord
{
    use RedirectsToIndex;

    protected static string $resource = SellerVariantResource::class;

    public function getTitle(): string 
    {
        return 'Edit Variant "' . $this->record->name . '"';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }
}
