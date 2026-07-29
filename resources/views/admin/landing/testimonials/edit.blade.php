@extends('admin.layouts.app')
@section('title', __('admin.landing.edit'))

@section('content')
<h1 class="text-2xl font-semibold text-slate-900 mb-4">{{ __('admin.landing.edit') }}</h1>
@include('admin.landing._subnav')

<form action="{{ route('admin.landing.testimonials.update', $testimonial) }}" method="POST" enctype="multipart/form-data" class="bg-white border border-slate-200 rounded-lg p-6 max-w-xl space-y-5">
    @csrf @method('PUT')
    @include('admin.landing.testimonials._form')
</form>
@endsection
