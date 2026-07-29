@extends('admin.layouts.app')
@section('title', __('admin.templates.index_title'))

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-semibold text-slate-900">{{ __('admin.templates.index_title') }}</h1>
        <p class="text-sm text-slate-500">{{ __('admin.templates.index_subtitle') }}</p>
    </div>
    <a href="{{ route('admin.templates.create') }}" class="bg-slate-900 text-white text-sm px-4 py-2 rounded-md hover:bg-slate-800">{{ __('admin.templates.add_template') }}</a>
</div>

@forelse ($templates as $category => $group)
    <div class="mb-8">
        <h2 class="text-sm font-semibold text-slate-500 uppercase tracking-wide mb-3">{{ __('admin.event_types.'.$category) }}</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach ($group as $template)
                <div class="bg-white border border-slate-200 rounded-lg overflow-hidden">
                    <div class="aspect-[3/4] bg-slate-100 flex items-center justify-center">
                        @if ($template->thumbnail_path)
                            <img src="{{ Storage::disk('public')->url($template->thumbnail_path) }}" class="w-full h-full object-cover" alt="{{ $template->name }}">
                        @elseif ($template->backgroundImageUrl())
                            <img src="{{ $template->backgroundImageUrl() }}" class="w-full h-full object-cover" alt="{{ $template->name }}">
                        @else
                            <span class="text-xs text-slate-400">{{ __('admin.templates.no_preview') }}</span>
                        @endif
                    </div>
                    <div class="p-3">
                        <p class="font-medium text-sm text-slate-900">{{ $template->name }}</p>
                        <p class="text-xs text-slate-500 mb-2">{{ $template->is_active ? __('admin.templates.status_active') : __('admin.templates.status_hidden') }}</p>
                        <div class="flex gap-2 text-xs">
                            <a href="{{ route('admin.templates.edit', $template) }}" class="text-slate-700 underline">{{ __('admin.templates.edit') }}</a>
                            <form action="{{ route('admin.templates.destroy', $template) }}" method="POST" onsubmit="return confirm('{{ __('admin.templates.delete_confirm') }}')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 underline">{{ __('admin.templates.delete') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@empty
    <div class="bg-white border border-dashed border-slate-300 rounded-lg p-8 text-center text-slate-500">
        {{ __('admin.templates.empty') }} <a href="{{ route('admin.templates.create') }}" class="text-slate-900 underline">{{ __('admin.templates.empty_cta') }}</a>.
    </div>
@endforelse
@endsection
