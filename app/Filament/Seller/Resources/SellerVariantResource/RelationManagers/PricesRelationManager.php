<?php

namespace App\Filament\Seller\Resources\SellerVariantResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PricesRelationManager extends RelationManager
{
    protected static string $relationship = 'prices';

    protected static ?string $recordTitleAttribute = 'value';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('currency_id')
                    ->relationship('currency', 'name')
                    ->disabled()
                    ->required(),
                Forms\Components\TextInput::make('amount')
                    ->numeric()
                    ->prefix('$')
                    ->required()
                    ->step(0.01)
                    ->formatStateUsing(fn ($state) => number_format($state / 100, 2))
                    ->dehydrateStateUsing(fn ($state) => (float)str_replace(',', '', $state) * 100)
                    ->minValue(0),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('currency.name'),
                Tables\Columns\TextColumn::make('amount')
                    ->money(fn ($record) => $record->currency->code, 100)
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }
} 