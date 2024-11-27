<?php

namespace App\Filament\Owner\Resources\ApplicationsResource\Pages;

use App\Filament\Owner\Resources\ApplicationsResource;
use App\Models\Message;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditApplications extends EditRecord
{
    protected static string $resource = ApplicationsResource::class;

    protected function getHeaderActions(): array
    {
        return [


            Actions\Action::make('Accept')
                ->label('Accept')
                ->icon('heroicon-o-check')
                ->color('success')
                ->requiresConfirmation()
                ->hidden(fn() => $this->record->seller->partnership?->status !== 'submitted')
                ->action(function () {
                    $partnership = $this->record->seller->partnership;
                    $partnership->status = 'accepted';
                    $partnership->save();

                    // Create acceptance message for the seller
                    Message::create([
                        'seller_id' => $this->record->seller->id,
                        'content' => 'Congratulations! Your seller application has been accepted. You can now start setting up your store and listing your products.',
                    ]);
                }),

            Actions\Action::make('Reject')
                ->label('Reject')
                ->icon('heroicon-o-x-mark')
                ->color('danger')
                ->requiresConfirmation()
                ->hidden(fn() => $this->record->seller->partnership?->status !== 'submitted')
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
                    $partnership = $this->record->seller->partnership;
                    $partnership->status = 'rejected';
                    $partnership->rejection_reason = $data['rejection_reason'];
                    $partnership->save();

                    // Create rejection message for the seller
                    Message::create([
                        'seller_id' => $this->record->seller->id,
                        'content' => "Your seller application has been rejected.\n\nReason: {$data['rejection_reason']}",
                    ]);
                }),
        ];
    }
}
