<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeocodingService
{
    /**
     * Get coordinates for a given address.
     *
     * @param string $address
     * @return array|null Returns ['lat' => float, 'lng' => float] or null if not found
     */
    public function getCoordinates(string $address): ?array
    {
        if (empty($address)) {
            return null;
        }

        try {
            // Using OpenStreetMap Nominatim API
            // Requirement: Must provide a valid User-Agent
            $response = Http::withHeaders([
                'User-Agent' => config('app.name', 'Laravel') . ' GeocodingService/1.0',
            ])->get('https://nominatim.openstreetmap.org/search', [
                'q' => $address,
                'format' => 'json',
                'limit' => 1,
            ]);

            if ($response->successful() && !empty($response->json())) {
                $data = $response->json()[0];
                return [
                    'lat' => (float) $data['lat'],
                    'lng' => (float) $data['lon'],
                ];
            }
        } catch (\Exception $e) {
            Log::error('Geocoding failed for address: ' . $address . '. Error: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Reverse geocode coordinates to get address details.
     *
     * @param float $lat
     * @param float $lng
     * @return array|null Returns structured address or null if not found
     */
    public function reverseGeocode(float $lat, float $lng): ?array
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => config('app.name', 'Laravel') . ' GeocodingService/1.0',
            ])->get('https://nominatim.openstreetmap.org/reverse', [
                'lat' => $lat,
                'lon' => $lng,
                'format' => 'json',
                'addressdetails' => 1,
            ]);

            if ($response->successful() && !empty($response->json())) {
                $data = $response->json();
                $address = $data['address'] ?? [];

                return [
                    'country' => $address['country'] ?? null,
                    'region' => $address['state'] ?? $address['province'] ?? $address['region'] ?? null,
                    'district' => $address['county'] ?? $address['state_district'] ?? null,
                    'city' => $address['city'] ?? $address['town'] ?? $address['village'] ?? null,
                    'street' => $address['road'] ?? null,
                    'address' => $address['house_number'] ?? null,
                    'full_address' => $data['display_name'] ?? null,
                ];
            }
        } catch (\Exception $e) {
            Log::error('Reverse geocoding failed for coordinates: ' . $lat . ', ' . $lng . '. Error: ' . $e->getMessage());
        }

        return null;
    }
}
