<?php

namespace App\Filament\Resources\WeaponResource\Pages;

use App\Filament\Resources\WeaponResource;
use App\Models\Weapon;
use App\Models\ArmDealer;
use App\Models\WeaponType;
use App\Models\Bore;
use App\Models\Make;
use App\Models\LicenseIssuer;
use Filament\Actions;
use Filament\Forms\Components;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Components as SchemaComponents;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth; // Import Auth facade
use Illuminate\Support\Facades\Log;

class CreateWeapon extends CreateRecord
{
    protected static string $resource = WeaponResource::class;
    protected static bool $shouldCacheForm = true;
    
    public bool $showCreateForm = false;
    public ?string $searchCnic = null;
    public ?string $searchWeaponNo = null;
    public ?string $searchFslDiaryNo = null;
    protected array $prefillData = [];

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns([
                'default' => 1,
                'xl' => 1,
            ])
            ->schema([
                $this->getSearchSection(),
                $this->getWeaponFormSection(),
            ]);
    }

    protected function getSearchSection(): SchemaComponents\Section
    {
        return SchemaComponents\Section::make('Add New Weapon')
            ->description('Enter weapon details to add a new record.')
            ->schema([
                Components\TextInput::make('searchCnic')
                    ->label('CNIC')
                    ->required(fn () => !$this->showCreateForm) // Only required when search form is visible
                    ->placeholder('1234512345678')
                    ->maxLength(255)
                    ->live()
                    ->default(fn () => $this->searchCnic)
                    ->afterStateUpdated(fn ($state) => $this->searchCnic = $state)
                    ->regex('/^\d{13}$/')
                    ->validationMessages([
                        'regex' => 'CNIC must be exactly 13 digits',
                    ])
                    ->columnSpan(1),

                Components\TextInput::make('searchWeaponNo')
                    ->label('Weapon Number')
                    ->required(fn () => !$this->showCreateForm) // Only required when search form is visible
                    ->maxLength(255)
                    ->live()
                    ->default(fn () => $this->searchWeaponNo)
                    ->afterStateUpdated(fn ($state) => $this->searchWeaponNo = $state)
                    ->columnSpan(1),

                Components\TextInput::make('searchFslDiaryNo')
                    ->label('FSL Diary Number')
                    ->required(fn () => !$this->showCreateForm) // Only required when search form is visible
                    ->maxLength(255)
                    ->live()
                    ->default(fn () => $this->searchFslDiaryNo)
                    ->afterStateUpdated(fn ($state) => $this->searchFslDiaryNo = $state)
                    ->regex('/^\d+\/\d{2}$/')
                    ->validationMessages([
                        'regex' => 'FSL Diary Number format: Number/Year (e.g., 12345/25)',
                    ])
                    ->columnSpan(1),
            ])
            ->columns(3)
            ->footerActions([
                Actions\Action::make('search')
                    ->label('Search')
                    ->icon('heroicon-o-magnifying-glass')
                    ->color('primary')
                    ->action('searchWeapon'),
            ])
            ->columnSpan(1);
    }

    protected function getWeaponFormSection(): SchemaComponents\Group
    {
        return SchemaComponents\Group::make([
                $this->getWeaponFormSchema(),
            ])
            // Always render the form but control visibility through CSS
            ->visible(fn () => true)
            ->extraAttributes(fn () => [
                'style' => $this->showCreateForm ? '' : 'display: none;'
            ])
            ->columnSpan(1);
    }

    protected function getWeaponFormSchema(): SchemaComponents\Section
    {
        return SchemaComponents\Section::make('Weapon Information')
            ->schema([
                Components\TextInput::make('cnic')
                    ->label('CNIC')
                    ->required()
                    ->placeholder('420003566955')
                    ->helperText('Multiple CNICs allowed. Enter CNICs separated by comma (e.g., 420003566955, 1234567890123)')
                    ->maxLength(255)
                    ->disabled(function () {
                        return $this->showCreateForm && 
                               !empty($this->prefillData) && 
                               isset($this->prefillData['cnic']);
                    })
                    ->columnSpanFull(),

                Components\TextInput::make('weapon_no')
                    ->label('Weapon Number')
                    ->required()
                    ->maxLength(255)
                    ->disabled(function () {
                        return $this->showCreateForm && 
                               !empty($this->prefillData) && 
                               isset($this->prefillData['weapon_no']);
                    })
                    ->columnSpanFull(),

                Components\Select::make('arm_dealer_id')
                    ->label('Arm Dealer')
                    ->options(\App\Models\ArmDealer::pluck('shop_name', 'id'))
                    ->searchable(['shop_name', 'name'])
                    ->required()
                    ->getOptionLabelFromRecordUsing(fn (\App\Models\ArmDealer $record): string => "{$record->shop_name} - {$record->name}")
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
                    ->helperText('Format: Number/Year (e.g., 12345/25). Ye field unique honi chahiye.')
                    ->regex('/^\d+\/\d{2}$/')
                    ->live(onBlur: true)
                    ->disabled(function () {
                        return $this->showCreateForm && 
                               !empty($this->prefillData) && 
                               isset($this->prefillData['fsl_diary_no']);
                    })
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
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getFormActions(): array
    {
        // Only show form actions when the create form is visible
        if ($this->showCreateForm) {
            return WeaponResource::baseFormActions();
        }
        
        return [];
    }

    public function searchWeapon(): void
    {
        // Validate that all three fields are filled
        if (empty($this->searchCnic) || empty($this->searchWeaponNo) || empty($this->searchFslDiaryNo)) {
            Notification::make()
                ->title('Missing Required Fields')
                ->body('All three fields (CNIC, Weapon Number, and FSL Diary Number) are required.')
                ->danger()
                ->send();
            return;
        }
        
        // Validate CNIC format
        if (!preg_match('/^\d{13}$/', $this->searchCnic)) {
            Notification::make()
                ->title('Invalid CNIC')
                ->body('CNIC must be exactly 13 digits')
                ->danger()
                ->send();
            return;
        }
        
        // Validate FSL Diary Number format
        if (!preg_match('/^\d+\/\d{2}$/', $this->searchFslDiaryNo)) {
            Notification::make()
                ->title('Invalid FSL Diary Number')
                ->body('FSL Diary Number format: Number/Year (e.g., 12345/25)')
                ->danger()
                ->send();
            return;
        }
        
        Log::info('Search initiated', [
            'searchCnic' => $this->searchCnic,
            'searchWeaponNo' => $this->searchWeaponNo,
            'searchFslDiaryNo' => $this->searchFslDiaryNo
        ]);
        
        $query = Weapon::query();

        if (!empty($this->searchCnic)) {
            $query->where('cnic', $this->searchCnic);
        }

        if (!empty($this->searchWeaponNo)) {
            $query->where('weapon_no', $this->searchWeaponNo);
        }

        if (!empty($this->searchFslDiaryNo)) {
            $query->where('fsl_diary_no', $this->searchFslDiaryNo);
        }

        $weapon = $query->first();

        if ($weapon) {
            Notification::make()
                ->title('Weapon record found!')
                ->success()
                ->body('Redirecting to edit page...')
                ->send();

            $this->redirect(WeaponResource::getUrl('edit', ['record' => $weapon]));
        } else {
            // Instead of using Filament actions, show the form directly with a notification
            $this->showCreateForm = true;
            $this->prefillFormData();
            
            // Log the prefill data
            Log::info('Prefill data prepared', $this->prefillData);
            
            // Force form refresh to apply prefill data
            $this->form->fill($this->prefillData);
            
            // Dispatch a refresh to ensure the UI updates
            $this->dispatch('$refresh');
            
            Notification::make()
                ->title('No record found')
                ->body('Creating a new weapon record with your search criteria.')
                ->info()
                ->send();
        }
    }

    protected function prefillFormData(): void
    {
        $this->prefillData = array_filter([
            'cnic' => $this->searchCnic,
            'weapon_no' => $this->searchWeaponNo,
            'fsl_diary_no' => $this->searchFslDiaryNo,
        ], fn ($value) => filled($value));
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if ($this->showCreateForm && !empty($this->prefillData)) {
            $data = array_merge($data, $this->prefillData);
        } else {
            $this->prefillData = [];
        }

        return $data;
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Ensure the search field data is included in the final form data
        if (!empty($this->searchCnic) && empty($data['cnic'])) {
            $data['cnic'] = $this->searchCnic;
        }
        if (!empty($this->searchWeaponNo) && empty($data['weapon_no'])) {
            $data['weapon_no'] = $this->searchWeaponNo;
        }
        if (!empty($this->searchFslDiaryNo) && empty($data['fsl_diary_no'])) {
            $data['fsl_diary_no'] = $this->searchFslDiaryNo;
        }
        
        // Set the user_id to the currently authenticated user
        $data['user_id'] = Auth::id();
        
        // Set the range_id from the user's range_id if available
        $user = Auth::user();
        if ($user && $user->range_id) {
            $data['range_id'] = $user->range_id;
        }
        
        return $data;
    }

    protected function getInitialFormState(): array
    {
        return array_merge($this->form->getState(), $this->prefillData);
    }
    
    // Override the mount method to ensure proper form initialization
    public function mount(): void
    {
        parent::mount();
        
        // Initialize the form with empty state
        $this->form->fill();
    }
}
