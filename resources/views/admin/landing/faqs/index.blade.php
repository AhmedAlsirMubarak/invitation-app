@extends('admin.layouts.app')
@section('title', __('admin.landing.faqs_title'))

@section('content')
<h1 class="text-2xl font-semibold text-slate-900 mb-4">{{ __('admin.landing.nav_faqs') }}</h1>
@include('admin.landing._subnav')

<div class="mb-4">
    <a href="{{ route('admin.landing.faqs.create') }}" class="bg-slate-900 text-white text-sm px-4 py-2 rounded-md hover:bg-slate-800">{{ __('admin.landing.add') }}</a>
</div>

<div class="bg-white border border-slate-200 rounded-lg divide-y divide-slate-100">
    @forelse ($faqs as $faq)
        <div class="flex items-center justify-between px-5 py-3 text-sm">
            <div>
                <p class="text-slate-900 font-medium">{{ $faq->t('question') }}</p>
                <p class="text-slate-500 text-xs">{{ \Illuminate\Support\Str::limit($faq->t('answer'), 100) }}</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs {{ $faq->is_active ? 'text-emerald-600' : 'text-slate-400' }}">{{ $faq->is_active ? __('admin.landing.active') : __('admin.landing.inactive') }}</span>
                <a href="{{ route('admin.landing.faqs.edit', $faq) }}" class="text-slate-700 underline text-xs">{{ __('admin.landing.edit') }}</a>
                <form action="{{ route('admin.landing.faqs.destroy', $faq) }}" method="POST" onsubmit="return confirm('{{ __('admin.landing.delete_confirm') }}')">
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
