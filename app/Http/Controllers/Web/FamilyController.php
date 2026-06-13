<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Family;
use App\Services\TreeBuilderService;
use Illuminate\Http\Request;

class FamilyController extends Controller
{
    public function __construct(
        protected TreeBuilderService $treeService
    ) {}

    public function index()
    {
        $families = Family::with([
            'clan',
            'members' => function($query) {
                $query->where('generation_number', 1)->limit(1);
            }
        ])->withCount('members')->paginate(20);
        
        return view('families.index', compact('families'));
    }

    public function create()
    {
        return view('families.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'clan_id' => 'required|exists:clans,id',
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'description' => 'nullable|string',
            'origin_place' => 'nullable|string|max:255',
            'established_date' => 'nullable|date',
            'is_active' => 'boolean',
        ]);

        Family::create($validated);

        return redirect()->route('families.index')
            ->with('success', 'Family created successfully!');
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Family $family)
    {
        try {
            $familyName = $family->name;
            
            // Delete the family (members will be cascade deleted due to foreign key constraints)
            $family->delete();
            
            return redirect()->route('families.index')
                ->with('success', "Family '{$familyName}' has been deleted successfully!");
        } catch (\Exception $e) {
            return redirect()->route('families.index')
                ->with('error', 'Error deleting family: ' . $e->getMessage());
        }
    }

    /**
     * Display the parents of clan founders (Generation 0).
     */
    public function parents(Family $family)
    {
        // Get Generation 1 members (clan founders) with their parents
        $founders = $family->members()
            ->where('generation_number', 1)
            ->with(['father', 'mother'])
            ->orderBy('date_of_birth')
            ->get();

        // Get all Generation 0 members (parents of founders)
        $generation0Members = $family->members()
            ->where('generation_number', 0)
            ->with(['childrenAsFather', 'childrenAsMother'])
            ->orderBy('date_of_birth')
            ->get();

        return view('families.parents', compact('family', 'founders', 'generation0Members'));
    }

    /**
     * Display the family tree.
     */
    public function tree(Family $family)
    {
        // Get all members for the table view
        $allMembers = $family->members()
            ->with(['father', 'mother', 'children', 'marriagesAsHusband.wife', 'marriagesAsWife.husband'])
            ->orderBy('generation_number')
            ->orderBy('date_of_birth')
            ->get();

        return view('families.tree', compact('family', 'allMembers'));
    }

    /**
     * Display the clan founder view (Generation 1 only).
     */
    public function founder(Family $family)
    {
        // Get Generation 1 members (clan founders)
        $founders = $family->members()
            ->where('generation_number', 1)
            ->with(['marriagesAsHusband.wife', 'marriagesAsWife.husband', 'children'])
            ->get();

        return view('families.founder', compact('family', 'founders'));
    }

    /**
     * Display dynamic interactive family tree
     */
    public function dynamicTree(Family $family)
    {
        $treeData = $this->treeService->getD3TreeData($family->id);
        return view('families.tree_dynamic', compact('family', 'treeData'));
    }
}
