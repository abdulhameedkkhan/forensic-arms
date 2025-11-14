<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WeaponResource\Pages;
use App\Models\Weapon;
use App\Models\ArmDealer;
use App\Models\WeaponType;
use App\Models\Bore;
use App\Models\Make;
use App\Models\LicenseIssuer;
use BackedEnum;
use Filament\Actions;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Filament\Schemas\Components as SchemaComponents;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class WeaponResource extends Resource
{
    protected static ?string $model = Weapon::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationLabel = 'Weapons';

    protected static ?string $modelLabel = 'Weapon';

    protected static ?string $pluralModelLabel = 'Weapons';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema(static::baseFormSchema());
    }

    public static function baseFormSchema(): array
    {
        return [
            SchemaComponents\Section::make('Weapon Information')
                ->schema([
                    Components\TextInput::make('cnic')
                        ->label('CNIC')
                        ->required()
                        ->placeholder('420003566955')
                        ->helperText('Multiple CNICs allowed. Enter CNICs separated by comma (e.g., 420003566955, 1234567890123)')
                        ->maxLength(255)
                        ->columnSpanFull(),

                    Components\TextInput::make('weapon_no')
                        ->label('Weapon Number')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),

                    Components\Select::make('arm_dealer_id')
                        ->label('Arm Dealer')
                        ->relationship('armDealer', 'name', function ($query) {
                            $user = auth()->user();
                            if ($user && $user->range_id) {
                                // Filter arm dealers by user's range
                                return $query->where('range_id', (int) $user->range_id);
                            } elseif ($user && !$user->hasRole('admin')) {
                                // Non-admin users without range_id see nothing
                                return $query->whereRaw('1 = 0');
                            }
                            // Admin users see all arm dealers
                            return $query;
                        })
                        ->searchable()
                        ->preload()
                        ->required()
                        ->getOptionLabelFromRecordUsing(fn (ArmDealer $record): string => "{$record->name} - {$record->shop_name}")
                        ->columnSpanFull(),

                    Components\TextInput::make('arm_dealer_invoice_no')
                        ->label('Arm Dealer Invoice#')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),

                    Components\TextInput::make('fsl_diary_no')
                        ->label('FSL Diary Number')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255)
                        ->placeholder('12345/25')
                        ->helperText('Format: Number/Year (e.g., 12345/25). This field must be unique.')
                        ->regex('/^\d+\/\d{2}$/')
                        ->live(onBlur: true)
                        ->validationMessages([
                            'regex' => 'FSL Diary Number format: Number/Year hona chahiye (masalan: 12345/25)',
                            'unique' => 'Ye FSL Diary Number (1234/25) pehle se maujood hai! Kripya koi doosra unique number dalein.',
                            'required' => 'FSL Diary Number zaroori hai.',
                        ])
                        ->columnSpanFull(),

                    Components\TextInput::make('license_no')
                        ->label('License No')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),

                    Components\Select::make('weapon_type_id')
                        ->label('Weapon Type')
                        ->relationship('weaponType', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->createOptionForm([
                            Components\TextInput::make('name')
                                ->label('Weapon Type')
                                ->required()
                                ->unique(WeaponType::class, 'name')
                                ->maxLength(255),
                        ])
                        ->createOptionUsing(function (array $data): int {
                            return WeaponType::create($data)->getKey();
                        })
                        ->columnSpanFull(),

                    Components\Select::make('bore_id')
                        ->label('Bore')
                        ->relationship('bore', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->createOptionForm([
                            Components\TextInput::make('name')
                                ->label('Bore')
                                ->required()
                                ->unique(Bore::class, 'name')
                                ->maxLength(255),
                        ])
                        ->createOptionUsing(function (array $data): int {
                            return Bore::create($data)->getKey();
                        })
                        ->columnSpanFull(),

                    Components\Select::make('make_id')
                        ->label('Make')
                        ->relationship('make', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->createOptionForm([
                            Components\TextInput::make('name')
                                ->label('Make')
                                ->required()
                                ->unique(Make::class, 'name')
                                ->maxLength(255),
                        ])
                        ->createOptionUsing(function (array $data): int {
                            return Make::create($data)->getKey();
                        })
                        ->columnSpanFull(),

                    Components\Select::make('license_issuer_id')
                        ->label('License Issued by')
                        ->relationship('licenseIssuer', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->createOptionForm([
                            Components\TextInput::make('name')
                                ->label('License Issuer')
                                ->required()
                                ->unique(LicenseIssuer::class, 'name')
                                ->maxLength(255),
                        ])
                        ->createOptionUsing(function (array $data): int {
                            return LicenseIssuer::create($data)->getKey();
                        })
                        ->columnSpanFull(),

                    Components\FileUpload::make('attachments')
                        ->label('Attachments')
                        ->multiple()
                        ->directory('weapons/attachments')
                        ->visibility('public')
                        ->disk('public')
                        ->acceptedFileTypes(['application/pdf', 'image/*'])
                        ->maxSize(10240) // 10MB
                        ->helperText('You can upload multiple files (PDF, Images). Max size: 10MB per file.')
                        ->columnSpanFull()
                        ->downloadable()
                        ->openable(),
                ]),
        ];
    }

    public static function baseFormActions(): array
    {
        return [
            Actions\ButtonAction::make('create')->label('Create')->color('primary')->action('create'),
            Actions\ButtonAction::make('createAnother')->label('Create & create another')->color('primary')->action('createAnother'),
            Actions\ButtonAction::make('cancel')->label('Cancel')->color('gray')->action('cancel'),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('cnic')
                    ->label('CNIC')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('weapon_no')
                    ->label('Weapon Number')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('armDealer.name')
                    ->label('Arm Dealer')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('arm_dealer_invoice_no')
                    ->label('Arm Dealer Invoice#')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('fsl_diary_no')
                    ->label('FSL Diary No')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('license_no')
                    ->label('License No')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('weaponType.name')
                    ->label('Weapon Type')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('bore.name')
                    ->label('Bore')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('make.name')
                    ->label('Make')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('licenseIssuer.name')
                    ->label('License Issued by')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('range.name')
                    ->label('Range')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('attachments')
                    ->label('Has Attachments')
                    ->boolean()
                    ->getStateUsing(fn ($record) => !empty($record->attachments))
                    ->toggleable(),

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
                Tables\Filters\SelectFilter::make('arm_dealer_id')
                    ->label('Arm Dealer')
                    ->relationship('armDealer', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('range_id')
                    ->label('Range')
                    ->relationship('range', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->recordActions([
                Actions\ViewAction::make()
                    ->visible(fn ($record) => auth()->user()?->can('view weapons') ?? false),
                Actions\EditAction::make()
                    ->visible(fn ($record) => auth()->user()?->can('edit weapons') ?? false),
                Actions\DeleteAction::make()
                    ->visible(fn ($record) => auth()->user()?->can('delete weapons') ?? false),
                Actions\RestoreAction::make()
                    ->visible(fn ($record) => auth()->user()?->can('edit weapons') ?? false),
                Actions\ForceDeleteAction::make()
                    ->visible(fn ($record) => auth()->user()?->can('delete weapons') ?? false),
            ])
            ->bulkActions([
                Actions\DeleteBulkAction::make()
                    ->visible(fn () => auth()->user()?->can('delete weapons') ?? false),
                Actions\RestoreBulkAction::make()
                    ->visible(fn () => auth()->user()?->can('edit weapons') ?? false),
                Actions\ForceDeleteBulkAction::make()
                    ->visible(fn () => auth()->user()?->can('delete weapons') ?? false),
            ])
            ->headerActions([
                Actions\CreateAction::make()
                    ->visible(fn () => auth()->user()?->can('create weapons') ?? false),
            ]);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();
        
        $user = auth()->user();
        if ($user) {
            // If user has range_id, only show weapons from their range
            if ($user->range_id) {
                // Ensure both are compared as integers to avoid type mismatch
                $query->where('range_id', (int) $user->range_id);
            } 
            // If user has no range_id and is NOT admin, show nothing
            elseif (!$user->hasRole('admin')) {
                $query->whereRaw('1 = 0'); // This will return no results
            }
            // If user has no range_id and IS admin, show all weapons (no filter)
        }
        
        return $query;
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
            'index' => Pages\ListWeapons::route('/'),
            'create' => Pages\CreateWeapon::route('/create'),
            'view' => Pages\ViewWeapon::route('/{record}'),
            'edit' => Pages\EditWeapon::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }
        
        return $user->can('view weapons');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('view weapons') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('create weapons') ?? false;
    }

    public static function canView($record): bool
    {
        $user = auth()->user();
        if (!$user || !$user->can('view weapons')) {
            return false;
        }
        
        // If user has range_id, can only view weapons from their range
        if ($user->range_id) {
            // Compare as integers to avoid type mismatch
            return (int) $record->range_id === (int) $user->range_id;
        }
        
        // If user has no range_id and is NOT admin, cannot view anything
        if (!$user->hasRole('admin')) {
            return false;
        }
        
        // Admin users can view all weapons
        return true;
    }

    public static function canEdit($record): bool
    {
        $user = auth()->user();
        if (!$user || !$user->can('edit weapons')) {
            return false;
        }
        
        // If user has range_id, can only edit weapons from their range
        if ($user->range_id) {
            // Compare as integers to avoid type mismatch
            return (int) $record->range_id === (int) $user->range_id;
        }
        
        // If user has no range_id and is NOT admin, cannot edit anything
        if (!$user->hasRole('admin')) {
            return false;
        }
        
        // Admin users can edit all weapons
        return true;
    }

    public static function canDelete($record): bool
    {
        $user = auth()->user();
        if (!$user || !$user->can('delete weapons')) {
            return false;
        }
        
        // If user has range_id, can only delete weapons from their range
        if ($user->range_id) {
            // Compare as integers to avoid type mismatch
            return (int) $record->range_id === (int) $user->range_id;
        }
        
        // If user has no range_id and is NOT admin, cannot delete anything
        if (!$user->hasRole('admin')) {
            return false;
        }
        
        // Admin users can delete all weapons
        return true;
    }
}