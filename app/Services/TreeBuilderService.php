<?php

namespace App\Services;

use App\Models\Member;
use App\Models\Clan;
use Illuminate\Support\Collection;

class TreeBuilderService
{
    /**
     * Get all descendants of a member with their depth
     * 
     * @param int $memberId
     * @param int $maxDepth Maximum depth to traverse (0 = unlimited)
     * @return Collection
     */
    public function getDescendants(int $memberId, int $maxDepth = 0): Collection
    {
        $member = Member::with(['children', 'spouse'])->find($memberId);
        
        if (!$member) {
            return collect();
        }

        return $this->buildDescendantTree($member, 1, $maxDepth);
    }

    /**
     * Recursively build descendant tree
     */
    protected function buildDescendantTree(Member $member, int $currentDepth, int $maxDepth): Collection
    {
        $descendants = collect();
        
        if ($maxDepth > 0 && $currentDepth > $maxDepth) {
            return $descendants;
        }

        $children = Member::where(function($query) use ($member) {
            $query->where('father_id', $member->id)
                  ->orWhere('mother_id', $member->id);
        })->with(['spouse'])->get();

        foreach ($children as $child) {
            $descendants->push([
                'member' => $child,
                'depth' => $currentDepth,
                'parent_id' => $member->id,
            ]);
            
            // Recursively get children's descendants
            $childDescendants = $this->buildDescendantTree($child, $currentDepth + 1, $maxDepth);
            $descendants = $descendants->merge($childDescendants);
        }

        return $descendants;
    }

    /**
     * Get all ancestors of a member
     * 
     * @param int $memberId
     * @return Collection
     */
    public function getAncestors(int $memberId): Collection
    {
        $member = Member::with(['father', 'mother'])->find($memberId);
        
        if (!$member) {
            return collect();
        }

        return $this->buildAncestorTree($member);
    }

    /**
     * Recursively build ancestor tree
     */
    protected function buildAncestorTree(Member $member, int $depth = 1): Collection
    {
        $ancestors = collect();

        if ($member->father) {
            $ancestors->push([
                'member' => $member->father,
                'depth' => $depth,
                'relationship' => 'father',
            ]);
            
            $ancestors = $ancestors->merge($this->buildAncestorTree($member->father, $depth + 1));
        }

        if ($member->mother) {
            $ancestors->push([
                'member' => $member->mother,
                'depth' => $depth,
                'relationship' => 'mother',
            ]);
            
            $ancestors = $ancestors->merge($this->buildAncestorTree($member->mother, $depth + 1));
        }

        return $ancestors->unique(function ($item) {
            return $item['member']->id;
        });
    }

    /**
     * Get complete family tree for a clan
     * Returns hierarchical structure optimized for visualization
     * 
     * @param int $clanId
     * @return array
     */
    public function getFullTree(int $clanId): array
    {
        $clan = Clan::with(['families'])->find($clanId);
        
        if (!$clan) {
            return [];
        }

        // Find root members (those without parents)
        $rootMembers = Member::where('clan_id', $clanId)
                            ->whereNull('father_id')
                            ->whereNull('mother_id')
                            ->with(['spouse', 'children'])
                            ->orderBy('generation_number')
                            ->orderBy('date_of_birth')
                            ->get();

        $tree = [];
        
        foreach ($rootMembers as $root) {
            $tree[] = $this->buildNodeTree($root);
        }

        return [
            'clan' => $clan,
            'total_members' => Member::where('clan_id', $clanId)->count(),
            'generations' => Member::where('clan_id', $clanId)->max('generation_number'),
            'roots' => $tree,
        ];
    }

    /**
     * Build tree node with children
     */
    protected function buildNodeTree(Member $member): array
    {
        $children = Member::where(function($query) use ($member) {
            $query->where('father_id', $member->id)
                  ->orWhere('mother_id', $member->id);
        })
        ->with(['spouse'])
        ->orderBy('date_of_birth')
        ->get();

        $childrenNodes = [];
        foreach ($children as $child) {
            $childrenNodes[] = $this->buildNodeTree($child);
        }

        return [
            'id' => $member->id,
            'name' => $member->full_name,
            'gender' => $member->gender,
            'birth_year' => $member->date_of_birth?->format('Y'),
            'death_year' => $member->date_of_death?->format('Y'),
            'status' => $member->status,
            'generation' => $member->generation_number,
            'spouse' => $member->spouse ? [
                'id' => $member->spouse->id,
                'name' => $member->spouse->full_name,
            ] : null,
            'children' => $childrenNodes,
            'children_count' => count($childrenNodes),
        ];
    }

    /**
     * Get siblings of a member
     * 
     * @param int $memberId
     * @return Collection
     */
    public function getSiblings(int $memberId): Collection
    {
        $member = Member::find($memberId);
        
        if (!$member || (!$member->father_id && !$member->mother_id)) {
            return collect();
        }

        $query = Member::where('id', '!=', $memberId);

        if ($member->father_id && $member->mother_id) {
            // Full siblings (same father and mother)
            $query->where('father_id', $member->father_id)
                  ->where('mother_id', $member->mother_id);
        } elseif ($member->father_id) {
            // Half siblings through father
            $query->where('father_id', $member->father_id);
        } else {
            // Half siblings through mother
            $query->where('mother_id', $member->mother_id);
        }

        return $query->get();
    }

    /**
     * Get statistics for a family tree
     * 
     * @param int $clanId
     * @return array
     */
    public function getTreeStatistics(int $clanId): array
    {
        $totalMembers = Member::where('clan_id', $clanId)->count();
        $aliveMembers = Member::where('clan_id', $clanId)->where('status', 'alive')->count();
        $deceasedMembers = Member::where('clan_id', $clanId)->where('status', 'deceased')->count();
        
        $maleCount = Member::where('clan_id', $clanId)->where('gender', 'male')->count();
        $femaleCount = Member::where('clan_id', $clanId)->where('gender', 'female')->count();
        
        $maxGeneration = Member::where('clan_id', $clanId)->max('generation_number') ?? 0;
        
        $ageDistribution = Member::where('clan_id', $clanId)
                                ->where('status', 'alive')
                                ->get()
                                ->groupBy(function($member) {
                                    $age = $member->age ?? 0;
                                    if ($age < 18) return '0-17';
                                    if ($age < 30) return '18-29';
                                    if ($age < 50) return '30-49';
                                    if ($age < 70) return '50-69';
                                    return '70+';
                                })
                                ->map->count();

        return [
            'total_members' => $totalMembers,
            'alive_members' => $aliveMembers,
            'deceased_members' => $deceasedMembers,
            'male_count' => $maleCount,
            'female_count' => $femaleCount,
            'total_generations' => $maxGeneration,
            'age_distribution' => $ageDistribution,
        ];
    }
    /**
     * Get family tree data formatted for D3.js
     * 
     * @param int $familyId
     * @return array
     */
    public function getD3TreeData(int $familyId): array
    {
        $family = \App\Models\Family::find($familyId);
        
        if (!$family) {
            return [];
        }

        // Find the clan founder (Generation 1) for this family
        // Or the oldest ancestor in this family
        $rootMember = Member::where('family_id', $familyId)
            ->where('generation_number', 1)
            ->first();

        if (!$rootMember) {
            // Fallback: find member with no parents in this family
            $rootMember = Member::where('family_id', $familyId)
                ->whereNull('father_id')
                ->whereNull('mother_id')
                ->first();
        }

        if (!$rootMember) {
            return [];
        }

        return $this->buildD3Node($rootMember);
    }

    /**
     * Recursively build D3 node structure
     */
    protected function buildD3Node(Member $member): array
    {
        $node = [
            'name' => $member->full_name,
            'attributes' => [
                'generation' => $member->generation_number,
                'status' => $member->status,
                'gender' => $member->gender,
                'photo' => $member->profile_photo_url,
            ]
        ];

        // Add spouse info if exists
        if ($member->spouse) {
            $node['attributes']['spouse'] = $member->spouse->full_name;
        }

        // Get children
        $children = Member::where(function($query) use ($member) {
            $query->where('father_id', $member->id)
                  ->orWhere('mother_id', $member->id);
        })
        ->orderBy('date_of_birth')
        ->get();

        if ($children->isNotEmpty()) {
            $node['children'] = [];
            foreach ($children as $child) {
                $node['children'][] = $this->buildD3Node($child);
            }
        }

        return $node;
    }
}
