<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use App\Models\Role;
use BackedEnum;
use Filament\Actions;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Filament\Schemas\Components as SchemaComponents;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationLabel = 'Roles';

    protected static ?string $modelLabel = 'Role';

    protected static ?string $pluralModelLabel = 'Roles';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                SchemaComponents\Section::make('Role Information')
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
                            ->helperText('A unique identifier for the role (e.g., admin, editor, user)'),

                        Components\Textarea::make('description')
                            ->maxLength(65535)
                            ->columnSpanFull()
                            ->rows(3),
                    ]),

                SchemaComponents\Section::make('Permissions')
                    ->schema([
                        Components\CheckboxList::make('permissions')
                            ->relationship('permissions', 'name')
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
        return parent::getEloquentQuery()->with(['permissions', 'users']);
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
                    ->color('primary'),

                Tables\Columns\TextColumn::make('description')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }
                        return $state;
                    }),

                Tables\Columns\TextColumn::make('permissions_count')
                    ->counts('permissions')
                    ->label('Permissions')
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
                    ->visible(fn ($record) => auth()->user()?->can('edit roles') ?? false),
                Actions\DeleteAction::make()
                    ->visible(fn ($record) => auth()->user()?->can('delete roles') ?? false),
            ])
            ->bulkActions([
                Actions\DeleteBulkAction::make()
                    ->visible(fn () => auth()->user()?->can('delete roles') ?? false),
            ])
            ->headerActions([
                Actions\CreateAction::make()
                    ->visible(fn () => auth()->user()?->can('create roles') ?? false),
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
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('view roles') ?? false;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('view roles') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('create roles') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('edit roles') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('delete roles') ?? false;
    }
}

