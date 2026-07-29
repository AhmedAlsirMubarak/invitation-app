<?php

use App\Http\Controllers\Api\CheckinController;
use Illuminate\Support\Facades\Route;

// The scanner page is only ever opened from inside the authenticated
// admin session (see resources/views/scanner/index.blade.php), so this
// uses the same 'web' session guard + CSRF token rather than Sanctum —
// simpler for a single in-house tool with no separate mobile app.
Route::middleware(['web', 'auth'])->group(function () {
    Route::post('/checkin', [CheckinController::class, 'store'])->name('api.checkin');
});
