@php
    $otherLocale = app()->getLocale() === 'ar' ? 'en' : 'ar';

    // Small inline icon set (Heroicons-style outline/solid paths) reused across sections.
    $icoArrow = 'M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3';
    $icoCheck = 'M4.5 12.75l6 6 9-13.5';
    $icoChevron = 'M19.5 8.25l-7.5 7.5-7.5-7.5';
    $icoChat = 'M8.625 9.75a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z';
    $icoBell = 'M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0';
    $icoStar = 'M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 21.539a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z';
    $icoPhone = 'M2.25 6.75c0 8.284 6.716 15 15 15h1.5a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z';

    // "Get started" should message the studio on WhatsApp when a contact
    // number is configured, instead of sending visitors to the login form.
    $heroWhatsappUrl = null;
    if ($settings['contact_phone'] ?? null) {
        $digits = preg_replace('/\D+/', '', $settings['contact_phone']);
        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        }
        if (str_starts_with($digits, '0')) {
            $digits = '966'.substr($digits, 1);
        } elseif (! str_starts_with($digits, '966') && strlen($digits) <= 10) {
            $digits = '966'.$digits;
        }
        $heroWhatsappMessage = app()->getLocale() === 'ar'
            ? 'مرحبًا، أرغب في معرفة المزيد عن خدمة الدعوات الإلكترونية.'
            : "Hello, I'd like to know more about the digital invitations service.";
        $heroWhatsappUrl = 'https://api.whatsapp.com/send?phone='.$digits.'&text='.rawurlencode($heroWhatsappMessage);
    }
    $heroCtaUrl = auth()->check() ? route('admin.dashboard') : ($heroWhatsappUrl ?? route('login'));
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('landing.brand') }}</title>
    <meta name="description" content="{{ $settings['hero_subtitle'] ?? '' }}">
    @vite(['resources/css/app.css'])
</head>
<body class="bg-white text-slate-900 antialiased">

<header class="sticky top-0 z-50 border-b border-slate-100 bg-white/80 backdrop-blur-md">
    <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
        <a href="{{ url('/') }}" class="flex items-center">
            <img src="{{ \App\Models\LandingSetting::logoUrl('logo') }}" alt="{{ __('landing.brand') }}" class="h-20 w-auto object-contain">
        </a>
        <div class="flex items-center gap-4 text-sm">
            <a href="{{ request()->fullUrlWithQuery(['lang' => $otherLocale]) }}" class="text-slate-500 hover:text-slate-900 transition">{{ __('landing.nav_language') }}</a>
            @auth
                <a href="{{ route('admin.dashboard') }}" class="bg-slate-900 text-white px-4 py-2 rounded-lg hover:bg-slate-800 transition font-medium">{{ __('landing.nav_dashboard') }}</a>
            @else
                <a href="{{ route('login') }}" class="bg-slate-900 text-white px-4 py-2 rounded-lg hover:bg-slate-800 transition font-medium">{{ __('landing.nav_login') }}</a>
            @endauth
        </div>
    </div>
</header>

{{-- Hero --}}
<section class="relative overflow-hidden bg-gradient-to-br from-ink-950 via-ink-900 to-ink-800 text-white">
    <div class="absolute inset-0 bg-dot-grid opacity-40"></div>
    <div class="absolute -top-24 -end-24 w-96 h-96 rounded-full bg-gold-500/20 blur-3xl"></div>
    <div class="absolute -bottom-32 -start-24 w-96 h-96 rounded-full bg-gold-500/10 blur-3xl"></div>

    <div class="max-w-6xl mx-auto px-6 py-20 md:py-28 relative grid md:grid-cols-2 gap-14 items-center">
        <div>
            <h1 class="font-display text-4xl md:text-5xl leading-tight mb-5">{{ $settings['hero_title'] ?? '' }}</h1>
            <p class="text-white/70 text-lg max-w-xl mb-8 leading-relaxed">{{ $settings['hero_subtitle'] ?? '' }}</p>
            <a href="{{ $heroCtaUrl }}"
               @if ($heroWhatsappUrl && ! auth()->check()) target="_blank" rel="noopener" @endif
               class="group inline-flex items-center gap-2 px-8 py-3.5 rounded-full font-medium shadow-lg shadow-gold-900/20 hover:shadow-xl hover:-translate-y-0.5 transition"
               style="background:#C79A3B;color:#16261F;">
                {{ $settings['hero_cta'] ?? '' }}
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4 rtl:rotate-180 group-hover:translate-x-0.5 rtl:group-hover:-translate-x-0.5 transition"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icoArrow }}"/></svg>
            </a>
            @if ($settings['trust_statement'] ?? null)
                <p class="inline-flex items-center gap-2 text-white/50 text-xs mt-8">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#D8B45C" stroke-width="2" class="w-4 h-4 shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icoCheck }}"/></svg>
                    {{ $settings['trust_statement'] }}
                </p>
            @endif
        </div>

        <div class="relative hidden md:block">
            <div class="relative rounded-3xl overflow-hidden shadow-2xl ring-1 ring-white/10 rotate-2 hover:rotate-0 transition duration-500 aspect-4/5">
                @if ($settings['hero_image'] ?? null)
                    <img src="{{ $settings['hero_image'] }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full bg-gradient-to-br from-ink-800 to-ink-950 flex items-center justify-center p-10">
                        <img src="{{ \App\Models\LandingSetting::logoUrl('logo_dark') }}" alt="{{ __('landing.brand') }}" class="w-full h-auto object-contain opacity-90">
                    </div>
                @endif
            </div>

            <div class="absolute -start-8 top-10 bg-white text-slate-900 rounded-2xl shadow-xl p-3 flex items-center gap-2.5 -rotate-6 hover:rotate-0 transition">
                <span class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icoChat }}"/></svg>
                </span>
                <span class="w-9 h-9 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icoBell }}"/></svg>
                </span>
            </div>

            <div class="absolute -end-6 bottom-10 bg-white text-slate-900 rounded-2xl shadow-xl px-4 py-3 flex items-center gap-2 rotate-3 hover:rotate-0 transition">
                <span class="w-8 h-8 rounded-lg bg-gold-500/15 text-gold-600 flex items-center justify-center shrink-0">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icoCheck }}"/></svg>
                </span>
                <span class="text-xs font-medium text-slate-500">RSVP</span>
            </div>
        </div>
    </div>
</section>

{{-- Core services --}}
@if ($coreFeatures->isNotEmpty())
<section class="max-w-6xl mx-auto px-6 py-20">
    <div class="text-center mb-14">
        <span class="block w-12 h-1 rounded-full bg-gold-500 mx-auto mb-4"></span>
        <h2 class="font-display text-3xl">{{ $settings['features_title'] ?? '' }}</h2>
    </div>
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($coreFeatures as $feature)
            <div class="bg-white border border-slate-100 rounded-2xl p-7 shadow-sm hover:shadow-xl hover:-translate-y-1 transition duration-300">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-5 text-2xl bg-gradient-to-br from-gold-400/15 to-gold-500/25 ring-1 ring-gold-500/10">{{ $feature->icon ?: '•' }}</div>
                <h3 class="font-semibold text-lg mb-2">{{ $feature->t('title') }}</h3>
                <p class="text-sm text-slate-500 leading-relaxed">{{ $feature->t('body') }}</p>
            </div>
        @endforeach
    </div>
</section>
@endif

{{-- Design showcase --}}
@if ($showcaseItems->isNotEmpty())
<section class="bg-slate-50 border-y border-slate-100">
    <div class="max-w-6xl mx-auto px-6 py-20">
        <div class="text-center mb-14">
            <span class="block w-12 h-1 rounded-full bg-gold-500 mx-auto mb-4"></span>
            <h2 class="font-display text-3xl">{{ $settings['showcase_title'] ?? '' }}</h2>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach ($showcaseItems as $item)
                <div class="group relative rounded-2xl overflow-hidden bg-white border border-slate-200 shadow-sm hover:shadow-xl transition duration-300">
                    <div class="aspect-3/4 bg-slate-100 overflow-hidden">
                        <img src="{{ $item->imageUrl() }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                    </div>
                    @if ($item->t('caption'))
                        <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/70 to-transparent pt-8 pb-3 px-3">
                            <p class="text-xs text-white text-center font-medium">{{ $item->t('caption') }}</p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Highlights --}}
@if ($highlightFeatures->isNotEmpty())
<section class="max-w-5xl mx-auto px-6 py-20">
    <div class="text-center mb-14">
        <span class="block w-12 h-1 rounded-full bg-gold-500 mx-auto mb-4"></span>
        <h2 class="font-display text-3xl">{{ $settings['highlights_title'] ?? '' }}</h2>
    </div>
    <div class="grid sm:grid-cols-3 gap-6">
        @foreach ($highlightFeatures as $feature)
            <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm hover:shadow-md transition text-center">
                <div class="w-12 h-12 mx-auto rounded-2xl flex items-center justify-center mb-4 text-xl bg-ink-900 text-white">{{ $feature->icon ?: '•' }}</div>
                <h3 class="font-semibold mb-2">{{ $feature->t('title') }}</h3>
                <p class="text-sm text-slate-500 leading-relaxed">{{ $feature->t('body') }}</p>
            </div>
        @endforeach
    </div>
</section>
@endif

{{-- How it works --}}
@if ($howItWorks->isNotEmpty())
<section class="bg-slate-50 border-y border-slate-100">
    <div class="max-w-5xl mx-auto px-6 py-20">
        <div class="text-center mb-16">
            <span class="block w-12 h-1 rounded-full bg-gold-500 mx-auto mb-4"></span>
            <h2 class="font-display text-3xl">{{ $settings['how_title'] ?? '' }}</h2>
        </div>
        <div class="relative grid sm:grid-cols-3 gap-10 text-center">
            <div class="hidden sm:block absolute top-6 inset-x-16 h-px bg-slate-300"></div>
            @foreach ($howItWorks as $i => $step)
                <div class="relative">
                    <div class="w-12 h-12 mx-auto rounded-full bg-gradient-to-br from-ink-900 to-ink-800 text-white flex items-center justify-center font-semibold mb-5 ring-8 ring-slate-50 relative z-10">{{ $i + 1 }}</div>
                    <h3 class="font-semibold mb-2">{{ $step->t('title') }}</h3>
                    <p class="text-sm text-slate-500 leading-relaxed">{{ $step->t('body') }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Testimonials --}}
@if ($testimonials->isNotEmpty())
<section class="max-w-6xl mx-auto px-6 py-20">
    <div class="text-center mb-14">
        <span class="block w-12 h-1 rounded-full bg-gold-500 mx-auto mb-4"></span>
        <h2 class="font-display text-3xl">{{ $settings['testimonials_title'] ?? '' }}</h2>
    </div>
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($testimonials as $testimonial)
            <div class="relative bg-white border border-slate-100 rounded-2xl p-7 shadow-sm hover:shadow-md transition overflow-hidden">
                <span class="font-display text-7xl leading-none text-gold-500/10 absolute top-2 {{ app()->getLocale() === 'ar' ? '-scale-x-100' : '' }} end-4 select-none">&rdquo;</span>
                <div class="flex gap-0.5 mb-3 relative">
                    @for ($i = 1; $i <= 5; $i++)
                        <svg viewBox="0 0 24 24" class="w-4 h-4 {{ $i <= $testimonial->rating ? 'text-gold-500' : 'text-slate-200' }}" fill="currentColor"><path d="{{ $icoStar }}"/></svg>
                    @endfor
                </div>
                <p class="text-sm text-slate-700 leading-relaxed mb-5 relative">"{{ $testimonial->t('quote') }}"</p>
                <div class="flex items-center gap-3 relative">
                    @if ($testimonial->avatarUrl())
                        <img src="{{ $testimonial->avatarUrl() }}" class="w-10 h-10 rounded-full object-cover ring-2 ring-gold-500/20">
                    @else
                        <span class="w-10 h-10 rounded-full bg-ink-900 text-white flex items-center justify-center text-sm font-semibold">{{ mb_substr($testimonial->author_name, 0, 1) }}</span>
                    @endif
                    <div>
                        <p class="text-sm font-medium text-slate-900">{{ $testimonial->author_name }}</p>
                        @if ($testimonial->t('event_category'))
                            <p class="text-xs text-slate-400">{{ $testimonial->t('event_category') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>
@endif

{{-- Packages --}}
@if ($packages->isNotEmpty())
<section class="bg-slate-50 border-y border-slate-100">
    <div class="max-w-5xl mx-auto px-6 py-20">
        <div class="text-center mb-14">
            <span class="block w-12 h-1 rounded-full bg-gold-500 mx-auto mb-4"></span>
            <h2 class="font-display text-3xl">{{ $settings['packages_title'] ?? '' }}</h2>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 items-start">
            @foreach ($packages as $package)
                <div class="relative bg-white rounded-2xl p-7 transition {{ $package->t('badge') ? 'ring-2 ring-gold-500 shadow-xl sm:-translate-y-2' : 'border border-slate-200 shadow-sm hover:shadow-md' }}">
                    @if ($package->t('badge'))
                        <span class="absolute -top-3 start-1/2 -translate-x-1/2 rtl:translate-x-1/2 bg-gold-500 text-ink-900 text-xs font-semibold px-3 py-1 rounded-full whitespace-nowrap">{{ $package->t('badge') }}</span>
                    @endif
                    <h3 class="font-display text-xl mb-1">{{ $package->t('name') }}</h3>
                    <p class="text-2xl font-display text-slate-900 mb-4">{{ $package->t('price_label') }}</p>
                    @if ($package->t('description'))
                        <p class="text-sm text-slate-600 mb-4">{{ $package->t('description') }}</p>
                    @endif
                    <ul class="text-sm text-slate-600 space-y-3 mb-7">
                        @foreach ($package->featureLines() as $line)
                            <li class="flex items-start gap-2.5">
                                <span class="w-5 h-5 rounded-full bg-gold-500/15 text-gold-600 flex items-center justify-center shrink-0 mt-0.5">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="w-3 h-3"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icoCheck }}"/></svg>
                                </span>
                                {{ $line }}
                            </li>
                        @endforeach
                    </ul>
                    <a href="{{ $package->cta_url ?: (auth()->check() ? route('admin.dashboard') : route('login')) }}"
                       class="block text-center text-sm px-4 py-3 rounded-xl font-medium transition {{ $package->t('badge') ? 'bg-gold-500 text-ink-900 hover:bg-gold-400' : 'bg-slate-900 text-white hover:bg-slate-800' }}">
                        {{ $package->t('cta_label') ?: __('landing.hero_cta') }}
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- FAQ --}}
@if ($faqs->isNotEmpty())
<section class="max-w-3xl mx-auto px-6 py-20">
    <div class="text-center mb-14">
        <span class="block w-12 h-1 rounded-full bg-gold-500 mx-auto mb-4"></span>
        <h2 class="font-display text-3xl">{{ $settings['faq_title'] ?? '' }}</h2>
    </div>
    <div class="space-y-3">
        @foreach ($faqs as $faq)
            <details class="group bg-white border border-slate-100 rounded-xl px-5 py-4 shadow-sm">
                <summary class="cursor-pointer list-none flex items-center justify-between gap-4 font-medium text-slate-900">
                    {{ $faq->t('question') }}
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4 text-slate-400 shrink-0 group-open:rotate-180 transition"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icoChevron }}"/></svg>
                </summary>
                <p class="text-sm text-slate-500 mt-3 leading-relaxed">{{ $faq->t('answer') }}</p>
            </details>
        @endforeach
    </div>
</section>
@endif

{{-- Contact --}}
<section class="relative overflow-hidden bg-gradient-to-br from-ink-950 via-ink-900 to-ink-800 text-white">
    <div class="absolute inset-0 bg-dot-grid opacity-30"></div>
    <div class="absolute -top-20 start-1/3 w-72 h-72 rounded-full bg-gold-500/15 blur-3xl"></div>
    <div class="max-w-3xl mx-auto px-6 py-20 text-center relative">
        <span class="w-14 h-14 rounded-2xl bg-white/10 flex items-center justify-center mx-auto mb-6">
            <svg viewBox="0 0 24 24" fill="none" stroke="#D8B45C" stroke-width="1.5" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icoPhone }}"/></svg>
        </span>
        <h2 class="font-display text-3xl mb-3">{{ $settings['contact_title'] ?? '' }}</h2>
        @if ($settings['contact_subtitle'] ?? null)
            <p class="text-white/70 mb-8 max-w-xl mx-auto leading-relaxed">{{ $settings['contact_subtitle'] }}</p>
        @endif
        @if ($settings['contact_phone'] ?? null)
            <a href="tel:{{ $settings['contact_phone'] }}" class="inline-flex items-center gap-2 px-7 py-3 rounded-full font-medium shadow-lg shadow-gold-900/20 hover:-translate-y-0.5 transition" style="background:#C79A3B;color:#16261F;">
                <svg viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path d="{{ $icoPhone }}"/></svg>
                {{ $settings['contact_phone'] }}
            </a>
        @else
            <a href="{{ route('login') }}" class="inline-block px-7 py-3 rounded-full font-medium shadow-lg shadow-gold-900/20 hover:-translate-y-0.5 transition" style="background:#C79A3B;color:#16261F;">{{ $settings['hero_cta'] ?? '' }}</a>
        @endif
    </div>
</section>

<footer class="border-t border-slate-100">
    <div class="max-w-6xl mx-auto px-6 py-8 flex items-center justify-between text-sm text-slate-400">
        <a href="{{ url('/') }}" class="flex items-center">
            <img src="{{ \App\Models\LandingSetting::logoUrl('logo') }}" alt="{{ __('landing.brand') }}" class="h-20 w-auto object-contain">
        </a>
        <div class="text-end">
            <p>&copy; {{ now()->year }} — {{ $settings['footer_rights'] ?? '' }}</p>
            <p class="text-xs text-slate-300 mt-0.5">{{ __('landing.developed_by') }} Ahmed Alsir</p>
        </div>
    </div>
</footer>

</body>
</html>
