<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BoreResource\Pages;
use App\Models\Bore;
use BackedEnum;
use Filament\Actions;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Filament\Schemas\Components as SchemaComponents;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class BoreResource extends Resource
{
    protected static ?string $model = Bore::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-circle-stack';

    protected static ?string $navigationLabel = 'Bores';

    protected static ?string $modelLabel = 'Bore';

    protected static ?string $pluralModelLabel = 'Bores';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                SchemaComponents\Section::make('Bore Information')
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
                    ->visible(fn ($record) => auth()->user()?->can('edit bores') ?? false),
                Actions\DeleteAction::make()
                    ->visible(fn ($record) => auth()->user()?->can('delete bores') ?? false),
                Actions\RestoreAction::make()
                    ->visible(fn ($record) => auth()->user()?->can('edit bores') ?? false),
                Actions\ForceDeleteAction::make()
                    ->visible(fn ($record) => auth()->user()?->can('delete bores') ?? false),
            ])
            ->bulkActions([
                Actions\DeleteBulkAction::make()
                    ->visible(fn () => auth()->user()?->can('delete bores') ?? false),
                Actions\RestoreBulkAction::make()
                    ->visible(fn () => auth()->user()?->can('edit bores') ?? false),
                Actions\ForceDeleteBulkAction::make()
                    ->visible(fn () => auth()->user()?->can('delete bores') ?? false),
            ])
            ->headerActions([
                Actions\CreateAction::make()
                    ->visible(fn () => auth()->user()?->can('create bores') ?? false),
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
            'index' => Pages\ListBores::route('/'),
            'create' => Pages\CreateBore::route('/create'),
            'edit' => Pages\EditBore::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('view bores') ?? false;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('view bores') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('create bores') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('edit bores') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('delete bores') ?? false;
    }
}
