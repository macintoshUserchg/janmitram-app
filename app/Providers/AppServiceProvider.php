<?php

namespace App\Providers;

use App\Models\GeneraleSetting;
use App\Models\Language;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Schema::defaultStringLength(191);

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

            // $storageLink — storage symlink flag used by layout
            $view->with('storageLink', true);

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
                $pending = \App\Models\Order::where('order_status', \App\Enums\OrderStatus::PENDING)->count();
            } catch (\Exception $e) {
                $pending = 0;
            }
            $view->with('pending', $pending);
        });
    }
}
