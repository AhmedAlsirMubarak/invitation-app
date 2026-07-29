@extends('admin.layouts.app')
@section('title', $event->title)

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <div class="flex items-center gap-2 mb-1">
            <h1 class="text-2xl font-semibold text-slate-900">{{ $event->title }}</h1>
            <span class="text-xs px-2 py-0.5 rounded-full {{ $event->hasFullService() ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                {{ $event->hasFullService() ? __('admin.packages.premium') : __('admin.packages.basic') }}
            </span>
        </div>
        <p class="text-sm text-slate-500">{{ __('admin.event_types.'.$event->event_type) }} · {{ $event->event_date->translatedFormat('d M Y, g:i A') }} · {{ $event->venue_name }}</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.events.scanner', $event) }}" class="bg-emerald-700 text-white text-sm px-4 py-2 rounded-md hover:bg-emerald-800">{{ __('admin.events.open_scanner') }}</a>
        <a href="{{ route('admin.events.edit', $event) }}" class="border border-slate-300 text-sm px-4 py-2 rounded-md hover:bg-slate-50">{{ __('admin.events.edit') }}</a>
        <form action="{{ route('admin.events.destroy', $event) }}" method="POST" onsubmit="return confirm('{{ __('admin.events.delete_confirm') }}')">
            @csrf @method('DELETE')
            <button class="border border-red-200 text-red-600 text-sm px-4 py-2 rounded-md hover:bg-red-50">{{ __('admin.events.delete') }}</button>
        </form>
    </div>
</div>

@php
    $statKeys = $event->hasFullService()
        ? ['invited', 'accepted', 'declined', 'checked_in', 'checked_out', 'in_venue']
        : ['invited', 'checked_in', 'checked_out', 'in_venue'];
@endphp
<div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-8">
    @foreach ($statKeys as $key)
        <div class="bg-white border border-slate-200 rounded-lg p-4">
            <p class="text-2xl font-semibold text-slate-900">{{ $stats[$key] }}</p>
            <p class="text-xs text-slate-500">{{ __('admin.events.stats.'.$key) }}</p>
        </div>
    @endforeach
</div>

<div class="bg-white border border-slate-200 rounded-lg p-5 mb-8">
    <p class="font-medium text-slate-900 text-sm mb-1">{{ __('admin.events.client_link_title') }}</p>
    <p class="text-xs text-slate-500 mb-3">{{ __('admin.events.client_link_hint') }}</p>
    <div class="flex items-center gap-2 mb-2">
        <input type="text" readonly value="{{ $event->clientUrl() }}" id="client-link-input" class="form-input flex-1 bg-slate-50" onclick="this.select()">
        <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('client-link-input').value)" class="border border-slate-300 text-sm px-3 py-2 rounded-md hover:bg-slate-50">{{ __('admin.events.client_link_copy') }}</button>
        <a href="{{ $event->clientUrl() }}" target="_blank" class="border border-slate-300 text-sm px-3 py-2 rounded-md hover:bg-slate-50">{{ __('admin.events.client_link_open') }}</a>
    </div>
    <div class="flex items-center justify-between">
        <p class="text-xs text-slate-500">
            {{ $event->guest_quota !== null ? __('admin.quota_usage', ['used' => $event->guests()->count(), 'total' => $event->guest_quota]) : __('admin.quota_usage_unlimited', ['used' => $event->guests()->count()]) }}
        </p>
        <form action="{{ route('admin.events.regenerate-client-link', $event) }}" method="POST" onsubmit="return confirm('{{ __('admin.events.client_link_regenerate_confirm') }}')">
            @csrf
            <button class="text-xs text-red-600 underline">{{ __('admin.events.client_link_regenerate') }}</button>
        </form>
    </div>
</div>

<div class="bg-white border border-slate-200 rounded-lg p-5 mb-8">
    <p class="font-medium text-slate-900 text-sm mb-1">{{ __('admin.events.scanner_link_title') }}</p>
    <p class="text-xs text-slate-500 mb-3">{{ __('admin.events.scanner_link_hint') }}</p>
    <div class="flex items-center gap-2 mb-2">
        <input type="text" readonly value="{{ $event->scannerUrl() }}" id="scanner-link-input" class="form-input flex-1 bg-slate-50" onclick="this.select()">
        <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('scanner-link-input').value)" class="border border-slate-300 text-sm px-3 py-2 rounded-md hover:bg-slate-50">{{ __('admin.events.scanner_link_copy') }}</button>
        <a href="{{ $event->scannerUrl() }}" target="_blank" class="border border-slate-300 text-sm px-3 py-2 rounded-md hover:bg-slate-50">{{ __('admin.events.scanner_link_open') }}</a>
    </div>
    <div class="flex items-center justify-end">
        <form action="{{ route('admin.events.regenerate-scanner-link', $event) }}" method="POST" onsubmit="return confirm('{{ __('admin.events.scanner_link_regenerate_confirm') }}')">
            @csrf
            <button class="text-xs text-red-600 underline">{{ __('admin.events.scanner_link_regenerate') }}</button>
        </form>
    </div>
</div>

<div class="flex items-center justify-between mb-3">
    <h2 class="text-lg font-semibold text-slate-900">{{ __('admin.events.guests_heading') }}</h2>
    <a href="{{ route('admin.events.guests.index', $event) }}" class="text-sm text-slate-700 underline">{{ __('admin.events.manage_guests') }}</a>
</div>

<div class="bg-white border border-slate-200 rounded-lg divide-y divide-slate-100">
    @forelse ($event->guests->take(10) as $guest)
        <div class="flex items-center justify-between px-5 py-3 text-sm">
            <span class="text-slate-900">{{ $guest->name }}</span>
            <span class="text-slate-500">{{ __('admin.rsvp_statuses.'.$guest->rsvp_status) }}</span>
            <span class="text-slate-500">{{ $guest->checkin?->checked_in_at ? __('admin.events.stats.checked_in') : '—' }}</span>
        </div>
    @empty
        <div class="px-5 py-6 text-center text-slate-500 text-sm">{{ __('admin.events.no_guests') }}</div>
    @endforelse
</div>
@endsection
