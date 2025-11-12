<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WeaponResource\Pages;
use App\Models\Weapon;
use App\Models\ArmDealer;
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
                        ->placeholder('420003566955')
                        ->helperText('Enter CNIC in format: XXXXX-XXXXXXX-X')
                        ->columnSpanFull(),

                    Components\TextInput::make('weapon_no')
                        ->label('Weapon Number')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),

                    Components\Select::make('arm_dealer_id')
                        ->label('Arm Dealer')
                        ->relationship('armDealer', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->getOptionLabelFromRecordUsing(fn (ArmDealer $record): string => "{$record->name} - {$record->shop_name}")
                        ->columnSpanFull(),

                    Components\TextInput::make('fsl_diary_no')
                        ->label('FSL Diary Number')
                        ->required() // Make this field mandatory
                        ->maxLength(255)
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

                Tables\Columns\TextColumn::make('fsl_diary_no')
                    ->label('FSL Diary No')
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