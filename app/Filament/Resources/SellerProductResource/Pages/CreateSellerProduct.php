<?php

namespace App\Filament\Resources\SellerProductResource\Pages;

use App\Filament\Resources\SellerProductResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Traits\RedirectsToIndex;

class CreateSellerProduct extends CreateRecord
{
    use RedirectsToIndex;

    protected static string $resource = SellerProductResource::class;
}
