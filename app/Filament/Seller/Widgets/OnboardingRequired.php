<?php

namespace App\Filament\Seller\Widgets;

use Filament\Facades\Filament;
use Filament\Widgets\Widget;

class OnboardingRequired extends Widget
{
    protected static string $view = 'filament.seller.widgets.onboarding-required';

    protected function getViewData(): array
    {
        return [
            'tenantId' => Filament::getTenant()->id,
        ];
    }
}
