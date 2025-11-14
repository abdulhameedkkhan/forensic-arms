<?php

namespace App\Filament\Resources\ArmDealerResource\Pages;

use App\Filament\Resources\ArmDealerResource;
use App\Services\GeocodingService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateArmDealer extends CreateRecord
{
    protected static string $resource = ArmDealerResource::class;

    public function fetchCoordinates(): void
    {
        $address = $this->form->getState()['address'] ?? null;
        
        if (filled($address) && strlen($address) > 10) {
            try {
                $coordinates = GeocodingService::getCoordinates($address);
                if ($coordinates) {
                    $this->form->fill([
                        'latitude' => number_format($coordinates['latitude'], 8),
                        'longitude' => number_format($coordinates['longitude'], 8),
                    ]);
                    
                    Notification::make()
                        ->title('Coordinates fetched successfully')
                        ->success()
                        ->send();
                } else {
                    Notification::make()
                        ->title('Coordinates not found')
                        ->warning()
                        ->body('Please check the address or enter coordinates manually')
                        ->send();
                }
            } catch (\Exception $e) {
                Notification::make()
                    ->title('Error fetching coordinates')
                    ->danger()
                    ->body($e->getMessage())
                    ->send();
            }
        } else {
            Notification::make()
                ->title('Please enter a complete address')
                ->warning()
                ->body('Address should be at least 10 characters long')
                ->send();
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Auto-set range_id from logged in user if user has a range
        // Always set it if user has range_id, even if form data has NULL or empty value
        $user = auth()->user();
        if ($user && $user->range_id) {
            // Ensure range_id is set as integer to match database type
            $data['range_id'] = (int) $user->range_id;
        }
        
        return $data;
    }
}

