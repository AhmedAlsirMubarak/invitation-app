<div>
    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.landing.field_author_name') }}</label>
    <input name="author_name" value="{{ old('author_name', $testimonial?->author_name ?? '') }}" required class="form-input">
</div>

<div>
    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.landing.field_event_category') }}</label>
    <div class="grid grid-cols-2 gap-3">
        <input name="event_category[ar]" value="{{ old('event_category.ar', $testimonial?->event_category['ar'] ?? '') }}" class="form-input" dir="rtl" placeholder="{{ __('admin.landing.ar') }}">
        <input name="event_category[en]" value="{{ old('event_category.en', $testimonial?->event_category['en'] ?? '') }}" class="form-input" dir="ltr" placeholder="{{ __('admin.landing.en') }}">
    </div>
</div>

<div>
    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.landing.field_quote') }}</label>
    <div class="grid grid-cols-2 gap-3">
        <textarea name="quote[ar]" rows="3" required class="form-textarea" dir="rtl" placeholder="{{ __('admin.landing.ar') }}">{{ old('quote.ar', $testimonial?->quote['ar'] ?? '') }}</textarea>
        <textarea name="quote[en]" rows="3" class="form-textarea" dir="ltr" placeholder="{{ __('admin.landing.en') }}">{{ old('quote.en', $testimonial?->quote['en'] ?? '') }}</textarea>
    </div>
</div>

<div>
    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.landing.field_rating') }}</label>
    <select name="rating" class="form-select">
        @foreach ([5,4,3,2,1] as $n)
            <option value="{{ $n }}" @selected(old('rating', $testimonial?->rating ?? 5) == $n)>{{ str_repeat('★', $n) }}</option>
        @endforeach
    </select>
</div>

<div>
    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.landing.field_avatar') }}</label>
    @if ($testimonial?->avatarUrl())
        <img src="{{ $testimonial->avatarUrl() }}" class="w-12 h-12 rounded-full object-cover mb-2">
    @endif
    <input type="file" name="avatar" accept="image/*" class="form-file">
</div>

<div class="grid grid-cols-2 gap-4 items-end">
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.landing.sort_order') }}</label>
        <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $testimonial?->sort_order ?? 0) }}" class="form-input">
    </div>
    <label class="flex items-center gap-2 text-sm text-slate-700 pb-2">
        <input type="checkbox" name="is_active" value="1" class="form-checkbox" @checked(old('is_active', $testimonial?->is_active ?? true))>
        {{ __('admin.landing.active') }}
    </label>
</div>

<button class="bg-slate-900 text-white text-sm px-4 py-2.5 rounded-lg font-medium hover:bg-slate-800 transition">{{ __('admin.landing.save') }}</button>
