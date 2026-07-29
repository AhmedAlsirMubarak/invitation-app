@php
    $otherLocale = app()->getLocale() === 'ar' ? 'en' : 'ar';
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', __('admin.nav.dashboard')) · {{ __('admin.nav.studio_name') }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-slate-50 text-slate-800 antialiased">
<div class="flex min-h-screen">
    <aside class="w-60 shrink-0 bg-slate-900 text-slate-200 flex flex-col">
        <div class="px-5 py-5 border-b border-slate-800">
            <a href="{{ route('admin.dashboard') }}" class="block mb-1">
                <img src="{{ \App\Models\LandingSetting::logoUrl('logo_dark') }}" alt="{{ __('admin.nav.studio_name') }}" class="h-10 w-auto object-contain">
            </a>
            <p class="text-xs text-slate-400">{{ __('admin.nav.studio_tagline') }}</p>
        </div>
        <nav class="flex-1 px-3 py-4 space-y-1 text-sm">
            <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded-md hover:bg-slate-800 {{ request()->routeIs('admin.dashboard') ? 'bg-slate-800 text-white' : '' }}">{{ __('admin.nav.dashboard') }}</a>
            <a href="{{ route('admin.events.index') }}" class="block px-3 py-2 rounded-md hover:bg-slate-800 {{ request()->routeIs('admin.events.*') ? 'bg-slate-800 text-white' : '' }}">{{ __('admin.nav.events') }}</a>
            <a href="{{ route('admin.templates.index') }}" class="block px-3 py-2 rounded-md hover:bg-slate-800 {{ request()->routeIs('admin.templates.*') ? 'bg-slate-800 text-white' : '' }}">{{ __('admin.nav.templates') }}</a>
            <a href="{{ route('admin.reminders.index') }}" class="block px-3 py-2 rounded-md hover:bg-slate-800 {{ request()->routeIs('admin.reminders.*') ? 'bg-slate-800 text-white' : '' }}">{{ __('admin.nav.reminders') }}</a>
            <a href="{{ route('admin.landing.settings.edit') }}" class="block px-3 py-2 rounded-md hover:bg-slate-800 {{ request()->routeIs('admin.landing.*') ? 'bg-slate-800 text-white' : '' }}">{{ __('admin.nav.landing_page') }}</a>
        </nav>
        <div class="px-5 py-3 border-t border-slate-800">
            <a href="{{ request()->fullUrlWithQuery(['lang' => $otherLocale]) }}" class="text-xs text-slate-400 hover:text-white">{{ __('admin.nav.language_switch') }}</a>
        </div>
        <div class="px-5 py-4 border-t border-slate-800 text-xs text-slate-400 flex items-center justify-between">
            @auth
                <span>{{ auth()->user()->name }}</span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="hover:text-white underline">{{ __('admin.nav.sign_out') }}</button>
                </form>
            @endauth
        </div>
    </aside>

    <main class="flex-1 p-8">
        @if (session('status'))
            <div class="mb-6 rounded-md bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm">
                {{ session('status') }}
            </div>
        @endif

        @yield('content')
    </main>
</div>
</body>
</html>
