@extends('admin.layouts.app')
@section('title', __('admin.reminders.title'))

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-semibold text-slate-900">{{ __('admin.reminders.title') }}</h1>
    <p class="text-sm text-slate-500">{{ __('admin.reminders.subtitle') }}</p>
</div>

<div class="bg-white border border-slate-200 rounded-lg overflow-hidden mb-8">
    <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between">
        <p class="font-medium text-sm text-slate-900">{{ __('admin.reminders.to_remind', ['count' => $pending->count()]) }}</p>
    </div>
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-left text-slate-500">
            <tr>
                <th class="px-4 py-2">{{ __('admin.reminders.col_guest') }}</th>
                <th class="px-4 py-2">{{ __('admin.reminders.col_event') }}</th>
                <th class="px-4 py-2">{{ __('admin.reminders.col_phone') }}</th>
                <th class="px-4 py-2"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse ($pending as $guest)
                <tr>
                    <td class="px-4 py-2 text-slate-900">{{ $guest->name }}</td>
                    <td class="px-4 py-2 text-slate-500">{{ $guest->event->title }}</td>
                    <td class="px-4 py-2 text-slate-500">{{ $guest->phone ?? '—' }}</td>
                    <td class="px-4 py-2 text-right">
                        @if ($guest->phone)
                            <a href="{{ route('admin.events.guests.remind', [$guest->event, $guest]) }}" target="_blank" class="inline-flex items-center gap-1 bg-emerald-600 text-white text-xs px-3 py-1.5 rounded-md hover:bg-emerald-700">{{ __('admin.reminders.send_reminder') }}</a>
                        @else
                            <span class="text-xs text-slate-400">{{ __('admin.reminders.no_phone') }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-4 py-6 text-center text-slate-500">{{ __('admin.reminders.empty') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($alreadyReminded->isNotEmpty())
<div class="bg-white border border-slate-200 rounded-lg overflow-hidden">
    <div class="px-5 py-3 border-b border-slate-100">
        <p class="font-medium text-sm text-slate-900">{{ __('admin.reminders.already_reminded', ['count' => $alreadyReminded->count()]) }}</p>
    </div>
    <table class="w-full text-sm">
        <tbody class="divide-y divide-slate-100">
            @foreach ($alreadyReminded as $guest)
                <tr>
                    <td class="px-4 py-2 text-slate-900">{{ $guest->name }}</td>
                    <td class="px-4 py-2 text-slate-500">{{ $guest->event->title }}</td>
                    <td class="px-4 py-2 text-slate-400 text-xs">{{ __('admin.reminders.sent_ago', ['time' => $guest->reminded_at->diffForHumans()]) }}</td>
                    <td class="px-4 py-2 text-right">
                        <a href="{{ route('admin.events.guests.remind', [$guest->event, $guest]) }}" target="_blank" class="text-xs text-slate-500 underline">{{ __('admin.reminders.resend') }}</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
@endsection
