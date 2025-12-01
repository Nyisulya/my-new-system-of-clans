<?php

namespace App\Services;

use App\Models\Member;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class DuplicateDetectionService
{
    /**
     * Find potential duplicate members
     * 
     * @param string $firstName
     * @param string $lastName
     * @param string|null $dateOfBirth
     * @param int|null $fatherId
     * @param int|null $motherId
     * @param int|null $excludeId Member ID to exclude from search (for updates)
     * @return Collection
     */
    public function findDuplicates(
        string $firstName,
        string $lastName,
        ?string $dateOfBirth = null,
        ?int $fatherId = null,
        ?int $motherId = null,
        ?int $excludeId = null
    ): Collection {
        $duplicates = collect();

        // Exact match on name and DOB
        if ($dateOfBirth) {
            $exactMatches = $this->findExactMatches($firstName, $lastName, $dateOfBirth, $excludeId);
            
            foreach ($exactMatches as $match) {
                $duplicates->push([
                    'member' => $match,
                    'confidence' => 95,
                    'reason' => 'Exact match on name and date of birth',
                ]);
            }
        }

        // Match on name and same parents
        if ($fatherId || $motherId) {
            $parentMatches = $this->findByParents($firstName, $lastName, $fatherId, $motherId, $excludeId);
            
            foreach ($parentMatches as $match) {
                if (!$duplicates->contains('member.id', $match->id)) {
                    $duplicates->push([
                        'member' => $match,
                        'confidence' => 85,
                        'reason' => 'Similar name with same parents',
                    ]);
                }
            }
        }

        // Fuzzy name matching
        $fuzzyMatches = $this->findFuzzyMatches($firstName, $lastName, $dateOfBirth, $excludeId);
        
        foreach ($fuzzyMatches as $match) {
            if (!$duplicates->contains('member.id', $match['member']->id)) {
                $duplicates->push($match);
            }
        }

        return $duplicates->sortByDesc('confidence')->values();
    }

    /**
     * Find exact matches on name and date of birth
     */
    protected function findExactMatches(string $firstName, string $lastName, string $dateOfBirth, ?int $excludeId): Collection
    {
        $query = Member::where('first_name', $firstName)
                      ->where('last_name', $lastName)
                      ->where('date_of_birth', $dateOfBirth);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->get();
    }

    /**
     * Find members with same or similar name and same parents
     */
    protected function findByParents(string $firstName, string $lastName, ?int $fatherId, ?int $motherId, ?int $excludeId): Collection
    {
        $query = Member::where(function($q) use ($firstName, $lastName) {
            $q->where('first_name', 'LIKE', "%{$firstName}%")
              ->where('last_name', 'LIKE', "%{$lastName}%");
        });

        if ($fatherId && $motherId) {
            $query->where('father_id', $fatherId)
                  ->where('mother_id', $motherId);
        } elseif ($fatherId) {
            $query->where('father_id', $fatherId);
        } elseif ($motherId) {
            $query->where('mother_id', $motherId);
        }

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->get();
    }

    /**
     * Find fuzzy matches using Levenshtein distance
     */
    protected function findFuzzyMatches(string $firstName, string $lastName, ?string $dateOfBirth, ?int $excludeId): Collection
    {
        $matches = collect();
        
        // Get candidates with similar last name
        $query = Member::where('last_name', 'LIKE', $lastName[0] . '%');
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $candidates = $query->limit(100)->get();

        foreach ($candidates as $candidate) {
            $similarity = $this->calculateSimilarity(
                $firstName . ' ' . $lastName,
                $candidate->first_name . ' ' . $candidate->last_name
            );

            if ($similarity >= 70) {
                $confidence = $similarity;
                
                // Boost confidence if DOB is close (within 1 year)
                if ($dateOfBirth && $candidate->date_of_birth) {
                    $dobDiff = abs(strtotime($dateOfBirth) - $candidate->date_of_birth->timestamp);
                    $daysDiff = $dobDiff / (60 * 60 * 24);
                    
                    if ($daysDiff <= 365) {
                        $confidence = min(90, $confidence + 15);
                    }
                }

                $matches->push([
                    'member' => $candidate,
                    'confidence' => (int) $confidence,
                    'reason' => "Similar name (${similarity}% match)",
                ]);
            }
        }

        return $matches;
    }

    /**
     * Calculate similarity percentage between two strings
     * Uses a combination of Levenshtein distance and similar_text
     */
    protected function calculateSimilarity(string $str1, string $str2): float
    {
        $str1 = strtolower(trim($str1));
        $str2 = strtolower(trim($str2));

        if ($str1 === $str2) {
            return 100;
        }

        similar_text($str1, $str2, $percent);
        
        return round($percent, 2);
    }

    /**
     * Check if a member is likely a duplicate (confidence > threshold)
     * 
     * @param string $firstName
     * @param string $lastName
     * @param string|null $dateOfBirth
     * @param int $threshold Confidence threshold (default: 80)
     * @return bool
     */
    public function isDuplicate(
        string $firstName,
        string $lastName,
        ?string $dateOfBirth = null,
        int $threshold = 80
    ): bool {
        $duplicates = $this->findDuplicates($firstName, $lastName, $dateOfBirth);
        
        return $duplicates->where('confidence', '>=', $threshold)->isNotEmpty();
    }

    /**
     * Get duplicate statistics for a family
     * 
     * @param int $familyId
     * @return array
     */
    public function getFamilyDuplicateStats(int $familyId): array
    {
        $members = Member::where('family_id', $familyId)->get();
        $potentialDuplicates = [];
        $checked = [];

        foreach ($members as $member) {
            $key = $member->id;
            
            if (in_array($key, $checked)) {
                continue;
            }

            $duplicates = $this->findDuplicates(
                $member->first_name,
                $member->last_name,
                $member->date_of_birth?->format('Y-m-d'),
                $member->father_id,
                $member->mother_id,
                $member->id
            );

            if ($duplicates->isNotEmpty()) {
                $potentialDuplicates[] = [
                    'original' => $member,
                    'duplicates' => $duplicates,
                ];

                // Mark all duplicates as checked
                foreach ($duplicates as $dup) {
                    $checked[] = $dup['member']->id;
                }
            }

            $checked[] = $key;
        }

        return [
            'total_members' => $members->count(),
            'potential_duplicate_groups' => count($potentialDuplicates),
            'duplicates' => $potentialDuplicates,
        ];
    }

    /**
     * Check for exact match (Name + DOB + Parent)
     * Returns the matching member if found, null otherwise.
     */
    public function checkExactMatch(string $firstName, string $lastName, ?string $dateOfBirth, ?int $fatherId, ?int $motherId, ?int $excludeId = null): ?Member
    {
        if (!$dateOfBirth) {
            return null;
        }

        $query = Member::where('first_name', $firstName)
            ->where('last_name', $lastName)
            ->where('date_of_birth', $dateOfBirth);

        // Check parents if provided
        if ($fatherId) {
            $query->where('father_id', $fatherId);
        }
        
        if ($motherId) {
            $query->where('mother_id', $motherId);
        }

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->first();
    }

    /**
     * Check for partial match (Same Name, Different DOB)
     * Returns a collection of potential matches.
     */
    public function checkPartialMatch(string $firstName, string $lastName, ?string $dateOfBirth, ?int $excludeId = null): Collection
    {
        $query = Member::where('first_name', $firstName)
            ->where('last_name', $lastName);

        if ($dateOfBirth) {
            $query->where('date_of_birth', '!=', $dateOfBirth);
        }

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->get();
    }
}
