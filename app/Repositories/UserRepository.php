<?php

namespace App\Repositories;

use Carbon\Carbon;
use App\Enums\Roles;
use App\Models\User;
use App\Models\Media;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\DriverLocation;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Support\Repositories\Repository;

class UserRepository extends Repository
{
    /**
     * Get the model.
     *
     * @return string The model class.
     */
    public static function model()
    {
        return User::class;
    }

    /**
     * Find a record by phone number.
     *
     * @param  datatype  $phone  description
     * @return Some_Return_Value
     */
    public static function findByPhone($phone)
    {
        return self::query()->where('phone', $phone)->orWhere('email', $phone)->first();
    }

    public static function findByContact($contact)
    {
        return self::query()->where('phone', $contact)
            ->orWhere('email', $contact)
            ->first();
    }

    /**
     * Check if a user with the given social auth provider and email exists in the database.
     * If the user does not exist, create a new user.
     *
     * @param  Request  $request  The request object
     * @param  string  $provider  The social auth provider
     * @return User The found or created user
     */
    public static function socialAuthCheckOrCreate($request, $provider)
    {
        if (! $request['email'] && ! $request['phone']) {
            $user = self::query()->where('auth_type', $provider)->where('auth_id', $request['id'])->first();
            if ($user) {
                return $user;
            }
        }

        $user = self::query()->where('auth_type', $provider)
            ->where('email', $request['email'])
            ->when(! empty($request['phone']), function ($query) use ($request) {
                $query->orWhere('phone', $request['phone']);
            })->first();

        if ($user) {
            return $user;
        }

        $profileUrl = $request['profile_url'];
        $media = null;

        // SSRF guard: profile_url is user-supplied. Only fetch it when it
        // passes the https-only / private-IP / host-allowlist validation
        // below; otherwise skip the fetch and store a null avatar.
        if ($profileUrl && self::isAllowedAvatarUrl($profileUrl)) {
            $filename = 'users/' . Str::random(10) . '.jpg';

            $response = Http::timeout(10)
                ->withOptions(['allow_redirects' => false])
                ->get($profileUrl);

            if ($response->successful()) {
                Storage::disk('public')->put($filename, $response->body());

                $media = Media::create([
                    'type' => 'image',
                    'name' => $filename,
                    'src' => $filename,
                    'extension' => 'jpg',
                ]);
            }
        }

        $user = self::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'phone' => $request['phone'],
            'auth_type' => $provider,
            'auth_id' => $request['id'],
            'gender' => $request['gender'],
            'is_active' => true,
            'password' => Hash::make('password'),
            'media_id' => $media ? $media->id : null,
        ]);

        // Create a new customer
        CustomerRepository::storeByRequest($user);

        // create wallet
        WalletRepository::storeByRequest($user);

        $user->assignRole(Roles::CUSTOMER->value);

        return $user;
    }

    /**
     * Register a new user.
     *
     * @param  Request  $request  The request object
     */
    public static function registerNewUser(Request $request): User
    {
        $thumbnail = null;
        if ($request->hasFile('profile_photo')) {
            $thumbnail = MediaRepository::storeByRequest(
                $request->profile_photo,
                'users/profile',
            );
        }

        return self::create([
            'name' => $request->first_name ?? $request->name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => Hash::make($request->password ?? ''),
            'media_id' => $thumbnail ? $thumbnail->id : null,
            'gender' => $request->gender,
            'date_of_birth' => $request->date_of_birth ?? null,
            'country' => $request->country,
            'phone_code' => $request->phone_code,
            'is_active' => true,
        ]);
    }

    public static function storeByRequest($request): User
    {
        $thumbnail = null;
        if ($request->hasFile('profile_photo')) {
            $thumbnail = MediaRepository::storeByRequest(
                $request->profile_photo,
                'users/profile',
                'image'
            );
        }

        return self::create([
            'name' => $request->first_name ?? $request->name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'gender' => $request->gender,
            'password' => Hash::make($request->password ?? $request->phone),
            'media_id' => $thumbnail ? $thumbnail->id : null,
            'driving_lience' => $request->driving_lience,
            'date_of_birth' => $request->date_of_birth,
            'vehicle_type' => $request->vehicle_type,
            'country' => $request->country,
            'phone_code' => $request->phone_code,
            'is_active' => $request->is_active ? true : false,
            'shop_id' => $request->shop_id ?? null,
        ]);
    }

    /**
     * Get the access token for the user.
     *
     * @param  User  $user  The user for whom the token is being obtained
     * @return array
     */
    public static function getAccessToken(User $user)
    {
        // $token = $user->createToken('user token');
        $token = $user->createToken('api_token')->plainTextToken;

        return [
            'auth_type' => 'Bearer',
            'token' => $token,
            'expires_at' => now()->addDays(30)->toDateTimeString(),
        ];
    }

    /**
     * Update user by request.
     *
     * @param  $request  The user request
     * @param  mixed  $user  The user
     */
    public static function updateByRequest($request, $user): User
    {
        $thumbnail = self::updateProfilePhoto($request, $user);
        $name = $request->name ?? $request->first_name;
        $user->update([
            'name' => $name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'media_id' => $thumbnail ? $thumbnail->id : null,
            'gender' => $request->gender,
            'date_of_birth' => $request->date_of_birth ? Carbon::parse($request->date_of_birth)->format('Y-m-d') : $user->date_of_birth,
            'driving_lience' => $request->driving_lience,
            'vehicle_type' => $request->vehicle_type,
            'country' => $request->country ?? $user->country,
            'phone_code' => $request->phone_code ?? $user->phone_code,
        ]);

        return $user;
    }

    /**
     * Update the user's profile photo.
     */
    private static function updateProfilePhoto($request, $user)
    {
        $thumbnail = $user->media;
        if ($request->hasFile('profile_photo') && $thumbnail == null) {
            $thumbnail = MediaRepository::storeByRequest(
                $request->profile_photo,
                'users/profile',
            );
        }

        if ($request->hasFile('profile_photo') && $thumbnail) {
            $thumbnail = MediaRepository::updateByRequest(
                $request->profile_photo,
                'users/profile',
                'image',
                $thumbnail
            );
        }

        return $thumbnail;
    }

    public static function registerGuestUser(Request $request, User $user)
    {
        $user->update([
            'name' => $request->first_name ?? $request->name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'gender' => $request->gender,
            'date_of_birth' => $request->date_of_birth ?? null,
            'country' => $request->country,
            'phone_code' => $request->phone_code,
            'is_active' => true,
        ]);
        return $user;
    }

    /**
     * SSRF guard for the social-avatar fetch.
     *
     * A profile_url supplied by an OAuth payload is attacker-influenceable,
     * so before Http::get() is ever called we require:
     *   1. a valid, https-only URL (no http/file/gopher/data schemes);
     *   2. a host on the known social-avatar allowlist
     *      (Google / Facebook / GitHub avatar CDNs);
     *   3. that EVERY DNS-resolved IP for that host is a public address —
     *      private, loopback, link-local and reserved ranges are rejected
     *      to stop DNS-rebinding / internal-metadata access.
     *
     * @param  string  $url
     */
    private static function isAllowedAvatarUrl($url): bool
    {
        if (! is_string($url) || $url === '') {
            return false;
        }

        $parts = parse_url($url);
        if ($parts === false || empty($parts['scheme']) || empty($parts['host'])) {
            return false;
        }

        // 1. https only.
        if (strtolower($parts['scheme']) !== 'https') {
            return false;
        }

        // No embedded credentials, no non-standard ports.
        if (isset($parts['user']) || isset($parts['pass'])) {
            return false;
        }
        if (isset($parts['port']) && (int) $parts['port'] !== 443) {
            return false;
        }

        $host = strtolower(rtrim($parts['host'], '.'));

        // 2. Host allowlist for known social-avatar CDNs.
        $allowedSuffixes = [
            'googleusercontent.com',        // Google avatars (lh3.googleusercontent.com)
            'google.com',
            'githubusercontent.com',         // GitHub avatars (avatars.githubusercontent.com)
            'fbcdn.net',                     // Facebook CDN
            'fbsbx.com',                     // Facebook (platform-lookaside.fbsbx.com)
            'facebook.com',                  // graph.facebook.com
        ];

        $hostAllowed = false;
        foreach ($allowedSuffixes as $suffix) {
            if ($host === $suffix || str_ends_with($host, '.' . $suffix)) {
                $hostAllowed = true;
                break;
            }
        }
        if (! $hostAllowed) {
            return false;
        }

        // 3. Resolve the host and reject any private/loopback/link-local IP.
        $ips = array_merge(
            (array) @gethostbynamel($host),
            array_column(@dns_get_record($host, DNS_AAAA) ?: [], 'ipv6')
        );
        $ips = array_filter($ips);

        if (empty($ips)) {
            // Could not resolve to a public address — fail closed.
            return false;
        }

        foreach ($ips as $ip) {
            if (! self::isPublicIp($ip)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Return true only for routable public IP addresses.
     *
     * Rejects private, loopback, link-local and other reserved ranges
     * (IPv4 and IPv6) via PHP's filter flags.
     *
     * @param  string  $ip
     */
    private static function isPublicIp($ip): bool
    {
        return filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        ) !== false;
    }

    public static function locationByRequest($request, $user): User
    {
        DriverLocation::updateOrCreate(
            [
                'driver_id' => $user->driver->id,
            ],
            [
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]
        );

        return $user;
    }
}
