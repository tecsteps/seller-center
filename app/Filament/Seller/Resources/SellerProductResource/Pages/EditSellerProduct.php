<?php

namespace App\Filament\Seller\Resources\SellerProductResource\Pages;

use App\Filament\Seller\Resources\SellerProductResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Facades\Filament;
use App\Filament\Traits\RedirectsToIndex;

class EditSellerProduct extends EditRecord
{
    use RedirectsToIndex;

    protected static string $resource = SellerProductResource::class;

    protected function afterSave(): void
    {
        $record = $this->record;
        $data = $this->data;
        
        // Extract and save prices
        collect($data)
            ->filter(fn($value, $key) => str_starts_with($key, 'price_'))
            ->each(function ($amount, $key) use ($record) {
                if ($amount !== null) {
                    $currencyId = str_replace('price_', '', $key);
                    $record->prices()->updateOrCreate(
                        [
                            'currency_id' => $currencyId,
                            'seller_variant_id' => null
                        ],
                        [
                            'amount' => $amount,
                            'seller_product_id' => $record->id,
                            'seller_id' => Filament::getTenant()->id
                        ]
                    );
                }
            });
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
