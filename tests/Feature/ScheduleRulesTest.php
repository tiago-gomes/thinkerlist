<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\ScheduleRule;
use App\Enums\ErrorCode;
use Laravel\Sanctum\Sanctum;
use App\Jobs\CreateBookingJob;
use Queue;

class ScheduleRulesTest extends TestCase
{
    use RefreshDatabase;

    public function testIndexReturnsPaginatedScheduleRules(): void
    {
        // Create a user and authenticate
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create some schedule rules for the authenticated user
        ScheduleRule::factory()->count(15)->create(['user_id' => $user->id]);

        // Make a GET request to the index action
        $response = $this->json('GET', '/api/schedule-rules');

        // Assert that the response is successful
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'current_page',
            'data' => [
                '*' => [
                    'title',
                    'description',
                    'is_recurring',
                    'is_custom',
                ],
            ],
            'first_page_url',
            'from',
            'last_page',
            'last_page_url',
            'links' => [
                '*' => [
                    'url',
                    'label',
                    'active',
                ],
            ],
            'next_page_url',
            'path',
            'per_page',
            'prev_page_url',
            'to',
            'total',
        ]);

        // Assert that the pagination information is present
        $response->assertJsonFragment([
            'current_page' => 1,
            'per_page' => 10, // Default perPage value
            'from' => 1,
            'to' => 10,
            'total' => 15,
        ]);
    }

    public function testIndexAppliesSearchFilters(): void
    {
        // Create a user and authenticate
        $user = User::factory()->create();

        $this->actingAs($user);

        // Create schedule rules for the authenticated user with specific titles and descriptions
        ScheduleRule::factory()->create([
            'user_id' => $user->id,
            'title' => 'Meeting 1',
            'description' => 'Team Meeting',
            'is_custom' => true
        ]);
        ScheduleRule::factory()->create([
            'user_id' => $user->id,
            'title' => 'Meeting 2',
            'description' => 'Project Discussion',
            'is_recurring' => true
        ]);

        // Make a GET request to the index action with a search term
        $response = $this->json('GET', '/api/schedule-rules', ['search' => 'Meeting']);

        // Assert that the response contains only the matching schedule rules
        $response->assertJsonFragment(['title' => 'Meeting 1', 'description' => 'Team Meeting']);
        $response->assertJsonFragment(['title' => 'Meeting 2', 'description' => 'Project Discussion']);
    }

    public function testIndexAppliesRecurringFilter(): void
    {
        // Create a user and authenticate
        $user = User::factory()->create();

        $this->actingAs($user);

        // Create a recurring schedule rule for the authenticated user
        ScheduleRule::factory()->create(['user_id' => $user->id, 'is_recurring' => true]);

        // Make a GET request to the index action with the recurring filter
        $response = $this->json('GET', '/api/schedule-rules', ['is_recurring' => true]);

        // Assert that the response contains the recurring schedule rule
        $response->assertJsonFragment(['is_recurring' => true]);
    }

    public function testIndexAppliesCustomFilter(): void
    {
        // Create a user and authenticate
        $user = User::factory()->create();

        // mockup authenticated user
        $this->actingAs($user);

        // Create a recurring schedule rule for the authenticated user
        ScheduleRule::factory()->create(['user_id' => $user->id, 'is_custom' => true]);

        // Make a GET request to the index action with the recurring filter
        $response = $this->json('GET', '/api/schedule-rules', ['is_custom' => true]);

        // Assert that the response contains the recurring schedule rule
        $response->assertJsonFragment(['is_custom' => true]);
    }

    public function testCreateScheduleRuleDaily()
    {
        Queue::fake();

        // generate a user
        $user = User::factory()->create();

        // Act as the authenticated user with Sanctum
        Sanctum::actingAs($user);

        $params = [
            'title' => 'test',
            'description' => 'test',
            'is_recurring' => true,
            'recurring_type' => 1, // Daily
            'recurring_duration_start_date' => '2024-03-01',
            'recurring_type_duration' => 5,
            'recurring_start_time' => '09:00',
            'recurring_end_time' => '17:00',
            'recurring_duration_minutes' => 60,
            'recurring_interval_minutes' => 15,
            'recurring_ignore_weekends' => true,
            'is_custom' => false,
        ];

        // Make a POST request to the store action with the data
        $response = $this->json('POST', '/api/schedule-rules', $params);
        $response->assertStatus(ErrorCode::CREATED->value);

        // Assert that the response contains the recurring schedule rule
        $response->assertJsonFragment([
            'is_recurring' => true,
            'recurring_type' => 1, // Daily
            'recurring_duration_start_date' => '2024-03-01',
            'recurring_type_duration' => 5,
            'recurring_start_time' => '09:00',
            'recurring_end_time' => '17:00',
            'recurring_duration_minutes' => 60,
            'recurring_interval_minutes' => 15,
            'recurring_ignore_weekends' => true,
        ]);

        // Assert that a schedule rule with the given title and user_id exists in the database
        $this->assertDatabaseHas('schedule_rules', [
            'title' => 'test',
            'user_id' => $user->id,
        ]);

        // Assert that the CreateBookingJob was added to the queue
        Queue::assertPushed(CreateBookingJob::class);
    }

    public function testCreateScheduleRuleDailyWithWeekends()
    {
        Queue::fake();

        // generate a user
        $user = User::factory()->create();

        // Act as the authenticated user with Sanctum
        Sanctum::actingAs($user);

        $params = [
            'title' => 'test with weekends',
            'description' => 'test',
            'is_recurring' => true,
            'recurring_type' => 1, // Daily
            'recurring_duration_start_date' => '2024-03-01',
            'recurring_type_duration' => 5,
            'recurring_start_time' => '09:00',
            'recurring_end_time' => '17:00',
            'recurring_duration_minutes' => 60,
            'recurring_interval_minutes' => 15,
            'recurring_ignore_weekends' => false,
            'is_custom' => false,
        ];

        // Make a POST request to the store action with the data
        $response = $this->json('POST', '/api/schedule-rules', $params);
        $response->assertStatus(ErrorCode::CREATED->value);

        // Assert that the response contains the recurring schedule rule
        $response->assertJsonFragment([
            'is_recurring' => true,
            'recurring_type' => 1, // Daily
            'recurring_duration_start_date' => '2024-03-01',
            'recurring_type_duration' => 5,
            'recurring_start_time' => '09:00',
            'recurring_end_time' => '17:00',
            'recurring_duration_minutes' => 60,
            'recurring_interval_minutes' => 15,
            'recurring_ignore_weekends' => false,
        ]);

        // Assert that a schedule rule with the given title and user_id exists in the database
        $this->assertDatabaseHas('schedule_rules', [
            'title' => 'test with weekends',
            'user_id' => $user->id,
        ]);

        // Assert that the CreateBookingJob was added to the queue
        Queue::assertPushed(CreateBookingJob::class);
    }
}
