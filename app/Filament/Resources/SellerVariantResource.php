<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SellerVariantResource\Pages;
use App\Filament\Resources\SellerVariantResource\RelationManagers;
use App\Models\SellerVariant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SellerVariantResource extends Resource
{
    protected static ?string $model = SellerVariant::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    
    protected static ?string $navigationLabel = 'Variants';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\Section::make('Variant Details')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->helperText('The display name for this variant'),
                    Forms\Components\Select::make('seller_product_id')
                        ->relationship('sellerProduct', 'name')
                        ->disabled()
                        ->required()
                        ->helperText('The product this variant belongs to. This cannot be changed once created.')
                        ->suffixAction(
                            Forms\Components\Actions\Action::make('viewProduct')
                                ->icon('heroicon-m-arrow-top-right-on-square')
                                ->url(fn ($record) => SellerProductResource::getUrl('edit', ['record' => $record->seller_product_id]))
                        ),
                    Forms\Components\TextInput::make('sku')
                        ->label('SKU')
                        ->helperText('Stock Keeping Unit - A unique identifier for this variant'),
                    Forms\Components\Textarea::make('description')
                        ->columnSpanFull()
                        ->helperText('Detailed description of this specific variant which overrides the product description'),
                    Forms\Components\KeyValue::make('attributes')
                        ->columnSpanFull()
                        ->helperText('Custom attributes for this variant (e.g. Color: Red, Size: Large)')
                        ->keyLabel('Key')
                        ->valueLabel('Value')
                        ->dehydrateStateUsing(fn ($state) => is_array($state) ? $state : [])
                        ->reorderable()
                        ->editableKeys()
                        ->editableValues(),
                    Forms\Components\Select::make('status_id')
                        ->relationship('status', 'name')
                        ->native(false)
                        ->required()
                        ->helperText('Current status of this variant (draft, pending approval, etc.)'),
                ])
                ->columns(2)
        ]);
   
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sellerProduct.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status.name')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'pending_approval' => 'warning',
                        'approved' => 'success', 
                        'rejected' => 'danger',
                        'active' => 'success',
                        'inactive' => 'gray',
                        'archived' => 'danger'
                    })
                    ->sortable(),
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
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PricesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSellerVariants::route('/'),
            'create' => Pages\CreateSellerVariant::route('/create'),
            'edit' => Pages\EditSellerVariant::route('/{record}/edit'),
        ];
    }

    // protected function mutateFormDataBeforeSave(array $data): array
    // {
    //     dd($data); // This will stop execution and show the data
        
    //     return $data;
    // }
}
