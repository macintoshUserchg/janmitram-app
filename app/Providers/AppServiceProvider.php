<?php

namespace App\Providers;

use App\Enums\OrderStatus;
use App\Models\Language;
use App\Models\Order;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Schema::defaultStringLength(191);

        // ponytail: Apache under CGI/FastCGI strips the Authorization header.
        // Sanctum's callback escape hatch reads $_SERVER directly as a fallback.
        Sanctum::$accessTokenRetrievalCallback = function ($request) {
            if ($token = $request->bearerToken()) {
                return $token;
            }

            foreach (['HTTP_AUTHORIZATION', 'REDIRECT_HTTP_AUTHORIZATION'] as $key) {
                if (! empty($_SERVER[$key])) {
                    $value = $_SERVER[$key];
                    if (str_starts_with(strtolower($value), 'bearer ')) {
                        return substr($value, 7);
                    }

                    return $value;
                }
            }

            return $request->header('X-Authorization');
        };

        // Share globally-scoped variables that were originally provided by the
        // removed server-withheld code (view composers / middleware). Reconstructed
        // from the variable names referenced in admin Blade views.
        View::composer('*', function ($view) {
            // $businessModel — single vs multi-shop mode
            $businessModel = null;
            if (function_exists('generaleSetting')) {
                $setting = generaleSetting('setting');
                if ($setting && $setting->shop_type) {
                    $businessModel = $setting->shop_type === 'single' ? 'single' : 'multi';
                }
            }
            $view->with('businessModel', $businessModel);

            // $generaleSetting — used in admin dashboard/layout for theme colors, shop type
            try {
                $gs = generaleSetting('setting');
            } catch (\Exception $e) {
                $gs = null;
            }
            $view->with('generaleSetting', $gs);

            // $seederRun — flag used by sidebar/layout template
            $view->with('seederRun', false);

            // $storageLink — storage symlink flag used by layout; hide warning if symlink exists
            $view->with('storageLink', ! is_link(public_path('storage')));

            // $languages — active languages for locale switcher
            try {
                $languages = Language::where('is_active', true)->get();
            } catch (\Exception $e) {
                $languages = collect([]);
            }
            $view->with('languages', $languages);
        });

        // Dashboard-specific variables (used in admin.dashboard view)
        View::composer('admin.dashboard', function ($view) {
            try {
                $pending = Order::where('order_status', OrderStatus::PENDING)->count();
            } catch (\Exception $e) {
                $pending = 0;
            }
            $view->with('pending', $pending);
        });
    }
}
