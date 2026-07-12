<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Clan;
use App\Models\Family;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberCreationTest extends TestCase
{
    use RefreshDatabase;

    protected Clan $clan;
    protected Family $family;

    protected function setUp(): void
    {
        parent::setUp();

        $this->clan = Clan::create([
            'name' => 'Linage Clan',
            'description' => 'Test Description',
            'founding_date' => '2000-01-01',
            'is_active' => true,
        ]);

        $this->family = Family::create([
            'clan_id' => $this->clan->id,
            'name' => 'Main Lineage Family',
            'surname' => 'Lineage',
            'established_date' => '2000-01-01',
            'is_active' => true,
        ]);
    }

    public function test_unlinked_user_can_access_creation_form(): void
    {
        $user = User::factory()->create(['member_id' => null]);

        $response = $this->actingAs($user)
            ->get(route('members.create'));

        $response->assertStatus(200);
    }

    public function test_unlinked_user_can_create_initial_profile(): void
    {
        $user = User::factory()->create(['member_id' => null]);

        $response = $this->actingAs($user)
            ->post(route('members.store'), [
                'first_name' => 'Initial',
                'last_name' => 'User',
                'gender' => 'male',
                'clan_id' => $this->clan->id,
                'family_id' => $this->family->id,
                'status' => 'alive',
            ]);

        $response->assertRedirect();
        $this->assertNotNull($user->fresh()->member_id);
    }

    public function test_linked_user_create_route_does_not_redirect_to_self_parent(): void
    {
        $member = Member::create([
            'clan_id' => $this->clan->id,
            'family_id' => $this->family->id,
            'first_name' => 'Linked',
            'last_name' => 'User',
            'gender' => 'male',
            'status' => 'alive',
        ]);
        $user = User::factory()->create(['member_id' => $member->id]);

        $response = $this->actingAs($user)
            ->get(route('members.create'));

        // Should return 200 OK directly, allowing them to add anyone instead of redirecting
        $response->assertStatus(200);
    }

    public function test_linked_user_can_access_create_form_with_self_as_father(): void
    {
        $member = Member::create([
            'clan_id' => $this->clan->id,
            'family_id' => $this->family->id,
            'first_name' => 'Linked',
            'last_name' => 'User',
            'gender' => 'male',
            'status' => 'alive',
        ]);
        $user = User::factory()->create(['member_id' => $member->id]);

        $response = $this->actingAs($user)
            ->get(route('members.create', ['father_id' => $member->id]));

        $response->assertStatus(200);
    }

    public function test_linked_user_cannot_access_create_form_for_others(): void
    {
        $member = Member::create([
            'clan_id' => $this->clan->id,
            'family_id' => $this->family->id,
            'first_name' => 'Linked',
            'last_name' => 'User',
            'gender' => 'male',
            'status' => 'alive',
        ]);
        $otherMember = Member::create([
            'clan_id' => $this->clan->id,
            'family_id' => $this->family->id,
            'first_name' => 'Other',
            'last_name' => 'User',
            'gender' => 'male',
            'status' => 'alive',
        ]);
        $user = User::factory()->create(['member_id' => $member->id]);

        $response = $this->actingAs($user)
            ->get(route('members.create', ['father_id' => $otherMember->id]));

        $response->assertStatus(200);
    }

    public function test_linked_user_can_create_child_when_father(): void
    {
        $member = Member::create([
            'clan_id' => $this->clan->id,
            'family_id' => $this->family->id,
            'first_name' => 'Linked',
            'last_name' => 'User',
            'gender' => 'male',
            'status' => 'alive',
        ]);
        $user = User::factory()->create(['member_id' => $member->id]);

        $response = $this->actingAs($user)
            ->post(route('members.store'), [
                'first_name' => 'Child',
                'last_name' => 'User',
                'gender' => 'female',
                'clan_id' => $this->clan->id,
                'family_id' => $this->family->id,
                'status' => 'alive',
                'father_id' => $member->id,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('members', [
            'first_name' => 'Child',
            'father_id' => $member->id,
        ]);
    }

    public function test_linked_user_can_create_child_for_others(): void
    {
        $member = Member::create([
            'clan_id' => $this->clan->id,
            'family_id' => $this->family->id,
            'first_name' => 'Linked',
            'last_name' => 'User',
            'gender' => 'male',
            'status' => 'alive',
        ]);
        $otherMember = Member::create([
            'clan_id' => $this->clan->id,
            'family_id' => $this->family->id,
            'first_name' => 'Other',
            'last_name' => 'User',
            'gender' => 'male',
            'status' => 'alive',
        ]);
        $user = User::factory()->create(['member_id' => $member->id]);

        $response = $this->actingAs($user)
            ->post(route('members.store'), [
                'first_name' => 'Child',
                'last_name' => 'User',
                'gender' => 'female',
                'clan_id' => $this->clan->id,
                'family_id' => $this->family->id,
                'status' => 'alive',
                'father_id' => $otherMember->id,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('members', [
            'first_name' => 'Child',
            'father_id' => $otherMember->id,
        ]);
    }

    public function test_admin_can_create_any_member(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'member_id' => null]);
        $otherMember = Member::create([
            'clan_id' => $this->clan->id,
            'family_id' => $this->family->id,
            'first_name' => 'Other',
            'last_name' => 'User',
            'gender' => 'male',
            'status' => 'alive',
        ]);

        $response = $this->actingAs($admin)
            ->post(route('members.store'), [
                'first_name' => 'Any',
                'last_name' => 'Member',
                'gender' => 'male',
                'clan_id' => $this->clan->id,
                'family_id' => $this->family->id,
                'status' => 'alive',
                'father_id' => $otherMember->id,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('members', [
            'first_name' => 'Any',
            'father_id' => $otherMember->id,
        ]);
    }

    public function test_can_create_deceased_member_without_date_of_death(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'member_id' => null]);
        
        $response = $this->actingAs($admin)
            ->post(route('members.store'), [
                'first_name' => 'Deceased',
                'last_name' => 'Ancestor',
                'gender' => 'male',
                'clan_id' => $this->clan->id,
                'family_id' => $this->family->id,
                'status' => 'deceased',
                'date_of_death' => null,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('members', [
            'first_name' => 'Deceased',
            'last_name' => 'Ancestor',
            'status' => 'deceased',
            'date_of_death' => null,
        ]);
    }

    public function test_can_create_member_without_last_name(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'member_id' => null]);
        
        $response = $this->actingAs($admin)
            ->post(route('members.store'), [
                'first_name' => 'LegacyNameOnly',
                'last_name' => null,
                'gender' => 'male',
                'clan_id' => $this->clan->id,
                'family_id' => $this->family->id,
                'status' => 'alive',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('members', [
            'first_name' => 'LegacyNameOnly',
            'last_name' => null,
        ]);
    }

    public function test_can_create_spouse_without_clan(): void
    {
        $member = Member::create([
            'clan_id' => $this->clan->id,
            'family_id' => $this->family->id,
            'first_name' => 'Existing',
            'last_name' => 'Member',
            'gender' => 'male',
            'status' => 'alive',
        ]);
        $user = User::factory()->create(['role' => 'admin', 'member_id' => null]);
        
        $response = $this->actingAs($user)
            ->post(route('members.store'), [
                'first_name' => 'SpouseNoClan',
                'last_name' => 'Wife',
                'gender' => 'female',
                'spouse_id' => $member->id,
                'status' => 'alive',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('members', [
            'first_name' => 'SpouseNoClan',
            'clan_id' => null,
        ]);
    }

    public function test_spouse_generation_is_synchronized_with_husband(): void
    {
        $ancestor1 = Member::create([
            'clan_id' => $this->clan->id,
            'family_id' => $this->family->id,
            'first_name' => 'Ancestor1',
            'last_name' => 'Name',
            'gender' => 'male',
            'status' => 'alive',
            'generation_number' => 1,
        ]);
        $ancestor2 = Member::create([
            'clan_id' => $this->clan->id,
            'family_id' => $this->family->id,
            'first_name' => 'Ancestor2',
            'last_name' => 'Name',
            'gender' => 'male',
            'father_id' => $ancestor1->id,
            'status' => 'alive',
            'generation_number' => 2,
        ]);
        $husband = Member::create([
            'clan_id' => $this->clan->id,
            'family_id' => $this->family->id,
            'first_name' => 'HusbandGen3',
            'last_name' => 'Name',
            'gender' => 'male',
            'father_id' => $ancestor2->id,
            'status' => 'alive',
            'generation_number' => 3,
        ]);

        $user = User::factory()->create(['role' => 'admin', 'member_id' => null]);
        
        $response = $this->actingAs($user)
            ->post(route('members.store'), [
                'first_name' => 'WifeGen3',
                'last_name' => 'Name',
                'gender' => 'female',
                'spouse_id' => $husband->id,
                'status' => 'alive',
            ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('members', [
            'first_name' => 'WifeGen3',
            'generation_number' => 3,
        ]);
    }
}

