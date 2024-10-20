<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Part;
use App\Services\PartService;
use App\Models\Episode;
use Tests\TestCase;
use Illuminate\Http\Response;

class PartControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $partService;

    protected function setUp(): void
    {
        parent::setUp();
        // Initialize the PartService
        $this->partService = $this->app->make(PartService::class);
    }

    public function testGetAllPartsReturns200JsonResponse()
    {
        $episodeId = 1;

        // Arrange: Create an episode and parts
        Episode::factory()
            ->create(['name'=>'test']);

        // Create parts for the episode
        Part::factory()->count(3)
            ->create(['episode_id' => $episodeId]);

        // Act: Call the method
        $response = $this->getJson(route('parts.all', ['episodeId' => $episodeId]));

        // Assert: Check the response
        $response->assertStatus(200);
    }

    public function testGetAllPartsReturns404ForNonExistentEpisode()
    {
        // Arrange: Define a non-existent episode ID
        $nonExistentEpisodeId = 999;

        // Act: Call the method
        $response = $this->getJson(route('parts.all', ['episodeId' => $nonExistentEpisodeId]));

        // Assert: Check the response
        $response->assertStatus(404); // Adjust according to your episode existence check
    }

    public function testGetAllPartsReturnsEmptyArrayForEpisodeWithNoParts()
    {
        $episodeId = 2;

        // Arrange: Create an episode without parts
        Episode::factory()
            ->count(2)
            ->create();

        // Act: Call the method
        $response = $this->getJson(route('parts.all', ['episodeId' => $episodeId]));

        // Assert: Check the response
        $response->assertStatus(200);
        $response->assertJson([]); // Should return an empty array
    }

    public function test_create_part_success()
    {
        // Create a sample episode
        $episode = Episode::factory()->create();

        $data = [
            'episode_id' => $episode->id,
            'position' => 1,
        ];

        $response = $this->json('POST', '/api/episodes/parts', $data);

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['id', 'episode_id', 'position']]);

        $this->assertDatabaseHas('parts', [
            'episode_id' => $episode->id,
            'id' => 1,
            'position' => 1,
        ]);
    }

    public function test_create_part_validation_failures()
    {
        // Test required fields
        $response = $this->json('POST', '/api/episodes/parts', []);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        // Test invalid episode_id
        $response = $this->json('POST', '/api/episodes/parts', [
            'episode_id' => 999, // Non-existent ID
            'part_id' => 1,
            'position' => 1,
        ]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        // Test non-unique position for existing episode
        $existingEpisode = Episode::factory()->create();
        Part::factory()->create([
            'episode_id' => $existingEpisode->id,
            'position' => 1,
        ]);

        $response = $this->json('POST', '/api/episodes/parts', [
            'episode_id' => $existingEpisode->id,
            'position' => 1,
        ]);
        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
