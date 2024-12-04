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
                $baseField = match ($attribute->field) {
                    'TextInput' => Forms\Components\TextInput::make("attributes.{$attribute->name}")
                        ->label($attribute->name)
                        ->helperText($attribute->description)
                        ->required($attribute->required),

                    'Select' => Forms\Components\Select::make("attributes.{$attribute->name}")
                        ->label($attribute->name)
                        ->options(function () use ($attribute) {
                            // Handle options as a simple array of values
                            return collect($attribute->options)->mapWithKeys(function ($option) {
                                return [$option => $option];
                            })->toArray();
                        })
                        ->helperText($attribute->description)
                        ->native(false)
                        ->required($attribute->required),

                    'ColorPicker' => Forms\Components\ColorPicker::make("attributes.{$attribute->name}")
                        ->label($attribute->name)
                        ->helperText($attribute->description)
                        ->required($attribute->required),

                    default => Forms\Components\TextInput::make("attributes.{$attribute->name}")
                        ->label($attribute->name)
                        ->helperText($attribute->description)
                        ->required($attribute->required),
                };

                $field = $baseField->afterStateHydrated(function ($component, $state, $record) use ($attribute) {
                    if (!$state && $record) {
                        $defaultLocale = Locale::where('default', true)->first();
                        if (!$defaultLocale) return;

                        $translation = $record->translations()
                            ->where('locale_id', $defaultLocale->id)
                            ->first();

                        $attributes = $translation?->attributes ?? [];
                        $component->state($attributes[$attribute->name] ?? null);
                    }
                });

                if ($attribute->validators) {
                    $validators = $attribute->validators;

                    if ($attribute->type === 'number') {
                        if (isset($validators['min'])) {
                            $field->minValue((float) $validators['min']);
                        }
                        if (isset($validators['max'])) {
                            $field->maxValue((float) $validators['max']);
                        }
                        if (isset($validators['decimal_places'])) {
                            $field->numeric()->step(pow(0.1, (int) $validators['decimal_places']));
                        }
                    }

                    if ($attribute->type === 'text') {
                        if (isset($validators['min_length'])) {
                            $field->minLength((int) $validators['min_length']);
                        }
                        if (isset($validators['max_length'])) {
                            $field->maxLength((int) $validators['max_length']);
                        }
                        if (isset($validators['pattern'])) {
                            $field->regex($validators['pattern']);
                        }
                    }
                }

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

        $record->translations()->create([
            'locale_id' => $localeId,
            'name' => $data['name'],
            'description' => $data['description'],
            'attributes' => $data['attributes'] ?? [],
            'product_type_id' => $data['product_type_id'],
        ]);

        return $record;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $localeId = $data['active_locale'] ?? Locale::where('default', true)->first()?->id;

        $record->update([
            'product_type_id' => $data['product_type_id'],
        ]);

        $translation = $record->translations()->updateOrCreate(
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
