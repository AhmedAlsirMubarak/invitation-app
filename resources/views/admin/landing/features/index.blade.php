@extends('admin.layouts.app')
@section('title', __('admin.landing.features_title'))

@section('content')
<h1 class="text-2xl font-semibold text-slate-900 mb-4">{{ __('admin.landing.nav_features') }}</h1>
@include('admin.landing._subnav')

<div class="flex gap-1 mb-6 text-sm">
    @foreach (['core' => __('admin.landing.features_group_core'), 'highlight' => __('admin.landing.features_group_highlight'), 'how_it_works' => __('admin.landing.features_group_how_it_works')] as $key => $label)
        <a href="{{ route('admin.landing.features.index', ['group' => $key]) }}" class="px-3 py-1.5 rounded-md {{ $group === $key ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">{{ $label }}</a>
    @endforeach
</div>

<div class="mb-4">
    <a href="{{ route('admin.landing.features.create', ['group' => $group]) }}" class="bg-slate-900 text-white text-sm px-4 py-2 rounded-md hover:bg-slate-800">{{ __('admin.landing.add') }}</a>
</div>

<div class="bg-white border border-slate-200 rounded-lg divide-y divide-slate-100">
    @forelse ($features as $feature)
        <div class="flex items-center justify-between px-5 py-3 text-sm">
            <div class="flex items-center gap-3">
                @if ($feature->icon)
                    <span class="text-xl">{{ $feature->icon }}</span>
                @endif
                <div>
                    <p class="text-slate-900 font-medium">{{ $feature->t('title') }}</p>
                    <p class="text-slate-500 text-xs">{{ $feature->t('body') }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs {{ $feature->is_active ? 'text-emerald-600' : 'text-slate-400' }}">{{ $feature->is_active ? __('admin.landing.active') : __('admin.landing.inactive') }}</span>
                <a href="{{ route('admin.landing.features.edit', $feature) }}" class="text-slate-700 underline text-xs">{{ __('admin.landing.edit') }}</a>
                <form action="{{ route('admin.landing.features.destroy', $feature) }}" method="POST" onsubmit="return confirm('{{ __('admin.landing.delete_confirm') }}')">
                    @csrf @method('DELETE')
                    <button class="text-red-600 underline text-xs">{{ __('admin.landing.delete') }}</button>
                </form>
            </div>
        </div>
    @empty
        <div class="px-5 py-6 text-center text-slate-500 text-sm">—</div>
    @endforelse
</div>
@endsection
