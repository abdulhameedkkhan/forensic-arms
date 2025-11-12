<?php

namespace App\Filament\Resources\WeaponResource\Pages;

use App\Filament\Resources\WeaponResource;
use App\Models\Weapon;
use Filament\Actions;
use Filament\Forms\Components;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Components as SchemaComponents;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Log;

class CreateWeapon extends CreateRecord
{
    protected static string $resource = WeaponResource::class;
    protected static bool $shouldCacheForm = false;

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
                    ->placeholder('420003566955')
                    ->maxLength(255)
                    ->live()
                    ->dehydrated(false)
                    ->default(fn () => $this->searchCnic)
                    ->afterStateUpdated(fn ($state) => $this->searchCnic = $state)
                    ->columnSpan(1),

                Components\TextInput::make('searchWeaponNo')
                    ->label('Weapon Number')
                    ->maxLength(255)
                    ->live()
                    ->dehydrated(false)
                    ->default(fn () => $this->searchWeaponNo)
                    ->afterStateUpdated(fn ($state) => $this->searchWeaponNo = $state)
                    ->columnSpan(1),

                Components\TextInput::make('searchFslDiaryNo')
                    ->label('FSL Diary Number')
                    ->maxLength(255)
                    ->live()
                    ->dehydrated(false)
                    ->default(fn () => $this->searchFslDiaryNo)
                    ->afterStateUpdated(fn ($state) => $this->searchFslDiaryNo = $state)
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
                ...WeaponResource::baseFormSchema(),
            ])
            // Always render the form but control visibility through CSS
            ->visible(fn () => true)
            ->extraAttributes(fn () => [
                'style' => $this->showCreateForm ? '' : 'display: none;'
            ])
            ->columnSpan(1);
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

        if (empty($this->searchCnic) && empty($this->searchWeaponNo) && empty($this->searchFslDiaryNo)) {
            Notification::make()
                ->title('Please enter at least one search criteria')
                ->warning()
                ->send();
            return;
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