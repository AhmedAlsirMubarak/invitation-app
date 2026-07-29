@extends('admin.layouts.app')
@section('title', __('admin.events.index_title'))

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-semibold text-slate-900">{{ __('admin.events.index_title') }}</h1>
    <a href="{{ route('admin.events.create') }}" class="bg-slate-900 text-white text-sm px-4 py-2 rounded-md hover:bg-slate-800">{{ __('admin.events.new_event') }}</a>
</div>

<div class="bg-white border border-slate-200 rounded-lg divide-y divide-slate-100">
    @foreach ($events as $event)
        <a href="{{ route('admin.events.show', $event) }}" class="flex items-center justify-between px-5 py-4 hover:bg-slate-50">
            <div>
                <p class="font-medium text-slate-900">{{ $event->title }}</p>
                <p class="text-sm text-slate-500">{{ __('admin.event_types.'.$event->event_type) }} · {{ $event->event_date->translatedFormat('d M Y') }} · {{ __('admin.event_statuses.'.$event->status) }}</p>
            </div>
            <span class="text-sm text-slate-500">{{ $event->guests_count }} {{ __('admin.events.guests_suffix') }}</span>
        </a>
    @endforeach
</div>

<div class="mt-4">{{ $events->links() }}</div>
@endsection
