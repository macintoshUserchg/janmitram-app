<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OlaMapsService
{
    protected string $apiKey;

    protected string $clientSecret;

    protected string $baseUrl;

    protected string $tilesUrl;

    public function __construct()
    {
        $this->apiKey = (string) config('services.olamaps.api_key', '');
        $this->clientSecret = (string) config('services.olamaps.client_secret', '');
        $this->baseUrl = (string) config('services.olamaps.api_base_url', 'https://api.olamaps.io');
        $this->tilesUrl = (string) config('services.olamaps.tiles_url', 'https://api.olamaps.io/tiles/vector/v1/styles/default-light-standard/style.json');
    }

    /**
     * Get or generate Ola Maps OAuth Bearer Token.
     */
    public function getAccessToken(): ?string
    {
        if (empty($this->apiKey) || empty($this->clientSecret)) {
            return null;
        }

        return Cache::remember('olamaps_bearer_token', 3600, function () {
            try {
                $response = Http::asForm()->timeout(5)->post("{$this->baseUrl}/auth/v1/token", [
                    'grant_type' => 'client_credentials',
                    'scope' => 'openid',
                    'client_id' => $this->apiKey,
                    'client_secret' => $this->clientSecret,
                ]);

                if ($response->successful()) {
                    return $response->json('access_token');
                }
            } catch (\Throwable $e) {
                Log::debug('OlaMaps OAuth token exchange notice: '.$e->getMessage());
            }

            return null;
        });
    }

    /**
     * Get Client Map Configuration for Frontend & Blade.
     */
    public function getClientConfig(): array
    {
        return [
            'api_key' => $this->apiKey,
            'tiles_url' => $this->tilesUrl,
            'base_url' => $this->baseUrl,
            'default_center' => [
                'lat' => 27.0056949,
                'lng' => 75.7775497,
            ],
            'default_zoom' => 13,
            'has_api_key' => ! empty($this->apiKey),
        ];
    }

    /**
     * Autocomplete Place Search with Fallback.
     */
    public function autocomplete(string $input, ?float $lat = null, ?float $lng = null, int $limit = 5): array
    {
        if (empty(trim($input))) {
            return [];
        }

        // 1. Try with OAuth Access Token if available
        $token = $this->getAccessToken();
        if ($token) {
            try {
                $params = ['input' => $input];
                if ($lat !== null && $lng !== null) {
                    $params['location'] = "{$lat},{$lng}";
                }
                $response = Http::withToken($token)->timeout(5)->get("{$this->baseUrl}/places/v1/autocomplete", $params);
                if ($response->successful()) {
                    $results = $this->formatPredictions($response->json(), $limit);
                    if (! empty($results)) {
                        return $results;
                    }
                }
            } catch (\Throwable $e) {
                Log::debug('OlaMaps OAuth autocomplete fallback: '.$e->getMessage());
            }
        }

        // 2. Try with direct API Key
        if (! empty($this->apiKey)) {
            try {
                $params = [
                    'input' => $input,
                    'api_key' => $this->apiKey,
                ];
                if ($lat !== null && $lng !== null) {
                    $params['location'] = "{$lat},{$lng}";
                }
                $response = Http::timeout(5)->get("{$this->baseUrl}/places/v1/autocomplete", $params);
                if ($response->successful()) {
                    $results = $this->formatPredictions($response->json(), $limit);
                    if (! empty($results)) {
                        return $results;
                    }
                }
            } catch (\Throwable $e) {
                Log::debug('OlaMaps API key autocomplete notice: '.$e->getMessage());
            }
        }

        // 3. Resilient Fallback to OpenStreetMap
        try {
            $osmResponse = Http::timeout(5)
                ->withHeaders(['User-Agent' => 'JanmitramApp/1.0'])
                ->get('https://nominatim.openstreetmap.org/search', [
                    'format' => 'json',
                    'q' => $input,
                    'limit' => $limit,
                    'addressdetails' => 1,
                ]);

            if ($osmResponse->successful()) {
                $osmData = $osmResponse->json();
                $results = [];
                foreach ($osmData as $item) {
                    $results[] = [
                        'display_name' => $item['display_name'] ?? '',
                        'place_id' => $item['place_id'] ?? null,
                        'lat' => (float) ($item['lat'] ?? 0),
                        'lng' => (float) ($item['lon'] ?? 0),
                        'source' => 'osm_fallback',
                    ];
                }

                return $results;
            }
        } catch (\Throwable $e) {
            Log::warning('Nominatim fallback autocomplete failed: '.$e->getMessage());
        }

        return [];
    }

    protected function formatPredictions(array $data, int $limit): array
    {
        $predictions = $data['predictions'] ?? $data['results'] ?? [];
        $results = [];

        foreach ($predictions as $item) {
            $pLat = $item['geometry']['location']['lat'] ?? $item['lat'] ?? null;
            $pLng = $item['geometry']['location']['lng'] ?? $item['lng'] ?? null;

            $results[] = [
                'display_name' => $item['description'] ?? $item['formatted_address'] ?? $item['name'] ?? '',
                'place_id' => $item['place_id'] ?? null,
                'lat' => $pLat ? (float) $pLat : null,
                'lng' => $pLng ? (float) $pLng : null,
                'source' => 'olamaps',
            ];
        }

        return array_slice($results, 0, $limit);
    }

    /**
     * Reverse Geocode (Lat/Lng -> Address string).
     */
    public function reverseGeocode(float $lat, float $lng): ?array
    {
        if (empty($lat) && empty($lng)) {
            return null;
        }

        // 1. Try with OAuth token
        $token = $this->getAccessToken();
        if ($token) {
            try {
                $response = Http::withToken($token)->timeout(5)->get("{$this->baseUrl}/places/v1/reverse-geocode", [
                    'latlng' => "{$lat},{$lng}",
                ]);
                if ($response->successful()) {
                    $firstResult = $response->json('results.0');
                    if ($firstResult) {
                        return [
                            'display_name' => $firstResult['formatted_address'] ?? '',
                            'lat' => (float) ($firstResult['geometry']['location']['lat'] ?? $lat),
                            'lng' => (float) ($firstResult['geometry']['location']['lng'] ?? $lng),
                            'source' => 'olamaps',
                        ];
                    }
                }
            } catch (\Throwable $e) {
                Log::debug('OlaMaps OAuth reverseGeocode notice: '.$e->getMessage());
            }
        }

        // 2. Try with direct API key
        if (! empty($this->apiKey)) {
            try {
                $response = Http::timeout(5)->get("{$this->baseUrl}/places/v1/reverse-geocode", [
                    'latlng' => "{$lat},{$lng}",
                    'api_key' => $this->apiKey,
                ]);

                if ($response->successful()) {
                    $firstResult = $response->json('results.0');
                    if ($firstResult) {
                        return [
                            'display_name' => $firstResult['formatted_address'] ?? '',
                            'lat' => (float) ($firstResult['geometry']['location']['lat'] ?? $lat),
                            'lng' => (float) ($firstResult['geometry']['location']['lng'] ?? $lng),
                            'source' => 'olamaps',
                        ];
                    }
                }
            } catch (\Throwable $e) {
                Log::debug('OlaMaps reverseGeocode notice: '.$e->getMessage());
            }
        }

        // 3. Fallback to Nominatim
        try {
            $osmResponse = Http::timeout(5)
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
            Log::warning('Nominatim reverseGeocode fallback failed: '.$e->getMessage());
        }

        return null;
    }

    /**
     * Get Driving Directions / Route Polyline between two points.
     */
    public function getDirections(float $originLat, float $originLng, float $destLat, float $destLng): array
    {
        if (! empty($this->apiKey)) {
            try {
                $response = Http::timeout(5)->get("{$this->baseUrl}/routing/v1/directions", [
                    'origin' => "{$originLat},{$originLng}",
                    'destination' => "{$destLat},{$destLng}",
                    'api_key' => $this->apiKey,
                ]);

                if ($response->successful()) {
                    $routes = $response->json('routes', []);
                    if (! empty($routes)) {
                        return [
                            'success' => true,
                            'route' => $routes[0],
                            'geometry' => $routes[0]['overview_polyline'] ?? $routes[0]['geometry'] ?? null,
                            'distance' => $routes[0]['legs'][0]['distance']['value'] ?? null,
                            'duration' => $routes[0]['legs'][0]['duration']['value'] ?? null,
                            'source' => 'olamaps',
                        ];
                    }
                }
            } catch (\Throwable $e) {
                Log::debug('OlaMaps directions notice: '.$e->getMessage());
            }
        }

        // Straight-line fallback polyline coordinates
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
