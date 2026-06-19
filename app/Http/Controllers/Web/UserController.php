<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with('member')->latest()->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Huwezi kujifuta wewe mwenyewe.');
        }

        // We only delete the user account (login credentials), not the actual member profile.
        $user->delete();

        return back()->with('success', 'Akaunti ya mtumiaji imefutwa kikamilifu.');
    }
}
