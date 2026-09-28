<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $title ?? 'Staff sign in' }} | {{ config('app.name') }}</title>
    <link rel="icon" href="{{ asset('assets/images/logo.png') }}" type="image/png" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col justify-center items-center px-4 py-10 bg-gradient-to-br from-brand-ink via-brand-dark to-brand">
        <a href="{{ url('/') }}" class="mb-6">
            <img src="{{ asset('assets/images/logo.png') }}" alt="{{ config('app.name') }}" class="h-14 w-auto drop-shadow" />
        </a>
        <div class="w-full sm:max-w-md px-6 py-7 bg-white shadow-xl rounded-2xl">
            {{ $slot }}
        </div>
        <p class="mt-6 text-xs text-slate-300">Staff area. All activity is logged.</p>
    </div>
</body>
</html>
