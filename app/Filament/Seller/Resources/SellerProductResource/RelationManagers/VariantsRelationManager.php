<?php

namespace App\Filament\Seller\Resources\SellerProductResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Seller\Resources\SellerVariantResource;
use App\Filament\Seller\Resources\SellerProductResource;
use App\Filament\Seller\Resources\LocationResource;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'sellerVariants';

    protected static ?string $title = 'Variants';

    public function form(Form $form): Form
    {
        $priceFields = [];
        $stockFields = [];
        $allCurrencies = \App\Models\Currency::all();
        $locations = \App\Models\Location::where('seller_id', Filament::getTenant()->id)->get();
        $product = $this->getOwnerRecord();

        foreach ($allCurrencies as $currency) {
            $defaultPrice = $product->prices()
                ->where('currency_id', $currency->id)
                ->whereNull('seller_variant_id')
                ->first();

            $priceFields[] = Forms\Components\TextInput::make("price_{$currency->id}")
                ->label("{$currency->code} Price")
                ->numeric()
                ->prefix($currency->symbol)
                ->hint(function () use ($defaultPrice, $currency): ?string {
                    if ($defaultPrice) {
                        return "Default price: {$currency->symbol}{$defaultPrice->amount}";
                    }
                    return null;
                })
                ->hintColor('gray')
                ->afterStateHydrated(function (Forms\Components\TextInput $component, $state, ?Model $record) use ($currency): void {
                    if ($record) {
                        $price = $record->prices()->where('currency_id', $currency->id)->first();
                        $component->state($price?->amount);
                    }
                });
        }

        foreach ($locations as $location) {
            $stockFields[] = Forms\Components\TextInput::make("stock_{$location->id}")
                ->label("Stock at {$location->name}")
                ->numeric()
                ->minValue(0)
                ->default(0)
                ->afterStateHydrated(function (Forms\Components\TextInput $component, $state, ?Model $record) use ($location): void {
                    if ($record) {
                        $stock = $record->stocks()->where('location_id', $location->id)->first();
                        $component->state($stock?->quantity);
                    }
                });
        }

        return $form
            ->schema([
                Forms\Components\Section::make('Variant Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->helperText('The display name for this variant'),

                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'active' => 'Active',
                                'delisted' => 'Delisted'
                            ])
                            ->default('draft')
                            ->native(false)
                            ->helperText('Only Active products can be sold on the marketplace.'),

                        Forms\Components\TextInput::make('sku')
                            ->label('SKU')
                            ->helperText('Stock Keeping Unit - A unique identifier for this variant')
                            ->hint(fn($record) => $record?->sellerProduct?->sku ? "Product SKU: {$record->sellerProduct->sku}" : null),
                        Forms\Components\Textarea::make('description')
                            ->columnSpanFull()
                            ->helperText('Detailed description of this specific variant which overrides the product description'),
                        Forms\Components\KeyValue::make('attributes')
                            ->columnSpanFull()
                            ->helperText('Custom attributes for this variant (e.g. Color: Red, Size: Large)')
                            ->keyLabel('Key')
                            ->valueLabel('Value')
                            ->dehydrateStateUsing(fn($state) => is_array($state) ? $state : [])
                            ->reorderable()
                            ->editableKeys()
                            ->editableValues(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Pricing')
                    ->schema($priceFields)
                    ->columns(3),

                Forms\Components\Section::make('Stock')
                    ->schema(function () use ($locations, $stockFields) {
                        if ($locations->isEmpty()) {
                            return [
                                Forms\Components\Placeholder::make('no_locations')
                                    ->content('No warehouse locations configured')
                                    ->extraAttributes(['class' => 'text-warning-600'])
                                    ->helperText(function (): string {
                                        return "Please create a warehouse location first.";
                                    })
                            ];
                        }
                        return $stockFields;
                    })
                    ->columns($locations->isEmpty() ? 1 : 3)
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'draft' => 'gray',
                        'active' => 'success',
                        'delisted' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('sku'),
                Tables\Columns\TextColumn::make('description')
                    ->limit(50),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->slideOver()
                    ->using(function (array $data, string $model): Model {
                        $data['seller_id'] = Filament::getTenant()->id;
                        $data['seller_product_id'] = $this->getOwnerRecord()->id;

                        // Extract prices from form data
                        $prices = collect($data)
                            ->filter(fn($value, $key) => str_starts_with($key, 'price_'))
                            ->map(function ($amount, $key) {
                                $currencyId = str_replace('price_', '', $key);
                                return [
                                    'currency_id' => $currencyId,
                                    'amount' => $amount,
                                    'seller_product_id' => $this->getOwnerRecord()->id,
                                ];
                            })
                            ->values()
                            ->all();

                        // Extract stocks from form data
                        $stocks = collect($data)
                            ->filter(fn($value, $key) => str_starts_with($key, 'stock_'))
                            ->map(function ($quantity, $key) {
                                $locationId = str_replace('stock_', '', $key);
                                return [
                                    'location_id' => $locationId,
                                    'quantity' => $quantity,
                                ];
                            })
                            ->values()
                            ->all();

                        // Remove price and stock fields from variant data
                        $variantData = collect($data)
                            ->reject(fn($value, $key) => str_starts_with($key, 'price_'))
                            ->reject(fn($value, $key) => str_starts_with($key, 'stock_'))
                            ->all();

                        // Create variant
                        $variant = $model::create($variantData);

                        // Create prices with the new variant ID
                        foreach ($prices as $price) {
                            if ($price['amount'] !== null) {
                                $price['seller_variant_id'] = $variant->id;
                                $variant->prices()->create($price);
                            }
                        }

                        // Create stocks with the new variant ID
                        foreach ($stocks as $stock) {
                            if ($stock['quantity'] !== null) {
                                $stock['seller_variant_id'] = $variant->id;
                                $stock['seller_product_id'] = $this->getOwnerRecord()->id;
                                $stock['seller_id'] = Filament::getTenant()->id;
                                $variant->stocks()->create($stock);
                            }
                        }

                        return $variant;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->slideOver()
                    ->using(function (Model $record, array $data): Model {
                        // Extract prices from form data
                        $prices = collect($data)
                            ->filter(fn($value, $key) => str_starts_with($key, 'price_'))
                            ->map(function ($amount, $key) use ($record) {
                                $currencyId = str_replace('price_', '', $key);
                                return [
                                    'currency_id' => $currencyId,
                                    'amount' => $amount,
                                    'seller_product_id' => $this->getOwnerRecord()->id,
                                    'seller_variant_id' => $record->id,
                                ];
                            })
                            ->values()
                            ->all();

                        // Extract stocks from form data
                        $stocks = collect($data)
                            ->filter(fn($value, $key) => str_starts_with($key, 'stock_'))
                            ->map(function ($quantity, $key) use ($record) {
                                $locationId = str_replace('stock_', '', $key);
                                return [
                                    'location_id' => $locationId,
                                    'quantity' => $quantity,
                                    'seller_variant_id' => $record->id,
                                ];
                            })
                            ->values()
                            ->all();

                        // Remove price and stock fields from variant data
                        $variantData = collect($data)
                            ->reject(fn($value, $key) => str_starts_with($key, 'price_'))
                            ->reject(fn($value, $key) => str_starts_with($key, 'stock_'))
                            ->all();

                        // Update variant
                        $record->update($variantData);

                        // Update prices
                        foreach ($prices as $price) {
                            if ($price['amount'] !== null) {
                                $record->prices()->updateOrCreate(
                                    ['currency_id' => $price['currency_id']],
                                    [
                                        'amount' => $price['amount'],
                                        'seller_product_id' => $price['seller_product_id'],
                                        'seller_variant_id' => $price['seller_variant_id']
                                    ]
                                );
                            }
                        }

                        // Update stocks
                        foreach ($stocks as $stock) {
                            if ($stock['quantity'] !== null) {
                                $record->stocks()->updateOrCreate(
                                    ['location_id' => $stock['location_id']],
                                    [
                                        'quantity' => $stock['quantity'],
                                        'seller_variant_id' => $stock['seller_variant_id'],
                                        'seller_product_id' => $this->getOwnerRecord()->id,
                                        'seller_id' => Filament::getTenant()->id
                                    ]
                                );
                            }
                        }

                        return $record;
                    }),
                Tables\Actions\DeleteAction::make()
                    ->before(function (Model $record) {
                        $record->stocks()->delete();
                        $record->prices()->delete();
                    })
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([]),
            ]);
    }
}
