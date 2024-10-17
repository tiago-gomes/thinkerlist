<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Episode extends Model
{
    use HasFactory;

    // Define the table associated with the model
    protected $table = 'episodes'; // If your table name is not plural

    // Specify the primary key if it's not the default 'id'
    protected $primaryKey = 'id'; // Optional, since it defaults to 'id'

    // Define the fillable fields
    protected $fillable = [
        'name', // Name of the episode
    ];

    // Optionally, define the timestamps
    public $timestamps = true; // This will enable created_at and updated_at

    // Define any relationships
    public function parts()
    {
        return $this->hasMany(Part::class); // Assuming a one-to-many relationship with the Part model
    }

    public function operationLogs()
    {
        return $this->hasMany(OperationLog::class); // Assuming a one-to-many relationship with the OperationLog model
    }
}
