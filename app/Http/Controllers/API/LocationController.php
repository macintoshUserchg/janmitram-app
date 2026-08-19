<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Services\GoogleMapsService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LocationController extends Controller
{
    /**
     * Default platform fallback hub coordinates (Jaipur Central Hub).
     */
    protected const DEFAULT_LAT = 26.9985869;

    protected const DEFAULT_LNG = 75.7680702;

    protected const DEFAULT_CITY = 'Jaipur';

    protected const DEFAULT_STATE = 'Rajasthan';

    protected const DEFAULT_PINCODE = '302013';

    /**
     * Resolve user location with IP Geolocation as 1st priority,
     * calculate nearest shops, and return full vicinity details.
     */
    public function resolve(Request $request): JsonResponse
    {
        $lat = null;
        $lng = null;
        $city = self::DEFAULT_CITY;
        $state = self::DEFAULT_STATE;
        $pincode = self::DEFAULT_PINCODE;
        $source = 'ip';

        // Check if explicit coordinates were passed (e.g. user refreshed or refined via GPS)
        if ($request->filled('latitude') && $request->filled('longitude')) {
            $lat = (float) $request->latitude;
            $lng = (float) $request->longitude;
            $source = $request->input('source', 'gps');
            if ($request->filled('city')) {
                $city = $request->city;
            }
            if ($request->filled('pincode')) {
                $pincode = $request->pincode;
            }
        } else {
            // 1st Priority: Automated IP Geolocation
            $clientIp = $this->getClientIp($request);
            $geoData = $this->lookupIpGeolocation($clientIp);

            if ($geoData && isset($geoData['latitude'], $geoData['longitude']) && $geoData['latitude'] && $geoData['longitude']) {
                $lat = (float) $geoData['latitude'];
                $lng = (float) $geoData['longitude'];
                $city = $geoData['city'] ?? $city;
                $state = $geoData['state'] ?? $state;
                $pincode = $geoData['pincode'] ?? $pincode;
                $source = 'ip';
            } else {
                // Fallback to platform central hub
                $lat = self::DEFAULT_LAT;
                $lng = self::DEFAULT_LNG;
                $source = 'default_hub';
            }
        }

        // Rank shops by distance from resolved (lat, lng)
        $rankedShops = $this->calculateShopDistances($lat, $lng);
        $nearestShop = $rankedShops->first();

        return $this->json('Location resolved successfully', [
            'source' => $source,
            'city' => $city,
            'state' => $state,
            'pincode' => $pincode,
            'latitude' => $lat,
            'longitude' => $lng,
            'nearest_shop' => $nearestShop,
            'nearby_shops' => $rankedShops->values(),
        ]);
    }

    /**
     * Get nearest shops based on latitude and longitude coordinates.
     */
    public function nearestShops(Request $request): JsonResponse
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $lat = (float) $request->latitude;
        $lng = (float) $request->longitude;

        $rankedShops = $this->calculateShopDistances($lat, $lng);

        return $this->json('Nearest shops retrieved', [
            'latitude' => $lat,
            'longitude' => $lng,
            'nearest_shop' => $rankedShops->first(),
            'shops' => $rankedShops->values(),
        ]);
    }

    /**
     * Resolve location and nearest shop by postal Pin-code.
     */
    public function byPincode(Request $request, GoogleMapsService $googleMaps): JsonResponse
    {
        $request->validate([
            'pincode' => 'required|string|min:4|max:10',
        ]);

        $pincode = trim($request->pincode);
        $cacheKey = "pincode_geo_{$pincode}";

        $geoData = Cache::remember($cacheKey, 60 * 24 * 30, function () use ($pincode, $googleMaps) {
            // Try Google Geocoding first if available
            try {
                $geocode = $googleMaps->geocodeAddress($pincode.', India');
                if ($geocode && isset($geocode['lat'], $geocode['lng'])) {
                    return [
                        'latitude' => (float) $geocode['lat'],
                        'longitude' => (float) $geocode['lng'],
                        'city' => $geocode['city'] ?? self::DEFAULT_CITY,
                        'state' => $geocode['state'] ?? self::DEFAULT_STATE,
                        'pincode' => $pincode,
                    ];
                }
            } catch (Exception $e) {
                Log::debug('Google Geocode error for pincode '.$pincode.': '.$e->getMessage());
            }

            // Fallback to postal pincode API for India
            try {
                $response = Http::timeout(4)->get("https://api.postalpincode.in/pincode/{$pincode}");
                if ($response->successful() && isset($response[0]['PostOffice'][0])) {
                    $po = $response[0]['PostOffice'][0];
                    $district = $po['District'] ?? self::DEFAULT_CITY;
                    $state = $po['State'] ?? self::DEFAULT_STATE;

                    // Geocode city/district name
                    $geocode = $googleMaps->geocodeAddress("{$district}, {$state}, India");
                    if ($geocode && isset($geocode['lat'], $geocode['lng'])) {
                        return [
                            'latitude' => (float) $geocode['lat'],
                            'longitude' => (float) $geocode['lng'],
                            'city' => $district,
                            'state' => $state,
                            'pincode' => $pincode,
                        ];
                    }
                }
            } catch (Exception $e) {
                Log::debug('Postal Pincode API error: '.$e->getMessage());
            }

            return null;
        });

        if (! $geoData) {
            return $this->json('Pincode could not be resolved', [
                'pincode' => $pincode,
                'nearest_shop' => null,
            ], 404);
        }

        $lat = $geoData['latitude'];
        $lng = $geoData['longitude'];
        $rankedShops = $this->calculateShopDistances($lat, $lng);

        return $this->json('Pincode resolved successfully', [
            'source' => 'pincode',
            'city' => $geoData['city'],
            'state' => $geoData['state'],
            'pincode' => $pincode,
            'latitude' => $lat,
            'longitude' => $lng,
            'nearest_shop' => $rankedShops->first(),
            'nearby_shops' => $rankedShops->values(),
        ]);
    }

    /**
     * Compute distance in km for all active shops, sorted nearest first.
     */
    protected function calculateShopDistances(float $lat, float $lng)
    {
        $shops = Shop::where('status', 1)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        return $shops->map(function ($shop) use ($lat, $lng) {
            $shopLat = (float) $shop->latitude;
            $shopLng = (float) $shop->longitude;
            $distanceKm = haversineKm($lat, $lng, $shopLat, $shopLng);

            return [
                'id' => $shop->id,
                'name' => $shop->name,
                'address' => $shop->address,
                'latitude' => $shopLat,
                'longitude' => $shopLng,
                'distance_km' => round($distanceKm, 2),
                'estimated_delivery_time' => $shop->estimated_delivery_time ?? ($distanceKm <= 10 ? '30-45 mins' : '1-2 days'),
                'min_order_amount' => (float) $shop->min_order_amount,
                'delivery_charge' => (float) $shop->delivery_charge,
                'is_in_local_radius' => (bool) ($distanceKm <= 25),
            ];
        })->sortBy('distance_km')->values();
    }

    /**
     * Extract actual client IP considering reverse proxies / Cloudflare.
     */
    protected function getClientIp(Request $request): string
    {
        $headers = [
            'CF-Connecting-IP',
            'X-Real-IP',
            'X-Forwarded-For',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_CLIENT_IP',
        ];

        foreach ($headers as $header) {
            $value = $request->header($header);
            if (! empty($value)) {
                $ips = explode(',', $value);
                $ip = trim($ips[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }

        return $request->ip() ?? '127.0.0.1';
    }

    /**
     * Lookup IP Geolocation with caching to prevent external rate limits.
     */
    protected function lookupIpGeolocation(string $ip): ?array
    {
        // Don't lookup private/local ranges
        if (! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return null;
        }

        $cacheKey = "ip_geo_{$ip}";

        return Cache::remember($cacheKey, 60 * 24 * 7, function () use ($ip) {
            // 1. Try ip-api.com
            try {
                $response = Http::timeout(3)->get("http://ip-api.com/json/{$ip}?fields=status,message,country,regionName,city,zip,lat,lon");
                if ($response->successful() && $response->json('status') === 'success') {
                    return [
                        'latitude' => (float) $response->json('lat'),
                        'longitude' => (float) $response->json('lon'),
                        'city' => $response->json('city') ?: self::DEFAULT_CITY,
                        'state' => $response->json('regionName') ?: self::DEFAULT_STATE,
                        'pincode' => $response->json('zip') ?: self::DEFAULT_PINCODE,
                    ];
                }
            } catch (Exception $e) {
                Log::debug('ip-api.com lookup error: '.$e->getMessage());
            }

            // 2. Try ipapi.co fallback
            try {
                $response = Http::timeout(3)->get("https://ipapi.co/{$ip}/json/");
                if ($response->successful() && ! $response->json('error')) {
                    return [
                        'latitude' => (float) $response->json('latitude'),
                        'longitude' => (float) $response->json('longitude'),
                        'city' => $response->json('city') ?: self::DEFAULT_CITY,
                        'state' => $response->json('region') ?: self::DEFAULT_STATE,
                        'pincode' => $response->json('postal') ?: self::DEFAULT_PINCODE,
                    ];
                }
            } catch (Exception $e) {
                Log::debug('ipapi.co lookup error: '.$e->getMessage());
            }

            return null;
        });
    }
}
