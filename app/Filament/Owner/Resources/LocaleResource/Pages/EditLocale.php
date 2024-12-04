<?php

namespace App\Filament\Owner\Resources\LocaleResource\Pages;

use App\Filament\Owner\Resources\LocaleResource;
use App\Models\Locale;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditLocale extends EditRecord
{
    protected static string $resource = LocaleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        if (($data['default'] ?? false) && !$record->default) {
            // If setting this locale as default, remove default from other locales
            Locale::where('id', '!=', $record->id)
                ->where('default', true)
                ->update(['default' => false]);
        }

        $record->update($data);

        return $record;
    }
}
