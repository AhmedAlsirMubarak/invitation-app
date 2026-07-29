@extends('admin.layouts.app')
@section('title', __('admin.templates.edit_title'))

@section('content')
<h1 class="text-2xl font-semibold text-slate-900 mb-6">{{ __('admin.templates.edit_title') }}</h1>

<form action="{{ route('admin.templates.update', $template) }}" method="POST" enctype="multipart/form-data" class="bg-white border border-slate-200 rounded-lg p-6 max-w-xl space-y-5">
    @csrf @method('PUT')
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.templates.field_name') }}</label>
        <input name="name" value="{{ $template->name }}" required class="form-input">
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.templates.field_category') }}</label>
        <select name="category" required class="form-select">
            @foreach (\App\Support\EventTypes::ALL as $cat)
                <option value="{{ $cat }}" @selected($template->category === $cat)>{{ __('admin.event_types.'.$cat) }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.templates.field_description') }}</label>
        <textarea name="description" rows="2" class="form-textarea">{{ $template->description }}</textarea>
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.templates.field_blade_view') }}</label>
        <input id="blade_view" name="blade_view" value="{{ $template->blade_view }}" required class="form-input font-mono">
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.templates.field_background_image_replace') }}</label>
        @if ($template->backgroundImageUrl())
            <img src="{{ $template->backgroundImageUrl() }}" class="h-32 rounded-lg border border-slate-200 mb-2">
        @endif
        <input id="background_image" type="file" name="background_image" accept="image/*" class="form-file">
        <p class="text-xs text-slate-400 mt-1.5">{{ __('admin.templates.field_background_image_hint') }}</p>
    </div>
    <script>
        document.getElementById('background_image').addEventListener('change', function () {
            const bladeView = document.getElementById('blade_view');
            if (this.files.length && !bladeView.value) {
                bladeView.value = 'image-overlay';
            }
        });
    </script>

    <div class="grid grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.templates.field_primary_color') }}</label>
            <input type="color" name="primary_color" value="{{ $template->default_config['primary_color'] ?? '#16261F' }}" class="form-color">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.templates.field_accent_color') }}</label>
            <input type="color" name="accent_color" value="{{ $template->default_config['accent_color'] ?? '#C79A3B' }}" class="form-color">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.templates.field_text_color') }}</label>
            <input type="color" name="text_color" value="{{ $template->default_config['text_color'] ?? '#1F2937' }}" class="form-color">
        </div>
    </div>
    <p class="text-xs text-slate-400 -mt-3">{{ __('admin.templates.field_text_color_hint') }}</p>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.templates.field_font_family') }}</label>
        <input name="font_family" value="{{ $template->default_config['font_family'] ?? '' }}" class="form-input">
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.templates.field_thumbnail_replace') }}</label>
        <input type="file" name="thumbnail" accept="image/*" class="form-file">
    </div>

    <label class="flex items-center gap-2 text-sm text-slate-700">
        <input type="checkbox" name="is_active" value="1" class="form-checkbox" @checked($template->is_active)>
        {{ __('admin.templates.field_is_active') }}
    </label>

    <button class="bg-slate-900 text-white text-sm px-4 py-2.5 rounded-lg font-medium hover:bg-slate-800 transition">{{ __('admin.templates.edit_submit') }}</button>
</form>
@endsection
