<x-admin-layout title="Profile & security">
    <div class="grid lg:grid-cols-2 gap-6">
        <div class="space-y-6">
            <form method="POST" action="{{ route('admin.profile.update') }}" class="card" data-once>
                @csrf @method('PATCH')
                <div class="card-body space-y-4">
                    <h2 class="card-title">Your details</h2>
                    <x-form.input name="name" label="Name" :value="$user->name" required />
                    <x-form.input name="email" label="Email" type="email" :value="$user->email" required />
                    <button class="btn-primary">Save</button>
                </div>
            </form>

            <form method="POST" action="{{ route('admin.profile.password') }}" class="card" data-once>
                @csrf @method('PUT')
                <div class="card-body space-y-4">
                    <h2 class="card-title">Change password</h2>
                    <div><label class="form-label">Current password</label><input type="password" name="current_password" class="form-input" autocomplete="current-password" required>@if($errors->updatePassword->has('current_password'))<p class="form-error">{{ $errors->updatePassword->first('current_password') }}</p>@endif</div>
                    <div><label class="form-label">New password</label><input type="password" name="password" class="form-input" autocomplete="new-password" required>@if($errors->updatePassword->has('password'))<p class="form-error">{{ $errors->updatePassword->first('password') }}</p>@endif<p class="form-help">At least 12 characters with upper and lower case letters, a number and a symbol.</p></div>
                    <div><label class="form-label">Confirm new password</label><input type="password" name="password_confirmation" class="form-input" autocomplete="new-password" required></div>
                    <button class="btn-primary">Update password</button>
                </div>
            </form>
        </div>

        <div class="card" id="two-factor"><div class="card-body space-y-4">
            <h2 class="card-title"><i class="fa-solid fa-shield-halved text-gold"></i> Two-factor authentication</h2>

            @if($recoveryCodes)
                <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm">
                    <p class="font-semibold text-amber-800">Save these recovery codes now. They are shown only once.</p>
                    <p class="text-amber-700 mt-1">Each code signs you in once if you lose your phone.</p>
                    <pre id="recoveryCodes" class="mt-3 grid grid-cols-2 gap-x-6 font-mono text-sm">@foreach($recoveryCodes as $c){{ $c }}
@endforeach</pre>
                    <button type="button" data-copy="#recoveryCodes" class="btn-secondary btn-sm mt-2">Copy codes</button>
                </div>
            @endif

            @if($user->hasTwoFactorEnabled())
                <p class="text-sm"><span class="badge bg-green-100 text-green-800">Enabled</span> since {{ $user->two_factor_confirmed_at->format('j M Y') }}. You'll be asked for a code from your authenticator app each time you sign in.</p>
                <form method="POST" action="{{ route('admin.profile.two-factor.recovery') }}" class="flex flex-wrap items-end gap-2" data-once>
                    @csrf
                    <div class="flex-1 min-w-[200px]"><label class="form-label">Confirm password to regenerate recovery codes</label><input type="password" name="password" class="form-input" autocomplete="current-password" required></div>
                    <button class="btn-secondary">Regenerate codes</button>
                </form>
                <form method="POST" action="{{ route('admin.profile.two-factor.disable') }}" class="flex flex-wrap items-end gap-2 border-t border-gray-100 pt-4" data-confirm="Turn off two-factor authentication?" data-once>
                    @csrf @method('DELETE')
                    <div class="flex-1 min-w-[200px]"><label class="form-label">Confirm password to turn off 2FA</label><input type="password" name="password" class="form-input" autocomplete="current-password" required></div>
                    <button class="btn-danger">Turn off</button>
                </form>
            @elseif($pendingSecret)
                <ol class="text-sm text-gray-700 list-decimal pl-5 space-y-1">
                    <li>Install an authenticator app (Google Authenticator, Microsoft Authenticator, Authy or 1Password).</li>
                    <li>Scan this QR code, or enter the key manually.</li>
                    <li>Type the 6-digit code the app shows to finish.</li>
                </ol>
                <div class="flex flex-wrap items-center gap-6">
                    <div class="rounded-lg border border-gray-200 p-2 bg-white">{!! $qrSvg !!}</div>
                    <div class="text-sm"><p class="text-gray-500">Manual key</p><code class="block mt-1 rounded bg-gray-100 px-2 py-1 font-mono text-xs break-all">{{ chunk_split($pendingSecret, 4, ' ') }}</code></div>
                </div>
                <form method="POST" action="{{ route('admin.profile.two-factor.confirm') }}" class="flex flex-wrap items-end gap-2" data-once>
                    @csrf
                    <div><label class="form-label">6-digit code</label><input type="text" name="code" inputmode="numeric" maxlength="6" class="form-input tracking-widest" autocomplete="one-time-code" required autofocus>@error('code')<p class="form-error">{{ $message }}</p>@enderror</div>
                    <button class="btn-primary">Confirm &amp; enable</button>
                </form>
            @else
                <p class="text-sm text-gray-600">Add a second step to sign-in using a code from your phone. Strongly recommended for every account that can see patient records.</p>
                <form method="POST" action="{{ route('admin.profile.two-factor.enable') }}" data-once>@csrf<button class="btn-primary"><i class="fa-solid fa-qrcode"></i> Set up two-factor</button></form>
            @endif
        </div></div>
    </div>
</x-admin-layout>
