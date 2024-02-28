<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\ScheduleRule;
use Illuminate\Support\Facades\DB;
use App\Services\ScheduleRuleService;

class CreateBookingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private array $params;

    private ScheduleRule $scheduleRule;

    /**
     * Create a new job instance.
     */
    public function __construct(
        ScheduleRule $scheduleRule,
        array $params)
    {
        $this->params = $params;
        $this->scheduleRule = $scheduleRule;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {

            // generate time slots
            $scheduleRuleService = new ScheduleRuleService();
            $dates = $scheduleRuleService->generateRecurring($this->params);

            // start transaction
            DB::beginTransaction();

            $scheduleRule = $this->scheduleRule;

            collect($dates)->each(function ($date) use ($scheduleRule) {
                // Maps each date item to an array of start and end pairs.
                $startEndPairs = collect($date)->map(function ($item) use ($scheduleRule) {
                    return [
                        "user_id" => $scheduleRule->user_id,
                        "start_datetime" => $item['start'],
                        "end_datetime" => $item['end']
                    ];
                });

                // Split the start and end pairs into chunks and create bookings for each chunk
                $startEndPairs->chunk(count($date))->each(function ($chunk) use ($scheduleRule) {
                    // Create multiple bookings for a ScheduleRule
                    $scheduleRule->bookings()->createMany($chunk->toArray());
                });
            });

            // commit transaction
            DB::commit();
        } catch (\Exception $e) {
            // In case of failure, you can revert the changes made so far.
            DB::rollBack();
        }
    }
}
