@php
    $locale = app()->getLocale();
    $dir = $locale === 'ar' ? 'rtl' : 'ltr';
    $otherLocale = $locale === 'ar' ? 'en' : 'ar';
    $backgroundImage = $event->template?->backgroundImageUrl();
    $textColor = $design['text_color'] ?? '#1F2937';
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $dir }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $event->title }}</title>
    @vite(['resources/css/app.css'])
    <style>
        :root {
            --primary: {{ $design['primary_color'] ?? '#16261F' }};
            --accent: {{ $design['accent_color'] ?? '#C79A3B' }};
            --text: {{ $textColor }};
        }
        body { background: var(--primary); }
        .font-display { font-family: 'Playfair Display', serif; }
        .accent-text { color: var(--accent); }
        .accent-border { border-color: var(--accent); }
        .overlay-panel {
            color: var(--text);
        }
    </style>
</head>
<body class="min-h-screen antialiased">
<div class="min-h-screen flex items-center justify-center px-4 py-10">
{{-- The uploaded image keeps its own natural aspect ratio (width:100%,
     height:auto) instead of being CSS-cropped as a "cover" background —
     that's what let tall card designs get scaled against the whole
     scrollable page and show only a blank slice. The overlay content is
     absolutely centered over the full image instead. --}}
<div class="max-w-lg w-full relative rounded-2xl overflow-hidden shadow-lg">
    @if ($backgroundImage)
        <img src="{{ $backgroundImage }}" alt="" class="w-full h-auto block">
        <div class="absolute inset-0 flex items-center justify-center px-5 py-6">
            <div class="overlay-panel rounded-2xl px-5 py-5 text-center w-full">
                @include('invitations.templates._image-overlay-content')
            </div>
        </div>
    @else
        <div class="overlay-panel px-6 py-10 text-center">
            @include('invitations.templates._image-overlay-content')
        </div>
    @endif
</div>
</div>
</body>
</html>
