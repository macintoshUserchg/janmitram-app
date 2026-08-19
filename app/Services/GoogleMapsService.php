<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleMapsService
{
    protected string $apiKey;

    public function __construct()
    {
        $this->apiKey = (string) config('services.google_maps.key', '');
    }

    /**
     * Return public map client configuration.
     */
    public function getClientConfig(): array
    {
        return [
            'api_key' => $this->apiKey,
            'default_center' => [
                'lat' => 27.0056949,
                'lng' => 75.7775497,
            ],
            'default_zoom' => 15,
            'has_api_key' => ! empty($this->apiKey),
            'provider' => 'google_maps',
        ];
    }

    /**
     * Google Places Autocomplete Search (supports Places API New & Legacy + fallback).
     */
    public function autocomplete(string $input, ?float $lat = null, ?float $lng = null, int $limit = 6): array
    {
        $input = trim($input);
        if (empty($input)) {
            return [];
        }

        // 1. Try Google Places API (New) with Concurrent Pool
        if (! empty($this->apiKey)) {
            try {
                $payload = [
                    'input' => $input,
                    'includedRegionCodes' => ['in'],
                ];
                if ($lat !== null && $lng !== null) {
                    $payload['locationBias'] = [
                        'circle' => [
                            'center' => ['latitude' => $lat, 'longitude' => $lng],
                            'radius' => 50000.0,
                        ],
                    ];
                }

                $newResponse = Http::timeout(3)
                    ->withHeaders([
                        'Content-Type' => 'application/json',
                        'X-Goog-Api-Key' => $this->apiKey,
                    ])
                    ->post('https://places.googleapis.com/v1/places:autocomplete', $payload);

                if ($newResponse->successful()) {
                    $suggestions = $newResponse->json('suggestions', []);
                    if (! empty($suggestions)) {
                        $topSuggestions = array_slice($suggestions, 0, $limit);
                        $placeIds = [];
                        foreach ($topSuggestions as $s) {
                            $pid = $s['placePrediction']['placeId'] ?? null;
                            if ($pid) {
                                $placeIds[] = $pid;
                            }
                        }

                        // Batch resolve coordinates concurrently in parallel
                        $coordsMap = $this->getPlacesCoordinatesBatch($placeIds);

                        $results = [];
                        foreach ($topSuggestions as $s) {
                            $pred = $s['placePrediction'] ?? [];
                            $placeId = $pred['placeId'] ?? null;
                            $text = $pred['text']['text'] ?? '';
                            $mainText = $pred['structuredFormat']['mainText']['text'] ?? explode(',', $text)[0];
                            $secondaryText = $pred['structuredFormat']['secondaryText']['text'] ?? '';

                            $coords = $coordsMap[$placeId] ?? null;

                            $results[] = [
                                'display_name' => $text,
                                'main_text' => $mainText,
                                'secondary_text' => $secondaryText,
                                'place_id' => $placeId,
                                'lat' => $coords['lat'] ?? null,
                                'lng' => $coords['lng'] ?? null,
                                'source' => 'google_maps_new',
                            ];
                        }

                        if (! empty($results)) {
                            return $results;
                        }
                    }
                }
            } catch (\Throwable $e) {
                Log::debug('Google Places API (New) notice: ' . $e->getMessage());
            }

            // 2. Try Google Places API (Legacy)
            try {
                $params = [
                    'input' => $input,
                    'key' => $this->apiKey,
                    'components' => 'country:in',
                    'language' => 'en',
                ];
                if ($lat !== null && $lng !== null) {
                    $params['location'] = "{$lat},{$lng}";
                    $params['radius'] = 50000;
                }

                $legacyRes = Http::timeout(3)->get('https://maps.googleapis.com/maps/api/place/autocomplete/json', $params);
                if ($legacyRes->successful()) {
                    $preds = $legacyRes->json('predictions', []);
                    if (! empty($preds)) {
                        $topPreds = array_slice($preds, 0, $limit);
                        $placeIds = array_filter(array_column($topPreds, 'place_id'));
                        $coordsMap = $this->getPlacesCoordinatesBatch($placeIds);

                        $results = [];
                        foreach ($topPreds as $pred) {
                            $placeId = $pred['place_id'] ?? null;
                            $coords = $coordsMap[$placeId] ?? null;

                            $results[] = [
                                'display_name' => $pred['description'] ?? '',
                                'main_text' => $pred['structured_formatting']['main_text'] ?? '',
                                'secondary_text' => $pred['structured_formatting']['secondary_text'] ?? '',
                                'place_id' => $placeId,
                                'lat' => $coords['lat'] ?? null,
                                'lng' => $coords['lng'] ?? null,
                                'source' => 'google_maps_legacy',
                            ];
                        }

                        if (! empty($results)) {
                            return $results;
                        }
                    }
                }
            } catch (\Throwable $e) {
                Log::debug('Google Places Legacy notice: ' . $e->getMessage());
            }
        }

        // 3. OpenStreetMap Nominatim Fallback
        try {
            $osmResponse = Http::timeout(3)
                ->withHeaders(['User-Agent' => 'JanmitramApp/1.0'])
                ->get('https://nominatim.openstreetmap.org/search', [
                    'format' => 'json',
                    'q' => $input,
                    'limit' => $limit,
                    'addressdetails' => 1,
                    'countrycodes' => 'in',
                ]);

            if ($osmResponse->successful()) {
                $osmData = $osmResponse->json();
                if (! empty($osmData)) {
                    $results = [];
                    foreach ($osmData as $item) {
                        $displayName = $item['display_name'] ?? '';
                        $parts = explode(',', $displayName);
                        $results[] = [
                            'display_name' => $displayName,
                            'main_text' => trim($parts[0] ?? ''),
                            'secondary_text' => trim(implode(',', array_slice($parts, 1))),
                            'place_id' => $item['place_id'] ?? null,
                            'lat' => (float) ($item['lat'] ?? 0),
                            'lng' => (float) ($item['lon'] ?? 0),
                            'source' => 'osm_fallback',
                        ];
                    }

                    return $results;
                }
            }
        } catch (\Throwable $e) {
            Log::debug('Nominatim autocomplete notice: ' . $e->getMessage());
        }

        // 4. Photon Komoot Fuzzy Geocoder Fallback
        try {
            $photonResponse = Http::timeout(3)->get('https://photon.komoot.io/api/', [
                'q' => $input,
                'limit' => $limit,
                'lang' => 'en',
            ]);

            if ($photonResponse->successful()) {
                $features = $photonResponse->json('features', []);
                if (! empty($features)) {
                    $results = [];
                    foreach ($features as $f) {
                        $coords = $f['geometry']['coordinates'] ?? [0, 0];
                        $props = $f['properties'] ?? [];
                        $name = $props['name'] ?? '';
                        $nameParts = array_filter([
                            $props['street'] ?? null,
                            $props['district'] ?? null,
                            $props['city'] ?? null,
                            $props['state'] ?? null,
                        ]);
                        $secondary = implode(', ', $nameParts);
                        $displayName = $name ? ($secondary ? "{$name}, {$secondary}" : $name) : $secondary;

                        if (! empty($displayName)) {
                            $results[] = [
                                'display_name' => $displayName,
                                'main_text' => $name ?: $props['street'] ?? $displayName,
                                'secondary_text' => $secondary,
                                'place_id' => $props['osm_id'] ?? null,
                                'lat' => (float) ($coords[1] ?? 0),
                                'lng' => (float) ($coords[0] ?? 0),
                                'source' => 'photon_fuzzy',
                            ];
                        }
                    }

                    if (! empty($results)) {
                        return $results;
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::debug('Photon fuzzy geocode notice: ' . $e->getMessage());
        }

        return [];
    }

    /**
     * Batch resolve Coordinates for multiple Place IDs concurrently via HTTP Pool.
     */
    protected function getPlacesCoordinatesBatch(array $placeIds): array
    {
        if (empty($placeIds) || empty($this->apiKey)) {
            return [];
        }

        try {
            $responses = Http::pool(function ($pool) use ($placeIds) {
                $reqs = [];
                foreach ($placeIds as $id) {
                    $reqs[$id] = $pool->withHeaders([
                        'X-Goog-Api-Key' => $this->apiKey,
                        'X-Goog-FieldMask' => 'location',
                    ])
                        ->timeout(2.5)
                        ->get("https://places.googleapis.com/v1/places/{$id}");
                }
                return $reqs;
            });

            $coordsMap = [];
            foreach ($placeIds as $id) {
                $res = $responses[$id] ?? null;
                if ($res && $res->successful()) {
                    $loc = $res->json('location');
                    if ($loc && isset($loc['latitude'], $loc['longitude'])) {
                        $coordsMap[$id] = [
                            'lat' => (float) $loc['latitude'],
                            'lng' => (float) $loc['longitude'],
                        ];
                    }
                }
            }

            return $coordsMap;
        } catch (\Throwable $e) {
            Log::debug('getPlacesCoordinatesBatch error: ' . $e->getMessage());
        }

        return [];
    }

    /**
     * Get single place details with coordinates.
     */
    public function getPlaceDetails(string $placeId): ?array
    {
        if (empty($placeId) || empty($this->apiKey)) {
            return null;
        }

        try {
            $res = Http::timeout(3)
                ->withHeaders([
                    'X-Goog-Api-Key' => $this->apiKey,
                    'X-Goog-FieldMask' => 'location,displayName,formattedAddress',
                ])
                ->get("https://places.googleapis.com/v1/places/{$placeId}");

            if ($res->successful()) {
                $loc = $res->json('location');
                if ($loc && isset($loc['latitude'], $loc['longitude'])) {
                    return [
                        'lat' => (float) $loc['latitude'],
                        'lng' => (float) $loc['longitude'],
                        'formatted_address' => $res->json('formattedAddress'),
                        'display_name' => $res->json('displayName.text'),
                    ];
                }
            }
        } catch (\Throwable $e) {
            Log::debug('getPlaceDetails error: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Multi-stage Heuristic Search for Complex Addresses.
     */
    public function heuristicSearch(string $query, ?float $lat = null, ?float $lng = null): array
    {
        $query = trim($query);
        if (empty($query)) {
            return [];
        }

        // 1. Direct search
        $direct = $this->autocomplete($query, $lat, $lng, 6);
        if (! empty($direct)) {
            return $direct;
        }

        // 2. Strip unit prefixes (shop, flat, house, plot, gali, etc.)
        $cleaned = preg_replace('/^(shop|flat|house|h\.?no|plot|room|gali|lane|ward|block|khasra)\s*[\#\d\-\w\/,]+/i', '', $query);
        $cleaned = trim($cleaned, " ,-\t\n\r\0\x0B");

        if (! empty($cleaned) && $cleaned !== $query) {
            $cleanedResults = $this->autocomplete($cleaned, $lat, $lng, 6);
            if (! empty($cleanedResults)) {
                return array_map(function ($item) {
                    $item['is_heuristic'] = true;
                    return $item;
                }, $cleanedResults);
            }
        }

        // 3. Progressive chunk decomposition from right to left
        $commaParts = array_map('trim', explode(',', $query));
        if (count($commaParts) > 1) {
            while (count($commaParts) > 1) {
                array_shift($commaParts);
                $subQuery = implode(', ', $commaParts);
                if (strlen($subQuery) >= 3) {
                    $subResults = $this->autocomplete($subQuery, $lat, $lng, 6);
                    if (! empty($subResults)) {
                        return array_map(function ($item) use ($subQuery) {
                            $item['is_heuristic'] = true;
                            $item['heuristic_anchor'] = $subQuery;
                            return $item;
                        }, $subResults);
                    }
                }
            }
        }

        return [];
    }

    /**
     * Reverse Geocode (Lat/Lng -> Formatted Address).
     */
    public function reverseGeocode(float $lat, float $lng): ?array
    {
        if (empty($lat) && empty($lng)) {
            return null;
        }

        // 1. Google Places (New) searchNearby
        if (! empty($this->apiKey)) {
            try {
                $response = Http::timeout(3)
                    ->withHeaders([
                        'Content-Type' => 'application/json',
                        'X-Goog-Api-Key' => $this->apiKey,
                        'X-Goog-FieldMask' => 'places.displayName,places.formattedAddress,places.location',
                    ])
                    ->post('https://places.googleapis.com/v1/places:searchNearby', [
                        'locationRestriction' => [
                            'circle' => [
                                'center' => ['latitude' => $lat, 'longitude' => $lng],
                                'radius' => 150.0,
                            ],
                        ],
                        'maxResultCount' => 1,
                    ]);

                if ($response->successful()) {
                    $firstPlace = $response->json('places.0');
                    if ($firstPlace && ! empty($firstPlace['formattedAddress'])) {
                        return [
                            'display_name' => $firstPlace['formattedAddress'],
                            'lat' => (float) ($firstPlace['location']['latitude'] ?? $lat),
                            'lng' => (float) ($firstPlace['location']['longitude'] ?? $lng),
                            'source' => 'google_maps_new',
                        ];
                    }
                }
            } catch (\Throwable $e) {
                Log::debug('Google Places searchNearby notice: ' . $e->getMessage());
            }

            // 2. Google Geocoding API (Legacy)
            try {
                $response = Http::timeout(3)->get('https://maps.googleapis.com/maps/api/geocode/json', [
                    'latlng' => "{$lat},{$lng}",
                    'key' => $this->apiKey,
                    'language' => 'en',
                ]);

                if ($response->successful()) {
                    $firstResult = $response->json('results.0');
                    if ($firstResult) {
                        return [
                            'display_name' => $firstResult['formatted_address'] ?? '',
                            'lat' => (float) ($firstResult['geometry']['location']['lat'] ?? $lat),
                            'lng' => (float) ($firstResult['geometry']['location']['lng'] ?? $lng),
                            'place_id' => $firstResult['place_id'] ?? null,
                            'source' => 'google_maps',
                        ];
                    }
                }
            } catch (\Throwable $e) {
                Log::debug('Google reverse geocode notice: ' . $e->getMessage());
            }
        }

        // 3. OpenStreetMap Fallback
        try {
            $osmResponse = Http::timeout(3)
                ->withHeaders(['User-Agent' => 'JanmitramApp/1.0'])
                ->get('https://nominatim.openstreetmap.org/reverse', [
                    'format' => 'json',
                    'lat' => $lat,
                    'lon' => $lng,
                    'addressdetails' => 1,
                ]);

            if ($osmResponse->successful()) {
                $osmData = $osmResponse->json();

                return [
                    'display_name' => $osmData['display_name'] ?? '',
                    'lat' => (float) ($osmData['lat'] ?? $lat),
                    'lng' => (float) ($osmData['lon'] ?? $lng),
                    'source' => 'osm_fallback',
                ];
            }
        } catch (\Throwable $e) {
            Log::debug('Nominatim reverse geocode notice: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Turn-by-Turn Route / Directions Polyline.
     */
    public function getDirections(float $originLat, float $originLng, float $destLat, float $destLng): array
    {
        if (! empty($this->apiKey)) {
            try {
                $response = Http::timeout(3)->get('https://maps.googleapis.com/maps/api/directions/json', [
                    'origin' => "{$originLat},{$originLng}",
                    'destination' => "{$destLat},{$destLng}",
                    'key' => $this->apiKey,
                ]);

                if ($response->successful()) {
                    $routes = $response->json('routes', []);
                    if (! empty($routes)) {
                        return [
                            'success' => true,
                            'route' => $routes[0],
                            'geometry' => $routes[0]['overview_polyline']['points'] ?? null,
                            'distance' => $routes[0]['legs'][0]['distance']['value'] ?? null,
                            'duration' => $routes[0]['legs'][0]['duration']['value'] ?? null,
                            'source' => 'google_maps',
                        ];
                    }
                }
            } catch (\Throwable $e) {
                Log::debug('Google directions notice: ' . $e->getMessage());
            }
        }

        return [
            'success' => true,
            'geometry' => [
                [$originLng, $originLat],
                [$destLng, $destLat],
            ],
            'source' => 'direct_line',
        ];
    }
}
