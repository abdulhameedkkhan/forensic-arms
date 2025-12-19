<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WeaponTypeResource\Pages;
use App\Models\WeaponType;
use BackedEnum;
use Filament\Actions;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Filament\Schemas\Components as SchemaComponents;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class WeaponTypeResource extends Resource
{
    protected static ?string $model = WeaponType::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationLabel = 'Weapon Types';

    protected static ?string $modelLabel = 'Weapon Type';

    protected static ?string $pluralModelLabel = 'Weapon Types';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                SchemaComponents\Section::make('Weapon Type Information')
                    ->schema([
                        Components\TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
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
                Tables\Filters\TrashedFilter::make(),
            ])
            ->recordActions([
                Actions\EditAction::make()
                    ->visible(fn ($record) => auth()->user()?->can('edit weapon types') ?? false),
                Actions\DeleteAction::make()
                    ->visible(fn ($record) => auth()->user()?->can('delete weapon types') ?? false),
                Actions\RestoreAction::make()
                    ->visible(fn ($record) => auth()->user()?->can('edit weapon types') ?? false),
                Actions\ForceDeleteAction::make()
                    ->visible(fn ($record) => auth()->user()?->can('delete weapon types') ?? false),
            ])
            ->bulkActions([
                Actions\DeleteBulkAction::make()
                    ->visible(fn () => auth()->user()?->can('delete weapon types') ?? false),
                Actions\RestoreBulkAction::make()
                    ->visible(fn () => auth()->user()?->can('edit weapon types') ?? false),
                Actions\ForceDeleteBulkAction::make()
                    ->visible(fn () => auth()->user()?->can('delete weapon types') ?? false),
            ])
            ->headerActions([
                Actions\CreateAction::make()
                    ->visible(fn () => auth()->user()?->can('create weapon types') ?? false),
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
            'index' => Pages\ListWeaponTypes::route('/'),
            'create' => Pages\CreateWeaponType::route('/create'),
            'edit' => Pages\EditWeaponType::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('view weapon types') ?? false;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('view weapon types') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('create weapon types') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('edit weapon types') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('delete weapon types') ?? false;
    }
}
