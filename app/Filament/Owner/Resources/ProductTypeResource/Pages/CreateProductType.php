<?php

namespace App\Filament\Owner\Resources\ProductTypeResource\Pages;

use App\Filament\Owner\Resources\ProductTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProductType extends CreateRecord
{
    protected static string $resource = ProductTypeResource::class;
}
