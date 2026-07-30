<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\GuestController;
use App\Http\Controllers\Admin\Landing\FaqController;
use App\Http\Controllers\Admin\Landing\FeatureController;
use App\Http\Controllers\Admin\Landing\PackageController;
use App\Http\Controllers\Admin\Landing\SettingsController;
use App\Http\Controllers\Admin\Landing\ShowcaseController;
use App\Http\Controllers\Admin\Landing\TestimonialController;
use App\Http\Controllers\Admin\ReminderController;
use App\Http\Controllers\Admin\ScannerController;
use App\Http\Controllers\Admin\TemplateController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ClientPortalController;
use App\Http\Controllers\GuestInvitationController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\ScannerAccessController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'index'])->name('landing');

// Dynamic (not static public files) so both always reflect the real
// production domain (APP_URL) instead of whatever was hardcoded at build
// time. The landing page is the only real public/indexable route — every
// admin/client/guest/scanner route is unguessable-token-scoped or behind
// auth and explicitly disallowed here.
Route::get('/robots.txt', function () {
    $lines = [
        'User-agent: *',
        'Disallow: /admin',
        'Disallow: /login',
        'Disallow: /client',
        'Disallow: /scan',
        'Disallow: /i',
        'Allow: /',
        '',
        'Sitemap: '.url('/sitemap.xml'),
    ];

    return response(implode("\n", $lines))->header('Content-Type', 'text/plain');
})->name('robots');

Route::get('/sitemap.xml', function () {
    return response(view('sitemap')->render())->header('Content-Type', 'application/xml');
})->name('sitemap');

// --- Public guest-facing routes (no login) ---
Route::get('/i/{token}', [GuestInvitationController::class, 'show'])->name('invitations.show');
Route::post('/i/{token}/rsvp', [GuestInvitationController::class, 'rsvp'])->name('invitations.rsvp');

// --- Public client self-service portal (no login, scoped by unguessable token) ---
Route::prefix('client')->name('client.')->group(function () {
    Route::get('{token}', [ClientPortalController::class, 'show'])->name('show');
    Route::get('{token}/edit', [ClientPortalController::class, 'editEvent'])->name('edit');
    Route::put('{token}', [ClientPortalController::class, 'updateEvent'])->name('update');
    Route::post('{token}/guests', [ClientPortalController::class, 'storeGuest'])->name('guests.store');
    Route::post('{token}/guests/import', [ClientPortalController::class, 'importGuests'])->name('guests.import');
    Route::get('{token}/guests/{guest}/edit', [ClientPortalController::class, 'editGuest'])->name('guests.edit');
    Route::put('{token}/guests/{guest}', [ClientPortalController::class, 'updateGuest'])->name('guests.update');
    Route::delete('{token}/guests/{guest}', [ClientPortalController::class, 'destroyGuest'])->name('guests.destroy');
    Route::get('{token}/guests/{guest}/invite', [ClientPortalController::class, 'sendInvite'])->name('guests.invite');
    Route::get('{token}/guests-bulk-invite', [ClientPortalController::class, 'bulkInvite'])->name('guests.bulk-invite');
});

// --- Public door-staff scanner (no login, scoped by unguessable token) ---
Route::prefix('scan')->name('scan.')->group(function () {
    Route::get('{token}', [ScannerAccessController::class, 'show'])->name('show');
    Route::post('{token}/checkin', [ScannerAccessController::class, 'checkin'])->name('checkin');
});

// --- Auth ---
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});
Route::post('/logout', [LoginController::class, 'destroy'])->middleware(['auth'])->name('logout');

// --- Admin routes (auth required) ---
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('templates', TemplateController::class)->except(['show']);

    Route::resource('events', EventController::class);
    Route::post('events/{event}/regenerate-client-link', [EventController::class, 'regenerateClientLink'])->name('events.regenerate-client-link');
    Route::post('events/{event}/regenerate-scanner-link', [EventController::class, 'regenerateScannerLink'])->name('events.regenerate-scanner-link');

    Route::get('events/{event}/guests', [GuestController::class, 'index'])->name('events.guests.index');
    Route::post('events/{event}/guests', [GuestController::class, 'store'])->name('events.guests.store');
    Route::post('events/{event}/guests/import', [GuestController::class, 'import'])->name('events.guests.import');
    Route::get('events/{event}/guests/{guest}/edit', [GuestController::class, 'edit'])->name('events.guests.edit');
    Route::put('events/{event}/guests/{guest}', [GuestController::class, 'update'])->name('events.guests.update');
    Route::delete('events/{event}/guests/{guest}', [GuestController::class, 'destroy'])->name('events.guests.destroy');
    Route::get('events/{event}/guests/{guest}/invite', [GuestController::class, 'sendInvite'])->name('events.guests.invite');
    Route::get('events/{event}/guests/{guest}/remind', [GuestController::class, 'sendReminder'])->name('events.guests.remind');
    Route::get('events/{event}/guests-bulk-invite', [GuestController::class, 'bulkInvite'])->name('events.guests.bulk-invite');

    Route::get('events/{event}/scanner', [ScannerController::class, 'index'])->name('events.scanner');

    Route::get('reminders', [ReminderController::class, 'index'])->name('reminders.index');

    Route::prefix('landing')->name('landing.')->group(function () {
        Route::get('/', [SettingsController::class, 'edit'])->name('settings.edit');
        Route::put('/', [SettingsController::class, 'update'])->name('settings.update');

        Route::resource('features', FeatureController::class)->except(['show']);
        Route::resource('showcase', ShowcaseController::class)->except(['show']);
        Route::resource('testimonials', TestimonialController::class)->except(['show']);
        Route::resource('packages', PackageController::class)->except(['show']);
        Route::resource('faqs', FaqController::class)->except(['show']);
    });
});
