<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Services\RelationshipCalculatorService;
use Illuminate\Http\Request;

class RelationshipController extends Controller
{
    public function __construct(
        protected RelationshipCalculatorService $calculator
    ) {}

    public function index()
    {
        $members = Member::orderBy('first_name')->get(['id', 'first_name', 'last_name']);
        return view('relationships.calculator', compact('members'));
    }

    public function calculate(Request $request)
    {
        $request->validate([
            'member1_id' => 'required|exists:members,id',
            'member2_id' => 'required|exists:members,id|different:member1_id',
        ]);

        $result = $this->calculator->calculate(
            $request->member1_id,
            $request->member2_id
        );

        // Translate English result to Swahili
        $swResult = $this->translateRelationship($result);

        $member1 = Member::find($request->member1_id);
        $member2 = Member::find($request->member2_id);

        return response()->json([
            'result' => $swResult,
            'member1' => $member1->full_name,
            'member2' => $member2->full_name,
        ]);
    }

    /**
     * Translates English relationship service outputs to Swahili.
     */
    private function translateRelationship(string $relation): string
    {
        $translations = [
            'Same person' => 'Mtu yule yule',
            'Member not found' => 'Mwanachama hajapatikana',
            'Spouse' => 'Mwenzi (Mke/Mume)',
            'Child' => 'Mtoto',
            'Grandchild' => 'Mjukuu',
            'Great-grandchild' => 'Kitukuu',
            'Parent' => 'Mzazi (Baba/Mama)',
            'Grandparent' => 'Babu/Bibi',
            'Great-grandparent' => 'Babu/Bibi Mkuu',
            'Sibling' => 'Ndugu (Kaka/Dada)',
            'Aunt/Uncle' => 'Shangazi/Mjomba',
            'Niece/Nephew' => 'Mpwa',
            'No direct blood relationship found' => 'Hakuna uhusiano wa damu wa moja kwa moja uliopatikana',
        ];

        // Handle pattern match for "Great-(x)-grandchild"
        if (preg_match('/Great-\((\d+)\)-grandchild/', $relation, $matches)) {
            return "Kilembwe au Mzao wa Kizazi cha " . ($matches[1] + 3);
        }

        // Handle pattern match for "Great-(x)-grandparent"
        if (preg_match('/Great-\((\d+)\)-grandparent/', $relation, $matches)) {
            return "Babu/Bibi Mkuu wa Kizazi cha " . ($matches[1] + 3);
        }

        // Handle cousin relationships: e.g., "1st Cousin", "2nd Cousin 1x removed"
        if (preg_match('/(\d+)(st|nd|rd|th)\s+Cousin(.*)/i', $relation, $matches)) {
            $num = $matches[1];
            $removed = trim($matches[3]);
            $swCousin = "Binamu wa kizazi cha " . $num;
            if (!empty($removed)) {
                $swCousin .= " (" . str_ireplace('removed', 'mbali', $removed) . ")";
            }
            return $swCousin;
        }

        return $translations[$relation] ?? $relation;
    }
}
