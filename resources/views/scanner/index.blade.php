<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
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

    <div id="in-app-warning" class="hidden mb-4 rounded-md bg-amber-900/40 border border-amber-700 text-amber-200 text-xs px-3 py-2.5"></div>

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

    <div id="reader" class="rounded-lg overflow-hidden mb-3 min-h-[280px] bg-slate-900 flex items-center justify-center text-slate-500 text-sm text-center px-6"></div>

    <button id="start-scan-btn" class="w-full mb-4 py-3 rounded-md text-sm font-semibold bg-emerald-600 hover:bg-emerald-500 transition">
        {{ __('admin.scanner.start_scan') }}
    </button>

    <div id="result" class="rounded-md px-4 py-3 text-sm bg-slate-800 min-h-[52px] mb-4">
        {{ __('admin.scanner.placeholder') }}
    </div>

    <details class="text-sm">
        <summary class="cursor-pointer text-slate-400">{{ __('admin.scanner.manual_entry_toggle') }}</summary>
        <form id="manual-form" class="flex gap-2 mt-3">
            <input type="text" id="manual-token" placeholder="{{ __('admin.scanner.manual_entry_placeholder') }}" dir="ltr" class="form-input flex-1 bg-slate-900 border-slate-700 text-white placeholder:text-slate-500">
            <button type="submit" class="px-4 py-2 rounded-md text-sm font-medium bg-slate-700 hover:bg-slate-600 transition">{{ __('admin.scanner.manual_submit') }}</button>
        </form>
    </details>
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
    const startScanText = @json(__('admin.scanner.start_scan'));
    const startingText = @json(__('admin.scanner.starting'));
    const retryText = @json(__('admin.scanner.retry'));
    const inAppWarningText = @json(__('admin.scanner.in_app_browser_warning'));
    const readerBox = document.getElementById('reader');
    const startBtn = document.getElementById('start-scan-btn');
    const manualForm = document.getElementById('manual-form');
    const manualInput = document.getElementById('manual-token');

    function setMode(newMode) {
        mode = newMode;
        btnIn.classList.toggle('bg-emerald-600', mode === 'check_in');
        btnIn.classList.toggle('bg-slate-800', mode !== 'check_in');
        btnOut.classList.toggle('bg-emerald-600', mode === 'check_out');
        btnOut.classList.toggle('bg-slate-800', mode !== 'check_out');
    }
    btnIn.onclick = () => setMode('check_in');
    btnOut.onclick = () => setMode('check_out');

    // Links opened from inside WhatsApp/Instagram/Facebook/TikTok's built-in
    // browser (very likely for this exact link, since it's normally shared
    // over WhatsApp) often block or cripple camera access — flag it early
    // so door staff know to reopen in their real browser instead of just
    // seeing a mysterious camera failure.
    (function checkInAppBrowser() {
        const ua = navigator.userAgent || '';
        const isInApp = /FBAN|FBAV|Instagram|Line\/|MicroMessenger|TikTok|; ?wv\)/i.test(ua)
            || (ua.includes('WhatsApp'));
        if (isInApp) {
            const el = document.getElementById('in-app-warning');
            el.textContent = inAppWarningText;
            el.classList.remove('hidden');
        }
    })();

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
            resultBox.className = 'rounded-md px-4 py-3 text-sm min-h-[52px] mb-4 ' +
                (data.status === 'ok' ? 'bg-emerald-700' : data.status === 'duplicate' ? 'bg-amber-700' : 'bg-red-700');
            if (data.stats) {
                document.getElementById('in-venue-count').textContent = data.stats.in_venue;
                document.getElementById('checked-in-count').textContent = data.stats.checked_in;
            }
        } catch (e) {
            resultBox.textContent = networkErrorText;
            resultBox.className = 'rounded-md px-4 py-3 text-sm min-h-[52px] mb-4 bg-red-700';
        }
        setTimeout(() => { busy = false; }, 1500);
    }

    manualForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const value = manualInput.value.trim();
        if (value) {
            onScanSuccess(value);
            manualInput.value = '';
        }
    });

    if (!window.isSecureContext) {
        readerBox.textContent = cameraInsecureText;
        startBtn.remove();
    } else if (typeof Html5Qrcode === 'undefined') {
        // The scanner script is loaded from a CDN — if it's blocked (some
        // in-app browsers and corporate/school wifi filters block third-
        // party script domains), fail visibly instead of a dead button.
        readerBox.textContent = cameraErrorText;
        startBtn.remove();
    } else {
        const scanner = new Html5Qrcode('reader');

        function qrboxSize() {
            const width = readerBox.clientWidth || 280;
            return Math.max(180, Math.min(260, Math.floor(width * 0.7)));
        }

        function describeError(err) {
            const message = String(err?.name || err || '');
            if (message.includes('NotAllowedError') || message.includes('Permission')) {
                return cameraDeniedText;
            }
            if (message.includes('NotFoundError') || message.includes('OverconstrainedError')) {
                return cameraNotFoundText;
            }
            return cameraErrorText;
        }

        async function startScanning() {
            startBtn.disabled = true;
            startBtn.textContent = startingText;
            readerBox.textContent = '';

            const config = { fps: 10, qrbox: qrboxSize() };

            try {
                await scanner.start({ facingMode: 'environment' }, config, onScanSuccess);
                startBtn.remove();
                return;
            } catch (firstError) {
                // Some devices (older Android WebViews, tablets, laptops
                // pretending to be mobile) reject an exact facingMode
                // constraint outright. Fall back to enumerating cameras
                // and picking the last one — on phones with multiple rear
                // cameras that's typically the main rear sensor, and it's
                // at least *a* working camera rather than nothing.
                try {
                    const cameras = await Html5Qrcode.getCameras();
                    if (cameras && cameras.length) {
                        await scanner.start(cameras[cameras.length - 1].id, config, onScanSuccess);
                        startBtn.remove();
                        return;
                    }
                } catch (secondError) {
                    // fall through to error display below
                }
                readerBox.textContent = describeError(firstError);
                startBtn.disabled = false;
                startBtn.textContent = retryText;
            }
        }

        startBtn.addEventListener('click', startScanning);
    }
</script>
</body>
</html>
