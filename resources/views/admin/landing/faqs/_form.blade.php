<div>
    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.landing.field_question') }}</label>
    <div class="grid grid-cols-2 gap-3">
        <input name="question[ar]" value="{{ old('question.ar', $faq?->question['ar'] ?? '') }}" required class="form-input" dir="rtl" placeholder="{{ __('admin.landing.ar') }}">
        <input name="question[en]" value="{{ old('question.en', $faq?->question['en'] ?? '') }}" class="form-input" dir="ltr" placeholder="{{ __('admin.landing.en') }}">
    </div>
</div>

<div>
    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.landing.field_answer') }}</label>
    <div class="grid grid-cols-2 gap-3">
        <textarea name="answer[ar]" rows="3" required class="form-textarea" dir="rtl" placeholder="{{ __('admin.landing.ar') }}">{{ old('answer.ar', $faq?->answer['ar'] ?? '') }}</textarea>
        <textarea name="answer[en]" rows="3" class="form-textarea" dir="ltr" placeholder="{{ __('admin.landing.en') }}">{{ old('answer.en', $faq?->answer['en'] ?? '') }}</textarea>
    </div>
</div>

<div class="grid grid-cols-2 gap-4 items-end">
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.landing.sort_order') }}</label>
        <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $faq?->sort_order ?? 0) }}" class="form-input">
    </div>
    <label class="flex items-center gap-2 text-sm text-slate-700 pb-2">
        <input type="checkbox" name="is_active" value="1" class="form-checkbox" @checked(old('is_active', $faq?->is_active ?? true))>
        {{ __('admin.landing.active') }}
    </label>
</div>

<button class="bg-slate-900 text-white text-sm px-4 py-2.5 rounded-lg font-medium hover:bg-slate-800 transition">{{ __('admin.landing.save') }}</button>
