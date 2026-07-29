@php
    $featuresAr = collect($package?->features ?? [])->map(fn ($f) => $f['ar'] ?? '')->implode("\n");
    $featuresEn = collect($package?->features ?? [])->map(fn ($f) => $f['en'] ?? '')->implode("\n");
@endphp

<div>
    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.landing.field_package_name') }}</label>
    <div class="grid grid-cols-2 gap-3">
        <input name="name[ar]" value="{{ old('name.ar', $package?->name['ar'] ?? '') }}" required class="form-input" dir="rtl" placeholder="{{ __('admin.landing.ar') }}">
        <input name="name[en]" value="{{ old('name.en', $package?->name['en'] ?? '') }}" class="form-input" dir="ltr" placeholder="{{ __('admin.landing.en') }}">
    </div>
</div>

<div>
    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.landing.field_price_label') }}</label>
    <div class="grid grid-cols-2 gap-3">
        <input name="price_label[ar]" value="{{ old('price_label.ar', $package?->price_label['ar'] ?? '') }}" class="form-input" dir="rtl" placeholder="{{ __('admin.landing.ar') }}">
        <input name="price_label[en]" value="{{ old('price_label.en', $package?->price_label['en'] ?? '') }}" class="form-input" dir="ltr" placeholder="{{ __('admin.landing.en') }}">
    </div>
</div>

<div>
    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.landing.field_description') }}</label>
    <div class="grid grid-cols-2 gap-3">
        <textarea name="description[ar]" rows="2" class="form-textarea" dir="rtl" placeholder="{{ __('admin.landing.ar') }}">{{ old('description.ar', $package?->description['ar'] ?? '') }}</textarea>
        <textarea name="description[en]" rows="2" class="form-textarea" dir="ltr" placeholder="{{ __('admin.landing.en') }}">{{ old('description.en', $package?->description['en'] ?? '') }}</textarea>
    </div>
</div>

<div>
    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.landing.field_features_list') }}</label>
    <div class="grid grid-cols-2 gap-3">
        <textarea name="features_ar" rows="4" class="form-textarea" dir="rtl" placeholder="{{ __('admin.landing.ar') }}">{{ old('features_ar', $featuresAr) }}</textarea>
        <textarea name="features_en" rows="4" class="form-textarea" dir="ltr" placeholder="{{ __('admin.landing.en') }}">{{ old('features_en', $featuresEn) }}</textarea>
    </div>
</div>

<div>
    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.landing.field_badge') }}</label>
    <div class="grid grid-cols-2 gap-3">
        <input name="badge[ar]" value="{{ old('badge.ar', $package?->badge['ar'] ?? '') }}" class="form-input" dir="rtl" placeholder="{{ __('admin.landing.ar') }}">
        <input name="badge[en]" value="{{ old('badge.en', $package?->badge['en'] ?? '') }}" class="form-input" dir="ltr" placeholder="{{ __('admin.landing.en') }}">
    </div>
</div>

<div>
    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.landing.field_cta_label') }}</label>
    <div class="grid grid-cols-2 gap-3">
        <input name="cta_label[ar]" value="{{ old('cta_label.ar', $package?->cta_label['ar'] ?? '') }}" class="form-input" dir="rtl" placeholder="{{ __('admin.landing.ar') }}">
        <input name="cta_label[en]" value="{{ old('cta_label.en', $package?->cta_label['en'] ?? '') }}" class="form-input" dir="ltr" placeholder="{{ __('admin.landing.en') }}">
    </div>
</div>

<div>
    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.landing.field_cta_url') }}</label>
    <input name="cta_url" value="{{ old('cta_url', $package?->cta_url ?? '') }}" class="form-input" dir="ltr" placeholder="https://...">
</div>

<div class="grid grid-cols-2 gap-4 items-end">
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.landing.sort_order') }}</label>
        <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $package?->sort_order ?? 0) }}" class="form-input">
    </div>
    <label class="flex items-center gap-2 text-sm text-slate-700 pb-2">
        <input type="checkbox" name="is_active" value="1" class="form-checkbox" @checked(old('is_active', $package?->is_active ?? true))>
        {{ __('admin.landing.active') }}
    </label>
</div>

<button class="bg-slate-900 text-white text-sm px-4 py-2.5 rounded-lg font-medium hover:bg-slate-800 transition">{{ __('admin.landing.save') }}</button>
