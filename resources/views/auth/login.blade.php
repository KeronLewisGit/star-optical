<x-guest-layout title="Staff sign in">
    <h1 class="text-lg font-semibold text-gray-900">Staff sign in</h1>
    <p class="mt-1 text-sm text-gray-600">Access to bookings and patient records.</p>

    <x-auth-session-status class="mt-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="mt-5 space-y-4" data-once>
        @csrf
        <div>
            <label for="email" class="form-label">Email</label>
            <input id="email" class="form-input" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" />
            @error('email')<p class="form-error">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="password" class="form-label">Password</label>
            <input id="password" class="form-input" type="password" name="password" required autocomplete="current-password" />
            @error('password')<p class="form-error">{{ $message }}</p>@enderror
        </div>
        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center text-sm text-gray-600">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-brand focus:ring-brand" name="remember">
                <span class="ms-2">Remember me</span>
            </label>
            <a class="text-sm text-brand hover:underline" href="{{ route('password.request') }}">Forgot password?</a>
        </div>
        <button type="submit" class="btn-primary w-full">Sign in</button>
    </form>
</x-guest-layout>
