<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaderboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        User::factory()->create(['role' => 'volunteer', 'name' => 'Volunteer A', 'points' => 100]);
        User::factory()->create(['role' => 'volunteer', 'name' => 'Volunteer B', 'points' => 90]);
        User::factory()->create(['role' => 'volunteer', 'name' => 'Volunteer C', 'points' => 50]);
        User::factory()->create(['role' => 'volunteer', 'name' => 'Volunteer D', 'points' => 20]);
        User::factory()->create(['role' => 'volunteer', 'name' => 'Volunteer E', 'points' => 0]);
        Event::factory()->count(14)->create(['status' => 'published']);
    }

    public function test_LDB_TC01_view_summary_dashboard()
    {
        $user = User::where('name', 'Volunteer A')->first();
        
        $response = $this->actingAs($user)->get('/leaderboard');

        $response->assertStatus(200);
        $response->assertSee('Volunteer Leaderboard');
        $response->assertSee('Top Volunteers');
        $response->assertSee('Achievements');
        $response->assertSee('Quick Stats');
    }

    public function test_LDB_TC02_view_full_list()
    {
        $user = User::where('name', 'Volunteer A')->first();

        $response = $this->actingAs($user)->get('/leaderboard/full');

        $response->assertStatus(200);
        $response->assertSee('Volunteer A');
        $response->assertSee('Volunteer E');
    }

    public function test_LDB_TC03_sort_top_ranked()
    {
        $user = User::first();
        
        $response = $this->actingAs($user)->get('/leaderboard/full?sort=desc');

        $response->assertStatus(200);
        $response->assertSeeTextInOrder(['Volunteer A', 'Volunteer E']); 
    }

    public function test_LDB_TC04_sort_lowest_ranked()
    {
        $user = User::first();
        $response = $this->actingAs($user)->get('/leaderboard/full?sort=asc');
        $response->assertStatus(200);
        $response->assertSeeTextInOrder(['Volunteer E', 'Volunteer A']); 
    }

    public function test_LDB_TC05_user_rank_widget_calc()
    {
        $user = User::where('name', 'Volunteer A')->first();

        $response = $this->actingAs($user)->get('/leaderboard');

        $response->assertStatus(200);
        $response->assertSee('#1');
        $response->assertSee('100');
    }

    public function test_LDB_TC06_unauthenticated_access()
    {
        $response1 = $this->get('/leaderboard');
        $response1->assertRedirect('/login');

        $response2 = $this->get('/leaderboard/full');
        $response2->assertRedirect('/login');
    }

    public function test_LDB_TC07_invalid_sort_param_fallback()
    {
        $user = User::first();
        
        $response = $this->actingAs($user)->get('/leaderboard/full?sort=xyz');
        $response->assertStatus(200);
        $response->assertSeeTextInOrder(['Volunteer A', 'Volunteer E']);
    }

    public function test_LDB_TC08_quick_stats_accuracy()
    {
        $user = User::first();

        $response = $this->actingAs($user)->get('/leaderboard');

        $response->assertStatus(200);
        $response->assertSee('Total Volunteers');
        $response->assertSee('5'); 
        $response->assertSee('Events This Month');
        $response->assertSee('14');
    }
}