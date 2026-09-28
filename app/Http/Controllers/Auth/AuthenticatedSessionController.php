<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();
        $request->session()->forget('two_factor.passed');

        $user = $request->user();
        $user->forceFill(['last_login_at' => now(), 'last_login_ip' => $request->ip()])->saveQuietly();
        ActivityLog::record('login', $user, description: 'Signed in');

        if ($user->hasTwoFactorEnabled()) {
            return redirect()->route('two-factor.challenge');
        }

        return redirect()->intended(route('admin.dashboard', absolute: false));
    }

    public function destroy(Request $request): RedirectResponse
    {
        if ($request->user()) {
            ActivityLog::record('logout', $request->user(), description: 'Signed out');
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
