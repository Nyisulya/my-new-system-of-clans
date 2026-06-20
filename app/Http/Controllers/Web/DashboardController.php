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
        
        $query = Member::query();

        if ($category && str_starts_with($category, 'generation_')) {
            $genNum = (int) str_replace('generation_', '', $category);
            $query->where('generation_number', $genNum);
        } else {
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
        }

        $members = $query->select('id', 'first_name', 'middle_name', 'last_name', 'profile_photo')
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

        // Get statistics (direct calculation to ensure freshness) globally
        $stats = $this->treeBuilder->getTreeStatistics();

        // Get recent members globally
        $recentMembers = Member::with(['father', 'mother', 'family'])
            ->latest()
            ->limit(10)
            ->get();

        // Get all clans for selector
        $clans = Clan::withCount('members')->get();

        // Get families
        $families = Family::withCount('members')->get();

        // Get dynamic generations with member counts globally
        $maxGen = Member::max('generation_number') ?? 0;
        
        $generationCounts = [];
        for ($i = 1; $i <= $maxGen; $i++) {
            $generationCounts[$i] = 0;
        }

        $rawCounts = Member::select('generation_number', \DB::raw('count(*) as total'))
            ->groupBy('generation_number')
            ->get();

        foreach ($rawCounts as $rc) {
            $generationCounts[$rc->generation_number] = $rc->total;
        }

        return view('dashboard.index', compact(
            'stats',
            'recentMembers',
            'clans',
            'families',
            'clan',
            'user',
            'generationCounts'
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
