@extends('admin.layouts.app')
@section('title', __('admin.guests.edit_title'))

@section('content')
<h1 class="text-2xl font-semibold text-slate-900 mb-6">{{ __('admin.guests.edit_title') }}</h1>

<form action="{{ route('admin.events.guests.update', [$event, $guest]) }}" method="POST" class="bg-white border border-slate-200 rounded-lg p-6 max-w-xl space-y-5">
    @csrf @method('PUT')
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.guests.field_name') }}</label>
        <input name="name" value="{{ $guest->name }}" required class="form-input">
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.guests.field_phone') }}</label>
            <div class="grid grid-cols-3 gap-2">
                <select name="country_code" class="form-select">
                    @foreach (\App\Support\PhoneNumber::COUNTRIES as $code => $label)
                        <option value="{{ $code }}" @selected($code === $phone['country_code'])>{{ $label }}</option>
                    @endforeach
                </select>
                <input name="phone" value="{{ $phone['local'] }}" class="col-span-2 form-input">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.guests.field_email') }}</label>
            <input name="email" value="{{ $guest->email }}" class="form-input">
        </div>
    </div>

    @if ($event->showsCompanions() || $event->showsTableNumber())
        <div class="grid grid-cols-2 gap-4">
            @if ($event->showsCompanions())
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ $event->companionsLabel() }}</label>
                    <input name="companions_count" type="number" min="0" value="{{ $guest->companions_count }}" class="form-input">
                </div>
            @endif
            @if ($event->showsTableNumber())
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.guests.field_table') }}</label>
                    <input name="table_number" value="{{ $guest->table_number }}" class="form-input">
                </div>
            @endif
        </div>
    @endif

    <button class="bg-slate-900 text-white text-sm px-4 py-2.5 rounded-lg font-medium hover:bg-slate-800 transition">{{ __('admin.guests.edit_submit') }}</button>
</form>
@endsection
