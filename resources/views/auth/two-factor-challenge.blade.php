<x-guest-layout title="Two-factor authentication">
    <h1 class="text-lg font-semibold text-gray-900">Two-factor authentication</h1>
    <p class="mt-1 text-sm text-gray-600">Enter the 6-digit code from your authenticator app to finish signing in.</p>

    <form method="POST" action="{{ route('two-factor.challenge') }}" class="mt-5 space-y-4" data-once>
        @csrf
        <div>
            <label for="code" class="form-label">Authentication code</label>
            <input id="code" name="code" type="text" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9]*" maxlength="6" class="form-input tracking-[0.4em] text-center text-lg" autofocus />
            @error('code')<p class="form-error">{{ $message }}</p>@enderror
        </div>
        <button type="submit" class="btn-primary w-full">Verify</button>
    </form>

    <details class="mt-5 text-sm">
        <summary class="cursor-pointer text-gray-600 hover:text-gray-900">Lost your device? Use a recovery code</summary>
        <form method="POST" action="{{ route('two-factor.challenge') }}" class="mt-3 space-y-3" data-once>
            @csrf
            <div>
                <label for="recovery_code" class="form-label">Recovery code</label>
                <input id="recovery_code" name="recovery_code" type="text" autocomplete="off" class="form-input uppercase" placeholder="XXXXX-XXXXX" />
            </div>
            <button type="submit" class="btn-secondary w-full">Use recovery code</button>
        </form>
    </details>

    <form method="POST" action="{{ route('logout') }}" class="mt-6 text-center">
        @csrf
        <button type="submit" class="text-xs text-gray-500 underline hover:text-gray-800">Cancel and sign out</button>
    </form>
</x-guest-layout>
