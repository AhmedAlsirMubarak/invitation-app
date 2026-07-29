<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    // Arabic by default, per this app's primary audience. ?lang=en switches
    // it and the choice is remembered in session for subsequent requests.
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->query('lang', session('locale', 'ar'));
        $locale = in_array($locale, ['ar', 'en'], true) ? $locale : 'ar';

        session(['locale' => $locale]);
        App::setLocale($locale);

        return $next($request);
    }
}
