<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status',
        'user_id',
        'recurring_type',
        'recurring_type_duration',
        'recurring_start_time',
        'recurring_end_time',
        'recurring_start_lunch_time',
        'recurring_end_lunch_time',
        'recurring_ignore_weekends',
        'recurring_duration_minutes',
        'recurring_interval_minutes',
        'recurring_time_of_day',
        'recurring_has_lunch_time',
        'recurring_weekdays',
        'recurring_duration_start_date',
        'custom_date_times',
        'is_recurring',
        'is_custom',
    ];

    protected $casts = [
        'recurring_ignore_weekends' => 'boolean',
        'recurring_has_lunch_time' => 'boolean',
        'recurring_weekdays' => 'json',
        'custom_date_times' => 'json',
        'is_recurring' => 'boolean',
        'is_custom' => 'boolean',
    ];

    protected $dates = [
        'recurring_start_time',
        'recurring_end_time',
        'recurring_start_lunch_time',
        'recurring_end_lunch_time',
        'recurring_duration_start_date',
    ];

    /**
     * Get the user associated with the schedule rule.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
