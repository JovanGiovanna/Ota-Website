<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
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

        // Limit login attempts by email + IP to mitigate brute force
        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->input('email');
            return Limit::perMinute(5)
                ->by(strtolower($email).'|'.$request->ip())
                ->response(function (Request $request, array $headers) {
                    $retryAfter = (int)($headers['Retry-After'] ?? 60);
                    if ($request->expectsJson()) {
                        return response()->json([
                            'status' => false,
                            'title' => 'Terlalu Banyak Percobaan',
                            'message' => "Coba lagi dalam {$retryAfter} detik.",
                            'icon' => 'error',
                        ], 429, $headers);
                    }
                    return redirect()->back()->withInput()->with(
                        'error',
                        "Terlalu banyak percobaan. Coba lagi dalam {$retryAfter} detik."
                    );
                });
        });

        // Limit registration attempts by IP
        RateLimiter::for('register', function (Request $request) {
            return Limit::perMinute(10)
                ->by($request->ip())
                ->response(function (Request $request, array $headers) {
                    $retryAfter = (int)($headers['Retry-After'] ?? 60);
                    if ($request->expectsJson()) {
                        return response()->json([
                            'status' => false,
                            'title' => 'Terlalu Banyak Permintaan',
                            'message' => "Coba lagi dalam {$retryAfter} detik.",
                            'icon' => 'error',
                        ], 429, $headers);
                    }
                    return redirect()->back()->withInput()->with(
                        'error',
                        "Terlalu banyak permintaan. Coba lagi dalam {$retryAfter} detik."
                    );
                });
        });

        // Limit auxiliary email checks by IP
        RateLimiter::for('check-email', function (Request $request) {
            return Limit::perMinute(20)->by($request->ip());
        });
    }
}
