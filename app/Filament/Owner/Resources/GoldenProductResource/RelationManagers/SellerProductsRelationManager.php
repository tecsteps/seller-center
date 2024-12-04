<?php

namespace App\Filament\Owner\Resources\GoldenProductResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Model;
use App\Models\SellerVariant;
use App\Models\Currency;
use App\Models\Location;

class SellerProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'sellerProducts';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        return $form
            ->schema(function ($record) {
                $schema = [
                    Section::make('General Product Details')
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('brand')
                                ->maxLength(255),
                            Forms\Components\TextInput::make('sku')
                                ->maxLength(255),
                            Forms\Components\Textarea::make('description')
                                ->columnSpan(2)
                                ->maxLength(65535),
                            Forms\Components\KeyValue::make('attributes')
                                ->columnSpan(2)
                                ->reorderable()
                                ->editableKeys(false)
                                ->editableValues(false),
                            Section::make('Default Prices')
                                ->schema(function () use ($record) {
                                    $allCurrencies = Currency::all();
                                    return $allCurrencies->map(function ($currency) use ($record) {
                                        $price = $record ? $record->prices()->where('currency_id', $currency->id)->whereNull('seller_variant_id')->first() : null;
                                        return Forms\Components\TextInput::make("price_{$currency->id}")
                                            ->label("{$currency->code} Price")
                                            ->disabled()
                                            ->prefix($currency->symbol)
                                            ->afterStateHydrated(function (Forms\Components\TextInput $component) use ($price) {
                                                $component->state($price?->amount);
                                            });
                                    })->toArray();
                                })
                                ->collapsible()
                                ->collapsed()
                                ->columns(3)
                                ->columnSpanFull(),
                            Section::make('Images')
                                ->schema([
                                    Forms\Components\Repeater::make('images')
                                        ->label('Product Images')
                                        ->relationship(
                                            'images',
                                            modifyQueryUsing: fn($query) => $query->whereNull('seller_variant_id')
                                        )
                                        ->disabled()
                                        ->schema([
                                            Forms\Components\TextInput::make('image')
                                                ->label('Image URL')
                                                ->url()
                                                ->disabled(),
                                        ])
                                        ->columns(1),
                                ])
                                ->collapsible()
                                ->collapsed()
                                ->columnSpanFull(),
                        ])
                        ->collapsible()
                        ->collapsed()
                        ->columns(2),
                ];

                if ($record) {
                    $variants = SellerVariant::where('seller_product_id', $record->id)->get();
                    $allCurrencies = Currency::all();

                    $schema[] = Forms\Components\Placeholder::make('variants_explanation')
                        ->content('Below you will find all variants of this seller product. Each variant represents a specific version or configuration of the product (e.g., different sizes, colors, or other attributes). Variants have their own pricing, stock levels, and images.')
                        ->extraAttributes(['class' => 'text-gray-500'])
                        ->columnSpanFull();

                    $variantSections = $variants->map(
                        fn($variant) =>
                        Section::make($variant->name ?? "Variant {$variant->id}")
                            ->description("SKU: {$variant->sku}")
                            ->schema([
                                Forms\Components\TextInput::make("variant_{$variant->id}_name")
                                    ->label('Name')
                                    ->helperText('The display name for this variant')
                                    ->disabled(),

                                Forms\Components\TextInput::make("variant_{$variant->id}_sku")
                                    ->label('SKU')
                                    ->helperText('Stock Keeping Unit - A unique identifier for this variant')
                                    ->disabled(),

                                Forms\Components\Select::make("variant_{$variant->id}_status")
                                    ->label('Status')
                                    ->options([
                                        'draft' => 'Draft',
                                        'active' => 'Active',
                                        'delisted' => 'Delisted'
                                    ])
                                    ->disabled()
                                    ->afterStateHydrated(function (Forms\Components\Select $component) use ($variant) {
                                        $component->state($variant->status);
                                    }),

                                Forms\Components\Textarea::make("variant_{$variant->id}_description")
                                    ->label('Description')
                                    ->helperText('Detailed description of this specific variant')
                                    ->columnSpanFull()
                                    ->disabled()
                                    ->afterStateHydrated(function (Forms\Components\Textarea $component) use ($variant) {
                                        $component->state($variant->description);
                                    }),

                                Forms\Components\KeyValue::make("variant_{$variant->id}_attributes")
                                    ->label('Attributes')
                                    ->helperText('Custom attributes for this variant (e.g. Color: Red, Size: Large)')
                                    ->columnSpanFull()
                                    ->reorderable()
                                    ->disabled()
                                    ->afterStateHydrated(function (Forms\Components\KeyValue $component) use ($variant) {
                                        $component->state($variant->attributes);
                                    }),

                                Section::make('Pricing')
                                    ->schema(
                                        $allCurrencies->map(function ($currency) use ($variant) {
                                            $price = $variant->prices()->where('currency_id', $currency->id)->first();
                                            return Forms\Components\TextInput::make("variant_{$variant->id}_price_{$currency->id}")
                                                ->label("{$currency->code} Price")
                                                ->disabled()
                                                ->prefix($currency->symbol)
                                                ->afterStateHydrated(function (Forms\Components\TextInput $component) use ($price) {
                                                    $component->state($price?->amount);
                                                });
                                        })->toArray()
                                    )
                                    ->collapsible()
                                    ->collapsed()
                                    ->columns(3),

                                Section::make('Stock')
                                    ->schema(function () use ($variant) {
                                        $locations = Location::where('seller_id', $variant->seller_id)->get();

                                        if ($locations->isEmpty()) {
                                            return [
                                                Forms\Components\Placeholder::make('no_locations')
                                                    ->content('No warehouse locations configured')
                                                    ->extraAttributes(['class' => 'text-warning-600'])
                                            ];
                                        }

                                        return $locations->map(function ($location) use ($variant) {
                                            $stock = $variant->stocks()->where('location_id', $location->id)->first();
                                            return Forms\Components\TextInput::make("variant_{$variant->id}_stock_{$location->id}")
                                                ->label("Stock at {$location->name}")
                                                ->disabled()
                                                ->afterStateHydrated(function (Forms\Components\TextInput $component) use ($stock) {
                                                    $component->state($stock?->quantity);
                                                });
                                        })->toArray();
                                    })
                                    ->collapsible()
                                    ->collapsed()
                                    ->columns(3),

                                Section::make('Images')
                                    ->schema([
                                        Forms\Components\Repeater::make("variant_{$variant->id}_images")
                                            ->label('Additional images for this variant')
                                            ->disabled()
                                            ->relationship(
                                                'images',
                                                modifyQueryUsing: fn($query) => $query->where('seller_variant_id', $variant->id)
                                            )
                                            ->schema([
                                                Forms\Components\TextInput::make('image')
                                                    ->label('Image URL')
                                                    ->url()
                                                    ->disabled(),
                                            ])
                                            ->columns(1),
                                    ])
                                    ->collapsible()
                                    ->collapsed()
                                    ->columns(1),
                            ])
                            ->collapsible()
                            ->collapsed()
                            ->columns(2)
                    )->toArray();

                    return array_merge($schema, $variantSections);
                }

                return $schema;
            });
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('brand')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sku')
                    ->searchable(),
                Tables\Columns\TextColumn::make('seller.name')
                    ->label('Seller')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                //
            ]);
    }
}
