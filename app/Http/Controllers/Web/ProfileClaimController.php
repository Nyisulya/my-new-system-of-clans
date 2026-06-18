<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileClaimController extends Controller
{
    /**
     * Show the profile search/claim page.
     */
    public function showSearchForm()
    {
        // Enforce that the user must not be linked already
        if (Auth::user()->member_id !== null) {
            return redirect()->route('dashboard')->with('error', 'Tayari una wasifu wa mwanachama uliounganishwa.');
        }

        return view('members.claim_search');
    }

    /**
     * Search for unlinked members.
     */
    public function search(Request $request)
    {
        $term = $request->query('query');
        if (empty($term)) {
            return response()->json([]);
        }

        // Find members that do not have a user account linked
        $members = Member::whereNotExists(function ($query) {
            $query->selectRaw(1)
                  ->from('users')
                  ->whereColumn('users.member_id', 'members.id');
        })
        ->where(function ($q) use ($term) {
            $q->where('first_name', 'like', "%{$term}%")
              ->orWhere('last_name', 'like', "%{$term}%")
              ->orWhere('middle_name', 'like', "%{$term}%");
        })
        ->with(['clan', 'family'])
        ->limit(10)
        ->get()
        ->map(function ($member) {
            return [
                'id' => $member->id,
                'full_name' => $member->full_name,
                'date_of_birth' => $member->date_of_birth ? $member->date_of_birth->format('d M Y') : 'Hajajaza',
                'clan_name' => $member->clan ? $member->clan->name : 'N/A',
                'family_name' => $member->family ? $member->family->name : 'N/A',
                'photo_url' => $member->profile_photo_url,
            ];
        });

        return response()->json($members);
    }

    /**
     * Claim/link an unlinked member profile.
     */
    public function claim(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
        ]);

        $user = Auth::user();

        // Enforce that the user must not be linked already
        if ($user->member_id !== null) {
            return redirect()->route('dashboard')->with('error', 'Tayari una wasifu wa mwanachama uliounganishwa.');
        }

        $memberId = $request->input('member_id');

        // Verify that this member is still unlinked
        $isLinked = \App\Models\User::where('member_id', $memberId)->exists();
        if ($isLinked) {
            return back()->with('error', 'Wasifu huu umeshajiunganishwa na akaunti nyingine tayari.');
        }

        // Update the user account with the member_id
        $user->update(['member_id' => $memberId]);

        return redirect()->route('dashboard')->with('success', 'Wasifu wako wa mwanachama umeunganishwa kikamilifu!');
    }
}
