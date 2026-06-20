<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Family;
use App\Models\Clan;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    /**
     * Display the landing page.
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        // Redirect if already authenticated
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }

        // Get dynamic count of entities for the landing page
        $stats = [
            'members'  => Member::count(),
            'families' => Family::count(),
            'clans'    => Clan::core()->count(),
        ];

        return view('landing', compact('stats'));
    }
}
