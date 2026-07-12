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

        $member1 = Member::find($request->member1_id);
        $member2 = Member::find($request->member2_id);

        // Translate English result to Swahili, passing member1 for gender context
        $swResult = $this->translateRelationship($result, $member1);

        return response()->json([
            'result' => $swResult,
            'member1' => $member1->full_name,
            'member2' => $member2->full_name,
        ]);
    }

    /**
     * Translates English relationship service outputs to Swahili with gender awareness.
     */
    private function translateRelationship(string $relation, Member $member): string
    {
        $gender = $member->gender; // 'male', 'female', or 'other'

        // Gender-specific maps
        $translations = [
            'male' => [
                'Same person' => 'Mtu yule yule',
                'Member not found' => 'Mwanachama hajapatikana',
                'Spouse' => 'Mume', // Husband
                'Child' => 'Mtoto wa Kiume',
                'Grandchild' => 'Mjukuu',
                'Great-grandchild' => 'Kitukuu',
                'Parent' => 'Baba', // Father
                'Grandparent' => 'Babu', // Grandfather
                'Great-grandparent' => 'Babu Mkuu', // Great-grandfather
                'Sibling' => 'Kaka', // Brother
                'Aunt/Uncle' => 'Mjomba', // Uncle
                'Niece/Nephew' => 'Mpwa wa Kiume',
                'No direct blood relationship found' => 'Hakuna uhusiano wa damu wa moja kwa moja uliopatikana',
            ],
            'female' => [
                'Same person' => 'Mtu yule yule',
                'Member not found' => 'Mwanachama hajapatikana',
                'Spouse' => 'Mke', // Wife
                'Child' => 'Mtoto wa Kike',
                'Grandchild' => 'Mjukuu',
                'Great-grandchild' => 'Kitukuu',
                'Parent' => 'Mama', // Mother
                'Grandparent' => 'Bibi', // Grandmother
                'Great-grandparent' => 'Bibi Mkuu', // Great-grandmother
                'Sibling' => 'Dada', // Sister
                'Aunt/Uncle' => 'Shangazi', // Aunt
                'Niece/Nephew' => 'Mpwa wa Kike',
                'No direct blood relationship found' => 'Hakuna uhusiano wa damu wa moja kwa moja uliopatikana',
            ],
            'other' => [
                'Same person' => 'Mtu yule yule',
                'Member not found' => 'Mwanachama hajapatikana',
                'Spouse' => 'Mwenzi',
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
            ]
        ];

        // Choose map based on gender
        $map = $translations[$gender] ?? $translations['other'];

        // Handle pattern match for "Great-(x)-grandchild"
        if (preg_match('/Great-\((\d+)\)-grandchild/', $relation, $matches)) {
            $genNumber = $matches[1] + 3;
            if ($gender === 'male') {
                return "Mzao wa Kiume wa Kizazi cha " . $genNumber;
            } elseif ($gender === 'female') {
                return "Mzao wa Kike wa Kizazi cha " . $genNumber;
            } else {
                return "Mzao wa Kizazi cha " . $genNumber;
            }
        }

        // Handle pattern match for "Great-(x)-grandparent"
        if (preg_match('/Great-\((\d+)\)-grandparent/', $relation, $matches)) {
            $genNumber = $matches[1] + 3;
            if ($gender === 'male') {
                return "Babu Mkuu wa Kizazi cha " . $genNumber;
            } elseif ($gender === 'female') {
                return "Bibi Mkuu wa Kizazi cha " . $genNumber;
            } else {
                return "Babu/Bibi Mkuu wa Kizazi cha " . $genNumber;
            }
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

        return $map[$relation] ?? ($translations['other'][$relation] ?? $relation);
    }
}
