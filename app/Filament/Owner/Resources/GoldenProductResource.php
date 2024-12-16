<?php

namespace App\Filament\Owner\Resources;

use App\Filament\Owner\Resources\GoldenProductResource\Pages;
use App\Filament\Owner\Resources\GoldenProductResource\RelationManagers;
use App\Models\GoldenProduct;
use App\Models\GoldenProductLocalized;
use App\Models\ProductType;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use App\Models\Locale;
use App\Filament\Owner\Resources\GoldenProductResource\RelationManagers\SellerProductsRelationManager;
use App\Filament\Owner\Resources\GoldenProductResource\RelationManagers\VariantsRelationManager;
use App\Models\GoldenProductAttribute;
use App\Models\GoldenProductAttributeValue;
use App\Models\ProductTypeAttribute;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GoldenProductResource extends Resource
{
    protected static ?string $model = GoldenProduct::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationGroup = 'Catalog';

    protected static ?int $navigationSort = 20;

    protected static ?int $navigationGroupSort = 10;

    public static function getRecordTitle(?Model $record): string|null
    {
        return $record?->translations()
            ->whereHas('locale', fn($query) => $query->where('default', true))
            ->first()?->name;
    }

    public static function form(Form $form): Form
    {
        $defaultLocale = Locale::where('default', true)->first();
        $locales = Locale::orderBy('default', 'desc')->get();

        $tabs = [];
        foreach ($locales as $locale) {
            $tabs[] = Tab::make($locale->name)
                ->badge($locale->default ? 'Default' : null)

                ->schema([
                    Section::make('General Information')
                        ->description('Basic product information and categorization')
                        ->columns(2)
                        ->schema([
                            Forms\Components\TextInput::make("translations.{$locale->id}.name")
                                ->label('Name')
                                ->required($locale->default)
                                ->afterStateHydrated(function (Forms\Components\TextInput $component, $state, ?GoldenProduct $record) use ($locale) {
                                    if (!$record) return;

                                    $translation = $record->translations()
                                        ->where('locale_id', $locale->id)
                                        ->first();

                                    if ($translation) {
                                        $component->state($translation->name);
                                    }
                                }),
                            Forms\Components\Select::make('product_type_id')
                                ->relationship('productType', 'name')
                                ->required()
                                ->disabled()
                                ->afterStateUpdated(function (Forms\Set $set, $state) {
                                    $set('name', '');
                                    $set('description', '');
                                })
                                ->selectablePlaceholder(false)
                                ->label('Product Type'),
                            Forms\Components\RichEditor::make("translations.{$locale->id}.description")
                                ->label('Description')
                                ->required($locale->default)
                                ->columnSpanFull()
                                ->afterStateHydrated(function (Forms\Components\RichEditor $component, $state, ?GoldenProduct $record) use ($locale) {
                                    if (!$record) return;

                                    $translation = $record->translations()
                                        ->where('locale_id', $locale->id)
                                        ->first();

                                    if ($translation) {
                                        $component->state($translation->description);
                                    }
                                }),
                        ]),

                    Section::make('Attributes')
                        ->description(fn(Get $get) => 'Product type: ' . (ProductType::find($get('product_type_id'))?->name))
                        ->schema(fn(Get $get): array => static::getAttributeFields($get('product_type_id'), $locale->id))
                        ->columns(2)
                        ->visible(fn(Get $get): bool => (bool)$get('product_type_id'))
                        ->key("attributesSection_{$locale->id}")
                ]);
        }

        return $form
            ->schema([
                Forms\Components\Hidden::make('active_locale')
                    ->default($locales->first()->id),
                Forms\Components\Hidden::make('product_type_id')
                    ->default(fn($record) => $record?->product_type_id),
                Tabs::make('Locales')
                    ->tabs($tabs)
                    ->columnSpanFull()
                    ->persistTab()
                    ->extraAttributes([
                        'x-on:tab-changed' => 'document.querySelector(\'input[name="active_locale"]\').value = $event.detail.tab.id',
                    ]),

            ]);
    }

    protected static function getAttributeFields(?string $productTypeId, ?string $localeId): array
    {
        if (!$productTypeId) return [];

        $productType = ProductType::with('attributes')->find($productTypeId);
        if (!$productType) return [];

        return $productType->attributes
            ->sortBy('rank')
            ->map(function (ProductTypeAttribute $attribute) use ($localeId) {
                $baseField = match ($attribute->field) {
                    'TextInput' => Forms\Components\TextInput::make("golden_product_attributes.{$localeId}.{$attribute->id}")
                        ->label($attribute->name)
                        ->when($attribute->unit, fn($component) => $component->suffix($attribute->unit)),
                    'Textarea' => Forms\Components\Textarea::make("golden_product_attributes.{$localeId}.{$attribute->id}")
                        ->label($attribute->name),
                    'RichEditor' => Forms\Components\RichEditor::make("golden_product_attributes.{$localeId}.{$attribute->id}")
                        ->label($attribute->name),
                    'MarkdownEditor' => Forms\Components\MarkdownEditor::make("golden_product_attributes.{$localeId}.{$attribute->id}")
                        ->label($attribute->name),
                    'Checkbox' => Forms\Components\Checkbox::make("golden_product_attributes.{$localeId}.{$attribute->id}")
                        ->label($attribute->name),
                    'Toggle' => Forms\Components\Toggle::make("golden_product_attributes.{$localeId}.{$attribute->id}")
                        ->label($attribute->name),
                    'Select' => Forms\Components\Select::make("golden_product_attributes.{$localeId}.{$attribute->id}")
                        ->label($attribute->name)
                        ->options(function () use ($attribute, $localeId) {
                            return $attribute->options()
                                ->with(['values' => function ($query) use ($localeId) {
                                    $query->where('locale_id', $localeId);
                                }])
                                ->get()
                                ->mapWithKeys(function ($option) use ($localeId) {
                                    $optionValue = $option->values->where('locale_id', $localeId)->first();

                                    // Log::info($localeId . '-' . $optionValue->id);
                                    return $optionValue ? [$optionValue->id => $optionValue->value] : [];
                                })
                                ->filter();
                        }),
                    'MultiSelect' => Forms\Components\MultiSelect::make("golden_product_attributes.{$localeId}.{$attribute->id}")
                        ->label($attribute->name)
                        ->options(function () use ($attribute, $localeId) {
                            return $attribute->options()
                                ->with(['values' => function ($query) use ($localeId) {
                                    $query->where('locale_id', $localeId);
                                }])
                                ->get()
                                ->mapWithKeys(function ($option) {
                                    return [$option->id => $option->values->first()?->value];
                                })
                                ->filter();
                        }),
                    'CheckboxList' => Forms\Components\CheckboxList::make("golden_product_attributes.{$localeId}.{$attribute->id}")
                        ->label($attribute->name)
                        ->options(function () use ($attribute, $localeId) {
                            return $attribute->options()
                                ->with(['values' => function ($query) use ($localeId) {
                                    $query->where('locale_id', $localeId);
                                }])
                                ->get()
                                ->mapWithKeys(function ($option) {
                                    return [$option->id => $option->values->first()?->value];
                                })
                                ->filter();
                        }),
                    'Radio' => Forms\Components\Radio::make("golden_product_attributes.{$localeId}.{$attribute->id}")
                        ->label($attribute->name)
                        ->options(function () use ($attribute, $localeId) {
                            return $attribute->options()
                                ->with(['values' => function ($query) use ($localeId) {
                                    $query->where('locale_id', $localeId);
                                }])
                                ->get()
                                ->mapWithKeys(function ($option) {
                                    return [$option->id => $option->values->first()?->value];
                                })
                                ->filter();
                        }),
                    'TagsInput' => Forms\Components\TagsInput::make("golden_product_attributes.{$localeId}.{$attribute->id}")
                        ->label($attribute->name),
                    'ColorPicker' => Forms\Components\ColorPicker::make("golden_product_attributes.{$localeId}.{$attribute->id}")
                        ->label($attribute->name),
                    default => Forms\Components\TextInput::make("golden_product_attributes.{$localeId}.{$attribute->id}")
                        ->label($attribute->name),
                };

                return $baseField
                    ->helperText($attribute->description)
                    ->required($attribute->required)
                    ->afterStateHydrated(function ($component, $state, ?Model $record) use ($attribute, $localeId) {
                        if (!$record) return;

                        $goldenProductAttribute = $record->attributes()
                            ->where('product_type_attribute_id', $attribute->id)
                            ->first();

                        if (!$goldenProductAttribute) return;

                        if (!$goldenProductAttribute->is_option) {

                            $value = $goldenProductAttribute->values()
                                ->where('locale_id', $localeId)
                                ->first();


                            if ($value) {
                                $component->state($value->value);
                            }
                        } else {
                            $optionValue = DB::table('golden_product_attribute_product_type_attribute_option_value as pivot')
                                ->join('product_type_attribute_option_values as values', 'values.id', '=', 'pivot.product_type_attribute_option_value_id')
                                ->where('pivot.golden_product_attribute_id', $goldenProductAttribute->id)
                                ->where('values.locale_id', $localeId)
                                ->select('values.id')
                                ->first();

                            if ($optionValue) {
                                $component->state($optionValue->id);
                            }
                        }
                    });
            })
            ->toArray();
    }

    protected static function getAttributeValue($record, $attribute, $localeId): mixed
    {
        if (!$localeId) return null;

        $goldenProductAttribute = $record->attributes()
            ->where('product_type_attribute_id', $attribute->id)
            ->first();

        if (!$goldenProductAttribute) return null;

        return self::getLocalizedAttributeValue($goldenProductAttribute, $localeId);
    }

    protected static function getLocalizedAttributeValue(GoldenProductAttribute $goldenProductAttribute, $localeId): mixed
    {
        if ($goldenProductAttribute->is_option) {
            $optionValue = $goldenProductAttribute->productTypeAttributeOptionValues()
                ->whereHas('locale', fn($query) => $query->where('id', $localeId))
                ->first();

            return $optionValue?->product_type_attribute_option_value_id;
        } else {

            $value = $goldenProductAttribute->values()
                ->where('locale_id', $localeId)
                ->first();

            return $value?->value;
        }
    }

    protected function handleRecordCreation(array $data): Model
    {
        $localeId = $data['active_locale'] ?? Locale::where('default', true)->first()?->id;

        $record = new GoldenProduct([
            'product_type_id' => $data['product_type_id'],
        ]);
        $record->save();

        // Create translations
        $record->translations()->create([
            'locale_id' => $localeId,
            'name' => $data['name'],
            'description' => $data['description'],
            'product_type_id' => $data['product_type_id'],
        ]);

        // Create attributes with their values
        if (isset($data['attributes'])) {
            foreach ($data['attributes'] as $attributeId => $value) {
                $attribute = $record->productType->attributes()->find($attributeId);
                if (!$attribute) continue;

                $goldenProductAttribute = $record->attributes()->create([
                    'product_type_attribute_id' => $attributeId,
                    'golden_product_id' => $record->id,
                    'is_option' => $attribute->type === 'select'
                ]);

                if ($attribute->type === 'select') {
                    $goldenProductAttribute->productTypeAttributeOptionValues()->attach($value);
                } else {
                    $goldenProductAttribute->values()->create([
                        'value' => $value,
                        'locale_id' => $localeId
                    ]);
                }
            }
        }

        return $record;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $localeId = $data['active_locale'] ?? Locale::where('default', true)->first()?->id;

        $record->update([
            'product_type_id' => $data['product_type_id'],
        ]);

        // Update translations
        $translation = $record->translations()->updateOrCreate(
            ['locale_id' => $localeId],
            [
                'name' => $data['name'],
                'description' => $data['description'],
                'product_type_id' => $data['product_type_id'],
            ]
        );

        // Update attributes with their values
        if (isset($data['attributes'])) {
            // First, delete existing attributes and their values
            $record->attributes()->delete();

            // Then create new ones
            foreach ($data['attributes'] as $attributeId => $value) {
                $attribute = $record->productType->attributes()->find($attributeId);
                if (!$attribute) continue;

                $goldenProductAttribute = $record->attributes()->create([
                    'product_type_attribute_id' => $attributeId,
                    'golden_product_id' => $record->id,
                    'is_option' => $attribute->type === 'select'
                ]);

                if ($attribute->type === 'select') {
                    $goldenProductAttribute->productTypeAttributeOptionValues()->attach($value);
                } else {
                    $goldenProductAttribute->values()->create([
                        'value' => $value,
                        'locale_id' => $localeId
                    ]);
                }
            }
        }

        return $record;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('translations.name')
                    ->label('Name')
                    ->getStateUsing(function ($record) {
                        return $record->translations()
                            ->whereHas('locale', fn($query) => $query->where('default', true))
                            ->first()?->name;
                    })
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('translations', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%")
                                ->whereHas('locale', fn($q) => $q->where('default', true));
                        });
                    })
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy(
                            GoldenProductLocalized::select('name')
                                ->whereColumn('golden_product_id', 'golden_products.id')
                                ->whereHas('locale', fn($q) => $q->where('default', true))
                                ->limit(1),
                            $direction
                        );
                    }),
                Tables\Columns\TextColumn::make('translations.description')
                    ->label('Description')
                    ->getStateUsing(function ($record) {
                        return $record->translations()
                            ->whereHas('locale', fn($query) => $query->where('default', true))
                            ->first()?->description;
                    })
                    ->limit(50)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('productType.name')
                    ->label('Product Type')
                    ->sortable(),
                Tables\Columns\TextColumn::make('seller_products_count')
                    ->label('Seller Products')
                    ->counts('sellerProducts')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            VariantsRelationManager::class,
            SellerProductsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGoldenProducts::route('/'),
            'create' => Pages\CreateGoldenProduct::route('/create'),
            'edit' => Pages\EditGoldenProduct::route('/{record}/edit'),
        ];
    }
}
