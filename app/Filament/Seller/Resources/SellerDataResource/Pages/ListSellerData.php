<?php

namespace App\Filament\Seller\Resources\SellerDataResource\Pages;

use App\Filament\Seller\Resources\SellerDataResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Facades\Filament;

class ListSellerData extends ListRecords
{
    protected static string $resource = SellerDataResource::class;

    public function mount(): void
    {
        $seller = Filament::getTenant();

        if ($seller->sellerData) {
            $this->redirect($this->getResource()::getUrl('edit', [
                'record' => $seller->sellerData->id
            ]));
        } else {
            $this->redirect($this->getResource()::getUrl('create'));
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
