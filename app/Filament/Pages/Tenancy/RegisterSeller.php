<?php

namespace App\Filament\Pages\Tenancy;

use App\Models\Seller;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\RegisterTenant;

class RegisterSeller extends RegisterTenant
{
    public static function getLabel(): string
    {
        return 'Register Seller';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name'),
                // ...
            ]);
    }

    protected function handleRegistration(array $data): Seller
    {
        $seller = Seller::create($data);

        $seller->users()->attach(auth()->user());

        return $seller;
    }
}
