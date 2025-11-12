<?php

namespace App\Filament\Resources\ArmDealerResource\Pages;

use App\Filament\Resources\ArmDealerResource;
use App\Services\GeocodingService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditArmDealer extends EditRecord
{
    protected static string $resource = ArmDealerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->visible(fn () => auth()->user()?->can('delete arm dealers') ?? false),
            Actions\RestoreAction::make()
                ->visible(fn () => auth()->user()?->can('edit arm dealers') ?? false),
            Actions\ForceDeleteAction::make()
                ->visible(fn () => auth()->user()?->can('delete arm dealers') ?? false),
        ];
    }

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
}

