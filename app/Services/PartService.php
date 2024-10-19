<?php

namespace App\Services;

use App\Services\Contract\PartServiceInterface;
use App\Models\Episode;
use App\Models\Part;
use Exception;
use Illuminate\Support\Collection;
use DB;

class PartService implements PartServiceInterface
{
    public function validate(array $item)
    {
        if (!isset($item['episode_id']) || !is_numeric($item['episode_id'])) {
            throw new Exception('Episode ID is required', 412);
        }

        if (!isset($item['part_id']) || !is_numeric($item['part_id'])) {
            throw new Exception('Part ID is required', 412);
        }

        if (!isset($item['position']) || !is_numeric($item['position'])) {
            throw new Exception('position is required', 412);
        }

        return true;
    }

    public function checkIfEpisodeExists(int $id): bool
    {
        return Episode::where('id', $id)
            ->exists();
    }

    public function checkIfPositionExists(array $item)
    {
        return Part::where('episode_id', $item['episode_id'])
            ->where('position', $item['position'])
            ->exists();
    }

    public function getEpisodeParts(array $item): Collection
    {
        // Retrieve all parts for a specific episode
        return Part::where('episode_id', $item['episode_id'])
            ->orderBy('position')
            ->get();
    }

    public function create(array $item): Part
    {
        if (!isset($item['episode_id']) || !is_numeric($item['episode_id'])) {
            throw new Exception('Episode ID is required', 412);
        }

        if (!isset($item['position']) || !is_numeric($item['position'])) {
            throw new Exception('position is required', 412);
        }

        $existingEpisode = $this->checkIfEpisodeExists($item['episode_id']);
        if (!$existingEpisode) {
            throw new Exception('Episode ID does not exist', 404);
        }

        $existingPosition = $this->checkIfPositionExists($item);
        if ($existingPosition) {
            throw new Exception('Position already exists', 404);
        }

        // Create a new part if none exists
        return Part::create($item);
    }

    public function update(array $item, int $newPositionId): Part
    {
        $this->validate($item);

        $existingEpisode = $this->checkIfEpisodeExists($item['episode_id']);
        if (!$existingEpisode) {
            throw new Exception('Episode ID does not exist', 404);
        }

        // Check if a part already exists at the new position
        $existingPart = Part::where('episode_id', $item['episode_id'])
            ->where('part_id', $item['part_id'])
            ->where('position', $newPositionId)
            ->first();

        // If no part exists at the new position, create it
        if (!$existingPart) {
            return $this->create(
                array_merge($item, ["position" => $newPositionId])
            );
        }

        // Reindex if part exists at new position
        $this->reIndex($item, $newPositionId);

        // Return the updated part
        return Part::where('episode_id', $item['episode_id'])
            ->where('position', $newPositionId)
            ->first();
    }

    public function delete(array $item): bool
    {
        $this->validate($item);

        // Attempt to delete the part and return success or failure
        return Part::where('episode_id', $item['episode_id'])
            ->where('position', $item['position'])
            ->delete();
    }

    public function reIndex(array $item, int $newPositionId): void
    {
        $this->validate($item);

        // Find the part that is being moved
        $partBeingMoved = Part::where('episode_id', $item['episode_id'])
            ->where("id", $item['part_id'])
            ->where('position', $item['position'])
            ->first();

        if (!$partBeingMoved) {
            throw new Exception('The part being moved does not exist.');
        }

        // Determine if the part is being moved up or down
        $currentPosition = $item['position'];

        if ($currentPosition < $newPositionId) {
            // Moving the part down: decrement positions of parts between current and new position
            DB::table('parts')
                ->where('episode_id', $item['episode_id'])
                ->where('position', '>', $currentPosition)
                ->where('position', '<=', $newPositionId)
                ->decrement('position');
        } elseif ($currentPosition > $newPositionId) {
            // Moving the part up: increment positions of parts between new and current position
            DB::table('parts')
                ->where('episode_id', $item['episode_id'])
                ->where('position', '<', $currentPosition)
                ->where('position', '>=', $newPositionId)
                ->increment('position');
        }

        // Update the part being moved to the new position
        $partBeingMoved->update(['position' => $newPositionId]);
    }
}
