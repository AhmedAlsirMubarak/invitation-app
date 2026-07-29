@extends('admin.layouts.app')
@section('title', __('admin.landing.settings_title'))

@section('content')
<h1 class="text-2xl font-semibold text-slate-900 mb-4">{{ __('admin.landing.nav_settings') }}</h1>
@include('admin.landing._subnav')

<form action="{{ route('admin.landing.settings.update') }}" method="POST" enctype="multipart/form-data" class="bg-white border border-slate-200 rounded-lg p-6 max-w-2xl space-y-6">
    @csrf @method('PUT')

    @foreach ($textKeys as $key)
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.landing.field_key.'.$key) }}</label>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <span class="text-xs text-slate-400">{{ __('admin.landing.ar') }}</span>
                    @if (str_contains($key, 'subtitle'))
                        <textarea name="value[{{ $key }}][ar]" rows="2" class="form-textarea" dir="rtl">{{ old("value.$key.ar", $settings[$key]->value['ar'] ?? '') }}</textarea>
                    @else
                        <input name="value[{{ $key }}][ar]" value="{{ old("value.$key.ar", $settings[$key]->value['ar'] ?? '') }}" class="form-input" dir="rtl">
                    @endif
                </div>
                <div>
                    <span class="text-xs text-slate-400">{{ __('admin.landing.en') }}</span>
                    @if (str_contains($key, 'subtitle'))
                        <textarea name="value[{{ $key }}][en]" rows="2" class="form-textarea" dir="ltr">{{ old("value.$key.en", $settings[$key]->value['en'] ?? '') }}</textarea>
                    @else
                        <input name="value[{{ $key }}][en]" value="{{ old("value.$key.en", $settings[$key]->value['en'] ?? '') }}" class="form-input" dir="ltr">
                    @endif
                </div>
            </div>
        </div>
    @endforeach

    @foreach ($imageKeys as $key)
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.landing.settings_hero_image') }}</label>
            @if (($settings[$key]->image_path ?? null))
                <p class="text-xs text-slate-400 mb-1">{{ __('admin.landing.settings_current_image') }}</p>
                <img src="{{ Storage::disk('public')->url($settings[$key]->image_path) }}" class="h-24 rounded-lg border border-slate-200 mb-2">
            @endif
            <input type="file" name="images[{{ $key }}]" accept="image/*" class="form-file">
        </div>
    @endforeach

    <button class="bg-slate-900 text-white text-sm px-4 py-2.5 rounded-lg font-medium hover:bg-slate-800 transition">{{ __('admin.landing.save') }}</button>
</form>
@endsection
