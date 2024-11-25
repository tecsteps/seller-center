<?php

namespace App\Filament\Owner\Resources\CategoryResource\Pages;

use App\Filament\Owner\Resources\CategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;
}
