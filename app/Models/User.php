<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Set the password attribute and automatically hash it.
     *
     * @param  string  $value
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function getJWTIdentifier(): array
    {
        return $this->getKey();
    }

    /**
     * Get Jwt Custom Clains
     *
     * @return mixed
     */
    public function getJWTCustomClaims(): mixed
    {
        return [];
    }

    /**
     * Get the schedule rules associated with the user.
     */
    public function scheduleRules()
    {
        return $this->hasMany(ScheduleRule::class);
    }

    /**
     * Get the customers associated with the user.
     */
    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function bookingss()
    {
        return $this->hasMany(Booking::class);
    }
}
