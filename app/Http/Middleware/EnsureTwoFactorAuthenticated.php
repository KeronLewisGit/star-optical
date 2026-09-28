<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gate for the admin area:
 *  - users with 2FA enabled must have completed the code challenge this session;
 *  - when the "require two-factor" setting is on, users without 2FA are sent to set it up.
 */
class EnsureTwoFactorAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        if ($user->hasTwoFactorEnabled() && ! $request->session()->get('two_factor.passed')) {
            return redirect()->route('two-factor.challenge');
        }

        if (! $user->hasTwoFactorEnabled() && Setting::get('require_two_factor') === '1'
            && ! $request->routeIs('admin.profile.*') && ! $request->routeIs('logout')) {
            return redirect()->route('admin.profile.edit')
                ->with('warning', 'Two-factor authentication is required for all staff. Please set it up below before continuing.');
        }

        return $next($request);
    }
}
