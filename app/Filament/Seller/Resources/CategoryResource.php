<?php

namespace App\Filament\Seller\Resources;

use App\Filament\Seller\Resources\CategoryResource\Pages;
use App\Filament\Seller\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use App\Models\SellerProduct;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static bool $isScopedToTenant = false;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Category Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required(),
                        Forms\Components\Textarea::make('description')
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('is_active'),
                        Forms\Components\Select::make('parent_id')
                            ->relationship('parent', 'name')
                            ->options(function ($record) {
                                // Get all descendant IDs to exclude them along with the current record
                                $excludeIds = [$record?->id ?? 0];
                                if ($record) {
                                    $descendants = Category::where('parent_id', $record->id)->get();
                                    foreach ($descendants as $descendant) {
                                        $excludeIds[] = $descendant->id;
                                        // Get children of descendants recursively
                                        $childIds = Category::where('parent_id', $descendant->id)->pluck('id')->toArray();
                                        $excludeIds = array_merge($excludeIds, $childIds);
                                    }
                                }
                                return Category::whereNotIn('id', $excludeIds)->pluck('name', 'id');
                            }),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('parent.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('seller_products_count')
                    ->counts('sellerProducts')
                    ->label('Products'),
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
            ->actions([])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\SellerProductsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            // 'create' => Pages\CreateCategory::route('/create'),
            // 'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
