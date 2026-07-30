<a href="{{ request()->fullUrlWithQuery(['lang' => $otherLocale]) }}"
   class="inline-block mb-3 text-xs border rounded-full px-3 py-1 opacity-70 hover:opacity-100"
   style="border-color: currentColor;">
    {{ __('invite.language') }}
</a>

<p class="uppercase tracking-[0.3em] text-xs accent-text mb-2">
    {{ $event->event_type === 'wedding' ? __('invite.invited_to_wedding') : __('invite.invited_to') }}
</p>

<h1 class="font-display text-3xl leading-tight mb-2">{{ $event->displayTitle() }}</h1>

<div class="w-12 h-px mx-auto accent-border border-t my-3"></div>

<p class="text-base mb-0.5">{{ $event->event_date->translatedFormat($locale === 'ar' ? 'l، d F Y' : 'l, d F Y') }}</p>
<p class="text-sm opacity-70 mb-3">{{ $event->event_date->translatedFormat('g:i A') }}</p>

@if ($event->venue_name)
    <p class="text-sm opacity-80 mb-0.5">{{ $event->venue_name }}</p>
@endif
@if ($event->venue_map_url)
    <a href="{{ $event->venue_map_url }}" target="_blank" class="text-xs accent-text underline">{{ __('invite.view_location') }}</a>
@endif

@if ($event->description)
    <p class="text-sm opacity-70 mt-3 leading-relaxed">{{ $event->description }}</p>
@endif

<div class="w-12 h-px mx-auto accent-border border-t my-4"></div>

<p class="text-sm accent-text mb-1">{{ in_array($event->event_type, ['workshop', 'sports']) ? __('invite.salutation_casual') : __('invite.salutation') }}</p>
<p class="font-display text-xl mb-1">{{ $guest->displayName() }}</p>
@if ($guest->companions_count > 0)
    <p class="text-sm opacity-70 mb-3">{{ __('invite.companions_line', ['count' => $guest->companions_count]) }}</p>
@else
    <p class="text-sm opacity-70 mb-3">{{ __('invite.no_companions_line') }}</p>
@endif

@if (session('status'))
    <div class="bg-black/5 rounded-md px-4 py-2 text-sm mb-3">{{ session('status') }}</div>
@endif

@if ($event->hasFullService())
    @if ($guest->rsvp_status === 'pending')
        <p class="text-xs opacity-60 mb-2">{{ __('invite.rsvp_prompt') }}</p>
        <form action="{{ route('invitations.rsvp', $guest->token) }}" method="POST" class="flex gap-3 justify-center mb-4">
            @csrf
            <button name="rsvp_status" value="accepted" class="px-6 py-2 rounded-full text-sm font-medium text-white" style="background: var(--accent);">{{ __('invite.accept') }}</button>
            <button name="rsvp_status" value="declined" class="px-6 py-2 rounded-full text-sm font-medium border" style="border-color: currentColor;">{{ __('invite.decline') }}</button>
        </form>
    @else
        <p class="text-sm mb-2 accent-text">
            {{ $guest->rsvp_status === 'accepted' ? __('invite.responded_accepted') : __('invite.responded_declined') }}
        </p>
        <form action="{{ route('invitations.rsvp', $guest->token) }}" method="POST" class="mb-4">
            @csrf
            <input type="hidden" name="rsvp_status" value="{{ $guest->rsvp_status === 'accepted' ? 'declined' : 'accepted' }}">
            <button class="text-xs opacity-50 underline">{{ __('invite.change_response') }}</button>
        </form>
    @endif
@endif

@if ((! $event->hasFullService() || $guest->rsvp_status === 'accepted') && $guest->qr_code_path)
    <div class="bg-white rounded-xl p-4 inline-block text-center" style="color: {{ $design['primary_color'] ?? '#16261F' }};">
        <p class="font-display text-sm mb-1">{{ __('invite.entry_card_title') }}</p>
        <img src="{{ Storage::disk('public')->url($guest->qr_code_path) }}" alt="{{ __('invite.entry_card_title') }}" class="w-32 h-32 mx-auto my-1">
        <p class="text-xs font-medium">{{ $guest->displayName() }}</p>
        @if ($guest->companions_count > 0)
            <p class="text-xs text-slate-500">{{ __('invite.guest_count') }}: {{ $guest->companions_count + 1 }}</p>
        @endif
        <p class="text-[11px] text-slate-400 mt-1">{{ __('invite.entry_card_subtitle') }}</p>
    </div>
@endif

<img src="{{ \App\Models\LandingSetting::logoUrl('logo_dark') }}" alt="{{ __('invite.footer_powered_by') }}" class="h-20 w-auto object-contain mx-auto mt-4 opacity-40">
<p class="text-[11px] opacity-30 mt-1">{{ __('invite.footer_powered_by') }}</p>
