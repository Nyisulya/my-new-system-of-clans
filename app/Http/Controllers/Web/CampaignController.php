<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CampaignController extends Controller
{
    public function index()
    {
        $campaigns = Campaign::withSum('contributions', 'amount')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('campaigns.index', compact('campaigns'));
    }

    public function create()
    {
        return view('campaigns.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'target_amount' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'description' => 'nullable|string',
        ]);

        Campaign::create([
            'title' => $request->title,
            'target_amount' => $request->target_amount,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'description' => $request->description,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('campaigns.index')->with('success', 'Campaign created successfully.');
    }

    public function show(Campaign $campaign)
    {
        $campaign->load(['contributions.member', 'creator']);
        $totalRaised = $campaign->contributions->sum('amount');
        $progress = $campaign->target_amount > 0 ? min(100, round(($totalRaised / $campaign->target_amount) * 100)) : 0;

        return view('campaigns.show', compact('campaign', 'totalRaised', 'progress'));
    }

    public function edit(Campaign $campaign)
    {
        return view('campaigns.edit', compact('campaign'));
    }

    public function update(Request $request, Campaign $campaign)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'target_amount' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:active,closed,completed',
        ]);

        $campaign->update($request->all());

        return redirect()->route('campaigns.show', $campaign)->with('success', 'Campaign updated successfully.');
    }
}
