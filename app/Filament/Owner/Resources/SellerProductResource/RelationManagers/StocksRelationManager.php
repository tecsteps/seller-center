<?php

namespace App\Filament\Owner\Resources\SellerProductResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use App\Models\Stock;

class StocksRelationManager extends RelationManager
{
    protected static string $relationship = 'sellerVariants';

    protected static ?string $title = 'Stock Levels';

    public function table(Table $table): Table
    {
        $locations = $this->getOwnerRecord()->seller->locations;

        $columns = [
            Tables\Columns\TextColumn::make('name')
                ->label('Variant')
                ->getStateUsing(function ($record) {
                    return $record->name ?? 'Default';
                })
                ->sortable(),
        ];

        // Add a column for each location
        foreach ($locations as $location) {
            $columns[] = Tables\Columns\TextColumn::make("location_{$location->id}")
                ->label($location->name)
                ->getStateUsing(function ($record) use ($location) {
                    return Stock::where('seller_variant_id', $record->id)
                        ->where('location_id', $location->id)
                        ->value('quantity') ?? '-';
                })
                ->alignCenter()
                ->sortable();
        }

        // Add total stock column
        $columns[] = Tables\Columns\TextColumn::make('total_stock')
            ->label('Total Stock')
            ->getStateUsing(function ($record) {
                $total = Stock::where('seller_variant_id', $record->id)
                    ->sum('quantity');
                return $total ?: '-';
            })
            ->alignCenter()
            ->sortable()
            ->color('success')
            ->weight('bold');

        return $table
            ->columns($columns)
            ->defaultSort('name', 'asc');
    }
}
