@extends('admin.layouts.app')
@section('title', __('admin.guests.subtitle', ['noun' => $event->guestNoun()]).' · '.$event->title)

@section('content')
<div class="flex items-start justify-between gap-4 mb-1">
    <h1 class="text-2xl font-semibold text-slate-900">{{ $event->title }}</h1>
    @if ($event->hasFullService())
        <a href="{{ route('admin.events.guests.bulk-invite', $event) }}" class="shrink-0 inline-flex items-center gap-1.5 bg-emerald-600 text-white text-sm px-4 py-2 rounded-lg font-medium hover:bg-emerald-700 transition">
            {{ __('admin.guests.bulk_invite') }}
        </a>
    @endif
</div>
<p class="text-sm text-slate-500 mb-6">{{ __('admin.guests.subtitle', ['noun' => $event->guestNoun()]) }}</p>

<div class="flex gap-1 mb-6 text-sm">
    @foreach (['all' => __('admin.guests.filter_all'), 'pending' => __('admin.guests.filter_pending'), 'accepted' => __('admin.guests.filter_accepted'), 'declined' => __('admin.guests.filter_declined')] as $key => $label)
        <a href="{{ route('admin.events.guests.index', [$event, 'status' => $key]) }}"
           class="px-3 py-1.5 rounded-md {{ $status === $key ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
            {{ $label }} ({{ $counts[$key] }})
        </a>
    @endforeach
</div>

<div class="grid md:grid-cols-2 gap-6 mb-8">
    <form action="{{ route('admin.events.guests.store', $event) }}" method="POST" class="bg-white border border-slate-200 rounded-lg p-5 space-y-3">
        @csrf
        <p class="font-medium text-slate-900 text-sm">{{ __('admin.guests.add_single') }}</p>
        <input name="name" required placeholder="{{ __('admin.guests.field_name_placeholder') }}" class="form-input">
        <div class="grid grid-cols-3 gap-3">
            <select name="country_code" class="form-select">
                @foreach (\App\Support\PhoneNumber::COUNTRIES as $code => $label)
                    <option value="{{ $code }}" @selected($code === '966')>{{ $label }}</option>
                @endforeach
            </select>
            <input name="phone" placeholder="{{ __('admin.guests.field_phone_placeholder') }}" class="col-span-2 form-input">
        </div>
        <input name="email" placeholder="{{ __('admin.guests.field_email_placeholder') }}" class="form-input">
        @if ($event->showsCompanions() || $event->showsTableNumber())
            <div class="flex gap-3">
                @if ($event->showsCompanions())
                    <input name="companions_count" type="number" min="0" placeholder="{{ $event->companionsLabel() }}" class="flex-1 form-input">
                @endif
                @if ($event->showsTableNumber())
                    <input name="table_number" placeholder="{{ __('admin.guests.field_table_placeholder') }}" class="flex-1 form-input">
                @endif
            </div>
        @endif
        <button class="bg-slate-900 text-white text-sm px-4 py-2.5 rounded-lg font-medium hover:bg-slate-800 transition">{{ __('admin.guests.add_submit') }}</button>
    </form>

    <form action="{{ route('admin.events.guests.import', $event) }}" method="POST" enctype="multipart/form-data" class="bg-white border border-slate-200 rounded-lg p-5 space-y-3">
        @csrf
        <p class="font-medium text-slate-900 text-sm">{{ __('admin.guests.bulk_import') }}</p>
        <p class="text-xs text-slate-500">{{ __('admin.guests.bulk_import_hint') }}</p>
        <a href="{{ asset('templates/guests-import-template.csv') }}" download class="text-xs text-slate-700 underline inline-block">{{ __('admin.guests.download_template') }}</a>
        <input type="file" name="file" accept=".csv,.xlsx" required class="form-file">
        <button class="bg-slate-900 text-white text-sm px-4 py-2.5 rounded-lg font-medium hover:bg-slate-800 transition">{{ __('admin.guests.import_submit') }}</button>
    </form>
</div>

<div class="bg-white border border-slate-200 rounded-lg overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-left text-slate-500">
            <tr>
                <th class="px-4 py-2">{{ __('admin.guests.col_name') }}</th>
                <th class="px-4 py-2">{{ __('admin.guests.col_rsvp') }}</th>
                @if ($event->showsTableNumber())
                    <th class="px-4 py-2">{{ __('admin.guests.col_table') }}</th>
                @endif
                <th class="px-4 py-2">{{ __('admin.guests.col_qr') }}</th>
                <th class="px-4 py-2">{{ __('admin.guests.col_invitation_link') }}</th>
                @if ($event->hasFullService())
                    <th class="px-4 py-2">{{ __('admin.guests.col_whatsapp') }}</th>
                @endif
                <th class="px-4 py-2"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @foreach ($guests as $guest)
                <tr>
                    <td class="px-4 py-2 text-slate-900">{{ $guest->name }}</td>
                    <td class="px-4 py-2">{{ __('admin.rsvp_statuses.'.$guest->rsvp_status) }}</td>
                    @if ($event->showsTableNumber())
                        <td class="px-4 py-2">{{ $guest->table_number ?? '—' }}</td>
                    @endif
                    <td class="px-4 py-2">
                        @if ($guest->qr_code_path)
                            <a href="{{ Storage::disk('public')->url($guest->qr_code_path) }}" target="_blank" class="text-slate-700 underline">{{ __('admin.guests.view_download') }}</a>
                        @endif
                    </td>
                    <td class="px-4 py-2">
                        <a href="{{ $guest->invitationUrl() }}" target="_blank" class="text-slate-700 underline">{{ __('admin.guests.open') }}</a>
                    </td>
                    @if ($event->hasFullService())
                        <td class="px-4 py-2">
                            @if ($guest->phone)
                                <a href="{{ route('admin.events.guests.invite', [$event, $guest]) }}" target="_blank" class="text-emerald-700 underline text-xs">
                                    {{ $guest->invite_sent_at ? __('admin.guests.resend_invite') : __('admin.guests.send_invite') }}
                                </a>
                                @if ($guest->invite_sent_at)
                                    <span class="text-slate-400 text-xs block">{{ __('admin.guests.sent_ago', ['time' => $guest->invite_sent_at->diffForHumans()]) }}</span>
                                @endif
                            @else
                                <span class="text-slate-400 text-xs">{{ __('admin.guests.no_phone') }}</span>
                            @endif
                        </td>
                    @endif
                    <td class="px-4 py-2 text-right whitespace-nowrap">
                        <a href="{{ route('admin.events.guests.edit', [$event, $guest]) }}" class="text-slate-700 text-xs underline">{{ __('admin.guests.edit') }}</a>
                        <form action="{{ route('admin.events.guests.destroy', [$event, $guest]) }}" method="POST" onsubmit="return confirm('{{ __('admin.guests.remove_confirm') }}')" class="inline">
                            @csrf @method('DELETE')
                            <button class="text-red-600 text-xs underline ms-2">{{ __('admin.guests.remove') }}</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
