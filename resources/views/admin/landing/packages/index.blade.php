@extends('admin.layouts.app')
@section('title', __('admin.landing.packages_title'))

@section('content')
<h1 class="text-2xl font-semibold text-slate-900 mb-4">{{ __('admin.landing.nav_packages') }}</h1>
@include('admin.landing._subnav')

<div class="mb-4">
    <a href="{{ route('admin.landing.packages.create') }}" class="bg-slate-900 text-white text-sm px-4 py-2 rounded-md hover:bg-slate-800">{{ __('admin.landing.add') }}</a>
</div>

<div class="grid md:grid-cols-3 gap-4">
    @forelse ($packages as $package)
        <div class="bg-white border border-slate-200 rounded-lg p-5">
            <p class="font-semibold text-slate-900">{{ $package->t('name') }}</p>
            <p class="text-sm text-slate-500 mb-2">{{ $package->t('price_label') }}</p>
            <ul class="text-xs text-slate-600 space-y-1 mb-3">
                @foreach ($package->featureLines() as $line)
                    <li>• {{ $line }}</li>
                @endforeach
            </ul>
            <p class="text-xs {{ $package->is_active ? 'text-emerald-600' : 'text-slate-400' }} mb-2">{{ $package->is_active ? __('admin.landing.active') : __('admin.landing.inactive') }}</p>
            <div class="flex gap-2 text-xs">
                <a href="{{ route('admin.landing.packages.edit', $package) }}" class="text-slate-700 underline">{{ __('admin.landing.edit') }}</a>
                <form action="{{ route('admin.landing.packages.destroy', $package) }}" method="POST" onsubmit="return confirm('{{ __('admin.landing.delete_confirm') }}')">
                    @csrf @method('DELETE')
                    <button class="text-red-600 underline">{{ __('admin.landing.delete') }}</button>
                </form>
            </div>
        </div>
    @empty
        <p class="text-sm text-slate-500 col-span-full">—</p>
    @endforelse
</div>
@endsection
