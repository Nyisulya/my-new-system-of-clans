<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;

class MapController extends Controller
{
    public function index()
    {
        $members = Member::whereNotNull('birth_lat')
            ->orWhereNotNull('current_lat')
            ->get()
            ->map(function ($member) {
                $locations = [];
                
                if ($member->birth_lat) {
                    $locations[] = [
                        'lat' => $member->birth_lat,
                        'lng' => $member->birth_lng,
                        'title' => 'Birthplace of ' . $member->full_name,
                        'type' => 'birth',
                        'name' => $member->birth_place,
                    ];
                }

                if ($member->current_lat) {
                    $locations[] = [
                        'lat' => $member->current_lat,
                        'lng' => $member->current_lng,
                        'title' => ($member->status == 'deceased' ? 'Resting place of ' : 'Current residence of ') . $member->full_name,
                        'type' => 'current',
                        'name' => $member->current_location,
                    ];
                }

                return [
                    'member' => $member,
                    'locations' => $locations,
                ];
            });

        return view('maps.index', compact('members'));
    }
}
