<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Marriage;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class TimelineController extends Controller
{
    public function index()
    {
        $events = collect();

        // 1. Births
        $births = Member::whereNotNull('date_of_birth')
            ->get()
            ->map(function ($member) {
                return [
                    'date' => $member->date_of_birth,
                    'type' => 'birth',
                    'title' => 'Birth of ' . $member->full_name,
                    'description' => 'Born to ' . ($member->father ? $member->father->full_name : 'Unknown') . ' and ' . ($member->mother ? $member->mother->full_name : 'Unknown'),
                    'icon' => 'fas fa-baby',
                    'color' => 'bg-success',
                    'model' => $member,
                ];
            });
        $events = $events->merge($births);

        // 2. Deaths
        $deaths = Member::whereNotNull('date_of_death')
            ->get()
            ->map(function ($member) {
                return [
                    'date' => $member->date_of_death,
                    'type' => 'death',
                    'title' => 'Death of ' . $member->full_name,
                    'description' => 'Passed away at age ' . $member->age,
                    'icon' => 'fas fa-cross',
                    'color' => 'bg-secondary',
                    'model' => $member,
                ];
            });
        $events = $events->merge($deaths);

        // 3. Marriages
        $marriages = Marriage::whereNotNull('marriage_date')
            ->with(['husband', 'wife'])
            ->get()
            ->map(function ($marriage) {
                return [
                    'date' => $marriage->marriage_date,
                    'type' => 'marriage',
                    'title' => 'Marriage of ' . $marriage->husband->first_name . ' & ' . $marriage->wife->first_name,
                    'description' => 'Married in ' . ($marriage->location ?? 'Unknown location'),
                    'icon' => 'fas fa-rings',
                    'color' => 'bg-pink',
                    'model' => $marriage,
                ];
            });
        $events = $events->merge($marriages);

        // Sort by date descending
        $events = $events->sortByDesc('date')->values();

        // Paginate collection manually (15 items per page)
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 15;
        $currentPageItems = $events->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $paginatedEvents = new LengthAwarePaginator(
            $currentPageItems,
            $events->count(),
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );

        // Group current page items by Year
        $groupedEvents = $currentPageItems->groupBy(function ($item) {
            return $item['date']->format('Y');
        });

        return view('timeline.index', compact('groupedEvents', 'paginatedEvents'));
    }
}
