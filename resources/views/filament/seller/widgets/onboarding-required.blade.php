<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center text-warning-500 gap-x-4">
            <x-heroicon-o-exclamation-triangle class="w-5 h-5" />
            <span class="text-primary-400">
                Please complete your onboarding process.
                <a href="/seller/{{ $tenantId }}/seller-datas" class="text-primary-500 hover:text-primary-600 underline">
                    Go to your profile
                </a>
                to finish the setup.
            </span>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>