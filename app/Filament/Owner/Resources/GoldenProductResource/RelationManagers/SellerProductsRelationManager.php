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

class SellerProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'sellerProducts';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Product Details')
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
                        Forms\Components\KeyValue::make('attributes')->columnSpan(2),
                    ])
                    ->columns(2),

                Section::make('Variants')
                    ->schema(function ($record) {
                        if (!$record) {
                            return [];
                        }

                        return SellerVariant::where('seller_product_id', $record->id)
                            ->get()
                            ->map(
                                fn($variant) =>
                                Section::make($variant->name)
                                    ->description("SKU: {$variant->sku}")
                                    ->schema([
                                        Forms\Components\TextInput::make("variant_{$variant->id}_name")
                                            ->label('Name')
                                            ->disabled()
                                            ->afterStateHydrated(function (Forms\Components\TextInput $component) use ($variant) {
                                                $component->state($variant->name);
                                            }),
                                        Forms\Components\TextInput::make("variant_{$variant->id}_sku")
                                            ->label('SKU')
                                            ->disabled()
                                            ->afterStateHydrated(function (Forms\Components\TextInput $component) use ($variant) {
                                                $component->state($variant->sku);
                                            }),
                                        Forms\Components\Textarea::make("variant_{$variant->id}_description")
                                            ->label('Description')
                                            ->disabled()
                                            ->columnSpanFull()
                                            ->afterStateHydrated(function (Forms\Components\Textarea $component) use ($variant) {
                                                $component->state($variant->description);
                                            }),
                                        Forms\Components\KeyValue::make("variant_{$variant->id}_attributes")
                                            ->label('Attributes')
                                            ->disabled()
                                            ->columnSpanFull()
                                            ->afterStateHydrated(function (Forms\Components\KeyValue $component) use ($variant) {
                                                $component->state($variant->attributes);
                                            }),
                                    ])
                                    ->collapsible()
                                    ->collapsed()
                                    ->columns(2)
                            )
                            ->toArray();
                    }),
            ]);
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
