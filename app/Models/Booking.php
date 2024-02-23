<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'start_datetime',
        'end_datetime',
        'customer_id',
        'user_id',
        'schedule_rule_id',
    ];

    /**
     * Get the user associated with the availability.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the schedule rule associated with the availability.
     */
    public function scheduleRule()
    {
        return $this->belongsTo(ScheduleRule::class);
    }

    /**
     * Get the customer associated with the availability.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
