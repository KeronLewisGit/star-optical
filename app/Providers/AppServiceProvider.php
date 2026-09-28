<?php

namespace App\Providers;

use App\Models\Booking;
use App\Models\Patient;
use App\Policies\BookingPolicy;
use App\Policies\PatientPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Always serve over HTTPS in production (the proxy is trusted in bootstrap/app.php).
        if ($this->app->isProduction()) {
            URL::forceScheme('https');
        }

        // Per-request nonce so inline scripts (analytics) pass the Content-Security-Policy.
        Vite::useCspNonce();

        // Fail loudly on unguarded attributes instead of silently dropping them.
        Model::preventSilentlyDiscardingAttributes(! $this->app->isProduction());

        // Strong password policy for every staff account.
        Password::defaults(function () {
            $rule = Password::min(12)->letters()->mixedCase()->numbers()->symbols();

            return $this->app->isProduction() ? $rule->uncompromised() : $rule;
        });

        // Public booking form: 5 submissions per minute per IP, 20 per day.
        RateLimiter::for('booking', function (Request $request) {
            return [
                Limit::perMinute(5)->by($request->ip()),
                Limit::perDay(20)->by($request->ip()),
            ];
        });

        // Two-factor code attempts: 5 per minute per user/IP.
        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by(($request->user()?->id ?? 'guest').'|'.$request->ip());
        });

        Gate::policy(Booking::class, BookingPolicy::class);
        Gate::policy(Patient::class, PatientPolicy::class);
    }
}
