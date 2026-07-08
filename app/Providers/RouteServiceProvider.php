<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

/**
 * Standard route provider.
 *
 * Replaces the installer-redirect stub that the malicious web-installer
 * package left behind (it redirected every request to the now-removed
 * `installer.welcome.index`). This loads the conventional `web` and `api`
 * route files, matching the app's Laravel-10-style structure
 * (config/app.php registers this provider; middleware groups live in
 * app/Http/Kernel.php).
 */
class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route used after authentication.
     */
    public const HOME = '/';

    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
