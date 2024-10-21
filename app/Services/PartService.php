<?php

namespace App\Services;

use App\Services\Contract\PartServiceInterface;
use App\Jobs\UpdateEpisodeCacheJob;
use App\Models\Episode;
use App\Models\Part;
use Exception;
use Cache;
use DB;

class PartService implements PartServiceInterface
{
    const CACHE_MINUTES = 1;

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

    public function create(array $item): Part
    {
        DB::beginTransaction();

        try {
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
            $part =  Part::create($item);

            // Update the cache
            $this->updateEpisodeCache($item['episode_id']);

            // Commit the transaction
            DB::commit();

            return $part;
        } catch (Exception $e) {
            // Rollback the transaction in case of error
            DB::rollBack();
            throw $e;
        }
    }

    public function update(array $item, int $newPositionId): Part
    {
        DB::beginTransaction();

        try {

            $this->validate($item);

            $existingEpisode = $this->checkIfEpisodeExists($item['episode_id']);
            if (!$existingEpisode) {
                throw new Exception('Episode ID does not exist', 404);
            }

            // Check if a part already exists at the new position
            $existingPart = Part::where('episode_id', $item['episode_id'])
                ->where('id', $item['part_id'])
                ->where('position', $newPositionId)
                ->first();

            // If no part exists at the new position, create it
            if (!$existingPart) {
                $newPart = $this->create(
                    array_merge($item, ["position" => $newPositionId])
                );
                DB::commit();
                return $newPart;
            }

            // Reindex if part exists at new position
            $this->reIndex($item, $newPositionId);

            // Return the updated part
            $updated =  Part::where('episode_id', $item['episode_id'])
                ->where('position', $newPositionId)
                ->first();

            // Update the cache
            $this->updateEpisodeCache($item['episode_id']);

            // Commit the transaction
            DB::commit();

            return $updated;
        } catch (Exception $e) {
            // Rollback the transaction in case of error
            DB::rollBack();
            throw $e;
        }
    }

    public function delete(array $item): bool
    {
        DB::beginTransaction();

        try {
            $this->validate($item);

            // Attempt to delete the part and return success or failure
            $delete = Part::where('episode_id', $item['episode_id'])
                ->where('id', $item['part_id'])
                ->where('position', $item['position'])
                ->delete();

            if (!$delete) {
                throw new Exception('Failed to delete the part', 500);
            }

            // Update the cache
            $this->updateEpisodeCache($item['episode_id']);

            // Commit the transaction
            DB::commit();

            return $delete;
        } catch (Exception $e) {
            // Rollback the transaction in case of error
            DB::rollBack();
            throw $e;
        }
    }

    public function reIndex(array $item, int $newPositionId): void
    {
        $this->validate($item);

        DB::beginTransaction();

        try {

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

            // update the cache
            $this->updateEpisodeCache($item['episode_id']);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack(); // Roll back changes on error
            throw $e; // Re-throw the exception if needed
        }
    }

    public function updateEpisodeCache(int $episode): mixed
    {
        $cacheKeyV1 = "episode:{$episode}:parts:v1";
        $cacheKeyV2 = "episode:{$episode}:parts:v2";

        // Dispatch the job to update the cache asynchronously
        UpdateEpisodeCacheJob::dispatch($episode);

        // Check if v2 cache is available
        if (Cache::has($cacheKeyV2)) {
            return Cache::get($cacheKeyV2);
        }

        // Check if v1 cache is available
        if (Cache::has($cacheKeyV1)) {
            return Cache::get($cacheKeyV1);
        }

        // If not in cache, fetch directly from the database as a fallback
        return Part::where('episode_id', $episode)
            ->orderBy('position')
            ->get()
            ->toArray();
    }

    public function getEpisodeParts(int $episode): mixed
    {
        $cacheKeyV1 = "episode:{$episode}:parts:v1";
        $cacheKeyV2 = "episode:{$episode}:parts:v2";

        // Check if v1 cache is missing but v2 exists, return v2
        if (!Cache::has($cacheKeyV1) && Cache::has($cacheKeyV2)) {
            return Cache::get($cacheKeyV2);
        }

        // If both v1 and v2 are missing, update the cache
        return $this->updateEpisodeCache($episode);
    }

    public function duplicateEpisode(int $episodeId): Episode
    {

        DB::beginTransaction();

        try {

            $originalEpisode = $this->checkIfEpisodeExists( $episodeId);
            if (!$originalEpisode) {
                throw new Exception('Episode ID does not exist', 404);
            }

            $originalEpisode = Episode::find($episodeId);

            // Duplicate the episode
            $newEpisode = $originalEpisode->replicate();
            $newEpisode->save();

            // Duplicate related parts
            foreach ($originalEpisode->parts as $part) {
                $newPart = $part->replicate();
                $newPart->position =  $part->position; // Assign the new position ID
                $newPart->episode_id = $newEpisode->id; // Assign the new episode ID
                $newPart->save();
            }

            return $newEpisode;

        } catch (Exception $e) {
            DB::rollBack(); // Roll back changes on error
            throw $e; // Re-throw the exception if needed
        }
    }
}
