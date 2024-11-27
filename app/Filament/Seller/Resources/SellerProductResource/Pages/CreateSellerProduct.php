<?php

namespace App\Filament\Seller\Resources\SellerProductResource\Pages;

use App\Filament\Seller\Resources\SellerProductResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Facades\Filament;
use App\Filament\Traits\RedirectsToIndex;

class CreateSellerProduct extends CreateRecord
{
    use RedirectsToIndex;

    protected static string $resource = SellerProductResource::class;

    protected function afterCreate(): void
    {
        $record = $this->record;
        $data = $this->data;
        
        // Extract and save prices
        collect($data)
            ->filter(fn($value, $key) => str_starts_with($key, 'price_'))
            ->each(function ($amount, $key) use ($record) {
                if ($amount !== null) {
                    $currencyId = str_replace('price_', '', $key);
                    $record->prices()->create([
                        'currency_id' => $currencyId,
                        'amount' => $amount,
                        'seller_product_id' => $record->id,
                        'seller_id' => Filament::getTenant()->id
                    ]);
                }
            });
    }
}
