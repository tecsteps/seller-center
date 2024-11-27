<?php

namespace App\Filament\Owner\Resources\SellerProductResource\Pages;

use App\Filament\Owner\Resources\SellerProductResource;
use Filament\Resources\Pages\ViewRecord;

class ViewSellerProduct extends ViewRecord
{
    protected static string $resource = SellerProductResource::class;

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }

    public function getContentTabLabel(): ?string
    {
        return 'General';
    }
}
