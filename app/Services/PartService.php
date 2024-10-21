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
}
