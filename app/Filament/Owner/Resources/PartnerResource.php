<?php

namespace App\Filament\Owner\Resources;

use App\Filament\Owner\Resources\PartnerResource\Pages;
use App\Filament\Owner\Resources\PartnerResource\RelationManagers;
use App\Models\Partner;
use App\Models\Seller;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PartnerResource extends Resource
{
    protected static ?string $model = Seller::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';


    protected static ?string $navigationLabel = 'Partnerships';

    protected static ?string $modelLabel = 'Partnerships';

    protected static ?string $pluralModelLabel = 'Partnerships';

    public static function getNavigationGroup(): ?string
    {
        return 'Sellers';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                Seller::query()->whereHas('partnership', function ($query) {
                    $query->where('status', 'accepted');
                })
            )
            ->columns([
                Tables\Columns\TextColumn::make('sellerData.company_name')
                    ->label('Company Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('seller_products_count')
                    ->label('Products')
                    ->counts('sellerProducts')
                    ->sortable()
                    ->url(
                        fn(Seller $record): string =>
                        SellerProductResource::getUrl('index', ['tableFilters[seller][value]' => $record->id])
                    )
                    ->color('primary'),
                Tables\Columns\ToggleColumn::make('partnership.select_all_products')
                    ->label('Select All Products')
                    ->onColor('success')
                    ->offColor('danger')

            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPartners::route('/'),
            'create' => Pages\CreatePartner::route('/create'),
            'edit' => Pages\EditPartner::route('/{record}/edit'),
        ];
    }
}
