<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WeaponResource\Pages;
use App\Models\Weapon;
use App\Models\ArmDealer;
use App\Models\Bore;
use App\Models\LicenseIssuer;
use App\Models\Make;
use App\Models\WeaponType;
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
                        ->unique(ignoreRecord: true)
                        ->maxLength(255)
                        ->placeholder('12345-1234567-1')
                        ->helperText('Enter CNIC in format: XXXXX-XXXXXXX-X')
                        ->columnSpanFull(),

                    Components\TextInput::make('weapon_no')
                        ->label('Weapon Number')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),

                    Components\Select::make('arm_dealer_id')
                        ->label('Arm Dealer')
                        ->options(\App\Models\ArmDealer::pluck('shop_name', 'id'))
                        ->searchable(['shop_name', 'name'])
                        ->required()
                        ->getOptionLabelFromRecordUsing(fn (ArmDealer $record): string => "{$record->shop_name} - {$record->name}")
                        ->columnSpanFull()
                        ->live(), // Enable live updates

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
                        ->options(\App\Models\WeaponType::pluck('name', 'id'))
                        ->searchable()
                        ->required()
                        ->columnSpanFull()
                        ->live(), // Enable live updates

                Components\Select::make('bore_id')
                    ->label('Bore')
                    ->options(\App\Models\Bore::pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->columnSpanFull()
                    ->live(), // Enable live updates

                Components\Select::make('make_id')
                    ->label('Make')
                    ->options(\App\Models\Make::pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->columnSpanFull()
                    ->live(), // Enable live updates

                Components\Select::make('license_issuer_id')
                    ->label('License Issued by')
                    ->options(\App\Models\LicenseIssuer::pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->columnSpanFull()
                    ->live(), // Enable live updates

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

                Tables\Filters\SelectFilter::make('weapon_type_id')
                    ->label('Weapon Type')
                    ->relationship('weaponType', 'name')
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

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('edit weapons') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('delete weapons') ?? false;
    }
}