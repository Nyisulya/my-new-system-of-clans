<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Clan;
use Illuminate\Http\Request;

class ClanController extends Controller
{
    public function index()
    {
        $clans = Clan::withCount(['members', 'families'])->paginate(20);
        return view('clans.index', compact('clans'));
    }

    public function create()
    {
        return view('clans.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'founding_date' => 'nullable|date',
            'origin_location' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        Clan::create($validated);

        return redirect()->route('clans.index')
            ->with('success', 'Clan created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Clan $clan)
    {
        return view('clans.edit', compact('clan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Clan $clan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'founding_date' => 'nullable|date',
            'origin_location' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $clan->update($validated);

        return redirect()->route('clans.index')
            ->with('success', 'Clan updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Clan $clan)
    {
        // Check if clan has any members or families
        if ($clan->members()->count() > 0) {
            return redirect()->route('clans.index')
                ->with('error', 'Cannot delete clan that has members. Please reassign or delete members first.');
        }

        if ($clan->families()->count() > 0) {
            return redirect()->route('clans.index')
                ->with('error', 'Cannot delete clan that has families. Please reassign or delete families first.');
        }

        $clan->delete();

        return redirect()->route('clans.index')
            ->with('success', 'Clan deleted successfully!');
    }
}
