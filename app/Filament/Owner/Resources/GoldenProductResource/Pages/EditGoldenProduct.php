<?php

namespace App\Filament\Owner\Resources\GoldenProductResource\Pages;

use App\Filament\Owner\Resources\GoldenProductResource;
use App\Models\GoldenProduct;
use App\Models\GoldenProductAttribute;
use App\Models\GoldenProductLocalized;
use App\Models\ProductTypeAttribute;
use App\Models\ProductTypeAttributeOptionValue;
use Attribute;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
        $this->saveGoldenProduct($record, $data);
        $this->saveGoldenProductAttributes($record, $data);
        return $record;
    }

    private function saveGoldenProduct(GoldenProduct $goldenProduct, array $data): void
    {
        foreach ($data['translations'] as $localeId => $translation) {
            $goldenProductLocalized = GoldenProductLocalized::updateOrCreate(
                [
                    'golden_product_id' => $goldenProduct->id,
                    'locale_id' => $localeId,
                ],
                [
                    'name' => $translation['name'],
                    'description' => $translation['description'],
                ]
            );
        }
    }

    private function saveGoldenProductAttributes(GoldenProduct $goldenProduct, array $data): void
    {
        dd($data);
        $deleted = [];
        foreach ($data['golden_product_attributes'] as $localeId => $values) {
            foreach ($values as $attributeId => $value) {

                $productTypeAttribute = ProductTypeAttribute::find($attributeId); // TODO too many queries

                $goldenProductAttribute = $goldenProduct->attributes()
                    ->where('product_type_attribute_id', $attributeId)
                    ->first();

                if ($goldenProductAttribute->is_option) {
                    // Get the selected option value
                    $optionValue = ProductTypeAttributeOptionValue::find($value);

                    if ($optionValue) {
                        dd($optionValue->id);
                        // First, remove any existing relations for this attribute
                        if (!in_array($goldenProductAttribute->id, $deleted)) {
                            // DB::table('golden_product_attribute_product_type_attribute_option_value')
                            //     ->where('golden_product_attribute_id', $goldenProductAttribute->id)
                            //     ->delete();
                            $deleted[] = $goldenProductAttribute->id;
                        }

                        // Then insert the new relation
                        DB::table('golden_product_attribute_product_type_attribute_option_value')->insert([
                            'golden_product_attribute_id' => $goldenProductAttribute->id,
                            'product_type_attribute_option_value_id' => $optionValue->id,
                        ]);
                    }
                } else {
                    $goldenProductAttribute->values()->updateOrCreate(
                        [
                            'golden_product_attribute_id' => $goldenProductAttribute->id,
                            'locale_id' => $localeId,
                        ],
                        [
                            'value' => $value,
                        ]
                    );
                }
            }
        }
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }
}
