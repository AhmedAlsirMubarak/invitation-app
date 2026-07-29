@extends('admin.layouts.app')
@section('title', __('admin.landing.testimonials_title'))

@section('content')
<h1 class="text-2xl font-semibold text-slate-900 mb-4">{{ __('admin.landing.nav_testimonials') }}</h1>
@include('admin.landing._subnav')

<div class="mb-4">
    <a href="{{ route('admin.landing.testimonials.create') }}" class="bg-slate-900 text-white text-sm px-4 py-2 rounded-md hover:bg-slate-800">{{ __('admin.landing.add') }}</a>
</div>

@if ($testimonials->isEmpty())
    <div class="bg-white border border-dashed border-slate-300 rounded-lg p-8 text-center text-slate-500 text-sm">
        {{ __('admin.landing.testimonials_empty') }}
    </div>
@else
    <div class="bg-white border border-slate-200 rounded-lg divide-y divide-slate-100">
        @foreach ($testimonials as $testimonial)
            <div class="flex items-center justify-between px-5 py-3 text-sm">
                <div class="flex items-center gap-3">
                    @if ($testimonial->avatarUrl())
                        <img src="{{ $testimonial->avatarUrl() }}" class="w-8 h-8 rounded-full object-cover">
                    @endif
                    <div>
                        <p class="text-slate-900 font-medium">{{ $testimonial->author_name }} · {{ str_repeat('★', $testimonial->rating) }}</p>
                        <p class="text-slate-500 text-xs">{{ $testimonial->t('quote') }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs {{ $testimonial->is_active ? 'text-emerald-600' : 'text-slate-400' }}">{{ $testimonial->is_active ? __('admin.landing.active') : __('admin.landing.inactive') }}</span>
                    <a href="{{ route('admin.landing.testimonials.edit', $testimonial) }}" class="text-slate-700 underline text-xs">{{ __('admin.landing.edit') }}</a>
                    <form action="{{ route('admin.landing.testimonials.destroy', $testimonial) }}" method="POST" onsubmit="return confirm('{{ __('admin.landing.delete_confirm') }}')">
                        @csrf @method('DELETE')
                        <button class="text-red-600 underline text-xs">{{ __('admin.landing.delete') }}</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
