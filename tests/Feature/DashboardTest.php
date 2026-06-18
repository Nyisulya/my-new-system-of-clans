<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Clan;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Clan $clan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->clan = Clan::create([
            'name' => 'Test Clan',
            'description' => 'Test Description',
            'founding_date' => '2000-01-01',
            'is_active' => true,
        ]);

        $this->user = User::factory()->create([
            'member_id' => null,
        ]);
    }

    public function test_authenticated_user_can_access_dashboard(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('dashboard'));

        $response->assertStatus(200);
    }

    public function test_authenticated_user_can_get_generation_members(): void
    {
        $member = Member::create([
            'clan_id' => $this->clan->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'gender' => 'male',
            'status' => 'alive',
            'generation_number' => 5,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson(route('dashboard.members', ['category' => 'generation_5']))
            ->assertStatus(200);

        $response->assertJsonStructure(['html']);
        $this->assertStringContainsString('John Doe', $response->json('html'));
    }

    public function test_authenticated_user_can_get_living_members(): void
    {
        $member = Member::create([
            'clan_id' => $this->clan->id,
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'gender' => 'female',
            'status' => 'alive',
            'generation_number' => 1,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson(route('dashboard.members', ['category' => 'living_members']))
            ->assertStatus(200);

        $response->assertJsonStructure(['html']);
        $this->assertStringContainsString('Jane Doe', $response->json('html'));
    }
}
