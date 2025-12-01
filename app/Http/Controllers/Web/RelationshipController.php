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

        return response()->json([
            'result' => $result,
            'member1' => $member1->full_name,
            'member2' => $member2->full_name,
        ]);
    }
}
