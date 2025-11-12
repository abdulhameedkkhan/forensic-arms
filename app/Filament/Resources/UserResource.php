<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use BackedEnum;
use Filament\Actions;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Filament\Schemas\Components as SchemaComponents;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Users';

    protected static ?string $modelLabel = 'User';

    protected static ?string $pluralModelLabel = 'Users';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                SchemaComponents\Section::make('User Information')
                    ->schema([
                        Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->columnSpanFull(),

                        Components\TextInput::make('password')
                            ->password()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrated(fn ($state) => filled($state))
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->maxLength(255)
                            ->columnSpanFull()
                            ->helperText(fn (string $operation): string => $operation === 'edit' ? 'Leave blank to keep current password' : ''),
                    ]),

                SchemaComponents\Section::make('Roles & Permissions')
                    ->schema([
                        Components\CheckboxList::make('roles')
                            ->relationship('roles', 'name')
                            ->searchable()
                            ->bulkToggleable()
                            ->gridDirection('row')
                            ->columns(3)
                            ->columnSpanFull()
                            ->helperText('Assign roles to this user. Permissions are inherited from roles.'),

                        Components\CheckboxList::make('permissions')
                            ->relationship('permissions', 'name')
                            ->searchable()
                            ->bulkToggleable()
                            ->gridDirection('row')
                            ->columns(3)
                            ->columnSpanFull()
                            ->helperText('Assign direct permissions to this user (in addition to role permissions).'),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with('roles');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('roles.name')
                    ->badge()
                    ->color('success')
                    ->separator(',')
                    ->searchable(),

                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable()
                    ->label('Verified'),

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
                Tables\Filters\SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->searchable(),
            ])
            ->recordActions([
                Actions\EditAction::make()
                    ->visible(fn ($record) => auth()->user()?->can('edit users') ?? false),
                Actions\DeleteAction::make()
                    ->visible(fn ($record) => auth()->user()?->can('delete users') ?? false),
            ])
            ->bulkActions([
                Actions\DeleteBulkAction::make()
                    ->visible(fn () => auth()->user()?->can('delete users') ?? false),
            ])
            ->headerActions([
                Actions\CreateAction::make()
                    ->visible(fn () => auth()->user()?->can('create users') ?? false),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('view users') ?? false;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('view users') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('create users') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('edit users') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('delete users') ?? false;
    }
}

