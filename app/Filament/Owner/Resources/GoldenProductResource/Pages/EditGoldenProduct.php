<?php

namespace App\Filament\Owner\Resources\GoldenProductResource\Pages;

use App\Filament\Owner\Resources\GoldenProductResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditGoldenProduct extends EditRecord
{
    protected static string $resource = GoldenProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $localeId = $data['active_locale'];

        $record->update([
            'product_type_id' => $data['product_type_id'],
        ]);

        $record->translations()->updateOrCreate(
            ['locale_id' => $localeId],
            [
                'name' => $data['name'],
                'description' => $data['description'],
                'attributes' => $data['attributes'] ?? [],
                'product_type_id' => $data['product_type_id'],
            ]
        );

        return $record;
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }
}
