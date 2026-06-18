<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMemberProfileIsComplete
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Only enforce this for regular logged-in members (admins and editors don't represent a single family tree node)
        if ($user && $user->role === 'member') {
            $member = $user->member;

            // An incomplete profile is one without a clan or family set
            if ($member && ($member->clan_id === null || $member->family_id === null)) {
                $currentRoute = $request->route() ? $request->route()->getName() : null;

                // Do not redirect if they are already on the edit/update page or logging out
                if (!in_array($currentRoute, ['members.edit', 'members.update', 'logout'])) {
                    return redirect()->route('members.edit', $member->id)
                        ->with('warning', 'Tafadhali kamilisha taarifa za wasifu wako (Jinsia, Ukoo, Familia na Wazazi) ili kuunganishwa kwenye mti wa familia kabla ya kuendelea.');
                }
            }
        }

        return $next($request);
    }
}
