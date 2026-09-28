@props(['title' => 'Dashboard'])
@php $user = auth()->user(); @endphp
<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $title }} | {{ config('app.name') }} Admin</title>
    <link rel="icon" href="{{ asset('assets/images/logo.png') }}" type="image/png" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-gray-900">
<div class="min-h-full lg:flex">
    {{-- Sidebar --}}
    <div id="sidebarOverlay" class="fixed inset-0 z-30 bg-black/40 hidden lg:hidden"></div>
    <aside id="sidebar" class="fixed inset-y-0 left-0 z-40 w-64 -translate-x-full lg:translate-x-0 lg:static lg:inset-auto transform transition-transform duration-200 bg-brand-ink text-white flex flex-col">
        <div class="flex items-center gap-3 px-5 h-16 border-b border-white/10">
            <img src="{{ asset('assets/images/logo.png') }}" alt="" class="h-9 w-auto" />
            <span class="text-sm font-semibold tracking-wide">Admin</span>
        </div>
        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
            <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}"><i class="fa-solid fa-gauge"></i> Dashboard</a>
            <a href="{{ route('admin.bookings.index') }}" class="nav-item {{ request()->routeIs('admin.bookings.*') ? 'is-active' : '' }}"><i class="fa-solid fa-calendar-check"></i> Bookings</a>
            <a href="{{ route('admin.patients.index') }}" class="nav-item {{ request()->routeIs('admin.patients.*') ? 'is-active' : '' }}"><i class="fa-solid fa-user-group"></i> Patients</a>
            <a href="{{ route('admin.promotions.index') }}" class="nav-item {{ request()->routeIs('admin.promotions.*') ? 'is-active' : '' }}"><i class="fa-solid fa-tags"></i> Promotions</a>
            @if($user->isAdmin())
            <p class="px-3 pt-4 pb-1 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Administration</p>
            <a href="{{ route('admin.users.index') }}" class="nav-item {{ request()->routeIs('admin.users.*') ? 'is-active' : '' }}"><i class="fa-solid fa-id-badge"></i> Staff accounts</a>
            <a href="{{ route('admin.settings.edit') }}" class="nav-item {{ request()->routeIs('admin.settings.*') ? 'is-active' : '' }}"><i class="fa-solid fa-sliders"></i> Settings &amp; tracking</a>
            <a href="{{ route('admin.activity.index') }}" class="nav-item {{ request()->routeIs('admin.activity.*') ? 'is-active' : '' }}"><i class="fa-solid fa-clock-rotate-left"></i> Activity log</a>
            @endif
        </nav>
        <div class="border-t border-white/10 p-3">
            <a href="{{ route('home') }}" target="_blank" rel="noopener" class="nav-item"><i class="fa-solid fa-arrow-up-right-from-square"></i> View website</a>
        </div>
    </aside>

    {{-- Main --}}
    <div class="flex-1 flex flex-col min-w-0">
        <header class="sticky top-0 z-20 bg-white border-b border-gray-200 h-16 flex items-center px-4 sm:px-6 gap-4">
            <button id="sidebarToggle" class="lg:hidden text-gray-600" aria-label="Open menu" aria-expanded="false"><i class="fa-solid fa-bars text-xl"></i></button>
            <h1 class="text-lg font-semibold text-gray-900 truncate">{{ $title }}</h1>
            <div class="ml-auto relative">
                <button data-toggle="#userMenu" class="flex items-center gap-2 rounded-lg px-2 py-1.5 text-sm text-gray-700 hover:bg-gray-100" aria-expanded="false">
                    <span class="grid h-8 w-8 place-items-center rounded-full bg-brand text-white text-xs font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    <span class="hidden sm:inline">{{ $user->name }}</span>
                    <i class="fa-solid fa-chevron-down text-xs text-gray-400"></i>
                </button>
                <div id="userMenu" data-dropdown class="hidden absolute right-0 mt-2 w-56 rounded-lg border border-gray-200 bg-white shadow-lg py-1 text-sm">
                    <div class="px-4 py-2 text-xs text-gray-500 border-b border-gray-100">{{ $user->email }}<br><span class="badge {{ $user->isAdmin() ? 'bg-gold-soft text-gold-dark' : 'bg-gray-100 text-gray-700' }} mt-1">{{ ucfirst($user->role) }}</span></div>
                    <a href="{{ route('admin.profile.edit') }}" class="block px-4 py-2 hover:bg-gray-50">Profile &amp; security</a>
                    <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-50">Sign out</button></form>
                </div>
            </div>
        </header>

        <main class="flex-1 p-4 sm:p-6 lg:p-8 space-y-6">
            @foreach(['success' => 'bg-green-50 border-green-200 text-green-800', 'warning' => 'bg-amber-50 border-amber-200 text-amber-800', 'error' => 'bg-red-50 border-red-200 text-red-800'] as $type => $classes)
                @if(session($type))
                    <div data-flash class="rounded-lg border px-4 py-3 text-sm transition-opacity {{ $classes }}" role="status">{{ session($type) }}</div>
                @endif
            @endforeach
            @if($errors->any() && ! $errors->hasBag('updatePassword'))
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
                    <strong>Please fix the following:</strong>
                    <ul class="list-disc pl-5 mt-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>
</div>
</body>
</html>
