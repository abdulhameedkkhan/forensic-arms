<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeocodingService
{
    /**
     * Get coordinates from address (Pakistan only)
     */
    public static function getCoordinates(string $address): ?array
    {
        try {
            // Clean and prepare address
            $address = trim($address);
            if (empty($address)) {
                return null;
            }

            // Add Pakistan if not already present
            $fullAddress = $address;
            if (stripos($address, 'pakistan') === false && stripos($address, 'pk') === false) {
                $fullAddress = $address . ', Pakistan';
            }
            
            // Using OpenStreetMap Nominatim API (free, no API key needed)
            // Note: Nominatim requires a User-Agent header
            $response = Http::timeout(10)
                ->withHeaders([
                    'User-Agent' => 'ForensicApp/1.0 (Contact: admin@forensic.com)',
                ])
                ->get('https://nominatim.openstreetmap.org/search', [
                    'q' => $fullAddress,
                    'format' => 'json',
                    'countrycodes' => 'pk', // Pakistan only
                    'limit' => 5, // Get more results for better matching
                    'addressdetails' => 1,
                    'extratags' => 1,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (!empty($data) && is_array($data)) {
                    // Try to find the best match
                    foreach ($data as $result) {
                        if (isset($result['lat']) && isset($result['lon'])) {
                            // Check if it's in Pakistan (additional validation)
                            $country = $result['address']['country'] ?? $result['address']['country_code'] ?? '';
                            if (stripos($country, 'pakistan') !== false || 
                                strtolower($country) === 'pk' || 
                                strtolower($country) === 'pakistan') {
                                return [
                                    'latitude' => (float) $result['lat'],
                                    'longitude' => (float) $result['lon'],
                                ];
                            }
                        }
                    }
                    
                    // If no exact match, use first result anyway
                    if (isset($data[0]['lat']) && isset($data[0]['lon'])) {
                        return [
                            'latitude' => (float) $data[0]['lat'],
                            'longitude' => (float) $data[0]['lon'],
                        ];
                    }
                }
            }
            
            Log::warning('Geocoding: No results found for address: ' . $address);
            return null;
        } catch (\Exception $e) {
            Log::error('Geocoding error: ' . $e->getMessage() . ' | Address: ' . $address);
            return null;
        }
    }
}

