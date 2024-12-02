<?php

namespace App\Filament\Owner\Resources;

use App\Filament\Owner\Resources\GoldenProductResource\Pages;
use App\Filament\Owner\Resources\GoldenProductResource\RelationManagers;
use App\Models\GoldenProduct;
use App\Models\ProductType;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;

class GoldenProductResource extends Resource
{
    protected static ?string $model = GoldenProduct::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationGroup = 'Catalog';

    protected static ?int $navigationSort = 20;

    protected static ?int $navigationGroupSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('General Information')
                    ->description('Basic product information and categorization')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required(),
                        Forms\Components\Textarea::make('description')
                            ->columnSpanFull(),
                        Forms\Components\Select::make('product_type_id')
                            ->relationship('productType', 'name')
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                $productTypeId = $get('product_type_id');
                                if (!$productTypeId) {
                                    $set('attributes', null);
                                    return;
                                }

                                $productType = ProductType::with('attributes')->find($productTypeId);
                                if (!$productType) return;

                                // Initialize empty attributes array
                                $attributes = collect($get('attributes') ?? []);
                                
                                // Update attributes structure based on product type
                                $newAttributes = $productType->attributes->mapWithKeys(function ($attribute) use ($attributes) {
                                    return [$attribute->name => $attributes[$attribute->name] ?? null];
                                })->toArray();
                                
                                $set('attributes', $newAttributes);
                            })
                            ->required(),
                    ]),
                Section::make('Attributes')
                    ->schema(fn (Get $get): array => static::getAttributeFields($get('product_type_id')))
                    ->columns(2)
                    ->visible(fn (Get $get): bool => (bool) $get('product_type_id')),
            ]);
    }

    protected static function getAttributeFields(?string $productTypeId): array
    {
        if (!$productTypeId) return [];

        $productType = ProductType::with('attributes')->find($productTypeId);
        if (!$productType) return [];

        return $productType->attributes
            ->sortBy('rank')
            ->map(function ($attribute) {
                $field = match ($attribute->field) {
                    'TextInput' => Forms\Components\TextInput::make("attributes.{$attribute->name}")
                        ->label($attribute->name)
                        ->helperText($attribute->description)
                        ->required($attribute->required),
                        
                    'Select' => Forms\Components\Select::make("attributes.{$attribute->name}")
                        ->label($attribute->name)
                        ->options(collect($attribute->options)->pluck('value', 'value')->toArray())
                        ->helperText($attribute->description)
                        ->native(false)
                        ->required($attribute->required),
                        
                    'ColorPicker' => Forms\Components\ColorPicker::make("attributes.{$attribute->name}")
                        ->label($attribute->name)
                        ->helperText($attribute->description)
                        ->required($attribute->required),
                        
                    default => Forms\Components\TextInput::make("attributes.{$attribute->name}")
                        ->label($attribute->name)
                        ->helperText($attribute->description)
                        ->required($attribute->required),
                };

                if ($attribute->validators) {
                    $validators = $attribute->validators ?? [];
                    
                    if ($attribute->type === 'number') {
                        if (isset($validators['min'])) {
                            $field->minValue((float) $validators['min']);
                        }
                        if (isset($validators['max'])) {
                            $field->maxValue((float) $validators['max']);
                        }
                        if (isset($validators['decimal_places'])) {
                            $field->numeric()->step(pow(0.1, (int) $validators['decimal_places']));
                        }
                    }
                    
                    if ($attribute->type === 'text') {
                        if (isset($validators['min_length'])) {
                            $field->minLength((int) $validators['min_length']);
                        }
                        if (isset($validators['max_length'])) {
                            $field->maxLength((int) $validators['max_length']);
                        }
                        if (isset($validators['pattern'])) {
                            $field->regex($validators['pattern']);
                        }
                    }
                }

                return $field;
            })
            ->toArray();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('productType.name')
                    ->numeric()
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGoldenProducts::route('/'),
            'create' => Pages\CreateGoldenProduct::route('/create'),
            'edit' => Pages\EditGoldenProduct::route('/{record}/edit'),
        ];
    }
}
