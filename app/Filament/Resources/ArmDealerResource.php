<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArmDealerResource\Pages;
use App\Models\ArmDealer;
use App\Services\GeocodingService;
use BackedEnum;
use Filament\Actions;
use Filament\Actions\Action as FormAction;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Filament\Schemas\Components as SchemaComponents;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Http;

class ArmDealerResource extends Resource
{
    protected static ?string $model = ArmDealer::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationLabel = 'Arm Dealers';

    protected static ?string $modelLabel = 'Arm Dealer';

    protected static ?string $pluralModelLabel = 'Arm Dealers';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                SchemaComponents\Section::make('Arm Dealer Information')
                    ->schema([
                        Components\TextInput::make('name')
                            ->label('Arm Dealer Name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Components\TextInput::make('shop_name')
                            ->label('Shop Name')
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Components\TextInput::make('cell')
                            ->label('Cell Number')
                            ->tel()
                            ->required()
                            ->maxLength(255),

                        Components\TextInput::make('phone')
                            ->label('Phone Number')
                            ->tel()
                            ->maxLength(255),

                        Components\TextInput::make('email')
                            ->email()
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                SchemaComponents\Section::make('Address & Location')
                    ->schema([
                        Components\Placeholder::make('fetch_button')
                            ->label('')
                            ->content(new \Illuminate\Support\HtmlString('
                                <div class="mb-2">
                                    <button 
                                        type="button" 
                                        wire:click="fetchCoordinates"
                                        class="inline-flex items-center justify-center gap-x-1.5 rounded-md border border-transparent bg-primary-600 px-2.5 py-1.5 text-xs font-medium text-white shadow-sm transition duration-75 hover:bg-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 dark:bg-primary-500 dark:hover:bg-primary-400 dark:focus:ring-primary-400"
                                    >
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                                        </svg>
                                        Fetch Coordinates
                                    </button>
                                    <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">
                                        Enter address below and click to fetch latitude & longitude (Pakistan only)
                                    </p>
                                </div>
                            '))
                            ->columnSpanFull(),

                        Components\Textarea::make('address')
                            ->label('Address')
                            ->rows(3)
                            ->required()
                            ->helperText('Enter complete address (e.g., "Karachi, Pakistan" or "F-7 Markaz, Islamabad, Pakistan")')
                            ->columnSpanFull(),

                        Components\TextInput::make('city')
                            ->label('City')
                            ->maxLength(255),

                        Components\TextInput::make('district')
                            ->label('District')
                            ->maxLength(255),

                        Components\TextInput::make('police_station')
                            ->label('Police Station')
                            ->maxLength(255),

                        Components\TextInput::make('postal_code')
                            ->label('Postal Code')
                            ->maxLength(255),

                        Components\TextInput::make('latitude')
                            ->label('Latitude')
                            ->numeric()
                            ->step(0.00000001)
                            ->helperText('Auto-filled from address')
                            ->maxLength(255),

                        Components\TextInput::make('longitude')
                            ->label('Longitude')
                            ->numeric()
                            ->step(0.00000001)
                            ->helperText('Auto-filled from address')
                            ->maxLength(255),
                    ])
                    ->columns(3),

                SchemaComponents\Section::make('License Information')
                    ->schema([
                        Components\TextInput::make('license_number')
                            ->label('License Number')
                            ->maxLength(255),

                        Components\DatePicker::make('license_expiry')
                            ->label('License Expiry Date')
                            ->native(false)
                            ->displayFormat('d/m/Y'),

                        Components\Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                                'suspended' => 'Suspended',
                            ])
                            ->default('active')
                            ->required(),
                    ])
                    ->columns(3),

                SchemaComponents\Section::make('Additional Information')
                    ->schema([
                        Components\Textarea::make('notes')
                            ->label('Notes')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('shop_name')
                    ->label('Shop Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('cell')
                    ->label('Cell')
                    ->searchable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('city')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('district')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('police_station')
                    ->label('Police Station')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'gray',
                        'suspended' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('license_expiry')
                    ->label('License Expiry')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'suspended' => 'Suspended',
                    ]),

                Tables\Filters\SelectFilter::make('district')
                    ->options(fn () => ArmDealer::whereNotNull('district')
                        ->distinct()
                        ->pluck('district', 'district')
                        ->toArray())
                    ->searchable(),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->recordActions([
                Actions\ViewAction::make()
                    ->visible(fn ($record) => auth()->user()?->can('view arm dealers') ?? false),
                Actions\EditAction::make()
                    ->visible(fn ($record) => auth()->user()?->can('edit arm dealers') ?? false),
                Actions\DeleteAction::make()
                    ->visible(fn ($record) => auth()->user()?->can('delete arm dealers') ?? false),
                Actions\RestoreAction::make()
                    ->visible(fn ($record) => auth()->user()?->can('edit arm dealers') ?? false),
                Actions\ForceDeleteAction::make()
                    ->visible(fn ($record) => auth()->user()?->can('delete arm dealers') ?? false),
            ])
            ->bulkActions([
                Actions\DeleteBulkAction::make()
                    ->visible(fn () => auth()->user()?->can('delete arm dealers') ?? false),
                Actions\RestoreBulkAction::make()
                    ->visible(fn () => auth()->user()?->can('edit arm dealers') ?? false),
                Actions\ForceDeleteBulkAction::make()
                    ->visible(fn () => auth()->user()?->can('delete arm dealers') ?? false),
            ])
            ->headerActions([
                Actions\CreateAction::make()
                    ->visible(fn () => auth()->user()?->can('create arm dealers') ?? false),
            ]);
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
            'index' => Pages\ListArmDealers::route('/'),
            'create' => Pages\CreateArmDealer::route('/create'),
            'view' => Pages\ViewArmDealer::route('/{record}'),
            'edit' => Pages\EditArmDealer::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }
        
        return $user->can('view arm dealers');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('view arm dealers') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('create arm dealers') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('edit arm dealers') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('delete arm dealers') ?? false;
    }
}