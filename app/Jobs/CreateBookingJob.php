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
use App\Enums\ErrorCode;
use App\Enums\ScheduleRuleStatus;
use Cache;

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

            // throw exception because we can not generate both!
            if (
                isset($this->params['is_custom']) && $this->params['is_custom'] == true &&
                isset($this->params['is_recurring']) && $this->params['is_recurring'] == true
            ) {
                throw new \InvalidArgumentException('Custom booking cannot be recurring', ErrorCode::UNPROCESSABLE_ENTITY->value);
            }

            // generate time slots
            $scheduleRuleService = new ScheduleRuleService();

            // generate recurring
            if (isset($this->params['is_recurring']) && $this->params['is_recurring'] == true) {
                $dates = $scheduleRuleService->generateRecurring($this->params);
            }

            // generatre custom
            if (isset($this->params['is_custom']) && $this->params['is_custom']) {
                $dates = $scheduleRuleService->generateCustom($this->params);
            }

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

            // generate cache v1
            if (isset($this->params['status']) && $this->params['status']  === ScheduleRuleStatus::LIVE->value) {
                Cache::put('schedule_rule_'.$scheduleRule->id.'_v1', json_encode($dates), -1);
            }

            // commit transaction
            DB::commit();
        } catch (\Exception $e) {
            // In case of failure, you can revert the changes made so far.
            DB::rollBack();
            throw new \Exception('Error while generating new booking schedules.', ErrorCode::INTERNAL_SERVER_ERROR->value);
        }
    }
}
