<?php

namespace App\Filament\Seller\Resources;

use App\Filament\Seller\Resources\MessageResource\Pages;
use App\Models\Message;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MessageResource extends Resource
{
    protected static ?string $model = Message::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-ellipsis';

    protected static ?string $navigationLabel = 'Messages';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('content')
                    ->required()
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('content')
                    ->wrap()
                    ->limit(150)
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                // TODO maybe useful in the future
                // Tables\Actions\Action::make('reply')
                //     ->icon('heroicon-m-arrow-uturn-left')
                //     ->color('primary')
                //     ->form([
                //         Forms\Components\Textarea::make('reply_content')
                //             ->label('Reply')
                //             ->required()
                //             ->maxLength(65535),
                //     ])
                //     ->action(function (Message $record, array $data): void {
                //         Message::create([
                //             'content' => $data['reply_content'],
                //             'seller_id' => $record->seller_id,
                //             'message_id' => $record->id, // Link to the original message
                //         ]);
                //     }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMessages::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('seller', function ($query) {
                $query->where('seller_id', auth()->user()->sellers->first()->id);
            })
            ->whereNull('message_id'); // Only show parent messages, not replies
    }
}
