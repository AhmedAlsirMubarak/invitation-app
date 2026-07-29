@extends('admin.layouts.app')
@section('title', __('admin.dashboard.title'))

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-semibold text-slate-900">{{ __('admin.dashboard.title') }}</h1>
    <a href="{{ route('admin.events.create') }}" class="bg-slate-900 text-white text-sm px-4 py-2 rounded-md hover:bg-slate-800">{{ __('admin.dashboard.new_event') }}</a>
</div>

<div class="grid gap-4">
    @forelse ($events as $event)
        <a href="{{ route('admin.events.show', $event) }}" class="bg-white border border-slate-200 rounded-lg p-5 flex items-center justify-between hover:border-slate-300">
            <div>
                <p class="font-medium text-slate-900">{{ $event->title }}</p>
                <p class="text-sm text-slate-500">{{ __('admin.event_types.'.$event->event_type) }} · {{ $event->event_date->translatedFormat('d M Y') }}</p>
            </div>
            <div class="text-sm text-slate-500">{{ $event->guests_count }} {{ __('admin.dashboard.guests_suffix') }}</div>
        </a>
    @empty
        <div class="bg-white border border-dashed border-slate-300 rounded-lg p-8 text-center text-slate-500">
            {{ __('admin.dashboard.empty') }} <a href="{{ route('admin.events.create') }}" class="text-slate-900 underline">{{ __('admin.dashboard.empty_cta') }}</a>.
        </div>
    @endforelse
</div>
@endsection
