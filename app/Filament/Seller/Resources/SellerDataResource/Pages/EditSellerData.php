<?php

namespace App\Filament\Seller\Resources\SellerDataResource\Pages;

use App\Filament\Seller\Resources\SellerDataResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditSellerData extends EditRecord
{
    protected static string $resource = SellerDataResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('submit')
                ->label('Submit Application')
                ->icon('heroicon-o-paper-airplane')
                ->color('success')
                ->tooltip('Submit your application for review - this cannot be undone')
                ->requiresConfirmation()
                ->visible(fn() => $this->record->seller->partnership?->status !== 'submitted')
                ->action(function () {
                    $partnership = $this->record->seller->partnership;
                    
                    if (!$partnership) {
                        $partnership = $this->record->seller->partnership()->create([
                            'status' => 'submitted'
                        ]);
                    } else {
                        $partnership->status = 'submitted';
                        $partnership->save();
                    }

                    Notification::make()
                        ->success()
                        ->title('Application Submitted')
                        ->body('Your application has been submitted for review.')
                        ->persistent()
                        ->actions([
                            \Filament\Notifications\Actions\Action::make('view')
                                ->button()
                                ->label('View Application')
                                ->url($this->getResource()::getUrl('edit', ['record' => $this->record->id])),
                        ])
                        ->send();
                }),
        ];
    }
}
