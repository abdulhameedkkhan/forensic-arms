<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LicenseIssuerResource\Pages;
use App\Models\LicenseIssuer;
use BackedEnum;
use Filament\Actions;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Filament\Schemas\Components as SchemaComponents;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class LicenseIssuerResource extends Resource
{
    protected static ?string $model = LicenseIssuer::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'License Issuers';

    protected static ?string $modelLabel = 'License Issuer';

    protected static ?string $pluralModelLabel = 'License Issuers';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                SchemaComponents\Section::make('License Issuer Information')
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
                    ->visible(fn ($record) => auth()->user()?->can('edit license issuers') ?? false),
                Actions\DeleteAction::make()
                    ->visible(fn ($record) => auth()->user()?->can('delete license issuers') ?? false),
                Actions\RestoreAction::make()
                    ->visible(fn ($record) => auth()->user()?->can('edit license issuers') ?? false),
                Actions\ForceDeleteAction::make()
                    ->visible(fn ($record) => auth()->user()?->can('delete license issuers') ?? false),
            ])
            ->bulkActions([
                Actions\DeleteBulkAction::make()
                    ->visible(fn () => auth()->user()?->can('delete license issuers') ?? false),
                Actions\RestoreBulkAction::make()
                    ->visible(fn () => auth()->user()?->can('edit license issuers') ?? false),
                Actions\ForceDeleteBulkAction::make()
                    ->visible(fn () => auth()->user()?->can('delete license issuers') ?? false),
            ])
            ->headerActions([
                Actions\CreateAction::make()
                    ->visible(fn () => auth()->user()?->can('create license issuers') ?? false),
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
            'index' => Pages\ListLicenseIssuers::route('/'),
            'create' => Pages\CreateLicenseIssuer::route('/create'),
            'edit' => Pages\EditLicenseIssuer::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('view license issuers') ?? false;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('view license issuers') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('create license issuers') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('edit license issuers') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('delete license issuers') ?? false;
    }
}
