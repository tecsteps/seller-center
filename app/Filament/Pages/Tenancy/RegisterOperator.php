<?php

namespace App\Filament\Pages\Tenancy;

use App\Models\Operator;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\RegisterTenant;

class RegisterOperator extends RegisterTenant
{
    public static function getLabel(): string
    {
        return 'Register Operator';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name'),
                // ...
            ]);
    }

    protected function handleRegistration(array $data): Operator
    {
        $operator = Operator::create($data);

        $operator->users()->attach(auth()->user());

        return $operator;
    }
}
