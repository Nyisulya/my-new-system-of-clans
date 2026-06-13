<?php

namespace App\Services;

use App\Models\Member;

class GenerationCalculatorService
{
    /**
     * Calculate generation number for a member based on their parents
     * 
     * @param int|null $fatherId
     * @param int|null $motherId
     * @return int
     */
    public function calculate(?int $fatherId, ?int $motherId): int
    {
        $fatherGeneration = 0;
        $motherGeneration = 0;

        if ($fatherId) {
            $father = Member::find($fatherId);
            $fatherGeneration = $father ? $father->generation_number : 0;
        }

        if ($motherId) {
            $mother = Member::find($motherId);
            $motherGeneration = $mother ? $mother->generation_number : 0;
        }

        // If both parents exist, take the max and add 1
        if ($fatherGeneration > 0 || $motherGeneration > 0) {
            return max($fatherGeneration, $motherGeneration) + 1;
        }

        // Root member (no parents) - Generation 1
        return 1;
    }

    /**
     * Recalculate generation numbers for all descendants of a member
     * This is useful when a member's generation changes
     * 
     * @param Member $member
     * @return void
     */
    public function recalculateDescendants(Member $member): void
    {
        $children = Member::where('father_id', $member->id)
                         ->orWhere('mother_id', $member->id)
                         ->get();

        foreach ($children as $child) {
            $child->generation_number = $this->calculate($child->father_id, $child->mother_id);
            $child->saveQuietly(); // Save without firing events
            
            // Recursively update descendants
            $this->recalculateDescendants($child);
        }
    }

    /**
     * Get the maximum generation number in a family
     * 
     * @param int $familyId
     * @return int
     */
    public function getMaxGeneration(int $familyId): int
    {
        return Member::where('family_id', $familyId)->max('generation_number') ?? 0;
    }

    /**
     * Get members in a specific generation
     * 
     * @param int $familyId
     * @param int $generation
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getMembersInGeneration(int $familyId, int $generation)
    {
        return Member::where('family_id', $familyId)
                    ->where('generation_number', $generation)
                    ->get();
    }
}
