<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Cache;
use App\Models\Part;

class UpdateEpisodeCacheJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const CACHE_MINUTES = 1;

    protected int $episode;

    // Define max number of attempts and retry delay
    public $tries = 5; // Maximum number of attempts

    public $retryAfter = 3; // Time in seconds before the job is retried

    public function __construct(int $episode)
    {
        $this->episode = $episode;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $cacheKeyV1 = "episode:{$this->episode}:parts:v1";
        $cacheKeyV2 = "episode:{$this->episode}:parts:v2";
        $cacheLockKey = "episode:{$this->episode}:cache-lock";

        // Use a cache lock to prevent race conditions
        $lock = Cache::lock($cacheLockKey, 2); // 2 seconds lock

        if ($lock->get()) {
            try {
                // Fetch parts from the database
                $parts = Part::where('episode_id', $this->episode)
                    ->orderBy('position')
                    ->get()
                    ->toArray();

                // Write to v2 first with a longer TTL
                Cache::put($cacheKeyV2, $parts, now()
                    ->addMinutes(self::CACHE_MINUTES + 1));

                // Delete v1 to avoid stale cache reads
                Cache::forget($cacheKeyV1);

                // After v2 is written, update v1 with the correct TTL
                Cache::put($cacheKeyV1, $parts, now()
                    ->addMinutes(self::CACHE_MINUTES));

            } finally {
                // Always release the lock after cache update
                $lock->release();
            }
        } else {
            // Optionally handle the case where the lock is not acquired
            // For example, you might want to throw an exception to trigger a retry
            throw new \Exception("Unable to acquire lock for episode {$this->episode}.");
        }
    }

    public function failed(\Throwable $exception)
    {
        // Handle the job failure, e.g., log the error or notify an admin
        \Log::error("Failed to update cache for episode {$this->episode}: {$exception->getMessage()}");
    }
}
