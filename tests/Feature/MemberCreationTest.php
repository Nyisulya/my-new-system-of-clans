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

    public function test_linked_user_create_route_redirects_to_self_parent(): void
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

        // Should redirect to members.create with father_id=member->id since user is male
        $response->assertRedirect(route('members.create', [
            'father_id' => $member->id,
            'clan_id' => $this->clan->id,
            'family_id' => $this->family->id,
        ]));
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

        $response->assertStatus(403);
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

    public function test_linked_user_cannot_create_child_for_others(): void
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

        $response->assertStatus(403);
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
}
