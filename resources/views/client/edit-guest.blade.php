@php
    $otherLocale = app()->getLocale() === 'ar' ? 'en' : 'ar';
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('client.edit_guest_title') }} · {{ $guest->name }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-slate-50 text-slate-800 antialiased">
<div class="max-w-2xl mx-auto px-4 py-8">

    <div class="flex items-center justify-between mb-6">
        <div>
            <a href="{{ route('client.show', $token) }}" class="text-xs text-slate-500 hover:text-slate-700 underline">{{ __('client.back_to_guests') }}</a>
            <h1 class="text-2xl font-semibold text-slate-900 mt-1">{{ __('client.edit_guest_title') }}</h1>
        </div>
        <a href="{{ request()->fullUrlWithQuery(['lang' => $otherLocale]) }}" class="text-xs text-slate-400 hover:text-slate-700">{{ __('client.language') }}</a>
    </div>

    @if ($errors->any())
        <div class="mb-6 rounded-md bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('client.guests.update', [$token, $guest]) }}" method="POST" class="bg-white border border-slate-200 rounded-lg p-6 space-y-5">
        @csrf @method('PUT')

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('client.field_name') }}</label>
            <input name="name" value="{{ old('name', $guest->name) }}" required class="form-input">
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('client.field_name_en') }}</label>
            <input name="name_en" value="{{ old('name_en', $guest->name_en) }}" dir="ltr" class="form-input">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('client.field_phone') }}</label>
                <div class="grid grid-cols-3 gap-2">
                    <select name="country_code" class="form-select">
                        @foreach (\App\Support\PhoneNumber::COUNTRIES as $code => $label)
                            <option value="{{ $code }}" @selected($code === $phone['country_code'])>{{ $label }}</option>
                        @endforeach
                    </select>
                    <input name="phone" value="{{ old('phone', $phone['local']) }}" class="col-span-2 form-input">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('client.field_email') }}</label>
                <input name="email" value="{{ old('email', $guest->email) }}" class="form-input">
            </div>
        </div>

        @if ($event->showsCompanions() || $event->showsTableNumber())
            <div class="grid grid-cols-2 gap-4">
                @if ($event->showsCompanions())
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ $event->companionsLabel() }}</label>
                        <input name="companions_count" type="number" min="0" value="{{ old('companions_count', $guest->companions_count) }}" class="form-input">
                    </div>
                @endif
                @if ($event->showsTableNumber())
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('client.field_table') }}</label>
                        <input name="table_number" value="{{ old('table_number', $guest->table_number) }}" class="form-input">
                    </div>
                @endif
            </div>
        @endif

        <button class="bg-slate-900 text-white text-sm px-4 py-2.5 rounded-lg font-medium hover:bg-slate-800 transition">{{ __('client.save_changes') }}</button>
    </form>
</div>
</body>
</html>
