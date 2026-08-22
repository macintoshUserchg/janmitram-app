<?php

use App\Models\Cart;
use App\Models\CartAccessToken;
use App\Models\Currency;
use App\Models\DeliveryCharge;
use App\Models\GeneraleSetting;
use App\Models\User;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\PersonalAccessToken;
use Nwidart\Modules\Facades\Module;
use Spatie\Permission\Exceptions\RoleDoesNotExist;

if (! function_exists('generaleSetting')) {
    /**
     * Get the generale setting Or shop Or default currency.
     *
     * @param  string|null  $type  = 'setting|shop|rootShop|defaultCurrency'
     *
     * @type setting|shop|rootShop|defaultCurrency
     *
     * @default GeneraleSetting
     *
     * @return GeneraleSetting|Shop|Currency
     *
     * @throws Exception
     */
    function generaleSetting($type = null, $authUser = null)
    {
        // Cache general setting data for  30 days
        $generaleSetting = Cache::remember('generale_setting', 60 * 24 * 30, function () {
            return GeneraleSetting::first();
        });

        if ($type == 'setting' || $type == null) {
            return $generaleSetting;
        }

        if ($type == 'rootShop') {
            return Cache::remember('admin_shop', 60 * 24 * 7, function () {
                try {
                    return User::role('root')->whereHas('shop')->first()?->shop;
                } catch (RoleDoesNotExist) {
                    return null;
                }
            });
        }

        if ($type == 'shop') {
            if ($generaleSetting?->shop_type == 'single') {
                $shop = selfGetRootShop();
            } else {
                /** @var User */
                $user = $authUser ?? auth()->user();
                $shop = $user?->shop ?? $user?->myShop;
            }

            if (! $shop) {
                $shop = selfGetRootShop();
            }

            return $shop;
        }

        if ($type == 'defaultCurrency') {
            $defaultCurrency = Cache::remember('default_currency', 60 * 24 * 30, function () {
                return Currency::where('is_default', 1)->first();
            });

            return $defaultCurrency;
        }

        return $generaleSetting;
    }
}

/**
 * Safely get the root user's shop, returning null if the role doesn't exist.
 */
function selfGetRootShop()
{
    try {
        return User::role('root')->whereHas('shop')->first()?->shop;
    } catch (RoleDoesNotExist) {
        return null;
    }
}

if (! function_exists('showCurrency')) {

    /**
     * Show the currency in the given amount.
     *
     * @param  float  $amount
     */
    function showCurrency($amount = null): string
    {
        $generaleSetting = generaleSetting('setting');

        $currency = $generaleSetting?->currency ?? '₹';

        $amount = ($amount == 0 || $amount == null) ? 0 : $amount;

        if ($generaleSetting?->currency_position == 'suffix') {
            return $amount.$currency;
        }

        return $currency.$amount;
    }
}

if (! function_exists('getDeliveryCharge')) {

    /**
     * get the delivery charge.
     *
     * @param  int  $orderQuantity
     */
    function getDeliveryCharge($orderQuantity): float
    {
        $deliveryCharge = DeliveryCharge::where('min_qty', '<=', $orderQuantity)
            ->where('max_qty', '>=', $orderQuantity)
            ->first();

        return $deliveryCharge?->charge ?? 0;
    }
}

if (! function_exists('permissionName')) {

    /**
     * get the permission name for the customer readable.
     *
     * @param  string  $permission
     */
    function permissionName($permission): string
    {
        $customerReadAbleNames = config('acl.customerReadableNames');

        if (isset($customerReadAbleNames[$permission])) {
            return trans($customerReadAbleNames[$permission]);
        }

        return trans($permission);
    }
}

if (! function_exists('diffInLargestUnit')) {
    function diffInLargestUnit(Carbon $from, ?Carbon $to = null): string
    {
        $to = $to ?? now();

        $diff = $from->diff($to);

        if ($diff->y >= 1) {
            return $diff->y.' year'.($diff->y > 1 ? 's' : '');
        }

        if ($diff->m >= 1) {
            return $diff->m.' month'.($diff->m > 1 ? 's' : '');
        }

        if ($diff->d >= 1) {
            return $diff->d.' day'.($diff->d > 1 ? 's' : '');
        }

        if ($diff->h >= 1) {
            return $diff->h.' hour'.($diff->h > 1 ? 's' : '');
        }

        if ($diff->i >= 1) {
            return $diff->i.' minute'.($diff->i > 1 ? 's' : '');
        }

        return $diff->s.' second'.($diff->s !== 1 ? 's' : '');
    }
}

if (! function_exists('daysToLargestUnit')) {
    function daysToLargestUnit(int $days): string
    {
        if ($days >= 365) {
            $years = floor($days / 365);

            return $years.' year'.($years > 1 ? 's' : '');
        }

        if ($days >= 30) {
            $months = floor($days / 30);

            return $months.' month'.($months > 1 ? 's' : '');
        }

        if ($days >= 7) {
            $weeks = floor($days / 7);

            return $weeks.' week'.($weeks > 1 ? 's' : '');
        }

        return $days.' day'.($days > 1 ? 's' : '');
    }
}

if (! function_exists('getFileImages')) {

    function getFileImages($attachment)
    {
        $source = Storage::url($attachment->src);

        $fileIcons = [
            'pdf' => asset('assets/attachments/pdf.svg'),
            'doc' => asset('assets/attachments/doc.svg'),
            'docx' => asset('assets/attachments/doc.svg'),
            'zip' => asset('assets/attachments/zip.svg'),
            'csv' => asset('assets/attachments/csv.svg'),
            'default' => asset('assets/attachments/random.svg'),
        ];

        $extension = strtolower(pathinfo($attachment->src, PATHINFO_EXTENSION));

        if (array_key_exists($extension, $fileIcons)) {
            return $fileIcons[$extension];
        }

        return $source;

    }
}

if (! function_exists('generateLicenseKey')) {

    function generateLicenseKey($blockCount = 4, $blockLen = 4)
    {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // excludes I, 1, O, 0
        $licenseParts = [];

        for ($b = 0; $b < $blockCount; $b++) {
            $part = '';
            for ($i = 0; $i < $blockLen; $i++) {
                $part .= $chars[random_int(0, strlen($chars) - 1)];
            }
            $licenseParts[] = $part;
        }

        return implode('-', $licenseParts);
    }
}

if (! function_exists('module_exists')) {
    /**
     * Check if a given module exists either by folder or in config/modules.php
     */
    function module_exists(string $moduleName): bool
    {
        $moduleName = ucfirst($moduleName);
        $modulePath = base_path("Modules/{$moduleName}");
        $configPath = config_path('modules.php');

        if (class_exists(Module::class) && Module::has($moduleName) && Module::isEnabled($moduleName) && File::isDirectory($modulePath) && File::exists($configPath)) {
            return true;
        }

        // Otherwise, module not found
        return false;
    }
}

if (! function_exists('cartAccessToken')) {
    function cartAccessToken(Request $request): array
    {
        $user = auth()->user();

        $customerId = $user->customer->id ?? null;
        $accessTokenValue = $request->header('X-Guest-Token') ?? $request->access_token;
        $guestToken = CartAccessToken::where('access_token', $accessTokenValue)->first();
        $isAuth = false;

        if ($request->bearerToken()) {
            $accessToken = PersonalAccessToken::findToken($request->bearerToken());
            if ($accessToken) {
                $user = $accessToken->tokenable;
                $customerId = $user->customer->id ?? null;
                $isAuth = true;
            }
        }

        if (! $isAuth && $guestToken) {
            $customerId = $guestToken->customer_id ?? null;
        }

        return [
            'customer_id' => $customerId ?? null,
            'access_token' => $guestToken->access_token ?? null,
            'is_auth' => $isAuth,
        ];
    }
}

if (! function_exists('userCart')) {
    function userCart(Request $request)
    {
        $tokens = cartAccessToken($request);
        $query = Cart::query();
        if ($tokens['customer_id'] && $tokens['access_token']) {
            $query->where(function ($q) use ($tokens) {
                $q->where('customer_id', $tokens['customer_id'])
                    ->orWhere('access_token', $tokens['access_token']);
            });
        } elseif ($tokens['customer_id']) {
            $query->where('customer_id', $tokens['customer_id']);
        } elseif ($tokens['access_token']) {
            $query->where('access_token', $tokens['access_token']);
        } else {
            $query->whereRaw('1 = 0');
        }

        return $query;
    }
}

if (! function_exists('formatAmount')) {
    function formatAmount($amount)
    {
        return Number::abbreviate($amount ?? 0, 2);
    }
}

if (! function_exists('warehouse')) {
    function warehouse(?string $type = null)
    {
        if ($type === 'central') {
            return Cache::remember('central_warehouse', 60 * 24 * 30, function () {
                return Warehouse::where('is_default', true)->first();
            });
        }
        $shop = generaleSetting('shop');

        return $shop?->warehouse;
    }
}

if (! function_exists('haversineKm')) {
    /**
     * Great-circle distance between two coordinates, in kilometres.
     */
    function haversineKm($lat1, $lng1, $lat2, $lng2): float
    {
        $earthRadiusKm = 6371.0;
        $dLat = deg2rad((float) $lat2 - (float) $lat1);
        $dLng = deg2rad((float) $lng2 - (float) $lng1);
        $a = sin($dLat / 2) ** 2
            + cos(deg2rad((float) $lat1)) * cos(deg2rad((float) $lat2)) * sin($dLng / 2) ** 2;

        return $earthRadiusKm * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}

if (! function_exists('renderStarRating')) {
    /**
     * Render HTML star rating icons based on numeric rating.
     */
    function renderStarRating(float|int $rating = 0): string
    {
        $rounded = round((float) $rating, 1);
        $fullStars = (int) floor($rounded);
        $hasHalf = ($rounded - $fullStars) >= 0.3 && ($rounded - $fullStars) <= 0.7;
        $emptyStars = max(0, 5 - $fullStars - ($hasHalf ? 1 : 0));

        $html = '<div class="d-inline-flex align-items-center text-warning" style="gap: 2px;">';
        for ($i = 0; $i < $fullStars; $i++) {
            $html .= '<i class="fa-solid fa-star"></i>';
        }
        if ($hasHalf) {
            $html .= '<i class="fa-solid fa-star-half-stroke"></i>';
        }
        for ($i = 0; $i < $emptyStars; $i++) {
            $html .= '<i class="fa-regular fa-star text-muted" style="opacity: 0.35;"></i>';
        }
        $html .= '</div>';

        return $html;
    }
}

if (! function_exists('formatIndianCurrency')) {
    /**
     * Format a number using Indian currency numbering (e.g. ₹ 72,50,040.00).
     */
    function formatIndianCurrency(float|int|string|null $number, bool $withSymbol = true): string
    {
        $number = (float) ($number ?? 0);
        $isNegative = $number < 0;
        $number = abs($number);

        $exploded = explode('.', number_format($number, 2, '.', ''));
        $integerPart = $exploded[0];
        $decimalPart = $exploded[1];

        $lastThree = substr($integerPart, -3);
        $otherNumbers = substr($integerPart, 0, -3);
        if ($otherNumbers !== '') {
            $lastThree = ','.$lastThree;
        }
        $formattedInteger = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $otherNumbers).$lastThree;

        $result = $formattedInteger.'.'.$decimalPart;
        if ($isNegative) {
            $result = '-'.$result;
        }

        return $withSymbol ? '₹ '.$result : $result;
    }
}

if (! function_exists('numberToIndianWords')) {
    /**
     * Convert numeric amount to Indian currency words (e.g. Seventy Two Lakh Fifty Thousand and Forty Rupees only).
     */
    function numberToIndianWords(float|int|string|null $number): string
    {
        $number = (float) ($number ?? 0);
        if ($number <= 0) {
            return 'Zero Rupees only';
        }

        $no = (int) floor($number);
        $decimal = (int) round(($number - $no) * 100);

        $words = [
            0 => '', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 5 => 'Five',
            6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine', 10 => 'Ten',
            11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
            16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen', 19 => 'Nineteen', 20 => 'Twenty',
            30 => 'Thirty', 40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty', 70 => 'Seventy',
            80 => 'Eighty', 90 => 'Ninety',
        ];

        $convertGroup = function ($n) use (&$convertGroup, $words) {
            if ($n < 20) {
                return $words[$n] ?? '';
            }
            if ($n < 100) {
                return trim(($words[(int) (floor($n / 10) * 10)] ?? '').' '.($words[$n % 10] ?? ''));
            }

            return trim(($words[(int) floor($n / 100)] ?? '').' Hundred '.$convertGroup($n % 100));
        };

        $crores = (int) floor($no / 10000000);
        $no %= 10000000;
        $lakhs = (int) floor($no / 100000);
        $no %= 100000;
        $thousands = (int) floor($no / 1000);
        $no %= 1000;
        $hundreds = $no;

        $str = [];
        if ($crores > 0) {
            $str[] = $convertGroup($crores).' Crore';
        }
        if ($lakhs > 0) {
            $str[] = $convertGroup($lakhs).' Lakh';
        }
        if ($thousands > 0) {
            $str[] = $convertGroup($thousands).' Thousand';
        }
        if ($hundreds > 0) {
            $str[] = $convertGroup($hundreds);
        }

        $result = implode(' ', array_filter($str)).' Rupees';

        if ($decimal > 0) {
            $result .= ' and '.$convertGroup($decimal).' Paise';
        }

        return trim($result).' only';
    }
}
