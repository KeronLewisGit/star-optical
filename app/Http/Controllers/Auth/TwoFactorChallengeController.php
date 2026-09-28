<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Services\TwoFactorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TwoFactorChallengeController extends Controller
{
    public function __construct(private readonly TwoFactorService $twoFactor) {}

    public function create(Request $request): View|RedirectResponse
    {
        if (! $request->user()->hasTwoFactorEnabled() || $request->session()->get('two_factor.passed')) {
            return redirect()->route('admin.dashboard');
        }

        return view('auth.two-factor-challenge');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['nullable', 'string', 'max:12'],
            'recovery_code' => ['nullable', 'string', 'max:20'],
        ]);

        $user = $request->user();
        $ok = false;

        if ($request->filled('code')) {
            $ok = $this->twoFactor->verify($user, $request->input('code'));
        } elseif ($request->filled('recovery_code')) {
            $ok = $this->twoFactor->verifyRecoveryCode($user, $request->input('recovery_code'));
            if ($ok) {
                ActivityLog::record('two_factor_recovery_used', $user, description: 'Signed in with a recovery code');
            }
        }

        if (! $ok) {
            ActivityLog::record('two_factor_failed', $user, description: 'Failed two-factor attempt');

            return back()->withErrors(['code' => 'The code is not valid.']);
        }

        $request->session()->regenerate();
        $request->session()->put('two_factor.passed', true);

        return redirect()->intended(route('admin.dashboard', absolute: false));
    }
}
