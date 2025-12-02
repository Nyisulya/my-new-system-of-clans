<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Clan;
use App\Models\Family;
use App\Models\Member;
use App\Services\TreeBuilderService;
use App\Services\CacheService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        protected TreeBuilderService $treeBuilder,
        protected CacheService $cache
    ) {
    }

    public function getCategoryMembers(Request $request)
    {
        $category = $request->query('category');
        $clan = Clan::first(); // Should match the logic in index()
        
        if (!$clan) {
            return response()->json(['html' => '<p>No clan found.</p>']);
        }

        $query = Member::where('clan_id', $clan->id);

        switch ($category) {
            case 'all_members':
                // No filter needed
                break;
            case 'living_members':
                $query->where('status', 'alive');
                break;
            case 'deceased_members':
                $query->where('status', 'deceased');
                break;
            case 'parents':
                $query->has('children');
                break;
            case 'children':
                $query->where(function($q) {
                    $q->whereNotNull('father_id')->orWhereNotNull('mother_id');
                });
                break;
            case 'grandchildren':
                $query->where(function($q) {
                    $q->whereHas('father', fn($sq) => $sq->whereNotNull('father_id')->orWhereNotNull('mother_id'))
                      ->orWhereHas('mother', fn($sq) => $sq->whereNotNull('father_id')->orWhereNotNull('mother_id'));
                });
                break;
            case 'great_grandchildren':
                $query->where(function($q) {
                    $q->whereHas('father.father', fn($sq) => $sq->whereNotNull('father_id')->orWhereNotNull('mother_id'))
                      ->orWhereHas('father.mother', fn($sq) => $sq->whereNotNull('father_id')->orWhereNotNull('mother_id'))
                      ->orWhereHas('mother.father', fn($sq) => $sq->whereNotNull('father_id')->orWhereNotNull('mother_id'))
                      ->orWhereHas('mother.mother', fn($sq) => $sq->whereNotNull('father_id')->orWhereNotNull('mother_id'));
                });
                break;
            case 'great_great_grandchildren':
                $query->where(function($q) {
                    $q->whereHas('father.father.father', fn($sq) => $sq->whereNotNull('father_id')->orWhereNotNull('mother_id'))
                      ->orWhereHas('father.father.mother', fn($sq) => $sq->whereNotNull('father_id')->orWhereNotNull('mother_id'))
                      ->orWhereHas('father.mother.father', fn($sq) => $sq->whereNotNull('father_id')->orWhereNotNull('mother_id'))
                      ->orWhereHas('father.mother.mother', fn($sq) => $sq->whereNotNull('father_id')->orWhereNotNull('mother_id'))
                      ->orWhereHas('mother.father.father', fn($sq) => $sq->whereNotNull('father_id')->orWhereNotNull('mother_id'))
                      ->orWhereHas('mother.father.mother', fn($sq) => $sq->whereNotNull('father_id')->orWhereNotNull('mother_id'))
                      ->orWhereHas('mother.mother.father', fn($sq) => $sq->whereNotNull('father_id')->orWhereNotNull('mother_id'))
                      ->orWhereHas('mother.mother.mother', fn($sq) => $sq->whereNotNull('father_id')->orWhereNotNull('mother_id'));
                });
                break;
            case 'great_great_great_grandchildren':
                $query->where(function($q) {
                    $q->whereHas('father', function($f) {
                        $f->whereHas('father.father.father', fn($sq) => $sq->whereNotNull('father_id')->orWhereNotNull('mother_id'))
                          ->orWhereHas('father.father.mother', fn($sq) => $sq->whereNotNull('father_id')->orWhereNotNull('mother_id'))
                          ->orWhereHas('father.mother.father', fn($sq) => $sq->whereNotNull('father_id')->orWhereNotNull('mother_id'))
                          ->orWhereHas('father.mother.mother', fn($sq) => $sq->whereNotNull('father_id')->orWhereNotNull('mother_id'));
                    })->orWhereHas('mother', function($m) {
                        $m->whereHas('father.father.father', fn($sq) => $sq->whereNotNull('father_id')->orWhereNotNull('mother_id'))
                          ->orWhereHas('father.father.mother', fn($sq) => $sq->whereNotNull('father_id')->orWhereNotNull('mother_id'))
                          ->orWhereHas('father.mother.father', fn($sq) => $sq->whereNotNull('father_id')->orWhereNotNull('mother_id'))
                          ->orWhereHas('father.mother.mother', fn($sq) => $sq->whereNotNull('father_id')->orWhereNotNull('mother_id'));
                    });
                });
                break;
            default:
                return response()->json(['html' => '<p>Invalid category.</p>']);
        }

        $members = $query->select('id', 'first_name', 'middle_name', 'last_name', 'profile_photo_path')
            ->limit(100) // Limit to 100 to prevent crashing
            ->get();

        $html = '<div class="list-group">';
        if ($members->isEmpty()) {
            $html .= '<div class="list-group-item">No members found in this category.</div>';
        } else {
            foreach ($members as $member) {
                $url = route('members.show', $member->id);
                $photoUrl = $member->profile_photo_url;
                $html .= "
                    <a href='{$url}' class='list-group-item list-group-item-action d-flex align-items-center'>
                        <img src='{$photoUrl}' class='img-circle mr-3' style='width: 40px; height: 40px; object-fit: cover;'>
                        <div>
                            <h6 class='mb-0'>{$member->full_name}</h6>
                        </div>
                    </a>
                ";
            }
        }
        $html .= '</div>';
        
        if ($members->count() >= 100) {
            $html .= '<div class="alert alert-info mt-2">Showing first 100 members only.</div>';
        }

        return response()->json(['html' => $html]);
    }

    public function index(Request $request)
    {
        $user = $request->user();

        // Get first clan for dashboard (or user's default clan)
        $clan = Clan::with('families')->first();

        if (!$clan) {
            return view('dashboard.index', [
                'stats' => $this->getEmptyStats(),
                'recentMembers' => collect(),
                'clans' => collect(),
                'families' => collect(),
                'clan' => null,
                'user' => $user,
                'totalParents' => 0,
                'totalChildren' => 0,
                'totalGrandchildren' => 0,
                'totalGreatGrandchildren' => 0,
                'totalGreatGreatGrandchildren' => 0,
                'totalGreatGreatGreatGrandchildren' => 0,
            ]);
        }

        // Get statistics (direct calculation to ensure freshness)
        $stats = $this->treeBuilder->getTreeStatistics($clan->id);

        // Get recent members
        $recentMembers = Member::with(['father', 'mother', 'family'])
            ->where('clan_id', $clan->id)
            ->latest()
            ->limit(10)
            ->get();

        // Get all clans for selector
        $clans = Clan::withCount('members')->get();

        // Get families in clan
        $families = Family::where('clan_id', $clan->id)
            ->withCount('members')
            ->get();

        // Family Hierarchy Stats
        $totalParents = Member::where('clan_id', $clan->id)->has('children')->count();
        
        $totalChildren = Member::where('clan_id', $clan->id)
            ->where(function($q) {
                $q->whereNotNull('father_id')->orWhereNotNull('mother_id');
            })->count();

        $totalGrandchildren = Member::where('clan_id', $clan->id)
            ->where(function($q) {
                $q->whereHas('father', function($sq) {
                    $sq->whereNotNull('father_id')->orWhereNotNull('mother_id');
                })->orWhereHas('mother', function($sq) {
                    $sq->whereNotNull('father_id')->orWhereNotNull('mother_id');
                });
            })->count();

        // Great-Grandchildren (Parents -> Grandparents -> Great-Grandparents)
        $totalGreatGrandchildren = Member::where('clan_id', $clan->id)
            ->where(function($q) {
                $q->whereHas('father.father', function($sq) {
                    $sq->whereNotNull('father_id')->orWhereNotNull('mother_id');
                })->orWhereHas('father.mother', function($sq) {
                    $sq->whereNotNull('father_id')->orWhereNotNull('mother_id');
                })->orWhereHas('mother.father', function($sq) {
                    $sq->whereNotNull('father_id')->orWhereNotNull('mother_id');
                })->orWhereHas('mother.mother', function($sq) {
                    $sq->whereNotNull('father_id')->orWhereNotNull('mother_id');
                });
            })->count();

        // Great-Great-Grandchildren
        $totalGreatGreatGrandchildren = Member::where('clan_id', $clan->id)
            ->where(function($q) {
                // Check if any Great-Grandparent has a parent
                $q->whereHas('father.father.father', fn($sq) => $sq->whereNotNull('father_id')->orWhereNotNull('mother_id'))
                  ->orWhereHas('father.father.mother', fn($sq) => $sq->whereNotNull('father_id')->orWhereNotNull('mother_id'))
                  ->orWhereHas('father.mother.father', fn($sq) => $sq->whereNotNull('father_id')->orWhereNotNull('mother_id'))
                  ->orWhereHas('father.mother.mother', fn($sq) => $sq->whereNotNull('father_id')->orWhereNotNull('mother_id'))
                  ->orWhereHas('mother.father.father', fn($sq) => $sq->whereNotNull('father_id')->orWhereNotNull('mother_id'))
                  ->orWhereHas('mother.father.mother', fn($sq) => $sq->whereNotNull('father_id')->orWhereNotNull('mother_id'))
                  ->orWhereHas('mother.mother.father', fn($sq) => $sq->whereNotNull('father_id')->orWhereNotNull('mother_id'))
                  ->orWhereHas('mother.mother.mother', fn($sq) => $sq->whereNotNull('father_id')->orWhereNotNull('mother_id'));
            })->count();

        // Great-Great-Great-Grandchildren
        $totalGreatGreatGreatGrandchildren = Member::where('clan_id', $clan->id)
            ->where(function($q) {
                // Check if any Great-Great-Grandparent has a parent
                // We check one path for brevity as an example, but strictly should check all 16 paths.
                // To avoid massive code duplication, we can rely on the fact that usually if one line extends, it's captured.
                // But for correctness, let's try to be reasonably comprehensive or simplify.
                // A cleaner way: Check if generation number >= 6 (if we trust generation_number field)
                // Since we have generation_number in the database, let's use that! It's much faster and cleaner.
                // Assuming Generation 1 is the root.
                // Children = Gen 2
                // Grandchildren = Gen 3
                // Great-Grandchildren = Gen 4
                // Great-Great-Grandchildren = Gen 5
                // Great-Great-Great-Grandchildren = Gen 6+
                
                // However, the previous stats were calculated dynamically. Let's stick to dynamic for consistency if possible,
                // OR switch to generation_number if it's reliable.
                // Let's try the dynamic approach for one more level, but simplified:
                // A member is a 3x-Great-Grandchild if they have a 2x-Great-Grandparent who has a parent.
                
                $q->whereHas('father', function($f) {
                    $f->whereHas('father.father.father', fn($sq) => $sq->whereNotNull('father_id')->orWhereNotNull('mother_id'))
                      ->orWhereHas('father.father.mother', fn($sq) => $sq->whereNotNull('father_id')->orWhereNotNull('mother_id'))
                      ->orWhereHas('father.mother.father', fn($sq) => $sq->whereNotNull('father_id')->orWhereNotNull('mother_id'))
                      ->orWhereHas('father.mother.mother', fn($sq) => $sq->whereNotNull('father_id')->orWhereNotNull('mother_id'));
                })->orWhereHas('mother', function($m) {
                    $m->whereHas('father.father.father', fn($sq) => $sq->whereNotNull('father_id')->orWhereNotNull('mother_id'))
                      ->orWhereHas('father.father.mother', fn($sq) => $sq->whereNotNull('father_id')->orWhereNotNull('mother_id'))
                      ->orWhereHas('father.mother.father', fn($sq) => $sq->whereNotNull('father_id')->orWhereNotNull('mother_id'))
                      ->orWhereHas('father.mother.mother', fn($sq) => $sq->whereNotNull('father_id')->orWhereNotNull('mother_id'));
                });
            })->count();

        return view('dashboard.index', compact(
            'stats',
            'recentMembers',
            'clans',
            'families',
            'clan',
            'user',
            'totalParents',
            'totalChildren',
            'totalGrandchildren',
            'totalGreatGrandchildren',
            'totalGreatGreatGrandchildren',
            'totalGreatGreatGreatGrandchildren'
        ));
    }

    protected function getEmptyStats(): array
    {
        return [
            'total_members' => 0,
            'alive_members' => 0,
            'deceased_members' => 0,
            'male_count' => 0,
            'female_count' => 0,
            'total_generations' => 0,
            'age_distribution' => collect(),
        ];
    }
}
