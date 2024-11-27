<?php

namespace App\Filament\Owner\Resources\SellerProductResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Table;

class ImagesRelationManager extends RelationManager
{
    protected static string $relationship = 'images';

    protected static ?string $title = 'Images';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('variant_name')
                    ->label('Variant')
                    ->getStateUsing(function ($record) {
                        return $record->seller_variant_id
                            ? $record->sellerVariant->name
                            : 'Default Image';
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('image')
                    ->label('URL')
                    ->size(100),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Added')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('seller_variant_id')
            ->groups([
                Tables\Grouping\Group::make('variant_name')
                    ->label('Variant')
                    ->getTitleFromRecordUsing(fn ($record) => $record->seller_variant_id 
                        ? ($record->sellerVariant->name ?? 'Unknown') 
                        : 'Default Image')
                    ->collapsible(),
            ])
            ->defaultGroup('variant_name')
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->form([
                        Forms\Components\TextInput::make('url')
                            ->label('Image URL')
                            ->required()
                            ->url(),
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->form([
                        Forms\Components\TextInput::make('url')
                            ->label('Image URL')
                            ->required()
                            ->url(),
                    ]),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
