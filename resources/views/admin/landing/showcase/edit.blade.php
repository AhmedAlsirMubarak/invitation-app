@extends('admin.layouts.app')
@section('title', __('admin.landing.edit'))

@section('content')
<h1 class="text-2xl font-semibold text-slate-900 mb-4">{{ __('admin.landing.edit') }}</h1>
@include('admin.landing._subnav')

<form action="{{ route('admin.landing.showcase.update', $item) }}" method="POST" enctype="multipart/form-data" class="bg-white border border-slate-200 rounded-lg p-6 max-w-xl space-y-5">
    @csrf @method('PUT')

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.landing.field_image') }}</label>
        <img src="{{ $item->imageUrl() }}" class="h-32 rounded-lg border border-slate-200 mb-2">
        <input type="file" name="image" accept="image/*" class="form-file">
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.landing.field_caption') }}</label>
        <div class="grid grid-cols-2 gap-3">
            <input name="caption[ar]" value="{{ $item->caption['ar'] ?? '' }}" class="form-input" dir="rtl" placeholder="{{ __('admin.landing.ar') }}">
            <input name="caption[en]" value="{{ $item->caption['en'] ?? '' }}" class="form-input" dir="ltr" placeholder="{{ __('admin.landing.en') }}">
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 items-end">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.landing.sort_order') }}</label>
            <input type="number" name="sort_order" min="0" value="{{ $item->sort_order }}" class="form-input">
        </div>
        <label class="flex items-center gap-2 text-sm text-slate-700 pb-2">
            <input type="checkbox" name="is_active" value="1" class="form-checkbox" @checked($item->is_active)>
            {{ __('admin.landing.active') }}
        </label>
    </div>

    <button class="bg-slate-900 text-white text-sm px-4 py-2.5 rounded-lg font-medium hover:bg-slate-800 transition">{{ __('admin.landing.save') }}</button>
</form>
@endsection
