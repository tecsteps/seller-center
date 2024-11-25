<?php

namespace App\Filament\Pages\Tenancy;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\EditTenantProfile;
use App\Enums\Countries;
use App\Filament\Seller\Resources\SellerResource\Pages;
use App\Filament\Seller\Resources\SellerResource\RelationManagers;
use App\Models\Seller;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EditSellerProfile extends EditTenantProfile
{
    public static function getLabel(): string
    {
        return 'Seller profile';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->description('Primary seller account details')
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->label('Primary Contact Email')
                            ->email()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(20),
                    ])->columns(3),

                Forms\Components\Section::make('Company Information')
                    ->description('Basic company details')
                    ->schema([
                        Forms\Components\TextInput::make('company_name')
                            ->label('Company Name')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->label('Business Description')
                            ->helperText('Describe your business and what products/services you plan to offer')
                            ->maxLength(1000)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Address Information')
                    ->description('Registered business address')
                    ->schema([
                        Forms\Components\TextInput::make('address_line1')
                            ->label('Street Address')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('address_line2')
                            ->label('Additional Address Details')
                            ->maxLength(255),
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('city')
                                    ->maxLength(100),
                                Forms\Components\TextInput::make('state')
                                    ->label('State/Province/Region')
                                    ->maxLength(100),
                                Forms\Components\TextInput::make('postal_code')
                                    ->maxLength(20),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('country_code')
                                    ->label('Country')
                                    ->options(Countries::LIST)
                                    ->native(false)
                                    ->searchable(),
                            ]),
                    ]),

                Forms\Components\Section::make('Tax Information')
                    ->description('Company tax registration details')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('vat')
                                    ->label('VAT Number')
                                    ->helperText('Value Added Tax identification number'),
                                Forms\Components\TextInput::make('tin')
                                    ->label('Tax ID Number')
                                    ->helperText('Tax Identification Number'),
                                Forms\Components\TextInput::make('eori')
                                    ->label('EORI Number')
                                    ->helperText('Economic Operators Registration and Identification number'),
                            ]),
                    ]),

                Forms\Components\Section::make('Banking Information')
                    ->description('Company bank account details')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('iban')
                                    ->label('IBAN')
                                    ->maxLength(34)
                                    ->helperText('International Bank Account Number'),
                                Forms\Components\TextInput::make('swift_bic')
                                    ->label('SWIFT/BIC')
                                    ->maxLength(11),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('bank_name')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('account_holder_name')
                                    ->maxLength(255),
                            ]),
                    ]),
            ]);
    }
}
