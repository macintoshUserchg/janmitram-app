<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LocationController extends Controller
{
    protected const DEFAULT_CITY = 'Jaipur';

    protected const DEFAULT_STATE = 'Rajasthan';

    protected const DEFAULT_PINCODE = '302013';

    /**
     * Resolve user location using IP Geolocation only to match and display
     * shops and products available in the user's vicinity.
     */
    public function resolve(Request $request): JsonResponse
    {
        $city = self::DEFAULT_CITY;
        $state = self::DEFAULT_STATE;
        $pincode = self::DEFAULT_PINCODE;
        $source = 'ip';

        // Check if explicit city/pincode was selected by user
        if ($request->filled('city')) {
            $city = trim($request->city);
            $source = 'manual';
            if ($request->filled('pincode')) {
                $pincode = trim($request->pincode);
            }
        } else {
            // Automated IP Geolocation (Zero Permission)
            $clientIp = $this->getClientIp($request);
            $geoData = $this->lookupIpGeolocation($clientIp);

            if ($geoData) {
                $city = $geoData['city'] ?? $city;
                $state = $geoData['state'] ?? $state;
                $pincode = $geoData['pincode'] ?? $pincode;
                $source = 'ip';
            } else {
                $source = 'default';
            }
        }

        // Match shops by city/vicinity
        $matchedShops = $this->getShopsInVicinity($city, $state);
        $primaryShop = $matchedShops->first() ?? Shop::where('status', 1)->first();

        return $this->json('Location resolved successfully', [
            'source' => $source,
            'city' => $city,
            'state' => $state,
            'pincode' => $pincode,
            'nearest_shop' => $primaryShop ? [
                'id' => $primaryShop->id,
                'name' => $primaryShop->name,
                'address' => $primaryShop->address,
                'estimated_delivery_time' => $primaryShop->estimated_delivery_time ?? 'Same Day Delivery',
                'min_order_amount' => (float) $primaryShop->min_order_amount,
                'delivery_charge' => (float) $primaryShop->delivery_charge,
            ] : null,
            'nearby_shops' => $matchedShops->map(function ($shop) {
                return [
                    'id' => $shop->id,
                    'name' => $shop->name,
                    'address' => $shop->address,
                    'estimated_delivery_time' => $shop->estimated_delivery_time ?? 'Same Day Delivery',
                    'min_order_amount' => (float) $shop->min_order_amount,
                    'delivery_charge' => (float) $shop->delivery_charge,
                ];
            })->values(),
        ]);
    }

    /**
     * Resolve location and shops by postal PIN code.
     */
    public function byPincode(Request $request): JsonResponse
    {
        $request->validate([
            'pincode' => 'required|string|min:4|max:10',
        ]);

        $pincode = trim($request->pincode);
        $cacheKey = "pincode_city_{$pincode}";

        $geoData = Cache::remember($cacheKey, 60 * 24 * 30, function () use ($pincode) {
            try {
                $response = Http::timeout(4)->get("https://api.postalpincode.in/pincode/{$pincode}");
                if ($response->successful() && isset($response[0]['PostOffice'][0])) {
                    $po = $response[0]['PostOffice'][0];

                    return [
                        'city' => $po['District'] ?? self::DEFAULT_CITY,
                        'state' => $po['State'] ?? self::DEFAULT_STATE,
                        'pincode' => $pincode,
                    ];
                }
            } catch (Exception $e) {
                Log::debug('Postal Pincode API error: '.$e->getMessage());
            }

            return null;
        });

        $city = $geoData['city'] ?? self::DEFAULT_CITY;
        $state = $geoData['state'] ?? self::DEFAULT_STATE;

        $matchedShops = $this->getShopsInVicinity($city, $state);
        $primaryShop = $matchedShops->first() ?? Shop::where('status', 1)->first();

        return $this->json('Pincode resolved successfully', [
            'source' => 'pincode',
            'city' => $city,
            'state' => $state,
            'pincode' => $pincode,
            'nearest_shop' => $primaryShop ? [
                'id' => $primaryShop->id,
                'name' => $primaryShop->name,
                'address' => $primaryShop->address,
                'estimated_delivery_time' => $primaryShop->estimated_delivery_time ?? 'Same Day Delivery',
                'min_order_amount' => (float) $primaryShop->min_order_amount,
                'delivery_charge' => (float) $primaryShop->delivery_charge,
            ] : null,
            'nearby_shops' => $matchedShops->map(function ($shop) {
                return [
                    'id' => $shop->id,
                    'name' => $shop->name,
                    'address' => $shop->address,
                    'estimated_delivery_time' => $shop->estimated_delivery_time ?? 'Same Day Delivery',
                ];
            })->values(),
        ]);
    }

    /**
     * Find active shops matching the user's city or state.
     */
    protected function getShopsInVicinity(string $city, string $state)
    {
        $cityTerm = strtolower(trim($city));

        // Search shops by city keywords in name or address
        $query = Shop::where('status', 1);

        if (! empty($cityTerm)) {
            // City alias matching e.g. Jaipur / jpr, Mumbai / Bombay
            $searchTerms = [$cityTerm];
            if (str_contains($cityTerm, 'jaipur')) {
                $searchTerms[] = 'jpr';
            } elseif ($cityTerm === 'jpr') {
                $searchTerms[] = 'jaipur';
            }

            $query->where(function ($q) use ($searchTerms) {
                foreach ($searchTerms as $term) {
                    $q->orWhere('name', 'like', "%{$term}%")
                        ->orWhere('address', 'like', "%{$term}%");
                }
            });
        }

        $shops = $query->get();

        // If no shops in that specific city, fall back to all active shops (with default shops first)
        if ($shops->isEmpty()) {
            $shops = Shop::where('status', 1)->get();
        }

        return $shops;
    }

    /**
     * Extract actual client IP.
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
     * Lookup IP Geolocation for City and State.
     */
    protected function lookupIpGeolocation(string $ip): ?array
    {
        if (! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return null;
        }

        $cacheKey = "ip_geo_city_{$ip}";

        return Cache::remember($cacheKey, 60 * 24 * 7, function () use ($ip) {
            try {
                $response = Http::timeout(3)->get("http://ip-api.com/json/{$ip}?fields=status,country,regionName,city,zip");
                if ($response->successful() && $response->json('status') === 'success') {
                    return [
                        'city' => $response->json('city') ?: self::DEFAULT_CITY,
                        'state' => $response->json('regionName') ?: self::DEFAULT_STATE,
                        'pincode' => $response->json('zip') ?: self::DEFAULT_PINCODE,
                    ];
                }
            } catch (Exception $e) {
                Log::debug('ip-api.com lookup error: '.$e->getMessage());
            }

            try {
                $response = Http::timeout(3)->get("https://ipapi.co/{$ip}/json/");
                if ($response->successful() && ! $response->json('error')) {
                    return [
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
