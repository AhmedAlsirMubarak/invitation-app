@php
    $otherLocale = app()->getLocale() === 'ar' ? 'en' : 'ar';
    $icoCheck = 'M4.5 12.75l6 6 9-13.5';
    $icoSparkle = 'M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456Z';
    $icoArrowLeft = 'M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18';
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('admin.auth.sign_in_title') }} · {{ __('admin.auth.studio_name') }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-slate-50 text-slate-800 antialiased">
<div class="min-h-screen grid md:grid-cols-2">

    {{-- Brand panel --}}
    <div class="relative hidden md:flex flex-col justify-between overflow-hidden bg-gradient-to-br from-ink-950 via-ink-900 to-ink-800 text-white p-12">
        <div class="absolute inset-0 bg-dot-grid opacity-40"></div>
        <div class="absolute -top-24 -end-24 w-96 h-96 rounded-full bg-gold-500/20 blur-3xl"></div>
        <div class="absolute -bottom-32 -start-24 w-96 h-96 rounded-full bg-gold-500/10 blur-3xl"></div>

        <a href="{{ url('/') }}" class="relative flex items-center gap-2.5">
            <span class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center shrink-0">
                <svg viewBox="0 0 24 24" fill="none" stroke="#D8B45C" stroke-width="1.5" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icoSparkle }}"/></svg>
            </span>
            <p class="font-display text-lg font-semibold">{{ __('admin.auth.studio_name') }}</p>
        </a>

        <div class="relative max-w-sm">
            <h1 class="font-display text-3xl leading-snug mb-4">{{ __('admin.auth.tagline') }}</h1>
            <ul class="space-y-3 text-sm text-white/70">
                <li class="flex items-center gap-2.5">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#D8B45C" stroke-width="2" class="w-4 h-4 shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icoCheck }}"/></svg>
                    {{ __('admin.auth.bullet_rsvp') }}
                </li>
                <li class="flex items-center gap-2.5">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#D8B45C" stroke-width="2" class="w-4 h-4 shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icoCheck }}"/></svg>
                    {{ __('admin.auth.bullet_whatsapp') }}
                </li>
                <li class="flex items-center gap-2.5">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#D8B45C" stroke-width="2" class="w-4 h-4 shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icoCheck }}"/></svg>
                    {{ __('admin.auth.bullet_qr') }}
                </li>
            </ul>
        </div>

        <p class="relative text-xs text-white/40">&copy; {{ now()->year }} {{ __('admin.auth.studio_name') }} — {{ __('landing.developed_by') }} Ahmed Alsir</p>
    </div>

    {{-- Form panel --}}
    <div class="flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-sm">
            <div class="flex items-center justify-between mb-8">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-1.5 text-xs text-slate-400 hover:text-slate-700 transition">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-3.5 h-3.5 rtl:rotate-180"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icoArrowLeft }}"/></svg>
                    {{ __('admin.auth.back_to_site') }}
                </a>
                <a href="{{ request()->fullUrlWithQuery(['lang' => $otherLocale]) }}" class="text-xs text-slate-400 hover:text-slate-700 transition">{{ __('admin.nav.language_switch') }}</a>
            </div>

            <p class="md:hidden text-center font-display text-lg font-semibold text-slate-900 mb-1">{{ __('admin.auth.studio_name') }}</p>
            <h2 class="text-2xl font-display font-semibold text-slate-900 mb-1">{{ __('admin.auth.sign_in_title') }}</h2>
            <p class="text-sm text-slate-500 mb-8">{{ __('admin.auth.tagline') }}</p>

            <form action="{{ route('login') }}" method="POST" class="space-y-5">
                @csrf

                @if (session('status'))
                    <div class="rounded-lg bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 text-sm">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="rounded-lg bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm">
                        {{ $errors->first() }}
                    </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.auth.field_email') }}</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus class="form-input">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.auth.field_password') }}</label>
                    <input type="password" name="password" required class="form-input">
                </div>

                <label class="flex items-center gap-2 text-sm text-slate-600">
                    <input type="checkbox" name="remember" class="form-checkbox">
                    {{ __('admin.auth.remember_me') }}
                </label>

                <button class="w-full bg-slate-900 text-white text-sm px-4 py-3 rounded-xl font-medium hover:bg-slate-800 shadow-sm hover:shadow-md transition">{{ __('admin.auth.submit') }}</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
