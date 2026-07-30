@php
    $otherLocale = app()->getLocale() === 'ar' ? 'en' : 'ar';
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('client.edit_event_title') }} · {{ $event->title }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-slate-50 text-slate-800 antialiased">
<div class="max-w-2xl mx-auto px-4 py-8">

    <div class="flex items-center justify-between mb-6">
        <div>
            <a href="{{ route('client.show', $event->client_token) }}" class="text-xs text-slate-500 hover:text-slate-700 underline">{{ __('client.back_to_guests') }}</a>
            <h1 class="text-2xl font-semibold text-slate-900 mt-1">{{ __('client.edit_event_title') }}</h1>
        </div>
        <a href="{{ request()->fullUrlWithQuery(['lang' => $otherLocale]) }}" class="text-xs text-slate-400 hover:text-slate-700">{{ __('client.language') }}</a>
    </div>

    @if (session('status'))
        <div class="mb-6 rounded-md bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 rounded-md bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('client.update', $event->client_token) }}" method="POST" class="bg-white border border-slate-200 rounded-lg p-6 space-y-5">
        @csrf @method('PUT')

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('client.field_title') }}</label>
            <input name="title" value="{{ old('title', $event->title) }}" required class="form-input">
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('client.field_title_en') }}</label>
            <input name="title_en" value="{{ old('title_en', $event->title_en) }}" dir="ltr" class="form-input">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('client.field_event_type') }}</label>
                <select name="event_type" required class="form-select">
                    @foreach (\App\Support\EventTypes::ALL as $type)
                        <option value="{{ $type }}" @selected(old('event_type', $event->event_type) === $type)>{{ __('client.event_types.'.$type) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('client.field_event_date') }}</label>
                <input type="datetime-local" name="event_date" value="{{ old('event_date', $event->event_date->format('Y-m-d\TH:i')) }}" required class="form-input">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('client.field_venue_name') }}</label>
            <input name="venue_name" value="{{ old('venue_name', $event->venue_name) }}" class="form-input">
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('client.field_venue_map_url') }}</label>
            <input name="venue_map_url" value="{{ old('venue_map_url', $event->venue_map_url) }}" class="form-input" placeholder="https://maps.google.com/...">
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('client.field_description') }}</label>
            <textarea name="description" rows="3" class="form-textarea">{{ old('description', $event->description) }}</textarea>
        </div>

        <button class="bg-slate-900 text-white text-sm px-4 py-2.5 rounded-lg font-medium hover:bg-slate-800 transition">{{ __('client.save_changes') }}</button>
    </form>
</div>
</body>
</html>
