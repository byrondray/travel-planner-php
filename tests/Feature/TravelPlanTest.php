<?php

namespace Tests\Feature;

use App\Models\TravelPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TravelPlanTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_dashboard(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
    }

    public function test_dashboard_shows_only_own_plans(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $ownPlan = TravelPlan::factory()->create(['user_id' => $user->id, 'title' => 'My Trip']);
        $otherPlan = TravelPlan::factory()->create(['user_id' => $otherUser->id, 'title' => 'Other Trip']);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertSee('My Trip');
        $response->assertDontSee('Other Trip');
    }

    public function test_user_can_view_own_travel_plan(): void
    {
        $user = User::factory()->create();
        $plan = TravelPlan::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get("/travel-plans/{$plan->id}");

        $response->assertStatus(200);
    }

    public function test_user_cannot_view_other_users_travel_plan(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $plan = TravelPlan::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->get("/travel-plans/{$plan->id}");

        $response->assertStatus(403);
    }

    public function test_user_can_delete_own_travel_plan(): void
    {
        $user = User::factory()->create();
        $plan = TravelPlan::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete("/travel-plans/{$plan->id}");

        $response->assertRedirect(route('travel-plans.index'));
        $this->assertDatabaseMissing('travel_plans', ['id' => $plan->id]);
    }

    public function test_user_cannot_delete_other_users_travel_plan(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $plan = TravelPlan::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->delete("/travel-plans/{$plan->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('travel_plans', ['id' => $plan->id]);
    }

    public function test_user_can_view_processing_status(): void
    {
        $user = User::factory()->create();
        $plan = TravelPlan::factory()->create([
            'user_id' => $user->id,
            'processing_status' => 'processing',
        ]);

        $response = $this->actingAs($user)->getJson("/travel-plans/{$plan->id}/status");

        $response->assertStatus(200)
            ->assertJson(['processing_status' => 'processing']);
    }

    public function test_user_cannot_view_other_users_processing_status(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $plan = TravelPlan::factory()->create([
            'user_id' => $otherUser->id,
            'processing_status' => 'processing',
        ]);

        $response = $this->actingAs($user)->getJson("/travel-plans/{$plan->id}/status");

        $response->assertStatus(403);
    }

    public function test_completed_processing_redirects_to_show(): void
    {
        $user = User::factory()->create();
        $plan = TravelPlan::factory()->create([
            'user_id' => $user->id,
            'processing_status' => 'completed',
        ]);

        $response = $this->actingAs($user)->get("/travel-plans/{$plan->id}/processing");

        $response->assertRedirect(route('travel-plans.show', $plan->id));
    }

    public function test_generate_requires_authentication(): void
    {
        $response = $this->post('/travel-plans/generate', [
            'title' => 'Test Plan',
            'destination' => 'Paris',
            'start_date' => '2026-04-01',
            'end_date' => '2026-04-05',
        ]);

        $response->assertRedirect('/login');
    }

    public function test_generate_validates_required_fields(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/travel-plans/generate', []);

        $response->assertSessionHasErrors(['title', 'destination', 'start_date', 'end_date']);
    }

    public function test_generate_validates_end_date_after_start_date(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/travel-plans/generate', [
            'title' => 'Test Plan',
            'destination' => 'Paris',
            'start_date' => '2026-04-05',
            'end_date' => '2026-04-01',
        ]);

        $response->assertSessionHasErrors(['end_date']);
    }
}
