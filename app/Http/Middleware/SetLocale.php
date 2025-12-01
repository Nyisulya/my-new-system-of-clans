<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated and has a locale preference
        if (auth()->check() && auth()->user()->locale) {
            $locale = auth()->user()->locale;
        } 
        // Otherwise check session
        elseif (Session::has('locale')) {
            $locale = Session::get('locale');
        } 
        // Default to English
        else {
            $locale = config('app.locale', 'en');
        }

        App::setLocale($locale);

        return $next($request);
    }
}
