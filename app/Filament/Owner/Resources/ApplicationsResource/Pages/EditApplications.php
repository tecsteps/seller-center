<?php

namespace App\Filament\Owner\Resources\ApplicationsResource\Pages;

use App\Filament\Owner\Resources\ApplicationsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditApplications extends EditRecord
{
    protected static string $resource = ApplicationsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
