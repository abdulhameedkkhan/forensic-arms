<?php

namespace App\Filament\Resources\WeaponResource\Pages;

use App\Filament\Resources\WeaponResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;
use Filament\Forms\Components;
use Filament\Schemas\Components as SchemaComponents;

class EditWeapon extends EditRecord
{
    protected static string $resource = WeaponResource::class;

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            SchemaComponents\Section::make('Weapon Information')
                ->schema([
                    Components\TextInput::make('cnic')
                        ->label('CNIC')
                        ->required()
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
                        ->getOptionLabelFromRecordUsing(fn (\App\Models\ArmDealer $record): string => "{$record->shop_name} - {$record->name}")
                        ->columnSpanFull()
                        ->live(),

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
                        ->live(),

                    Components\Select::make('bore_id')
                        ->label('Bore')
                        ->options(\App\Models\Bore::pluck('name', 'id'))
                        ->searchable()
                        ->required()
                        ->columnSpanFull()
                        ->live(),

                    Components\Select::make('make_id')
                        ->label('Make')
                        ->options(\App\Models\Make::pluck('name', 'id'))
                        ->searchable()
                        ->required()
                        ->columnSpanFull()
                        ->live(),

                    Components\Select::make('license_issuer_id')
                        ->label('License Issued by')
                        ->options(\App\Models\LicenseIssuer::pluck('name', 'id'))
                        ->searchable()
                        ->required()
                        ->columnSpanFull()
                        ->live(),

                    Components\FileUpload::make('attachments')
                        ->label('Attachments')
                        ->multiple()
                        ->directory('weapons/attachments')
                        ->visibility('public')
                        ->disk('public')
                        ->acceptedFileTypes(['application/pdf', 'image/*'])
                        ->maxSize(10240)
                        ->helperText('You can upload multiple files (PDF, Images). Max size: 10MB per file.')
                        ->columnSpanFull()
                        ->downloadable()
                        ->openable(),
                ]),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->visible(fn () => auth()->user()?->can('delete weapons') ?? false),
            Actions\RestoreAction::make()
                ->visible(fn () => auth()->user()?->can('edit weapons') ?? false),
            Actions\ForceDeleteAction::make()
                ->visible(fn () => auth()->user()?->can('delete weapons') ?? false),
        ];
    }
}