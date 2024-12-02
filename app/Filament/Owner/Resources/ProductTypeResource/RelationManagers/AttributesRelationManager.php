<?php

namespace App\Filament\Owner\Resources\ProductTypeResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class AttributesRelationManager extends RelationManager
{
    protected static string $relationship = 'attributes';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Attribute Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label('Attribute Name')
                            ->columnSpan(2)
                            ->placeholder('e.g., Color, Size, Material')
                            ->helperText('Enter a unique, descriptive name for this attribute.'),

                        Forms\Components\Toggle::make('is_variant_attribute')
                            ->label('Is Variant Attribute')
                            ->helperText('Enable this if this attribute differs between product variants'),

                        Forms\Components\Toggle::make('is_translatable')
                            ->label('Is Translatable')
                            ->helperText('Enable this if this attribute needs to be translated into different languages'),

                        Forms\Components\Textarea::make('description')
                            ->maxLength(255)
                            ->columnSpan(2)
                            ->helperText('Detailed description for attribute mapping for internal use. More context improves mapping precision.'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Attribute Configuration')
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->native(false)
                            ->options([
                                'text' => 'Text',
                                'boolean' => 'Boolean',
                                'number' => 'Number',
                                'select' => 'Select',
                                'url' => 'URL',
                                'color' => 'Color',
                            ])
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Forms\Set $set, ?string $state) {
                                // Always reset the field when type changes
                                $set('field', null);

                                // Set field automatically only for single-option types
                                $singleOptionTypes = [
                                    'number' => 'TextInput',
                                    'select' => 'Select',
                                    'url' => 'TextInput',
                                    'color' => 'ColorPicker',
                                ];

                                if (isset($singleOptionTypes[$state])) {
                                    $set('field', $singleOptionTypes[$state]);
                                }
                            })
                            ->helperText('Select the data type for this attribute. This determines how the attribute will be displayed and validated.'),

                        Forms\Components\Select::make('field')
                            ->native(false)
                            ->options(function (Forms\Get $get) {
                                return match ($get('type')) {
                                    'text' => [
                                        'TextInput' => 'Text Input',
                                        'Textarea' => 'Textarea',
                                        'RichEditor' => 'Rich Editor',
                                        'MarkdownEditor' => 'Markdown Editor',
                                    ],
                                    'boolean' => [
                                        'Checkbox' => 'Checkbox',
                                        'Toggle' => 'Toggle',
                                    ],
                                    'number' => [
                                        'TextInput' => 'Text Input',
                                    ],
                                    'select' => [
                                        'Select' => 'Single Select',
                                        'MultiSelect' => 'Mutli Select',
                                        'CheckboxList' => 'Checkbox List',
                                        'Radio' => 'Radio buttons',
                                        'TagsInput' => 'Tags',
                                    ],
                                    'url' => [
                                        'TextInput' => 'Text Input',
                                    ],
                                    'color' => [
                                        'ColorPicker' => 'Color Picker',
                                    ],
                                    default => [],
                                };
                            })
                            ->required()
                            ->disabled(fn(Forms\Get $get) => blank($get('type')))
                            ->helperText('Choose how this attribute will be displayed in forms'),

                        Forms\Components\Repeater::make('options')
                            ->label('Options')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('label')
                                            ->required(),
                                        Forms\Components\TextInput::make('value')
                                            ->required(),
                                    ]),
                            ])
                            ->collapsible()
                            ->collapsed()
                            ->itemLabel(fn(array $state): ?string => $state['label'] ?? null)
                            ->defaultItems(0)
                            ->visible(fn(Forms\Get $get): bool => $get('field') === 'Select' || $get('field') === 'ColorPicker')
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('unit')
                            ->maxLength(255)
                            ->label('Unit of Measurement')
                            ->placeholder('e.g., cm, kg, liters')
                            ->helperText('Optional. Specify the unit of measurement for this attribute, if applicable.')
                            ->nullable()
                            ->visible(fn(Forms\Get $get): bool => in_array($get('type'), [
                                'text',
                                'number',
                                'select',
                            ])),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Validation Rules')
                    ->schema([
                        Forms\Components\Toggle::make('required')
                            ->label('Required Field')
                            ->helperText('Make this attribute mandatory when creating or editing products'),

                        Forms\Components\TextInput::make('validators.min_length')
                            ->label('Minimum Length')
                            ->helperText('Minimum number of characters required')
                            ->numeric()
                            ->visible(fn(Forms\Get $get): bool => $get('type') === 'text'),

                        Forms\Components\TextInput::make('validators.max_length')
                            ->label('Maximum Length')
                            ->helperText('Maximum number of characters allowed')
                            ->numeric()
                            ->visible(fn(Forms\Get $get): bool => $get('type') === 'text'),

                        Forms\Components\TextInput::make('validators.regex')
                            ->label('Regular Expression Pattern')
                            ->helperText('Custom pattern for validation')
                            ->visible(fn(Forms\Get $get): bool => $get('type') === 'text'),


                        Forms\Components\TextInput::make('validators.decimal_places')
                            ->label('Decimal Places')
                            ->helperText('Number of decimal places allowed')
                            ->numeric()
                            ->visible(fn(Forms\Get $get): bool => $get('type') === 'number'),

                        Forms\Components\TextInput::make('validators.min')
                            ->label('Minimum Value')
                            ->helperText('Smallest number allowed')
                            ->numeric()
                            ->visible(fn(Forms\Get $get): bool => $get('type') === 'number'),

                        Forms\Components\TextInput::make('validators.max')
                            ->label('Maximum Value')
                            ->helperText('Largest number allowed')
                            ->numeric()
                            ->visible(fn(Forms\Get $get): bool => $get('type') === 'number'),

                        Forms\Components\Toggle::make('validators.active_url')
                            ->label('Validate Active URL')
                            ->helperText('Check if the URL is actually reachable')
                            ->visible(fn(Forms\Get $get): bool => $get('type') === 'url'),

                        Forms\Components\TextInput::make('validators.starts_with')
                            ->label('URL Must Start With')
                            ->helperText('Required URL prefix (e.g., https://)')
                            ->visible(fn(Forms\Get $get): bool => $get('type') === 'url'),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('required')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('field')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_variant_attribute')
                    ->boolean()
                    ->sortable(),
            ])
            ->defaultSort('rank')
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->slideOver(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->slideOver(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
