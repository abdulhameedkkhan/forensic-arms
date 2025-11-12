<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermissionResource\Pages;
use App\Models\Permission;
use BackedEnum;
use Filament\Actions;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Filament\Schemas\Components as SchemaComponents;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationLabel = 'Permissions';

    protected static ?string $modelLabel = 'Permission';

    protected static ?string $pluralModelLabel = 'Permissions';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                SchemaComponents\Section::make('Permission Information')
                    ->schema([
                        Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, $state, \Filament\Schemas\Components\Utilities\Set $set) {
                                if ($operation !== 'create') {
                                    return;
                                }
                                $set('slug', Str::slug($state));
                            })
                            ->columnSpanFull(),

                        Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->alphaDash()
                            ->columnSpanFull()
                            ->helperText('A unique identifier for the permission (e.g., create-posts, edit-users, delete-products)'),

                        Components\Textarea::make('description')
                            ->maxLength(65535)
                            ->columnSpanFull()
                            ->rows(3),
                    ]),

                SchemaComponents\Section::make('Roles')
                    ->schema([
                        Components\CheckboxList::make('roles')
                            ->relationship('roles', 'name')
                            ->searchable()
                            ->bulkToggleable()
                            ->gridDirection('row')
                            ->columns(3)
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with(['roles', 'users']);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('warning'),

                Tables\Columns\TextColumn::make('description')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }
                        return $state;
                    }),

                Tables\Columns\TextColumn::make('roles_count')
                    ->counts('roles')
                    ->label('Roles')
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('users_count')
                    ->counts('users')
                    ->label('Users')
                    ->badge()
                    ->color('info'),

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
                //
            ])
            ->recordActions([
                Actions\EditAction::make()
                    ->visible(fn ($record) => auth()->user()?->can('edit permissions') ?? false),
                Actions\DeleteAction::make()
                    ->visible(fn ($record) => auth()->user()?->can('delete permissions') ?? false),
            ])
            ->bulkActions([
                Actions\DeleteBulkAction::make()
                    ->visible(fn () => auth()->user()?->can('delete permissions') ?? false),
            ])
            ->headerActions([
                Actions\CreateAction::make()
                    ->visible(fn () => auth()->user()?->can('create permissions') ?? false),
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
            'index' => Pages\ListPermissions::route('/'),
            'create' => Pages\CreatePermission::route('/create'),
            'edit' => Pages\EditPermission::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('view permissions') ?? false;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('view permissions') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('create permissions') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('edit permissions') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('delete permissions') ?? false;
    }
}

