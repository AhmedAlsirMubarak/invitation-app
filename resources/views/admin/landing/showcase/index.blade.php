@extends('admin.layouts.app')
@section('title', __('admin.landing.showcase_title'))

@section('content')
<h1 class="text-2xl font-semibold text-slate-900 mb-4">{{ __('admin.landing.nav_showcase') }}</h1>
@include('admin.landing._subnav')

<div class="mb-4">
    <a href="{{ route('admin.landing.showcase.create') }}" class="bg-slate-900 text-white text-sm px-4 py-2 rounded-md hover:bg-slate-800">{{ __('admin.landing.add') }}</a>
</div>

<div class="grid grid-cols-2 md:grid-cols-4 gap-4">
    @forelse ($items as $item)
        <div class="bg-white border border-slate-200 rounded-lg overflow-hidden">
            <div class="aspect-3/4 bg-slate-100">
                <img src="{{ $item->imageUrl() }}" class="w-full h-full object-cover">
            </div>
            <div class="p-3">
                <p class="text-sm text-slate-900">{{ $item->t('caption') }}</p>
                <p class="text-xs {{ $item->is_active ? 'text-emerald-600' : 'text-slate-400' }} mb-2">{{ $item->is_active ? __('admin.landing.active') : __('admin.landing.inactive') }}</p>
                <div class="flex gap-2 text-xs">
                    <a href="{{ route('admin.landing.showcase.edit', $item) }}" class="text-slate-700 underline">{{ __('admin.landing.edit') }}</a>
                    <form action="{{ route('admin.landing.showcase.destroy', $item) }}" method="POST" onsubmit="return confirm('{{ __('admin.landing.delete_confirm') }}')">
                        @csrf @method('DELETE')
                        <button class="text-red-600 underline">{{ __('admin.landing.delete') }}</button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <p class="text-sm text-slate-500 col-span-full">—</p>
    @endforelse
</div>
@endsection
