<?php

namespace App\Filament\Owner\Resources\SellerProductResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PricesRelationManager extends RelationManager
{
    protected static string $relationship = 'prices';

    protected static ?string $title = 'Prices';

    public function table(Table $table): Table
    {
        return $table
            ->description('This table shows default prices for each currency and any variant-specific prices that override the defaults.')
            ->columns([
                Tables\Columns\TextColumn::make('variant_name')
                    ->label('Variant')
                    ->getStateUsing(function ($record) {
                        return $record->seller_variant_id
                            ? $record->sellerVariant->name
                            : 'Default Price';
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('currency.name')
                    ->label('Currency')
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->formatStateUsing(fn($state, $record) => "{$record->currency->symbol}{$state}")
                    ->sortable(),
            ])
            ->defaultSort('seller_variant_id', 'asc')
            ->groups([
                Tables\Grouping\Group::make('variant_name')
                    ->getTitleFromRecordUsing(fn($record) => $record->seller_variant_id
                        ? $record->sellerVariant->name
                        : 'Default Price')
                    ->label('Variant')
            ])
            ->defaultGroup('variant_name')
            ->filters([
                Tables\Filters\SelectFilter::make('currency')
                    ->relationship('currency', 'name'),
                Tables\Filters\TernaryFilter::make('is_default')
                    ->label('Price Type')
                    ->placeholder('All Prices')
                    ->trueLabel('Default Prices')
                    ->falseLabel('Variant Prices')
                    ->queries(
                        true: fn($query) => $query->whereNull('seller_variant_id'),
                        false: fn($query) => $query->whereNotNull('seller_variant_id'),
                    ),
            ]);
    }
}
