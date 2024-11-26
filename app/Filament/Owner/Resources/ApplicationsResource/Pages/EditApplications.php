<?php

namespace App\Filament\Owner\Resources\ApplicationsResource\Pages;

use App\Filament\Owner\Resources\ApplicationsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditApplications extends EditRecord
{
    protected static string $resource = ApplicationsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('statusInfo')
                ->label('Waiting for Submission')
                ->icon('heroicon-o-clock')
                ->color('gray')
                ->disabled()
                ->visible(fn () => $this->record->status === 'open')
                ->tooltip('This application is still being prepared and has not been submitted yet'),

            Actions\Action::make('Accept')
                ->label('Accept')
                ->icon('heroicon-o-check')
                ->color('success')
                ->requiresConfirmation()
                ->hidden(fn () => $this->record->status !== 'submitted')
                ->action(function () {
                    $this->record->status = 'accepted';
                    $this->record->save();
                }),
            
            Actions\Action::make('Reject')
                ->label('Reject')
                ->icon('heroicon-o-x-mark')
                ->color('danger')
                ->requiresConfirmation()
                ->hidden(fn () => $this->record->status !== 'submitted')
                ->modalHeading('Reject Application')
                ->modalDescription('Are you sure you want to reject this application? This action cannot be undone.')
                ->form([
                    \Filament\Forms\Components\Textarea::make('rejection_reason')
                        ->label('Rejection Reason')
                        ->required()
                        ->maxLength(1000)
                        ->placeholder('Please provide a reason for rejection'),
                ])
                ->action(function (array $data) {
                    $this->record->status = 'rejected';
                    $this->record->rejection_reason = $data['rejection_reason'];
                    $this->record->save();
                }),
        ];
    }
}
