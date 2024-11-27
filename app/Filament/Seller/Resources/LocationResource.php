<?php

namespace App\Filament\Seller\Resources;

use App\Filament\Seller\Resources\LocationResource\Pages;
use App\Filament\Seller\Resources\LocationResource\RelationManagers;
use App\Models\Location;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;

class LocationResource extends Resource
{
    protected static ?string $model = Location::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required(),
                Forms\Components\TextInput::make('address')
                    ->required(),
                Forms\Components\TextInput::make('default_delivery_days')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->searchable(),
                Tables\Columns\TextColumn::make('default_delivery_days')
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
                Tables\Actions\EditAction::make()
                    ->slideOver(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalDescription(fn (Location $record): string => "Are you sure you want to delete this location? This will also remove all stock records associated with this location.")
                    ->modalHeading('Delete Location')
                    ->before(function (Location $record) {
                        // Delete all stocks associated with this location
                        $record->stocks()->delete();
                    })
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalDescription('Are you sure you want to delete these locations? This will also remove all stock records associated with these locations.')
                        ->modalHeading('Delete Locations')
                        ->before(function (Collection $records) {
                            // Delete all stocks associated with these locations
                            foreach ($records as $record) {
                                $record->stocks()->delete();
                            }
                        }),
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
            'index' => Pages\ListLocations::route('/'),
        ];
    }

    public static function getNavigationActions(): array
    {
        return [
            Tables\Actions\CreateAction::make()
                ->slideOver(),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Settings';
    }
}
