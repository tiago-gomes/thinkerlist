<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Part;
use App\Services\PartService;
use App\Models\Episode;
use Tests\TestCase;

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
}
