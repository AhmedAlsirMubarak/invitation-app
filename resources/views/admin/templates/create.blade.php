@extends('admin.layouts.app')
@section('title', __('admin.templates.create_title'))

@section('content')
<h1 class="text-2xl font-semibold text-slate-900 mb-6">{{ __('admin.templates.create_title') }}</h1>

<form action="{{ route('admin.templates.store') }}" method="POST" enctype="multipart/form-data" class="bg-white border border-slate-200 rounded-lg p-6 max-w-xl space-y-5">
    @csrf
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.templates.field_name') }}</label>
        <input name="name" required class="form-input" placeholder="{{ __('admin.templates.field_name_placeholder') }}">
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.templates.field_category') }}</label>
        <select name="category" required class="form-select">
            @foreach (\App\Support\EventTypes::ALL as $cat)
                <option value="{{ $cat }}">{{ __('admin.event_types.'.$cat) }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.templates.field_description') }}</label>
        <textarea name="description" rows="2" class="form-textarea" placeholder="{{ __('admin.templates.field_description_placeholder') }}"></textarea>
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.templates.field_blade_view') }}</label>
        <input id="blade_view" name="blade_view" required class="form-input font-mono" placeholder="{{ __('admin.templates.field_blade_view_placeholder') }}">
        <p class="text-xs text-slate-400 mt-1.5">{{ __('admin.templates.field_blade_view_hint') }}</p>
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.templates.field_background_image') }}</label>
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
            <input type="color" name="primary_color" value="#16261F" class="form-color">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.templates.field_accent_color') }}</label>
            <input type="color" name="accent_color" value="#C79A3B" class="form-color">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.templates.field_text_color') }}</label>
            <input type="color" name="text_color" value="#1F2937" class="form-color">
        </div>
    </div>
    <p class="text-xs text-slate-400 -mt-3">{{ __('admin.templates.field_text_color_hint') }}</p>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.templates.field_font_family') }}</label>
        <input name="font_family" class="form-input" placeholder="{{ __('admin.templates.field_font_family_placeholder') }}">
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.templates.field_thumbnail') }}</label>
        <input type="file" name="thumbnail" accept="image/*" class="form-file">
    </div>

    <button class="bg-slate-900 text-white text-sm px-4 py-2.5 rounded-lg font-medium hover:bg-slate-800 transition">{{ __('admin.templates.create_submit') }}</button>
</form>
@endsection
