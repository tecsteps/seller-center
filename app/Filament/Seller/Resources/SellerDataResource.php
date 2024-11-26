<?php

namespace App\Filament\Seller\Resources;

use App\Enums\Countries;
use App\Filament\Seller\Resources\SellerDataResource\Pages;
use App\Filament\Seller\Resources\SellerDataResource\RelationManagers;
use App\Models\SellerData;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Toggle;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;


class SellerDataResource extends Resource
{
    protected static ?string $model = SellerData::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'My Data';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->description('Primary seller account details')
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->label('Primary Contact Email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->default(fn() => auth()->user()->email),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->required()
                            ->maxLength(20),
                    ])->columns(3),

                Forms\Components\Section::make('Company Information')
                    ->description('Basic company details')
                    ->schema([
                        Forms\Components\TextInput::make('company_name')
                            ->label('Company Name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->label('Business Description')
                            ->required()
                            ->helperText('Describe your business and what products/services you plan to offer')
                            ->maxLength(1000)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Address Information')
                    ->description('Registered business address')
                    ->schema([
                        Forms\Components\TextInput::make('address_line1')
                            ->label('Street Address')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('address_line2')
                            ->label('Additional Address Details')
                            ->maxLength(255),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('city')
                                    ->required()
                                    ->maxLength(100),
                                Forms\Components\TextInput::make('state')
                                    ->required()
                                    ->label('State/Province/Region')
                                    ->maxLength(100),
                                Forms\Components\TextInput::make('postal_code')
                                    ->required()
                                    ->maxLength(20),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('country_code')
                                    ->label('Country')
                                    ->required()
                                    ->options(Countries::LIST)
                                    ->native(false)
                                    ->searchable(),
                            ]),
                    ]),

                Forms\Components\Section::make('Tax Information')
                    ->description('Company tax registration details (at least one tax number required)')
                    ->schema([
                        Forms\Components\TextInput::make('vat')
                            ->label('VAT Number')
                            ->helperText('Value Added Tax identification number'),
                        // ->requiredWithout('vat,tin,eori'), TODO NOT WORKING
                        Forms\Components\TextInput::make('tin')
                            ->label('Tax ID Number')
                            ->helperText('Tax Identification Number'),
                        // ->requiredWithout('tin,vat,eori') TODO NOT WORKING
                        Forms\Components\TextInput::make('eori')
                            ->label('EORI Number')
                            ->helperText('Economic Operators Registration and Identification number'),
                        // ->requiredWithout('eori,vat,tin'),  TODO NOT WORKING
                    ])->columns(3),

                Forms\Components\Section::make('Banking Information')
                    ->description('Company bank account details')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('iban')
                                    ->label('IBAN')
                                    ->required()
                                    ->maxLength(34)
                                    ->helperText('International Bank Account Number'),
                                Forms\Components\TextInput::make('swift_bic')
                                    ->label('SWIFT/BIC')
                                    ->required()
                                    ->maxLength(11),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('bank_name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('account_holder_name')
                                    ->required()
                                    ->maxLength(255),
                            ]),
                    ]),

                Forms\Components\Section::make('Legal Documents')
                    ->description('Upload your Certificate of Incorporation or Trade Registry documents')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\FileUpload::make('file1')
                                    ->label('Certificate of Incorporation')
                                    ->helperText('Official document proving company registration')
                                    ->acceptedFileTypes(['application/pdf', 'image/*'])
                                    ->maxSize(10240)
                                    ->directory('seller-documents')
                                    ->openable(),

                                Forms\Components\FileUpload::make('file2')
                                    ->label('Trade Registry Extract')
                                    ->helperText('Recent extract from trade/commerce registry')
                                    ->acceptedFileTypes(['application/pdf', 'image/*'])
                                    ->maxSize(10240)
                                    ->directory('seller-documents')
                                    ->openable(),

                                Forms\Components\FileUpload::make('file3')
                                    ->label('Additional Documentation')
                                    ->helperText('Any other relevant business documentation')
                                    ->acceptedFileTypes(['application/pdf', 'image/*'])
                                    ->maxSize(10240)
                                    ->directory('seller-documents')
                                    ->openable(),
                            ]),
                    ])
                    ->collapsible(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('seller.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('company_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address_line1')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address_line2')
                    ->searchable(),
                Tables\Columns\TextColumn::make('city')
                    ->searchable(),
                Tables\Columns\TextColumn::make('state')
                    ->searchable(),
                Tables\Columns\TextColumn::make('postal_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('country_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('vat')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tin')
                    ->searchable(),
                Tables\Columns\TextColumn::make('eori')
                    ->searchable(),
                Tables\Columns\TextColumn::make('iban')
                    ->searchable(),
                Tables\Columns\TextColumn::make('swift_bic')
                    ->searchable(),
                Tables\Columns\TextColumn::make('bank_name')
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
            'index' => Pages\ListSellerData::route('/'),
            'create' => Pages\CreateSellerData::route('/create'),
            'edit' => Pages\EditSellerData::route('/{record}/edit'),
        ];
    }
}
