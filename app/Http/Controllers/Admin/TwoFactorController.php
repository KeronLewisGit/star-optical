<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Services\TwoFactorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TwoFactorController extends Controller
{
    public function __construct(private readonly TwoFactorService $twoFactor) {}

    /** Step 1: generate a secret and show the QR code (nothing is saved until confirmed). */
    public function enable(Request $request): RedirectResponse
    {
        $request->session()->put('two_factor.pending_secret', $this->twoFactor->generateSecret());

        return redirect()->route('admin.profile.edit')->withFragment('two-factor');
    }

    /** Step 2: the user proves the authenticator works by entering a valid code. */
    public function confirm(Request $request): RedirectResponse
    {
        $request->validate(['code' => ['required', 'digits:6']]);

        $user = $request->user();
        $secret = $request->session()->get('two_factor.pending_secret');

        if (! $secret) {
            return back()->withErrors(['code' => 'Start the setup again.']);
        }

        $user->forceFill(['two_factor_secret' => $secret]);

        if (! $this->twoFactor->verify($user, $request->input('code'))) {
            $user->forceFill(['two_factor_secret' => null]);

            return back()->withErrors(['code' => 'That code was not valid. Check your phone and try again.'])->withFragment('two-factor');
        }

        $user->forceFill(['two_factor_confirmed_at' => now()])->save();
        $codes = $this->twoFactor->generateRecoveryCodes($user);

        $request->session()->forget('two_factor.pending_secret');
        $request->session()->put('two_factor.passed', true);
        $request->session()->flash('two_factor.recovery_codes', $codes);

        ActivityLog::record('two_factor_enabled', $user, description: 'Enabled two-factor authentication');

        return redirect()->route('admin.profile.edit')->withFragment('two-factor')->with('success', 'Two-factor authentication is on. Save your recovery codes somewhere safe.');
    }

    public function regenerateRecoveryCodes(Request $request): RedirectResponse
    {
        $request->validate(['password' => ['required', 'current_password']]);

        $codes = $this->twoFactor->generateRecoveryCodes($request->user());
        $request->session()->flash('two_factor.recovery_codes', $codes);

        return redirect()->route('admin.profile.edit')->withFragment('two-factor')->with('success', 'New recovery codes generated. The old ones no longer work.');
    }

    public function disable(Request $request): RedirectResponse
    {
        $request->validate(['password' => ['required', 'current_password']]);

        $request->user()->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();

        ActivityLog::record('two_factor_disabled', $request->user(), description: 'Disabled two-factor authentication');

        return redirect()->route('admin.profile.edit')->withFragment('two-factor')->with('success', 'Two-factor authentication turned off.');
    }
}
