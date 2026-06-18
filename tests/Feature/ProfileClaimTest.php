<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileClaimTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_access_profile_claiming(): void
    {
        $this->get(route('profile.claim.search'))
            ->assertRedirect(route('login'));
    }

    public function test_linked_users_are_redirected_away_from_profile_claiming(): void
    {
        $member = Member::create([
            'first_name' => 'Juma',
            'last_name' => 'Kashinde',
            'gender' => 'male',
            'status' => 'alive',
        ]);

        $user = User::factory()->create([
            'member_id' => $member->id,
        ]);

        $this->actingAs($user)
            ->get(route('profile.claim.search'))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error');
    }

    public function test_unlinked_users_can_access_profile_claiming_form(): void
    {
        $user = User::factory()->create([
            'member_id' => null,
        ]);

        $this->actingAs($user)
            ->get(route('profile.claim.search'))
            ->assertStatus(200)
            ->assertViewIs('members.claim_search');
    }

    public function test_unlinked_users_can_search_for_unlinked_profiles(): void
    {
        $user = User::factory()->create([
            'member_id' => null,
        ]);

        // Create an unlinked profile
        $unlinkedMember = Member::create([
            'first_name' => 'Juma',
            'last_name' => 'Karori',
            'gender' => 'male',
            'status' => 'alive',
        ]);

        // Create a linked profile
        $linkedMember = Member::create([
            'first_name' => 'Juma',
            'last_name' => 'Kashinde',
            'gender' => 'male',
            'status' => 'alive',
        ]);
        User::factory()->create([
            'member_id' => $linkedMember->id,
        ]);

        $response = $this->actingAs($user)
            ->getJson(route('profile.claim.ajax_search', ['query' => 'Juma']))
            ->assertStatus(200);

        $response->assertJsonFragment([
            'full_name' => 'Juma Karori',
        ]);

        $response->assertJsonMissing([
            'full_name' => 'Juma Kashinde',
        ]);
    }

    public function test_unlinked_user_can_claim_an_unlinked_profile(): void
    {
        $user = User::factory()->create([
            'member_id' => null,
        ]);

        $member = Member::create([
            'first_name' => 'Juma',
            'last_name' => 'Karori',
            'gender' => 'male',
            'status' => 'alive',
        ]);

        $response = $this->actingAs($user)
            ->post(route('profile.claim.submit'), [
                'member_id' => $member->id,
            ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertEquals($member->id, $user->fresh()->member_id);
    }

    public function test_user_cannot_claim_an_already_linked_profile(): void
    {
        $user = User::factory()->create([
            'member_id' => null,
        ]);

        $member = Member::create([
            'first_name' => 'Juma',
            'last_name' => 'Karori',
            'gender' => 'male',
            'status' => 'alive',
        ]);

        // Link the profile to another user
        User::factory()->create([
            'member_id' => $member->id,
        ]);

        $response = $this->actingAs($user)
            ->post(route('profile.claim.submit'), [
                'member_id' => $member->id,
            ]);

        $response->assertSessionHas('error');
        $this->assertNull($user->fresh()->member_id);
    }

    public function test_unlinked_users_can_search_for_profiles_without_date_of_birth(): void
    {
        $user = User::factory()->create([
            'member_id' => null,
        ]);

        $member = Member::create([
            'first_name' => 'Juma',
            'last_name' => 'Karori',
            'gender' => 'male',
            'status' => 'alive',
            'date_of_birth' => null,
        ]);

        $response = $this->actingAs($user)
            ->getJson(route('profile.claim.ajax_search', ['query' => 'Juma']))
            ->assertStatus(200);

        $response->assertJsonFragment([
            'full_name' => 'Juma Karori',
            'date_of_birth' => __('common.not_specified'),
            'has_dob' => false,
            'parents_desc' => null,
            'has_parents' => false,
        ]);
    }

    public function test_unlinked_users_can_search_for_profiles_with_parents(): void
    {
        $user = User::factory()->create([
            'member_id' => null,
        ]);

        $father = Member::create([
            'first_name' => 'Michael',
            'last_name' => 'Doe',
            'gender' => 'male',
            'status' => 'alive',
        ]);

        $mother = Member::create([
            'first_name' => 'Grace',
            'last_name' => 'Doe',
            'gender' => 'female',
            'status' => 'alive',
        ]);

        $member = Member::create([
            'first_name' => 'Juma',
            'last_name' => 'Karori',
            'gender' => 'male',
            'status' => 'alive',
            'date_of_birth' => null,
            'father_id' => $father->id,
            'mother_id' => $mother->id,
        ]);

        $response = $this->actingAs($user)
            ->getJson(route('profile.claim.ajax_search', ['query' => 'Juma']))
            ->assertStatus(200);

        $response->assertJsonFragment([
            'full_name' => 'Juma Karori',
            'date_of_birth' => __('common.not_specified'),
            'has_dob' => false,
            'parents_desc' => __('common.child_of_both', ['father' => $father->full_name, 'mother' => $mother->full_name]),
            'has_parents' => true,
        ]);
    }
}
