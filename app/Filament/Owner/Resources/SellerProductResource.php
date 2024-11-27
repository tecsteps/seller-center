<?php

namespace App\Filament\Owner\Resources;

use App\Filament\Owner\Resources\SellerProductResource\Pages;
use App\Filament\Owner\Resources\SellerProductResource\RelationManagers;
use App\Models\SellerProduct;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SellerProductResource extends Resource
{
    protected static ?string $model = SellerProduct::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationGroup(): ?string
    {
        return 'Sellers';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required(),
                TextInput::make('sku')
                    ->label('SKU'),
                Textarea::make('description')
                    ->columnSpanFull(),
                Textarea::make('attributes')
                    ->columnSpanFull(),
                Select::make('category_id')
                    ->relationship('category', 'name'),
                Select::make('seller_id')
                    ->relationship('seller', 'name')
                    ->required(),
                TextInput::make('status')
                    ->required(),
            ]);
    }

    public static function infolist(Infolists\Infolist $infolist): Infolists\Infolist
    {
        $priceEntries = [];
        $currencies = \App\Models\Currency::all();

        foreach ($currencies as $currency) {
            $priceEntries[] = Infolists\Components\TextEntry::make('prices')
                ->label($currency->name)
                ->formatStateUsing(function ($state, $record) use ($currency) {
                    $price = $record->prices->where('currency_id', $currency->id)
                        ->whereNull('seller_variant_id')
                        ->first();
                    return $price ? "{$currency->symbol}{$price->amount}" : '-';
                });
        }

        return $infolist
            ->schema([
                Infolists\Components\Section::make('Product Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->weight(FontWeight::Bold),
                        Infolists\Components\TextEntry::make('brand'),
                        Infolists\Components\TextEntry::make('sku')
                            ->label('SKU'),
                        Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'active' => 'success',
                                'draft' => 'gray',
                                'delisted' => 'danger',
                                default => 'gray',
                            }),
                        Infolists\Components\TextEntry::make('seller.name')
                            ->label('Seller'),
                        Infolists\Components\TextEntry::make('category.name')
                            ->label('Category'),
                        Infolists\Components\TextEntry::make('description')
                            ->markdown()
                            ->columnSpan(2),
                    ])
                    ->collapsible()
                    ->columns(2),



                Infolists\Components\Section::make('Attributes')
                    ->schema([
                        Infolists\Components\KeyValueEntry::make('attributes')
                            ->getStateUsing(function ($record) {
                                return collect($record->attributes ?? [])->map(function ($value) {
                                    return is_array($value) ? implode(', ', $value) : $value;
                                })->toArray();
                            })
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }



    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('brand')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('seller.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->searchable(),
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
                Tables\Filters\SelectFilter::make('seller')
                    ->relationship('seller', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ImagesRelationManager::class,
            RelationManagers\PricesRelationManager::class,
            RelationManagers\StocksRelationManager::class,
        ];
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSellerProducts::route('/'),
            'view' => Pages\ViewSellerProduct::route('/{record}'),
        ];
    }
}
