<?php

use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [SetLocale::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        // A CSRF-expired ("419 Page Expired") request should never show
        // Laravel's bare error page. Note: the handler's prepareException()
        // already converts TokenMismatchException into a plain HttpException
        // (status 419) before custom render() callbacks run, so we have to
        // match on that mapped type + status code instead of the original
        // exception class. Guests hitting an admin-only route (most often the
        // login form itself after sitting open a while) go back to login with
        // an explanatory message; anyone already authenticated, or on a
        // token-based public page (client portal), just gets sent back to the
        // same page with a fresh CSRF token to retry.
        $exceptions->render(function (HttpException $e, Request $request) {
            if ($e->getStatusCode() !== 419) {
                return null;
            }

            if ($request->expectsJson()) {
                return null;
            }

            if ($request->is('login') || ($request->is('admin*') && ! Auth::check())) {
                return redirect()->route('login')->with('status', __('admin.auth.session_expired'));
            }

            return redirect($request->fullUrl())->with('status', __('admin.auth.session_expired'));
        });
    })->create();
