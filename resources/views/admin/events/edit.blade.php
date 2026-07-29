@extends('admin.layouts.app')
@section('title', __('admin.events.edit_title'))

@section('content')
<h1 class="text-2xl font-semibold text-slate-900 mb-6">{{ __('admin.events.edit_title') }}</h1>

<form action="{{ route('admin.events.update', $event) }}" method="POST" enctype="multipart/form-data" class="bg-white border border-slate-200 rounded-lg p-6 max-w-xl space-y-5">
    @csrf @method('PUT')
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.events.field_title') }}</label>
        <input name="title" value="{{ $event->title }}" required class="form-input">
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.events.field_type') }}</label>
            <select name="event_type" required class="form-select">
                @foreach (\App\Support\EventTypes::ALL as $type)
                    <option value="{{ $type }}" @selected($event->event_type === $type)>{{ __('admin.event_types.'.$type) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.events.field_date') }}</label>
            <input type="datetime-local" name="event_date" value="{{ $event->event_date->format('Y-m-d\TH:i') }}" required class="form-input">
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.events.field_template') }}</label>
        <select name="template_id" class="form-select">
            <option value="">{{ __('admin.events.field_template_none_edit') }}</option>
            @foreach ($templates as $template)
                <option value="{{ $template->id }}" @selected($event->template_id === $template->id)>{{ $template->name }} ({{ __('admin.event_types.'.$template->category) }})</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.events.field_venue_name') }}</label>
        <input name="venue_name" value="{{ $event->venue_name }}" class="form-input">
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.events.field_venue_map_url') }}</label>
        <input name="venue_map_url" value="{{ $event->venue_map_url }}" class="form-input">
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.events.field_description') }}</label>
        <textarea name="description" rows="3" class="form-textarea">{{ $event->description }}</textarea>
    </div>

    @if ($event->hasFullService())
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.events.field_invite_message') }}</label>
            <textarea name="invite_message_template" rows="4" class="form-textarea font-mono text-xs" dir="rtl" placeholder="{{ __('admin.events.message_template_hint') }}">{{ $event->invite_message_template }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.events.field_reminder_message') }}</label>
            <textarea name="reminder_message_template" rows="4" class="form-textarea font-mono text-xs" dir="rtl" placeholder="{{ __('admin.events.message_template_hint') }}">{{ $event->reminder_message_template }}</textarea>
        </div>
        <p class="text-xs text-slate-400 -mt-3">{{ __('admin.events.message_template_hint') }}</p>
    @endif

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.events.field_cover_image_replace') }}</label>
        <input type="file" name="cover_image" accept="image/*" class="form-file">
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.events.field_primary_color') }}</label>
            <input type="color" name="primary_color" value="{{ $event->design_overrides['primary_color'] ?? '#16261F' }}" class="form-color">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.events.field_accent_color') }}</label>
            <input type="color" name="accent_color" value="{{ $event->design_overrides['accent_color'] ?? '#C79A3B' }}" class="form-color">
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.events.field_status') }}</label>
        <select name="status" required class="form-select">
            @foreach (['draft','active','completed','archived'] as $status)
                <option value="{{ $status }}" @selected($event->status === $status)>{{ __('admin.event_statuses.'.$status) }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.events.field_package') }}</label>
        <select name="package" required class="form-select" id="package-select">
            <option value="premium" @selected($event->package === 'premium')>{{ __('admin.packages.premium') }}</option>
            <option value="basic" @selected($event->package === 'basic')>{{ __('admin.packages.basic') }}</option>
        </select>
        <p class="text-xs text-slate-400 mt-1.5" id="package-hint">{{ $event->package === 'basic' ? __('admin.packages.basic_hint') : __('admin.packages.premium_hint') }}</p>
    </div>
    <script>
        document.getElementById('package-select').addEventListener('change', function () {
            document.getElementById('package-hint').textContent = this.value === 'basic'
                ? @json(__('admin.packages.basic_hint'))
                : @json(__('admin.packages.premium_hint'));
        });
    </script>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.events.field_guest_quota') }}</label>
        <input type="number" name="guest_quota" min="0" value="{{ $event->guest_quota }}" class="form-input">
    </div>

    <button class="bg-slate-900 text-white text-sm px-4 py-2.5 rounded-lg font-medium hover:bg-slate-800 transition">{{ __('admin.events.edit_submit') }}</button>
</form>
@endsection
