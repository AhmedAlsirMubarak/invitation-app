@php
    $otherLocale = app()->getLocale() === 'ar' ? 'en' : 'ar';
    $remaining = $event->remainingQuota();
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $event->title }} · {{ __('client.title', ['noun' => $event->guestNoun()]) }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-slate-50 text-slate-800 antialiased">
<div class="max-w-4xl mx-auto px-4 py-8">

    <div class="flex items-center justify-between mb-1">
        <h1 class="text-2xl font-semibold text-slate-900">{{ $event->title }}</h1>
        <a href="{{ request()->fullUrlWithQuery(['lang' => $otherLocale]) }}" class="text-xs text-slate-400 hover:text-slate-700">{{ __('client.language') }}</a>
    </div>
    <p class="text-sm text-slate-500 mb-1">{{ $event->event_date->translatedFormat('l، d F Y') }} · {{ $event->venue_name }}</p>
    <p class="text-sm text-slate-500 mb-2">{{ __('client.subtitle') }}</p>
    <div class="flex items-center gap-4 mb-4">
        <a href="{{ route('client.edit', $event->client_token) }}" class="inline-block text-xs text-slate-700 underline">{{ __('client.edit_event') }}</a>
        @if ($event->hasFullService())
            <a href="{{ route('client.guests.bulk-invite', $event->client_token) }}" class="inline-flex items-center gap-1.5 bg-emerald-600 text-white text-xs px-3.5 py-1.5 rounded-lg font-medium hover:bg-emerald-700 transition">
                {{ __('client.bulk_invite') }}
            </a>
        @endif
    </div>

    <div class="bg-white border border-slate-200 rounded-lg px-5 py-3 mb-6 text-sm flex items-center justify-between">
        @if ($event->guest_quota !== null)
            <span class="text-slate-700">{{ __('client.quota_used', ['used' => $counts['all'], 'total' => $event->guest_quota]) }}</span>
            <span class="{{ $remaining > 0 ? 'text-emerald-600' : 'text-red-600' }}">{{ __('client.quota_remaining', ['count' => $remaining]) }}</span>
        @else
            <span class="text-slate-700">{{ __('client.quota_unlimited', ['used' => $counts['all']]) }}</span>
        @endif
    </div>

    <div class="flex gap-1 mb-6 text-sm">
        @foreach (['all' => __('client.filter_all'), 'pending' => __('client.filter_pending'), 'accepted' => __('client.filter_accepted'), 'declined' => __('client.filter_declined')] as $key => $label)
            <a href="{{ route('client.show', [$event->client_token, 'status' => $key]) }}"
               class="px-3 py-1.5 rounded-md {{ $status === $key ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                {{ $label }} ({{ $counts[$key] }})
            </a>
        @endforeach
    </div>

    <div class="bg-white border border-slate-200 rounded-lg p-5 mb-6">
        <p class="font-medium text-slate-900 text-sm mb-1">{{ __('client.scanner_link_title') }}</p>
        <p class="text-xs text-slate-500 mb-3">{{ __('client.scanner_link_hint') }}</p>
        <div class="flex items-center gap-2">
            <input type="text" readonly value="{{ $event->scannerUrl() }}" id="scanner-link-input" class="form-input flex-1 bg-slate-50" onclick="this.select()">
            <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('scanner-link-input').value)" class="border border-slate-300 text-sm px-3 py-2 rounded-md hover:bg-slate-50">{{ __('client.scanner_link_copy') }}</button>
            <a href="{{ $event->scannerUrl() }}" target="_blank" class="border border-slate-300 text-sm px-3 py-2 rounded-md hover:bg-slate-50">{{ __('client.scanner_link_open') }}</a>
        </div>
    </div>

    @if (session('status'))
        <div class="mb-6 rounded-md bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm">
            {{ session('status') }}
        </div>
    @endif

    @if ($event->hasReachedQuota())
        <div class="mb-6 rounded-md bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 text-sm">
            {{ __('client.quota_reached') }}
        </div>
    @else
    <div class="grid md:grid-cols-2 gap-6 mb-8">
        <form action="{{ route('client.guests.store', $event->client_token) }}" method="POST" class="bg-white border border-slate-200 rounded-lg p-5 space-y-3">
            @csrf
            <p class="font-medium text-slate-900 text-sm">{{ __('client.add_single') }}</p>
            <input name="name" required placeholder="{{ __('client.field_name') }}" class="form-input">
            <input name="name_en" placeholder="{{ __('client.field_name_en') }}" dir="ltr" class="form-input">
            <div class="grid grid-cols-3 gap-3">
                <select name="country_code" class="form-select">
                    @foreach (\App\Support\PhoneNumber::COUNTRIES as $code => $label)
                        <option value="{{ $code }}" @selected($code === '966')>{{ $label }}</option>
                    @endforeach
                </select>
                <input name="phone" placeholder="{{ __('client.field_phone') }}" class="col-span-2 form-input">
            </div>
            <input name="email" placeholder="{{ __('client.field_email') }}" class="form-input">
            @if ($event->showsCompanions() || $event->showsTableNumber())
                <div class="flex gap-3">
                    @if ($event->showsCompanions())
                        <input name="companions_count" type="number" min="0" placeholder="{{ $event->companionsLabel() }}" class="flex-1 form-input">
                    @endif
                    @if ($event->showsTableNumber())
                        <input name="table_number" placeholder="{{ __('client.field_table') }}" class="flex-1 form-input">
                    @endif
                </div>
            @endif
            <button class="bg-slate-900 text-white text-sm px-4 py-2.5 rounded-lg font-medium hover:bg-slate-800 transition">{{ __('client.add_submit') }}</button>
        </form>

        <form action="{{ route('client.guests.import', $event->client_token) }}" method="POST" enctype="multipart/form-data" class="bg-white border border-slate-200 rounded-lg p-5 space-y-3">
            @csrf
            <p class="font-medium text-slate-900 text-sm">{{ __('client.bulk_import') }}</p>
            <p class="text-xs text-slate-500">{{ __('client.bulk_import_hint') }}</p>
            <a href="{{ asset('templates/guests-import-template.csv') }}" download class="text-xs text-slate-700 underline inline-block">{{ __('client.download_template') }}</a>
            <input type="file" name="file" accept=".csv,.xlsx" required class="form-file">
            <button class="bg-slate-900 text-white text-sm px-4 py-2.5 rounded-lg font-medium hover:bg-slate-800 transition">{{ __('client.import_submit') }}</button>
        </form>
    </div>
    @endif

    <div class="bg-white border border-slate-200 rounded-lg overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-slate-500">
                <tr>
                    <th class="px-4 py-2">{{ __('client.col_name') }}</th>
                    <th class="px-4 py-2">{{ __('client.col_rsvp') }}</th>
                    @if ($event->showsTableNumber())
                        <th class="px-4 py-2">{{ __('client.col_table') }}</th>
                    @endif
                    <th class="px-4 py-2">{{ __('client.col_qr') }}</th>
                    <th class="px-4 py-2">{{ __('client.col_invitation_link') }}</th>
                    @if ($event->hasFullService())
                        <th class="px-4 py-2">{{ __('client.col_whatsapp') }}</th>
                    @endif
                    <th class="px-4 py-2"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($guests as $guest)
                    <tr>
                        <td class="px-4 py-2 text-slate-900">{{ $guest->name }}</td>
                        <td class="px-4 py-2">{{ ucfirst($guest->rsvp_status) }}</td>
                        @if ($event->showsTableNumber())
                            <td class="px-4 py-2">{{ $guest->table_number ?? '—' }}</td>
                        @endif
                        <td class="px-4 py-2">
                            @if ($guest->qr_code_path)
                                <a href="{{ Storage::disk('public')->url($guest->qr_code_path) }}" target="_blank" class="text-slate-700 underline">{{ __('client.view_download') }}</a>
                            @endif
                        </td>
                        <td class="px-4 py-2">
                            <a href="{{ $guest->invitationUrl() }}" target="_blank" class="text-slate-700 underline">{{ __('client.open') }}</a>
                        </td>
                        @if ($event->hasFullService())
                            <td class="px-4 py-2">
                                @if ($guest->phone)
                                    <a href="{{ route('client.guests.invite', [$event->client_token, $guest]) }}" target="_blank" class="text-emerald-700 underline text-xs">
                                        {{ $guest->invite_sent_at ? __('client.resend_invite') : __('client.send_invite') }}
                                    </a>
                                    @if ($guest->invite_sent_at)
                                        <span class="text-slate-400 text-xs block">{{ __('client.sent_ago', ['time' => $guest->invite_sent_at->diffForHumans()]) }}</span>
                                    @endif
                                @else
                                    <span class="text-slate-400 text-xs">{{ __('client.no_phone') }}</span>
                                @endif
                            </td>
                        @endif
                        <td class="px-4 py-2 text-right">
                            <form action="{{ route('client.guests.destroy', [$event->client_token, $guest]) }}" method="POST" onsubmit="return confirm('{{ __('client.remove_confirm') }}')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 text-xs underline">{{ __('client.remove') }}</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-6 text-center text-slate-500">{{ __('client.no_guests') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
