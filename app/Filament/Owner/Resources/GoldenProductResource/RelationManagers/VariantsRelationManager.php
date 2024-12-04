<?php

namespace App\Filament\Owner\Resources\GoldenProductResource\RelationManagers;

use App\Models\Locale;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';

    protected static ?string $title = 'Variants';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('active_locale')
                    ->label('')
                    ->options(function () {
                        return Locale::query()
                            ->orderBy('default', 'desc')
                            ->orderBy('name')
                            ->pluck('name', 'id');
                    })
                    ->default(function () {
                        return Locale::where('default', true)->first()?->id;
                    })
                    ->live()
                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                        $locale = Locale::find($state);
                        if (!$locale) return;

                        $record = $this->getOwnerRecord()->variants()->find($get('id'));
                        if (!$record) return;

                        $translation = $record->translations()
                            ->where('locale_id', $locale->id)
                            ->first();

                        if ($translation) {
                            $set('name', $translation->name);
                            $set('description', $translation->description);
                            $set('attributes', $translation->attributes ?? []);
                        } else {
                            $set('name', '');
                            $set('description', '');
                            $set('attributes', []);
                        }
                    })
                    ->selectablePlaceholder(false)
                    ->extraAttributes([
                        'class' => 'ml-auto w-[200px]'
                    ]),

                Forms\Components\Section::make('Variant Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->afterStateHydrated(function ($component, $state, $record) {
                                if (!$state && $record) {
                                    $defaultLocale = Locale::where('default', true)->first();
                                    if (!$defaultLocale) return;

                                    $translation = $record->translations()
                                        ->where('locale_id', $defaultLocale->id)
                                        ->first();

                                    $component->state($translation?->name);
                                }
                            }),

                        Forms\Components\Textarea::make('description')
                            ->columnSpanFull()
                            ->afterStateHydrated(function ($component, $state, $record) {
                                if (!$state && $record) {
                                    $defaultLocale = Locale::where('default', true)->first();
                                    if (!$defaultLocale) return;

                                    $translation = $record->translations()
                                        ->where('locale_id', $defaultLocale->id)
                                        ->first();

                                    $component->state($translation?->description);
                                }
                            }),

                        Forms\Components\KeyValue::make('attributes')
                            ->columnSpanFull()
                            ->helperText('Custom attributes for this variant (e.g. Color: Red, Size: Large)')
                            ->keyLabel('Key')
                            ->valueLabel('Value')
                            ->reorderable()
                            ->editableKeys()
                            ->editableValues()
                            ->afterStateHydrated(function ($component, $state, $record) {
                                if (!$state && $record) {
                                    $defaultLocale = Locale::where('default', true)->first();
                                    if (!$defaultLocale) return;

                                    $translation = $record->translations()
                                        ->where('locale_id', $defaultLocale->id)
                                        ->first();

                                    $component->state($translation?->attributes ?? []);
                                }
                            }),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->getStateUsing(function (Model $record): string {
                        $defaultLocale = Locale::where('default', true)->first();
                        if (!$defaultLocale) return '';

                        return $record->translations()
                            ->where('locale_id', $defaultLocale->id)
                            ->first()?->name ?? '';
                    })
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('translations', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%");
                        });
                    }),

                Tables\Columns\TextColumn::make('attributes')
                    ->getStateUsing(function (Model $record): string {
                        $defaultLocale = Locale::where('default', true)->first();
                        if (!$defaultLocale) return '';

                        $attributes = $record->translations()
                            ->where('locale_id', $defaultLocale->id)
                            ->first()?->attributes ?? [];

                        return collect($attributes)
                            ->map(fn($value, $key) => "{$key}: {$value}")
                            ->join(', ');
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->using(function (array $data, string $model): Model {
                        $variant = new $model();
                        $variant->golden_product_id = $this->getOwnerRecord()->id;
                        $variant->save();

                        $activeLocale = Locale::find($data['active_locale']);
                        if ($activeLocale) {
                            $variant->translations()->create([
                                'name' => $data['name'],
                                'description' => $data['description'],
                                'attributes' => $data['attributes'] ?? [],
                                'locale_id' => $activeLocale->id,
                            ]);
                        }

                        return $variant;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->using(function (Model $record, array $data): Model {
                        $activeLocale = Locale::find($data['active_locale']);
                        if ($activeLocale) {
                            $translation = $record->translations()
                                ->where('locale_id', $activeLocale->id)
                                ->first();

                            if ($translation) {
                                $translation->update([
                                    'name' => $data['name'],
                                    'description' => $data['description'],
                                    'attributes' => $data['attributes'] ?? [],
                                ]);
                            } else {
                                $record->translations()->create([
                                    'name' => $data['name'],
                                    'description' => $data['description'],
                                    'attributes' => $data['attributes'] ?? [],
                                    'locale_id' => $activeLocale->id,
                                ]);
                            }
                        }

                        return $record;
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
