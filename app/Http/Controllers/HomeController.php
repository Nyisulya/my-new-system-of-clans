<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Counts
        $totalMembers = \App\Models\Member::count();
        $totalClans = \App\Models\Clan::count();
        $totalFamilies = \App\Models\Family::count();
        $totalMarriages = \App\Models\Marriage::count();

        // Gender Distribution
        $maleCount = \App\Models\Member::where('gender', 'male')->count();
        $femaleCount = \App\Models\Member::where('gender', 'female')->count();

        // Status Distribution
        $aliveCount = \App\Models\Member::where('status', 'alive')->count();
        $deceasedCount = \App\Models\Member::where('status', 'deceased')->count();

        // Family Hierarchy Stats
        // Parents: Members who have at least one child
        $totalParents = \App\Models\Member::has('children')->count();

        // Children: Members who have a father OR a mother
        $totalChildren = \App\Models\Member::whereNotNull('father_id')
            ->orWhereNotNull('mother_id')
            ->count();

        // Grandchildren: Members whose parents have parents
        // This is a bit more complex, so we'll count members who have a grandparent
        $totalGrandchildren = \App\Models\Member::whereHas('father', function($q) {
                $q->whereNotNull('father_id')->orWhereNotNull('mother_id');
            })
            ->orWhereHas('mother', function($q) {
                $q->whereNotNull('father_id')->orWhereNotNull('mother_id');
            })
            ->count();

        // Recent Members
        $recentMembers = \App\Models\Member::with(['family', 'clan'])
            ->latest()
            ->take(5)
            ->take(5)
            ->get();

        // Active Announcements
        $activeAnnouncements = \App\Models\Announcement::active()
            ->orderBy('start_date', 'desc')
            ->get();

        return view('home', compact(
            'totalMembers',
            'totalClans',
            'totalFamilies',
            'totalMarriages',
            'maleCount',
            'femaleCount',
            'aliveCount',
            'deceasedCount',
            'totalParents',
            'totalChildren',
            'totalGrandchildren',
            'recentMembers',
            'activeAnnouncements'
        ));
    }
}
