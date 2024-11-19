<?php

namespace App\Filament\Seller\Resources\SellerVariantResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Location;

class StocksRelationManager extends RelationManager
{
    protected static string $relationship = 'stocks';
    protected static ?string $title = 'Inventory';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('location_id')
                    ->relationship(
                        'location', 
                        'name',
                        fn ($query, $record) => $query->whereNotIn(
                            'id', 
                            $this->getOwnerRecord()->stocks->pluck('location_id')->toArray()
                        )
                    )
                    ->default(function() {
                        $availableLocations = Location::whereNotIn(
                            'id',
                            $this->getOwnerRecord()->stocks->pluck('location_id')->toArray()
                        )->get();

                        if ($availableLocations->count() === 1) {
                            return $availableLocations->first()->id;
                        }
                        return null;
                    })
                    ->required(),
                Forms\Components\TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->integer(),
                Forms\Components\TextInput::make('reserved')
                    ->numeric()
                    ->integer(),
                Forms\Components\TextInput::make('safety_stock')
                    ->numeric()
                    ->integer(),
            ]);
    }

    public function table(Table $table): Table
    {
        $allLocationsUsed = Location::count() <= $this->getOwnerRecord()->stocks()->count();

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('location.name'),
                Tables\Columns\TextInputColumn::make('quantity')
                    ->type('number')
                    ->rules(['integer', 'min:0'])
                    ->sortable(),
                Tables\Columns\TextInputColumn::make('reserved')
                    ->type('number')
                    ->rules(['integer', 'min:0'])
                    ->sortable(),
                Tables\Columns\TextInputColumn::make('safety_stock')
                    ->type('number')
                    ->rules(['integer', 'min:0'])
                    ->sortable(),
                Tables\Columns\TextColumn::make('availability')
                    ->state(function ($record) {
                        return $record->quantity - $record->reserved - $record->safety_stock;
                    })
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'success' : 'danger')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->visible(!$allLocationsUsed),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
} 