<?php

namespace App\Services;

use InvalidArgumentException;
use App\Enums\RecurringType;
use Carbon\Carbon;

class ScheduleRuleService
{

    /**
     * Generate recurring schedule based on provided parameters.
     *
     * @param array $params
     * @return array
     */
    public function generateRecurring(array $params): array
    {
        $generatedDates = $this->generateDates($params);

        return $this->generateTimesPerDates($generatedDates, $params);
    }

    /**
     * Generate end date based on recurring type and duration.
     *
     * @param int $type
     * @param string $startDate
     * @param int $duration
     * @return Carbon
     */
    private function generateRecurringTypeEndDateDuration(int $type, string $startDate, int $duration): Carbon
    {
        if (empty($type)) {
            throw new InvalidArgumentException('Recurring type is required.');
        }

        if (empty($startDate)) {
            throw new InvalidArgumentException('Start date is required.');
        }

        if (empty($duration)) {
            throw new InvalidArgumentException('Duration is required.');
        }

        $start = Carbon::parse($startDate);

        switch ($type) {
            case RecurringType::DAILY->value:
                return $start->copy()->addDays($duration);

            case RecurringType::WEEKLY->value:
                return $start->copy()->addWeeks($duration);

            case RecurringType::MONTHLY->value:
                return $start->copy()->addMonths($duration);

            case RecurringType::YEARLY->value:
                return $start->copy()->addYears(1);

            default:
                throw new InvalidArgumentException('Invalid recurring type.');
        }
    }

    /**
     * Generate dates based on recurring parameters.
     *
     * @param array $params
     * @return array
     */
    private function generateDates(array $params): array
    {
        if (empty($params['recurring_duration_start_date'])) {
            throw new InvalidArgumentException('Start date is required for recurring event.');
        }

        if (!Carbon::hasFormat($params['recurring_duration_start_date'], 'Y-m-d')) {
            throw new InvalidArgumentException("Incorrect start date format, should be Y-m-d.");
        }

        if (empty($params['recurring_type']) && !is_numeric($params['recurring_type'])) {
            throw new InvalidArgumentException('Recurring type is required for recurring event.');
        }

        if (empty($params['recurring_type_duration']) && !is_numeric($params['recurring_type'])) {
            throw new InvalidArgumentException('Duration of recurring type is required for recurring event.');
        }

        $startDate = Carbon::createFromFormat('Y-m-d',$params['recurring_duration_start_date']);
        $endDate = $this->generateRecurringTypeEndDateDuration(
            $params['recurring_type'],
            $startDate,
            $params['recurring_type_duration']
        );

        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $now = Carbon::now();

        if ($end->isBefore($now)) {
            Throw new InvalidArgumentException('The end date is before now');
        }

        $dates = [];

        while ($start->lte($end)) {
            if ($this->shouldIncludeDate($start, $params)) {
                $dates[] = $start->toDateString();
            }

            $start->addDay();
        }

        return $dates;
    }

    /**
     * Generate time entries per date based on recurring parameters.
     *
     * @param array $dates
     * @param array $params
     * @return array $dateTimeEntries
     */
    private function generateTimesPerDates(array $dates, array $params): array
    {
        if (empty($params['recurring_start_time'])){
            throw new InvalidArgumentException('Start Time is required for recurring event.');
        }

        if (empty($params['recurring_end_time'])) {
            throw new InvalidArgumentException('End Time is required for recurring event.');
        }

        if (empty($params['recurring_duration_minutes'])){
            throw new InvalidArgumentException('Minutes are required when creating a recurring entry.');
        }

        if(empty($params['recurring_interval_minutes'])) {
            throw new InvalidArgumentException('Interval is required for recurring duration.');
        }

        $durationMinutes = (int)$params['recurring_duration_minutes'];
        $intervalMinutes = (int)$params['recurring_interval_minutes'];

        $dateTimeEntries = [];

        foreach ($dates as $date) {

            $startDate = Carbon::parse($date)->startOfDay();
            $startTime = Carbon::createFromFormat('H:i', $params['recurring_start_time'], 'America/New_York'); // Use 'H:i:s' format

            while ($startTime->lte(Carbon::parse($params['recurring_end_time']))) {
                if (!$this->isLunchTime($startTime, $params)) {
                    $time = $startDate->copy()->setTime($startTime->hour, $startTime->minute);
                    $endTime = $time->copy()->addMinutes($durationMinutes);

                    $dateTimeEntries[$date][] = [
                        'start' => $time->toDateTimeString(),
                        'end' => $endTime->toDateTimeString(),
                    ];
                }

                $startTime->addMinutes($durationMinutes + $intervalMinutes);
            }
        }

        return $dateTimeEntries;
    }

    /**
     * Check if a date should be included based on weekends and weekdays.
     *
     * @param Carbon $date
     * @param array $params
     * @return bool
     */
    private function shouldIncludeDate(Carbon $date, array $params): bool
    {
        if (empty($params['recurring_has_weekdays'])) {
            $params['recurring_has_weekdays'] = [
                "monday",
                "tuesday",
                "wednesday",
                "thursday",
                "friday"
            ];
        }

        if (empty($params['recurring_ignore_weekends'])) {
            $params['recurring_ignore_weekends'] = 1;
        }

        return !(
            $params['recurring_ignore_weekends'] && $date->isWeekend() ||
            $params['recurring_has_weekdays'] &&
            !empty($params['recurring_weekdays']) &&
            in_array(strtolower($date->englishDayOfWeek), $params['recurring_weekdays'])
        );
    }

    /**
     * Check if a given time is during lunchtime based on parameters.
     *
     * @param Carbon $time
     * @param array $params
     * @return bool
     */
    private function isLunchTime(Carbon $time, array $params): bool
    {
        if(empty($params['recurring_has_lunch_time'])) {
            return false;
        }

        if($params['recurring_has_lunch_time']) {
            if (empty($params['recurring_start_lunch_time'])) {
                throw new InvalidArgumentException('Missing start of lunch time');
            }
            if (empty($params['recurring_end_lunch_time'])) {
                throw new InvalidArgumentException('Missing start of lunch time');
            }
        }


        return (
            $params['recurring_has_lunch_time'] &&
            $time->isBetween(
                Carbon::parse($params['recurring_start_lunch_time']),
                Carbon::parse($params['recurring_end_lunch_time']),
                true
            )
        );
    }
}
