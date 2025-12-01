<?php

namespace App\Services;

use App\Models\Member;
use Illuminate\Support\Collection;

class RelationshipCalculatorService
{
    /**
     * Calculate the relationship between two members
     * 
     * @param int $memberId1
     * @param int $memberId2
     * @return string
     */
    public function calculate(int $memberId1, int $memberId2): string
    {
        if ($memberId1 === $memberId2) {
            return "Same person";
        }

        $person1 = Member::find($memberId1);
        $person2 = Member::find($memberId2);

        if (!$person1 || !$person2) {
            return "Member not found";
        }

        // Check for direct spouse relationship
        if ($this->areSpouses($person1, $person2)) {
            return "Spouse";
        }

        // Get ancestors for both
        $ancestors1 = $this->getAncestorsWithDistance($person1);
        $ancestors2 = $this->getAncestorsWithDistance($person2);

        // Find Lowest Common Ancestor (LCA)
        $lca = null;
        $distance1 = -1;
        $distance2 = -1;

        // Check if person 2 is an ancestor of person 1
        if (isset($ancestors1[$person2->id])) {
            return $this->getDirectDescendantRelationship($ancestors1[$person2->id]);
        }

        // Check if person 1 is an ancestor of person 2
        if (isset($ancestors2[$person1->id])) {
            return $this->getDirectAncestorRelationship($ancestors2[$person1->id]);
        }

        // Find common ancestor
        foreach ($ancestors1 as $id => $dist1) {
            if (isset($ancestors2[$id])) {
                $dist2 = $ancestors2[$id];
                
                // Found a common ancestor
                // We want the one with the smallest total distance (closest relative)
                if ($lca === null || ($dist1 + $dist2) < ($distance1 + $distance2)) {
                    $lca = $id;
                    $distance1 = $dist1;
                    $distance2 = $dist2;
                }
            }
        }

        if ($lca) {
            return $this->getCousinRelationship($distance1, $distance2);
        }

        return "No direct blood relationship found";
    }

    protected function areSpouses(Member $m1, Member $m2): bool
    {
        return \App\Models\Marriage::where(function($q) use ($m1, $m2) {
            $q->where('husband_id', $m1->id)->where('wife_id', $m2->id);
        })->orWhere(function($q) use ($m1, $m2) {
            $q->where('husband_id', $m2->id)->where('wife_id', $m1->id);
        })->exists();
    }

    /**
     * Get all ancestors and their distance (generations up)
     */
    protected function getAncestorsWithDistance(Member $member, int $distance = 0, array &$ancestors = []): array
    {
        // Add self (distance 0) to allow checking if one is ancestor of another
        if ($distance === 0) {
            // We don't add self to ancestors list for LCA check usually, but for direct check we handle it separately
        } else {
            $ancestors[$member->id] = $distance;
        }

        if ($member->father) {
            $this->getAncestorsWithDistance($member->father, $distance + 1, $ancestors);
        }
        if ($member->mother) {
            $this->getAncestorsWithDistance($member->mother, $distance + 1, $ancestors);
        }

        return $ancestors;
    }

    protected function getDirectDescendantRelationship(int $distance): string
    {
        if ($distance == 1) return "Child";
        if ($distance == 2) return "Grandchild";
        if ($distance == 3) return "Great-grandchild";
        return "Great-(" . ($distance - 2) . ")-grandchild";
    }

    protected function getDirectAncestorRelationship(int $distance): string
    {
        if ($distance == 1) return "Parent";
        if ($distance == 2) return "Grandparent";
        if ($distance == 3) return "Great-grandparent";
        return "Great-(" . ($distance - 2) . ")-grandparent";
    }

    protected function getCousinRelationship(int $d1, int $d2): string
    {
        // d1 = distance from person 1 to LCA
        // d2 = distance from person 2 to LCA
        
        // Siblings: d1=1, d2=1
        if ($d1 == 1 && $d2 == 1) return "Sibling";

        // Aunt/Uncle / Niece/Nephew
        if ($d1 == 1 && $d2 == 2) return "Niece/Nephew";
        if ($d1 == 2 && $d2 == 1) return "Aunt/Uncle";

        // Great Aunt/Uncle
        if ($d1 == 1 && $d2 == 3) return "Great-Niece/Nephew";
        if ($d1 == 3 && $d2 == 1) return "Great-Aunt/Uncle";

        // Cousins
        $min = min($d1, $d2);
        $diff = abs($d1 - $d2);

        $cousinOrd = $min - 1; // 1st cousin, 2nd cousin...
        
        $suffix = "";
        if ($diff > 0) {
            $suffix = " " . $diff . "x removed";
        }

        return $this->ordinal($cousinOrd) . " Cousin" . $suffix;
    }

    protected function ordinal($number) {
        $ends = array('th','st','nd','rd','th','th','th','th','th','th');
        if ((($number % 100) >= 11) && (($number%100) <= 13))
            return $number. 'th';
        else
            return $number. $ends[$number % 10];
    }
}
