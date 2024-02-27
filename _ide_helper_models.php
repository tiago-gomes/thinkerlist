<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\Booking
 *
 * @property int $id
 * @property int $schedule_rule_id
 * @property int|null $customer_id
 * @property int $user_id
 * @property string $start_datetime
 * @property string $end_datetime
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Customer|null $customer
 * @property-read \App\Models\ScheduleRule $scheduleRule
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Booking newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Booking newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Booking query()
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereEndDatetime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereScheduleRuleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereStartDatetime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereUserId($value)
 */
	class Booking extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Customer
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $phone
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Booking> $bookings
 * @property-read int|null $bookings_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Customer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer query()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereUpdatedAt($value)
 */
	class Customer extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ScheduleRule
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property int $user_id
 * @property int $recurring_type
 * @property int $recurring_type_duration
 * @property string $recurring_start_time
 * @property string $recurring_end_time
 * @property string|null $recurring_start_lunch_time
 * @property string|null $recurring_end_lunch_time
 * @property bool $recurring_ignore_weekends
 * @property int $recurring_duration_minutes
 * @property int $recurring_interval_minutes
 * @property bool $recurring_has_lunch_time
 * @property int $recurring_has_weekdays
 * @property array $recurring_weekdays
 * @property string|null $recurring_duration_start_date
 * @property array|null $custom_date_times
 * @property bool $is_recurring
 * @property bool $is_custom
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\ScheduleRuleFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRule query()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRule whereCustomDateTimes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRule whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRule whereIsCustom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRule whereIsRecurring($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRule whereRecurringDurationMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRule whereRecurringDurationStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRule whereRecurringEndLunchTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRule whereRecurringEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRule whereRecurringHasLunchTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRule whereRecurringHasWeekdays($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRule whereRecurringIgnoreWeekends($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRule whereRecurringIntervalMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRule whereRecurringStartLunchTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRule whereRecurringStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRule whereRecurringType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRule whereRecurringTypeDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRule whereRecurringWeekdays($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRule whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRule whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleRule whereUserId($value)
 */
	class ScheduleRule extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\User
 *
 * @property int $id
 * @property int $role
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property mixed $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Booking> $bookingss
 * @property-read int|null $bookingss_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Customer> $customers
 * @property-read int|null $customers_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ScheduleRule> $scheduleRules
 * @property-read int|null $schedule_rules_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

