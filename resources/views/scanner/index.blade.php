<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @include('partials.favicon')
    @include('partials.noindex')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('admin.scanner.title') }} · {{ $event->title }}</title>
    @vite(['resources/css/app.css'])
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
</head>
<body class="bg-slate-950 text-white min-h-screen">
<div class="max-w-md mx-auto px-4 py-6">
    <img src="{{ \App\Models\LandingSetting::logoUrl('logo_dark') }}" alt="{{ __('admin.auth.studio_name') }}" class="h-20 justify-center  w-auto object-contain ">
    <h1 class="text-lg font-semibold mb-1">{{ $event->title }}</h1>
    <p class="text-sm text-slate-400 mb-4">{{ __('admin.scanner.subtitle') }}</p>

    <div class="flex gap-2 mb-4 text-center">
        <div class="flex-1 bg-slate-900 rounded-md py-2">
            <p id="in-venue-count" class="text-xl font-semibold">{{ $stats['in_venue'] }}</p>
            <p class="text-xs text-slate-400">{{ __('admin.scanner.in_venue') }}</p>
        </div>
        <div class="flex-1 bg-slate-900 rounded-md py-2">
            <p id="checked-in-count" class="text-xl font-semibold">{{ $stats['checked_in'] }}</p>
            <p class="text-xs text-slate-400">{{ __('admin.scanner.total_checked_in') }}</p>
        </div>
    </div>

    <div class="flex gap-2 mb-4">
        <button id="mode-in" class="flex-1 py-2 rounded-md text-sm font-medium bg-emerald-600">{{ __('admin.scanner.check_in') }}</button>
        <button id="mode-out" class="flex-1 py-2 rounded-md text-sm font-medium bg-slate-800">{{ __('admin.scanner.check_out') }}</button>
    </div>

    <div id="reader" class="rounded-lg overflow-hidden mb-4 min-h-[280px] bg-slate-900 flex items-center justify-center text-slate-500 text-sm text-center px-6"></div>

    <div id="result" class="rounded-md px-4 py-3 text-sm bg-slate-800 min-h-[52px]">
        {{ __('admin.scanner.placeholder') }}
    </div>
</div>

<script>
    let mode = 'check_in';
    const resultBox = document.getElementById('result');
    const btnIn = document.getElementById('mode-in');
    const btnOut = document.getElementById('mode-out');
    const networkErrorText = @json(__('admin.scanner.network_error'));
    const checkinUrl = @json($checkinUrl);
    const cameraInsecureText = @json(__('admin.scanner.camera_insecure'));
    const cameraDeniedText = @json(__('admin.scanner.camera_denied'));
    const cameraNotFoundText = @json(__('admin.scanner.camera_not_found'));
    const cameraErrorText = @json(__('admin.scanner.camera_error'));
    const readerBox = document.getElementById('reader');

    function setMode(newMode) {
        mode = newMode;
        btnIn.classList.toggle('bg-emerald-600', mode === 'check_in');
        btnIn.classList.toggle('bg-slate-800', mode !== 'check_in');
        btnOut.classList.toggle('bg-emerald-600', mode === 'check_out');
        btnOut.classList.toggle('bg-slate-800', mode !== 'check_out');
    }
    btnIn.onclick = () => setMode('check_in');
    btnOut.onclick = () => setMode('check_out');

    let busy = false;
    async function onScanSuccess(decodedText) {
        if (busy) return;
        busy = true;
        try {
            const response = await fetch(checkinUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content ?? '',
                },
                body: JSON.stringify({ token: decodedText, action: mode }),
            });
            const data = await response.json();
            resultBox.textContent = data.message;
            resultBox.className = 'rounded-md px-4 py-3 text-sm min-h-[52px] ' +
                (data.status === 'ok' ? 'bg-emerald-700' : data.status === 'duplicate' ? 'bg-amber-700' : 'bg-red-700');
            if (data.stats) {
                document.getElementById('in-venue-count').textContent = data.stats.in_venue;
                document.getElementById('checked-in-count').textContent = data.stats.checked_in;
            }
        } catch (e) {
            resultBox.textContent = networkErrorText;
            resultBox.className = 'rounded-md px-4 py-3 text-sm min-h-[52px] bg-red-700';
        }
        setTimeout(() => { busy = false; }, 1500);
    }

    if (!window.isSecureContext) {
        readerBox.textContent = cameraInsecureText;
    } else {
        const scanner = new Html5Qrcode('reader');
        scanner.start(
            { facingMode: 'environment' },
            { fps: 10, qrbox: 240 },
            onScanSuccess
        ).catch((err) => {
            const message = String(err?.name || err || '');
            if (message.includes('NotAllowedError') || message.includes('Permission')) {
                readerBox.textContent = cameraDeniedText;
            } else if (message.includes('NotFoundError') || message.includes('OverconstrainedError')) {
                readerBox.textContent = cameraNotFoundText;
            } else {
                readerBox.textContent = cameraErrorText;
            }
        });
    }
</script>
</body>
</html>
