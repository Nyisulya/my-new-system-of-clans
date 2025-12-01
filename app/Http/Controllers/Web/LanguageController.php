<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    public function switch(Request $request, $locale)
    {
        // Validate locale
        if (!in_array($locale, ['en', 'sw'])) {
            return back();
        }

        // Set session
        Session::put('locale', $locale);

        // Update user preference if authenticated
        if (auth()->check()) {
            auth()->user()->update(['locale' => $locale]);
        }

        // Set app locale
        App::setLocale($locale);

        return back()->with('success', 'Language changed successfully');
    }
}
