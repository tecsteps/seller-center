<?php

namespace App\Filament\Resources\SellerVariantResource\Pages;

use App\Filament\Resources\SellerVariantResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Traits\RedirectsToIndex;

class CreateSellerVariant extends CreateRecord
{
    use RedirectsToIndex;

    protected static string $resource = SellerVariantResource::class;
}
