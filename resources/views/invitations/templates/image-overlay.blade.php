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
    @include('partials.favicon')
    @include('partials.noindex')
    <title>{{ $event->displayTitle() }}</title>
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
{{-- Image and content are stacked in the same CSS Grid cell so the card's
     height is always whichever is taller. When content is short, the cell
     matches the image's natural height and it renders uncropped. When
     content is long (e.g. the QR entry card pushes past the image's
     height), the cell grows to fit the content instead of clipping it —
     the image then stretches via object-fit:cover to keep covering the
     taller box, cropping its sides rather than the guest's text getting
     cut off. --}}
<div class="max-w-lg w-full relative rounded-2xl overflow-hidden shadow-lg grid">
    @if ($backgroundImage)
        <img src="{{ $backgroundImage }}" alt="" class="w-full h-full object-cover" style="grid-area: 1 / 1;">
        <div class="relative flex items-center justify-center px-5 py-10" style="grid-area: 1 / 1;">
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
