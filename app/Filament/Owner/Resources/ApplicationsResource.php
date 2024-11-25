<?php

namespace App\Filament\Owner\Resources;

use App\Filament\Owner\Resources\ApplicationsResource\Pages;
use App\Filament\Seller\Resources\SellerDataResource;
use App\Models\Seller;
use App\Models\SellerData;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Enums\Countries;
use Filament\Forms;

class ApplicationsResource extends Resource
{
    protected static ?string $model = Seller::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Applications';

    protected static bool $isScopedToTenant = false;

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes()
            ->with([
                'users'
            ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Application Status')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->native(false)
                            ->options([
                                'submitted' => 'Submitted',
                                'accepted' => 'Accepted',
                                'rejected' => 'Rejected',
                                'review' => 'Review'
                            ]),
                        Forms\Components\TextInput::make('email')
                            ->label('Primary Contact Email')
                            ->disabled(),
                        Forms\Components\DateTimePicker::make('created_at')
                            ->label('Application Date')
                            ->disabled(),
                    ])->columns(3),

                Forms\Components\Section::make('Company Information')
                    ->description('Basic company details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Legal Entity Name')
                            ->disabled(),
                        Forms\Components\TextInput::make('company_name')
                            ->label('Company Name')
                            ->disabled(),
                        Forms\Components\Textarea::make('description')
                            ->label('Business Description')
                            ->disabled()
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Address Information')
                    ->description('Registered business address')
                    ->schema([
                        Forms\Components\TextInput::make('address_line1')
                            ->label('Street Address')
                            ->disabled(),
                        Forms\Components\TextInput::make('address_line2')
                            ->label('Additional Address Details')
                            ->disabled(),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('city')
                                    ->disabled(),
                                Forms\Components\TextInput::make('state')
                                    ->label('State/Province/Region')
                                    ->disabled(),
                                Forms\Components\TextInput::make('postal_code')
                                    ->disabled(),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('country_code')
                                    ->label('Country')
                                    ->formatStateUsing(fn($state) => Countries::getName($state))
                                    ->disabled(),
                                Forms\Components\TextInput::make('phone')
                                    ->label('Phone Number')
                                    ->disabled(),
                            ]),
                    ]),

                Forms\Components\Section::make('Tax Information')
                    ->description('Company tax registration details')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('vat')
                                    ->label('VAT Number')
                                    ->disabled(),
                                Forms\Components\TextInput::make('tin')
                                    ->label('Tax ID Number')
                                    ->disabled(),
                                Forms\Components\TextInput::make('eori')
                                    ->label('EORI Number')
                                    ->disabled()
                                    ->helperText('Economic Owner Registration and Identification number'),
                            ]),
                    ]),

                Forms\Components\Section::make('Banking Information')
                    ->description('Company bank account details')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('iban')
                                    ->label('IBAN')
                                    ->disabled(),
                                Forms\Components\TextInput::make('swift_bic')
                                    ->label('SWIFT/BIC')
                                    ->disabled(),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('bank_name')
                                    ->disabled(),
                                Forms\Components\TextInput::make('account_holder_name')
                                    ->disabled(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'open' => 'gray',
                        'submitted' => 'info',
                        'accepted' => 'success',
                        'rejected' => 'danger',
                        'review' => 'warning',
                    }),
                Tables\Columns\TextColumn::make('country_code')
                    ->formatStateUsing(fn($state) => Countries::getName($state))
                    ->label('Country'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Review')
                    ->icon('heroicon-m-magnifying-glass'),
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
            'index' => Pages\ListApplications::route('/'),
            'create' => Pages\CreateApplications::route('/create'),
            'edit' => Pages\EditApplications::route('/{record}/edit'),
        ];
    }
}
