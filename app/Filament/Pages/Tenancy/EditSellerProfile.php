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
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Facades\Filament;

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
                Forms\Components\TextInput::make('name')
                    ->label('Company Name')
                    ->required()
                    ->maxLength(255),
            ]);
    }
}
