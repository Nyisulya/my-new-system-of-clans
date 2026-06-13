<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Clan;
use App\Models\Family;
use App\Models\Branch;
use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;
use App\Services\ImageService;
use App\Services\DuplicateDetectionService;
use App\Services\GeocodingService;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function __construct(
        protected ImageService $imageService,
        protected DuplicateDetectionService $duplicateService,
        protected GeocodingService $geocodingService
    ) {
        // Authorization handled by policies
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Check if user can view members
        $this->authorize('viewAny', Member::class);
        
        $query = Member::with(['clan', 'family', 'branch', 'father', 'mother', 'marriagesAsHusband.wife', 'marriagesAsWife.husband']);

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filters
        if ($request->filled('gender')) {
            $query->byGender($request->gender);
        }

        if ($request->filled('status')) {
            if ($request->status === 'alive') {
                $query->alive();
            } elseif ($request->status === 'deceased') {
                $query->deceased();
            }
        }

        if ($request->filled('generation')) {
            $query->byGeneration($request->generation);
        }

        if ($request->filled('family_id')) {
            $query->where('family_id', $request->family_id);
        }

        if ($request->filled('clan_id')) {
            $query->where('clan_id', $request->clan_id);
        }

        // Hierarchy Category Filter
        if ($request->filled('category')) {
            switch ($request->category) {
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
            }
        }

        // Sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $members = $query->paginate(20)->appends($request->query());

        // Get filter options
        $clans = Clan::all();
        $families = Family::all();

        return view('members.index', compact('members', 'clans', 'families'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $clans = Clan::with('families')->get();
        $potentialFathers = Member::where('gender', 'male')->orderBy('first_name')->get();
        $potentialMothers = Member::where('gender', 'female')->orderBy('first_name')->get();
        $potentialSpouses = Member::orderBy('first_name')->get();

        $selectedFatherId = $request->input('father_id');
        $selectedMotherId = $request->input('mother_id');
        $selectedClanId = $request->input('clan_id');
        $selectedFamilyId = $request->input('family_id');

        $selectedSpouseId = $request->input('spouse_id');
        $selectedSpouse = $selectedSpouseId ? Member::find($selectedSpouseId) : null;

        return view('members.create', compact(
            'clans',
            'potentialFathers',
            'potentialMothers',
            'potentialSpouses',
            'selectedFatherId',
            'selectedMotherId',
            'selectedClanId',
            'selectedFamilyId',
            'selectedSpouse'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMemberRequest $request)
    {
        // 1. Check for Exact Duplicate (Strict Block)
        $exactMatch = $this->duplicateService->checkExactMatch(
            $request->first_name,
            $request->last_name,
            $request->date_of_birth,
            $request->father_id,
            $request->mother_id
        );

        if ($exactMatch) {
            return back()
                ->withInput()
                ->with('error', 'This person already exists in the family tree. Would you like to view or update the existing profile instead?')
                ->with('existing_member_id', $exactMatch->id);
        }

        // 2. Check for Partial Duplicate (Warning)
        // Only check if user hasn't already confirmed
        if (!$request->has('confirm_duplicate')) {
            $partialMatches = $this->duplicateService->checkPartialMatch(
                $request->first_name,
                $request->last_name,
                $request->date_of_birth
            );

            if ($partialMatches->isNotEmpty()) {
                return back()
                    ->withInput()
                    ->with('warning', 'A similar record exists. Confirm if you are creating a different person.')
                    ->with('partial_matches', $partialMatches);
            }
        }

        $data = $request->validated();
        $data['created_by'] = $request->user()->id;

        // Handle clan_name and family_name for spouses (manual text input)
        if ($request->filled('clan_name')) {
            // Find or create clan by name
            $clan = Clan::firstOrCreate(
                ['name' => $request->clan_name],
                [
                    'description' => 'Auto-created from spouse entry',
                    'is_spouse_clan' => true  // Mark as spouse clan to hide from sidebar
                ]
            );
            $data['clan_id'] = $clan->id;
        }

        if ($request->filled('family_name')) {
            // Find or create family by name
            $family = Family::firstOrCreate(
                [
                    'name' => $request->family_name,
                    'clan_id' => $data['clan_id']
                ],
                [
                    'surname' => $request->last_name ?? $request->family_name,
                    'description' => 'Auto-created from spouse entry'
                ]
            );
            $data['family_id'] = $family->id;
        }

        // Handle parent names and branch name for spouses (manual text input)
        $additionalNotes = [];
        
        if ($request->filled('father_name')) {
            $additionalNotes[] = "Father: " . $request->father_name;
        }
        
        if ($request->filled('mother_name')) {
            $additionalNotes[] = "Mother: " . $request->mother_name;
        }
        
        if ($request->filled('branch_name')) {
            // Find or create branch
            $branch = Branch::firstOrCreate(
                [
                    'name' => $request->branch_name,
                    'family_id' => $data['family_id']
                ],
                [
                    'description' => 'Auto-created from spouse entry'
                ]
            );
            $data['branch_id'] = $branch->id;
        }
        
        // Append additional notes if any
        if (!empty($additionalNotes)) {
            $existingNotes = $data['notes'] ?? '';
            $newNotes = implode("\n", $additionalNotes);
            $data['notes'] = trim($existingNotes . "\n\n" . $newNotes);
        }

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            try {
                $upload = $this->imageService->uploadProfilePhoto($request->file('profile_photo'));
                $data['profile_photo'] = $upload['path'];
            } catch (\Exception $e) {
                return back()
                    ->withInput()
                    ->with('error', 'Failed to upload photo: ' . $e->getMessage());
            }
        }

        // Geocode Birth Place
        if (!empty($data['place_of_birth'])) {
            $coords = $this->geocodingService->getCoordinates($data['place_of_birth']);
            if ($coords) {
                $data['birth_lat'] = $coords['lat'];
                $data['birth_lng'] = $coords['lng'];
                $data['birth_place'] = $data['place_of_birth']; // Ensure consistency
            }
        }

        // Geocode Current Location (Street + Address + City + Country)
        $parts = array_filter([$data['street'] ?? null, $data['address'] ?? null, $data['city'] ?? null, $data['country'] ?? null]);
        $currentLocation = implode(', ', $parts);
        
        if (!empty($currentLocation)) {
            $coords = $this->geocodingService->getCoordinates($currentLocation);
            if ($coords) {
                $data['current_lat'] = $coords['lat'];
                $data['current_lng'] = $coords['lng'];
                $data['current_location'] = $currentLocation;
            }
        }

        // Handle Spouse Creation
        $spouseToMarry = null;
        if ($request->filled('spouse_name')) {
            $spouseNameParts = explode(' ', $request->spouse_name, 2);
            $spouseFirstName = $spouseNameParts[0];
            $spouseLastName = $spouseNameParts[1] ?? $request->last_name;

            $spouseData = [
                'first_name' => $spouseFirstName,
                'last_name' => $spouseLastName,
                'gender' => $request->gender === 'male' ? 'female' : 'male',
                'date_of_birth' => now()->subYears(30), // Placeholder date
                'clan_id' => $request->clan_id,
                'family_id' => $request->family_id,
                'status' => 'alive',
                'created_by' => $request->user()->id,
            ];

            $spouseToMarry = Member::create($spouseData);
        } elseif ($request->filled('spouse_id')) {
            $spouseToMarry = Member::find($request->spouse_id);
        }

        $member = Member::create($data);

        // Create marriage record if spouse exists
        if ($spouseToMarry) {
            $marriageData = [
                'husband_id' => $member->gender === 'male' ? $member->id : $spouseToMarry->id,
                'wife_id' => $member->gender === 'male' ? $spouseToMarry->id : $member->id,
                'status' => 'active',
                'created_by' => $request->user()->id,
            ];

            \App\Models\Marriage::create($marriageData);
        }

        // Redirect to parents page if Generation 1, otherwise to dashboard
        if (isset($data['generation_number']) && $data['generation_number'] == 1) {
            return redirect()
                ->route('parents.index')
                ->with('success', 'Founder added successfully! They now appear in the list below.');
        }

        return redirect()
            ->route('members.dashboard', $member)
            ->with('success', 'Member created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Member $member)
    {
        $member->load([
            'clan',
            'family',
            'branch',
            'father',
            'mother',
            'marriagesAsHusband.wife',
            'marriagesAsWife.husband',
            'childrenAsFather',
            'childrenAsMother',
            'creator',
            'updater'
        ]);

        // Get siblings
        $siblings = collect();
        if ($member->father_id || $member->mother_id) {
            $siblings = Member::where('id', '!=', $member->id)
                ->where(function ($q) use ($member) {
                    if ($member->father_id && $member->mother_id) {
                        $q->where('father_id', $member->father_id)
                          ->where('mother_id', $member->mother_id);
                    } elseif ($member->father_id) {
                        $q->where('father_id', $member->father_id);
                    } else {
                        $q->where('mother_id', $member->mother_id);
                    }
                })
                ->get();
        }

        return view('members.show', compact('member', 'siblings'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Member $member)
    {
        $clans = Clan::with('families')->get();
        $families = Family::where('clan_id', $member->clan_id)->get();
        $branches = Branch::where('family_id', $member->family_id)->get();
        
        $potentialFathers = Member::where('gender', 'male')
            ->where('id', '!=', $member->id)
            ->orderBy('first_name')
            ->get();
        
        $potentialMothers = Member::where('gender', 'female')
            ->where('id', '!=', $member->id)
            ->orderBy('first_name')
            ->get();
        
        $potentialSpouses = Member::where('id', '!=', $member->id)
            ->orderBy('first_name')
            ->get();

        return view('members.edit', compact(
            'member',
            'clans',
            'families',
            'branches',
            'potentialFathers',
            'potentialMothers',
            'potentialSpouses'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMemberRequest $request, Member $member)
    {
        $data = $request->validated();
        $data['updated_by'] = $request->user()->id;

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            try {
                // Delete old photo
                if ($member->profile_photo) {
                    $this->imageService->deleteImage($member->profile_photo);
                }

                $upload = $this->imageService->uploadProfilePhoto($request->file('profile_photo'));
                $data['profile_photo'] = $upload['path'];
            } catch (\Exception $e) {
                return back()
                    ->withInput()
                    ->with('error', 'Failed to upload photo: ' . $e->getMessage());
            }
        }

        // Geocode Birth Place if changed
        if ($member->place_of_birth !== ($data['place_of_birth'] ?? null)) {
            if (!empty($data['place_of_birth'])) {
                $coords = $this->geocodingService->getCoordinates($data['place_of_birth']);
                if ($coords) {
                    $data['birth_lat'] = $coords['lat'];
                    $data['birth_lng'] = $coords['lng'];
                    $data['birth_place'] = $data['place_of_birth'];
                }
            } else {
                $data['birth_lat'] = null;
                $data['birth_lng'] = null;
                $data['birth_place'] = null;
            }
        }

        // Geocode Current Location if City, Country, Address, or Street changed
        $newStreet = $data['street'] ?? $member->street;
        $newCity = $data['city'] ?? $member->city;
        $newCountry = $data['country'] ?? $member->country;
        $newAddress = $data['address'] ?? $member->address;
        
        if (
            $member->city !== ($data['city'] ?? null) || 
            $member->country !== ($data['country'] ?? null) ||
            $member->address !== ($data['address'] ?? null) ||
            $member->street !== ($data['street'] ?? null)
        ) {
            // Construct precise address:  "Street, Address, City, Country"
            $parts = array_filter([$newStreet, $newAddress, $newCity, $newCountry]);
            $currentLocation = implode(', ', $parts);
            
            if (!empty($currentLocation)) {
                $coords = $this->geocodingService->getCoordinates($currentLocation);
                if ($coords) {
                    $data['current_lat'] = $coords['lat'];
                    $data['current_lng'] = $coords['lng'];
                    $data['current_location'] = $currentLocation;
                }
            } else {
                $data['current_lat'] = null;
                $data['current_lng'] = null;
                $data['current_location'] = null;
            }
        }

        $member->update($data);

        return redirect()
            ->route('members.show', $member)
            ->with('success', 'Member updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Member $member)
    {
        // Delete profile photo
        if ($member->profile_photo) {
            $this->imageService->deleteImage($member->profile_photo);
        }

        $generation = $member->generation_number;
        $member->delete();

        if ($generation == 1) {
            return redirect()
                ->route('parents.index')
                ->with('success', 'Founder deleted successfully!');
        }

        return redirect()
            ->route('members.index')
            ->with('success', 'Member deleted successfully!');
    }


    /**
     * Display the member dashboard.
     */
    public function dashboard(Member $member)
    {
        $member->load(['childrenAsFather', 'childrenAsMother', 'clan', 'family']);
        $children = $member->children()->get();
        
        return view('members.dashboard', compact('member', 'children'));
    }

    /**
     * Display all Generation 1 members (first generation/founders).
     */
    public function parents()
    {
        $parents = Member::where('generation_number', 1)
            ->whereHas('clan', function ($query) {
                $query->where('is_spouse_clan', false);
            })
            ->with(['clan', 'family', 'childrenAsFather', 'childrenAsMother'])
            ->orderBy('clan_id')
            ->orderBy('family_id')
            ->orderBy('first_name')
            ->get();
        
        return view('members.parents', compact('parents'));
    }
}
