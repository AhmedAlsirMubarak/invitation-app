@php
    $locale = app()->getLocale();
    $dir = $locale === 'ar' ? 'rtl' : 'ltr';
    $otherLocale = $locale === 'ar' ? 'en' : 'ar';
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $dir }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $event->displayTitle() }}</title>
    @vite(['resources/css/app.css'])
    <style>
        :root {
            --primary: {{ $design['primary_color'] ?? '#16261F' }};
            --accent: {{ $design['accent_color'] ?? '#C79A3B' }};
        }
        body { background: var(--primary); }
        .font-display { font-family: 'Playfair Display', serif; }
        .accent-text { color: var(--accent); }
        .accent-border { border-color: var(--accent); }
    </style>
</head>
<body class="min-h-screen text-white antialiased">
<div class="max-w-md mx-auto px-6 py-14 text-center">

    <a href="{{ request()->fullUrlWithQuery(['lang' => $otherLocale]) }}"
       class="inline-block mb-6 text-xs border border-white/30 rounded-full px-3 py-1 text-white/70 hover:text-white hover:border-white/60">
        {{ __('invite.language') }}
    </a>

    <p class="uppercase tracking-[0.3em] text-xs accent-text mb-4">
        {{ $event->event_type === 'wedding' ? __('invite.invited_to_wedding') : __('invite.invited_to') }}
    </p>

    <h1 class="font-display text-4xl leading-tight mb-3">{{ $event->displayTitle() }}</h1>

    <div class="w-16 h-px mx-auto accent-border border-t my-6"></div>

    <p class="text-lg mb-1">{{ $event->event_date->translatedFormat($locale === 'ar' ? 'l، d F Y' : 'l, d F Y') }}</p>
    <p class="text-sm text-white/70 mb-6">{{ $event->event_date->translatedFormat('g:i A') }}</p>

    @if ($event->venue_name)
        <p class="text-sm text-white/80 mb-1">{{ $event->venue_name }}</p>
    @endif
    @if ($event->venue_map_url)
        <a href="{{ $event->venue_map_url }}" target="_blank" class="text-xs accent-text underline">{{ __('invite.view_location') }}</a>
    @endif

    @if ($event->description)
        <p class="text-sm text-white/70 mt-6 leading-relaxed">{{ $event->description }}</p>
    @endif

    <div class="w-16 h-px mx-auto accent-border border-t my-8"></div>

    <p class="text-sm accent-text mb-1">{{ in_array($event->event_type, ['workshop', 'sports']) ? __('invite.salutation_casual') : __('invite.salutation') }}</p>
    <p class="font-display text-2xl mb-1">{{ $guest->displayName() }}</p>
    @if ($guest->companions_count > 0)
        <p class="text-sm text-white/70 mb-6">{{ __('invite.companions_line', ['count' => $guest->companions_count]) }}</p>
    @else
        <p class="text-sm text-white/70 mb-6">{{ __('invite.no_companions_line') }}</p>
    @endif

    @if (session('status'))
        <div class="bg-white/10 rounded-md px-4 py-3 text-sm mb-6">{{ session('status') }}</div>
    @endif

    @if ($event->hasFullService())
        @if ($guest->rsvp_status === 'pending')
            <p class="text-xs text-white/60 mb-3">{{ __('invite.rsvp_prompt') }}</p>
            <form action="{{ route('invitations.rsvp', $guest->token) }}" method="POST" class="flex gap-3 justify-center mb-8">
                @csrf
                <button name="rsvp_status" value="accepted" class="px-6 py-2 rounded-full text-sm font-medium" style="background: var(--accent); color: var(--primary);">{{ __('invite.accept') }}</button>
                <button name="rsvp_status" value="declined" class="px-6 py-2 rounded-full text-sm font-medium border border-white/40 text-white">{{ __('invite.decline') }}</button>
            </form>
        @else
            <p class="text-sm mb-3 accent-text">
                {{ $guest->rsvp_status === 'accepted' ? __('invite.responded_accepted') : __('invite.responded_declined') }}
            </p>
            <form action="{{ route('invitations.rsvp', $guest->token) }}" method="POST" class="mb-8">
                @csrf
                <input type="hidden" name="rsvp_status" value="{{ $guest->rsvp_status === 'accepted' ? 'declined' : 'accepted' }}">
                <button class="text-xs text-white/50 underline">{{ __('invite.change_response') }}</button>
            </form>
        @endif
    @endif

    @if ((! $event->hasFullService() || $guest->rsvp_status === 'accepted') && $guest->qr_code_path)
        <div class="bg-white rounded-xl p-5 inline-block text-center" style="color: var(--primary);">
            <p class="font-display text-sm mb-1">{{ __('invite.entry_card_title') }}</p>
            <img src="{{ Storage::disk('public')->url($guest->qr_code_path) }}" alt="{{ __('invite.entry_card_title') }}" class="w-40 h-40 mx-auto my-2">
            <p class="text-xs font-medium">{{ $guest->displayName() }}</p>
            @if ($guest->companions_count > 0)
                <p class="text-xs text-slate-500">{{ __('invite.guest_count') }}: {{ $guest->companions_count + 1 }}</p>
            @endif
            <p class="text-[11px] text-slate-400 mt-2">{{ __('invite.entry_card_subtitle') }}</p>
        </div>
    @endif

    <img src="{{ \App\Models\LandingSetting::logoUrl('logo_dark') }}" alt="{{ __('invite.footer_powered_by') }}" class="h-6 w-auto object-contain mx-auto mt-10 opacity-40">
    <p class="text-[11px] text-white/30 mt-1">{{ __('invite.footer_powered_by') }}</p>

</div>
</body>
</html>
