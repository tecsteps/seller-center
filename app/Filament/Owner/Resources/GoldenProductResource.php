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
        return $form
            ->schema([
                Forms\Components\Select::make('active_locale')
                    ->label('')
                    ->options(function () {
                        return Locale::query()
                            ->orderBy('default', 'desc')
                            ->orderBy('name')
                            ->pluck('name', 'id');
                    })
                    ->default(function (Get $get) {
                        $defaultLocale = Locale::where('default', true)->first();
                        return $defaultLocale ? $defaultLocale->id : null;
                    })
                    ->live()
                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                        $locale = Locale::find($state);
                        if (!$locale) return;

                        $record = GoldenProduct::find($get('id'));
                        if (!$record) return;

                        $translation = $record->translations()
                            ->where('locale_id', $locale->id)
                            ->first();

                        if ($translation) {
                            $set('name', $translation->name);
                            $set('description', $translation->description);
                            $set('attributes', $translation->attributes ?? []);
                        } else {
                            $set('name', '');
                            $set('description', '');
                            $set('attributes', []);
                        }
                    })
                    ->selectablePlaceholder(false)
                    ->extraAttributes([
                        'class' => 'ml-auto w-[200px]'
                    ]),

                Section::make('General Information')
                    ->description('Basic product information and categorization')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->afterStateHydrated(function ($component, $state, $record) {
                                if (!$state && $record) {
                                    $defaultLocale = Locale::where('default', true)->first();
                                    if (!$defaultLocale) return;

                                    $translation = $record->translations()
                                        ->where('locale_id', $defaultLocale->id)
                                        ->first();

                                    $component->state($translation?->name);
                                }
                            }),
                        Forms\Components\Textarea::make('description')
                            ->columnSpanFull()
                            ->afterStateHydrated(function ($component, $state, $record) {
                                if (!$state && $record) {
                                    $defaultLocale = Locale::where('default', true)->first();
                                    if (!$defaultLocale) return;

                                    $translation = $record->translations()
                                        ->where('locale_id', $defaultLocale->id)
                                        ->first();

                                    $component->state($translation?->description);
                                }
                            }),

                        Forms\Components\Select::make('product_type_id')
                            ->relationship('productType', 'name')
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                $productTypeId = $get('product_type_id');
                                if (!$productTypeId) {
                                    $set('attributes', null);
                                    return;
                                }

                                $productType = ProductType::with('attributes')->find($productTypeId);
                                if (!$productType) return;

                                $attributes = collect($get('attributes') ?? []);

                                $newAttributes = $productType->attributes->mapWithKeys(function ($attribute) use ($attributes) {
                                    return [$attribute->name => $attributes[$attribute->name] ?? null];
                                })->toArray();

                                $set('attributes', $newAttributes);
                            })
                            ->required(),
                    ]),

                Section::make('Attributes')
                    ->schema(fn(Get $get): array => static::getAttributeFields($get('product_type_id')))
                    ->columns(2)
                    ->visible(fn(Get $get): bool => (bool) $get('product_type_id')),
            ]);
    }

    protected static function getAttributeFields(?string $productTypeId): array
    {
        if (!$productTypeId) return [];

        $productType = ProductType::with('attributes')->find($productTypeId);
        if (!$productType) return [];

        return $productType->attributes
            ->sortBy('rank')
            ->map(function ($attribute) {
                $baseField = match ($attribute->type) {
                    'select' => Forms\Components\Select::make("attributes.{$attribute->id}")
                        ->label($attribute->name)
                        ->options(function () use ($attribute) {
                            $defaultLocale = Locale::where('default', true)->first();
                            return $attribute->options()
                                ->with(['values' => function ($query) use ($defaultLocale) {
                                    $query->where('locale_id', $defaultLocale->id);
                                }])
                                ->get()
                                ->mapWithKeys(function ($option) {
                                    return [$option->id => $option->values->first()?->value];
                                });
                        })
                        ->helperText($attribute->description)
                        ->native(false)
                        ->required($attribute->required),

                    'color' => Forms\Components\ColorPicker::make("attributes.{$attribute->id}")
                        ->label($attribute->name)
                        ->helperText($attribute->description)
                        ->required($attribute->required),

                    'boolean' => Forms\Components\Toggle::make("attributes.{$attribute->id}")
                        ->label($attribute->name)
                        ->helperText($attribute->description)
                        ->required($attribute->required),

                    default => Forms\Components\TextInput::make("attributes.{$attribute->id}")
                        ->label($attribute->name)
                        ->helperText($attribute->description)
                        ->required($attribute->required),
                };

                $field = $baseField->afterStateHydrated(function ($component, $state, $record) use ($attribute) {
                    if (!$state && $record) {
                        $selectedLocaleId = $component->getState(); // Get the selected locale ID from the component
                        $goldenProductAttribute = $record->attributes()
                            ->where('product_type_attribute_id', $attribute->id)
                            ->first();

                        if (!$goldenProductAttribute) return;

                        if ($goldenProductAttribute->is_option) {
                            // Get the option value through the many-to-many relationship
                            $optionValue = $goldenProductAttribute->productTypeAttributeOptionValues()
                                // ->whereHas('locale', fn($query) => $query->where('id', $selectedLocaleId))
                                ->first();

                            if ($optionValue) {
                                $component->state($optionValue->product_type_attribute_option_id);
                            }
                        } else {
                            // Get the direct value from golden_product_attribute_values
                            $value = $goldenProductAttribute->values()
                                ->where('locale_id', $selectedLocaleId)
                                ->first();
                            $component->state($value?->value);
                        }
                    }
                });

                return $field;
            })
            ->toArray();
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
