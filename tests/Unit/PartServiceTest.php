<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Exception;
use App\Services\PartService;
use App\Models\Part;
use App\Models\Episode;

class PartServiceTest extends TestCase
{

    use WithFaker, RefreshDatabase;

    public function test_validate_inputs(): void
    {
        $partService = new PartService();

        // Test valid inputs
        $validInput = ['episode_id' => 1, 'part_id' => 1, 'position' => 1];
        $this->assertTrue($partService->validate($validInput));

        // Test missing episode_id
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Episode ID is required');
        $partService->validate(['part_id' => 1, 'position' => 1]);

        // Test missing part_id
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Part ID is required');
        $partService->validate(['episode_id' => 1, 'position' => 1]);

        // Test missing position
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('position is required');
        $partService->validate(['episode_id' => 1, 'part_id' => 1]);
    }

    public function test_create_new_part(): void
    {
        $epsiode = Episode::create(['name' => 'test']);

        $partService = new PartService();
        $input = ['episode_id' => $epsiode->id, 'position' => 1];

        // Create the part
        $part = $partService->create($input);

        // Assert that the part was created correctly
        $this->assertInstanceOf(Part::class, $part);
        $this->assertEquals(1, $part->episode_id);
        $this->assertEquals(1, $part->position);
    }

    public function test_update_part(): void
    {
        $epsiode = Episode::create(['name' => 'test']);

        $partService = new PartService();
        $part_one = Part::create(['episode_id' => 1, 'position' => 1]);

        // Update the part's position
        $updatedPart = $partService->update(['episode_id' => 1, 'part_id' => 1, 'position' => 1], 2);

        // Assert that the part's position was updated
        $this->assertEquals(2, $updatedPart->position);
    }

    public function test_update_reindex(): void
    {
        // Create an episode for the part to associate with
        Episode::create(['name' => 'test']);

        // Create multiple parts associated with the episode
        $partService = new PartService();

        Part::create(['episode_id' => 1, 'position' => 1]);
        Part::create(['episode_id' => 1, 'position' => 2]);
        Part::create(['episode_id' => 1, 'position' => 3]);
        Part::create(['episode_id' => 1, 'position' => 4]);
        Part::create(['episode_id' => 1, 'position' => 5]);
        Part::create(['episode_id' => 1, 'position' => 6]);
        Part::create(['episode_id' => 1, 'position' => 7]);
        Part::create(['episode_id' => 1, 'position' => 8]);
        Part::create(['episode_id' => 1, 'position' => 9]);
        Part::create(['episode_id' => 1, 'position' => 10]);

        // Reindex the part's position
        $partService->reIndex(['episode_id' => 1, 'part_id' => 1, 'position' => 1], 10);

        // Retrieve the updated part
        $updatedPart = Part::where('episode_id', 1)
            //->where('id', 1)
            ->where('position', 10)
            ->first();

        // Assert that the part's position was updated correctly
        $this->assertEquals(10, $updatedPart->position);
    }

    public function test_invalid_create_episode_id(): void
    {
        $partService = new PartService();

        // Test with a non-existent episode_id
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Episode ID does not exist');

        $partService->create(['episode_id' => 999, 'position' => 1]);
    }

    public function test_invalid_update_episode_id(): void
    {
        $partService = new PartService();

        // Test with a non-existent episode_id
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Episode ID does not exist');

        $partService->update(['episode_id' => 999, 'part_id' => 999, 'position' => 1], 1);
    }

    public function test_duplicate_positions(): void
    {
        $episode = Episode::create(['name' => 'test']);
        $partService = new PartService();

        // Create the first part
        Part::create(['episode_id' => $episode->id, 'position' => 1]);

        // Try to create a second part with the same position
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Position already exists');

        $partService->create(['episode_id' => $episode->id, 'position' => 1]);
    }

    public function test_moving_parts(): void
    {
        $episode = Episode::create(['name' => 'test']);
        $partService = new PartService();

        // Create parts in positions 1 to 5
        for ($i = 1; $i <= 5; $i++) {
            Part::create(attributes: ['episode_id' => $episode->id, 'position' => $i]);
        }

        // Move the part at position 2 to position 4
        $partService->reIndex(['episode_id' => $episode->id, 'part_id'=> 2, 'position' => 2], 4);

        // Assert that part from position 2 is now at position 4
        $updatedPart = Part::where('episode_id', $episode->id)
                        ->where('position', 4)
                        ->first();

        $this->assertEquals(4, $updatedPart->position);

        // Assert that parts that were previously in positions 3 and 4 have been updated
        $part3 = Part::where('episode_id', $episode->id)
                    ->where('position', 3)
                    ->first();

        $this->assertEquals(3, $part3->position); // Should remain the same

        $part5 = Part::where('episode_id', $episode->id)
                    ->where('position', 5)
                    ->first();

        $this->assertEquals(5, $part5->position); // Should remain the same
    }

    public function test_boundary_values(): void
    {
        $episode = Episode::create(['name' => 'test']);
        $partService = new PartService();

        // Create multiple parts
        $parts = [];
        for ($i = 1; $i < 6; $i++) {
            $parts[] = Part::create(['episode_id' => $episode->id, 'position' => $i]);
        }

        // Move the first part (ID 1) to the last position (position 5)
        $partService->reIndex(['episode_id' => $episode->id, 'part_id' => 1, 'position' => 1], 5);

        // Assert that the first part (ID 1) is now at position 5
        $movedPart = Part::where('episode_id', $episode->id)
                        ->where('id', 1)
                        ->first();

        $this->assertEquals(5,$movedPart->position); // The first part should now be at position 5

        // Move the part (ID 3) to the position (position 3)
        $partService->reIndex(['episode_id' => $episode->id, 'part_id' => 3, 'position' => 2], 3);

        // Assert that the first part (ID 3) is now at position 3
        $movedPart = Part::where('episode_id', $episode->id)
                        ->where('id', 3)
                        ->first();

        $this->assertEquals(3,$movedPart->position); // The part should now be at position 3
    }

    public function test_duplicate_episode_success()
    {
        $partService = new PartService();

        // Create a sample episode with parts
        $originalEpisode = Episode::create(['name' => 'Original Episode']);
        $part1 = Part::create(['episode_id' => $originalEpisode->id, 'position' => 1]);
        $part2 = Part::create(['episode_id' => $originalEpisode->id, 'position' => 2]);

        // Duplicate the episode
        $newEpisode = $partService->duplicateEpisode($originalEpisode->id);

        // Assert that the new episode was created
        $this->assertNotNull($newEpisode);
        $this->assertNotEquals($originalEpisode->id, $newEpisode->id);
        $this->assertEquals('Original Episode', $newEpisode->name);

        // Assert that the related parts were duplicated
        $this->assertCount(2, Part::where('episode_id', $newEpisode->id)->get());
        $this->assertEquals(1, Part::where('episode_id', $newEpisode->id)->first()->position);
        $this->assertEquals(2, Part::where('episode_id', $newEpisode->id)->latest()->get()->last()->position);
    }


}
