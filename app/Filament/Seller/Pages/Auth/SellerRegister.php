<?php

namespace App\Filament\Seller\Pages\Auth;

use App\Models\Seller;
use Filament\Pages\Auth\Register as BaseRegister;

class SellerRegister extends BaseRegister
{
    protected function handleRegistration(array $data): \App\Models\User
    {
        $user = parent::handleRegistration($data);

        $user->is_seller = true;
        $user->save();

        $seller = Seller::create(['user_id' => $user->id]);

        return $user;
    }
}
