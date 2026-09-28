<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\TwoFactorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request, TwoFactorService $twoFactor): View
    {
        $user = $request->user();
        $pendingSecret = $request->session()->get('two_factor.pending_secret');

        return view('admin.profile.edit', [
            'user' => $user,
            'pendingSecret' => $pendingSecret,
            'qrSvg' => $pendingSecret ? $twoFactor->qrCodeSvg($twoFactor->otpauthUrl($user, $pendingSecret)) : null,
            'recoveryCodes' => $request->session()->get('two_factor.recovery_codes'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'string', 'lowercase', 'email:rfc', 'max:255', Rule::unique(User::class)->ignore($request->user())],
        ]);

        $request->user()->update($data);

        return back()->with('success', 'Profile updated.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $data = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $request->user()->update(['password' => $data['password']]);

        // Invalidate every other session that may be signed in with the old password.
        Auth::logoutOtherDevices($data['password']);

        return back()->with('success', 'Password changed.');
    }
}
