<?php

namespace App\Filament\Resources\CategoryResource\RelationManagers;

use App\Models\SellerProduct;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Resources\SellerProductResource;

class SellerProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'sellerProducts';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $title = 'Products';


    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                ->url(fn ($record) => SellerProductResource::getUrl('edit', ['record' => $record]))
                ->openUrlInNewTab(false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
} 