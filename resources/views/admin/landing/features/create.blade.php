@extends('admin.layouts.app')
@section('title', __('admin.landing.add'))

@section('content')
@php($feature = null)
<h1 class="text-2xl font-semibold text-slate-900 mb-4">{{ __('admin.landing.add') }}</h1>
@include('admin.landing._subnav')

<form action="{{ route('admin.landing.features.store') }}" method="POST" class="bg-white border border-slate-200 rounded-lg p-6 max-w-xl space-y-5">
    @csrf
    @include('admin.landing.features._form')
</form>
@endsection
