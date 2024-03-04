<?php

namespace Tests\Unit;

use App\Services\ScheduleRuleService;
use Carbon\Carbon;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Illuminate\Contracts\Translation\Translator;
use Exception;

class ScheduleRuleServiceTest extends TestCase
{
    /**
     * @var ScheduleRuleService
     */
    private $scheduleRuleService;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock the Translator
        $translatorMock = $this->createMock(Translator::class);
        app()->instance(Translator::class, $translatorMock);

        $this->scheduleRuleService = app()->make(ScheduleRuleService::class);
    }

    /**
     * Test a basic recurring schedule generation.
     */
    public function testBasicRecurringScheduleGeneration(): void
    {
        $params = [
            'is_recurring' => true,
            'recurring_type' => 1, // Daily
            'recurring_duration_start_date' => '2024-03-01',
            'recurring_type_duration' => 5,
            'recurring_start_time' => '09:00',
            'recurring_end_time' => '17:00',
            'recurring_duration_minutes' => 60,
            'recurring_interval_minutes' => 15,
            'recurring_ignore_weekends' => true,
        ];

        try {
            $result = $this->scheduleRuleService->generateRecurring($params);
        } catch (Exception $e) {
            var_dump($e->getMessage());
        }

        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
    }

    /**
     * Test generating schedule with invalid recurring type.
     */
    public function testInvalidRecurringType(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $params = [
            'is_recurring' => true,
            'recurring_type' => 10, // Invalid type
            'recurring_duration_start_date' => '2024-02-10',
            'recurring_type_duration' => 5,
            'recurring_start_time' => '09:00',
            'recurring_end_time' => '17:00',
            'recurring_duration_minutes' => 60,
            'recurring_interval_minutes' => 15,
            'recurring_ignore_weekends' => false,
        ];

        $this->scheduleRuleService->generateRecurring($params);
    }

    /**
     * Test generating schedule with invalid date format.
     */
    public function testInvalidDateFormat(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $params = [
            'is_recurring' => true,
            'recurring_type' => 1, // Daily
            'recurring_duration_start_date' => 'invalid_date_format',
            'recurring_type_duration' => 5,
            'recurring_start_time' => '09:00',
            'recurring_end_time' => '17:00',
            'recurring_duration_minutes' => 60,
            'recurring_interval_minutes' => 15,
            'recurring_ignore_weekends' => false,
        ];

        $this->scheduleRuleService->generateRecurring($params);
    }

    /**
     * Test generating schedule with invalid start time.
     */
    public function testInvalidStartTime(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $params = [
            'is_recurring' => true,
            'recurring_type' => 1, // Daily
            'recurring_duration_start_date' => '2024-02-10',
            'recurring_type_duration' => 5,
            'recurring_start_time' => 'invalid_time_format',
            'recurring_end_time' => '17:00',
            'recurring_duration_minutes' => 60,
            'recurring_interval_minutes' => 15,
            'recurring_ignore_weekends' => false,
        ];

        $this->scheduleRuleService->generateRecurring($params);
    }

    /**
     * Test generating schedule with end time before start time.
     */
    public function testEndTimeBeforeNow(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $params = [
            'is_recurring' => true,
            'recurring_type' => 1, // Daily
            'recurring_duration_start_date' => '2024-01-10',
            'recurring_type_duration' => 5,
            'recurring_start_time' => '17:00',
            'recurring_end_time' => '09:00', // End time is before start time
            'recurring_duration_minutes' => 60,
            'recurring_interval_minutes' => 15,
            'recurring_ignore_weekends' => false,
        ];

        $this->scheduleRuleService->generateRecurring($params);
    }

    /**
     * Test generating schedule with invalid duration.
     */
    public function testInvalidDuration(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $params = [
            'is_recurring' => true,
            'recurring_type' => 1, // Daily
            'recurring_duration_start_date' => '2024-02-10',
            'recurring_type_duration' => 5,
            'recurring_start_time' => '09:00',
            'recurring_end_time' => '17:00',
            'recurring_duration_minutes' => 80, // Invalid duration
            'recurring_interval_minutes' => 15,
            'recurring_ignore_weekends' => false,
        ];

        $this->scheduleRuleService->generateRecurring($params);
    }

    /**
     * Test generating schedule with invalid interval.
     */
    public function testInvalidInterval(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $params = [
            'is_recurring' => true,
            'recurring_type' => 1, // Daily
            'recurring_duration_start_date' => '2024-02-10',
            'recurring_type_duration' => 5,
            'recurring_start_time' => '09:00',
            'recurring_end_time' => '17:00',
            'recurring_duration_minutes' => 60,
            'recurring_interval_minutes' => 20, // Invalid interval
            'recurring_ignore_weekends' => false,
        ];

        $this->scheduleRuleService->generateRecurring($params);
    }

    /**
     * Test generating schedule with invalid time of day.
     */
    public function testInvalidTimeOfDay(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $params = [
            'is_recurring' => true,
            'recurring_type' => 1, // Daily
            'recurring_duration_start_date' => '2024-02-10',
            'recurring_type_duration' => 5,
            'recurring_start_time' => '09:00',
            'recurring_end_time' => '17:00',
            'recurring_duration_minutes' => 60,
            'recurring_interval_minutes' => 15,
            'recurring_time_of_day' => 'invalid_time_of_day', // Invalid time of day
            'recurring_ignore_weekends' => false,
        ];

        $this->scheduleRuleService->generateRecurring($params);
    }

    /**
     * Test generating schedule ignoring weekends.
     */
    public function testGenerateScheduleIgnoringWeekends(): void
    {
        $params = [
            'is_recurring' => true,
            'recurring_type' => 1, // Daily
            'recurring_duration_start_date' => '2024-10-01', // Friday
            'recurring_type_duration' => 5,
            'recurring_start_time' => '09:00',
            'recurring_end_time' => '17:00',
            'recurring_duration_minutes' => 60,
            'recurring_interval_minutes' => 15,
            'recurring_ignore_weekends' => true,
        ];

        $result = $this->scheduleRuleService->generateRecurring($params);

        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertCount(4, $result); // No entries for Saturday and Sunday
    }

    /**
     * Test generating schedule with specific weekdays.
     */
    public function testGenerateScheduleWithSpecificWeekdays(): void
    {
        $params = [
            'is_recurring' => true,
            'recurring_type' => 1, // Daily
            'recurring_duration_start_date' => '2024-10-01',
            'recurring_type_duration' => 5,
            'recurring_start_time' => '09:00',
            'recurring_end_time' => '17:00',
            'recurring_duration_minutes' => 60,
            'recurring_interval_minutes' => 15,
            'recurring_ignore_weekends' => false,
            'recurring_weekdays' => ['monday', 'wednesday', 'friday'],
        ];

        $result = $this->scheduleRuleService->generateRecurring($params);

        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertCount(4, $result); // Only entries for Monday, Wednesday, and Friday
    }

    /**
     * Test generating schedule with lunchtime.
     */
    public function testGenerateScheduleWithLunchtime(): void
    {
        $params = [
            'is_recurring' => true,
            'recurring_type' => 1, // Daily
            'recurring_duration_start_date' => '2024-10-01',
            'recurring_type_duration' => 5,
            'recurring_start_time' => '09:00',
            'recurring_end_time' => '17:00',
            'recurring_duration_minutes' => 60,
            'recurring_interval_minutes' => 15,
            'recurring_ignore_weekends' => false,
            'recurring_has_lunch_time' => true,
            'recurring_start_lunch_time' => '12:00',
            'recurring_end_lunch_time' => '13:00',
        ];

        $result = $this->scheduleRuleService->generateRecurring($params);

        $this->assertIsArray($result);
        $this->assertNotEmpty($result);

        foreach ($result as $entries) {
            foreach ($entries as $entry) {
                $this->assertTrue(
                    Carbon::parse($entry['start'])->lt(Carbon::parse($params['recurring_start_lunch_time']))
                    || Carbon::parse($entry['end'])->gt(Carbon::parse($params['recurring_end_lunch_time']))
                );
            }
        }
    }

    /**
     * Test generating schedule with invalid lunchtime configuration.
     */
    public function testInvalidLunchtimeConfiguration(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $params = [
            'is_recurring' => true,
            'recurring_type' => 1, // Daily
            'recurring_duration_start_date' => '2024-02-10',
            'recurring_type_duration' => 5,
            'recurring_start_time' => '09:00',
            'recurring_end_time' => '17:00',
            'recurring_duration_minutes' => 60,
            'recurring_interval_minutes' => 15,
            'recurring_ignore_weekends' => false,
            'recurring_has_lunch_time' => true,
            'recurring_start_lunch_time' => '13:00', // Start lunchtime after end time
            'recurring_end_lunch_time' => '12:00', // End lunchtime before start time
        ];

        $this->scheduleRuleService->generateRecurring($params);
    }

    public function testCustomScheduleGeneration(): void
    {
        $params = [
            'is_custom' => true,
            'custom_date_times' => [
                ['start' => '2024-03-01 12:00:00', 'end' => '2024-03-01 13:00:00'],
                ['start' => '2024-03-02 09:00:00', 'end' => '2024-03-02 10:00:00'],
            ]
        ];

        try {
            $result = $this->scheduleRuleService->generateCustom($params);
        } catch (Exception $e) {
            var_dump($e->getMessage());
        }

        $this->assertNotEmpty($result);
        $this->assertCount(2, $result);
    }

    public function testCustomScheduleGenerationMissingIsCustom(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $params = [
            'custom_date_times' => [
                ['start' => '2024-03-01 12:00:00', 'end' => '2024-03-01 13:00:00'],
                ['start' => '2024-03-02 09:00:00', 'end' => '2024-03-02 10:00:00'],
            ]
        ];

        $this->scheduleRuleService->generateCustom($params);
    }

    public function testCustomScheduleGenerationMissingCustomDateTimesArray(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $params = [
            'is_custom' => true,
        ];

        $this->scheduleRuleService->generateCustom($params);
    }

    public function testCustomScheduleGenerationStartDate(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $params = [
            'is_custom' => true,
            'custom_date_times' => [
                ['end' => '2024-03-01 13:00:00'],
                ['start' => '2024-03-02 09:00:00', 'end' => '2024-03-02 10:00:00'],
            ]
        ];

        $this->scheduleRuleService->generateCustom($params);
    }
}
