<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Part extends Model
{
    use HasFactory;

    // Define the table associated with the model
    protected $table = 'parts'; // If your table name is not plural

    // Specify the primary key if it's not the default 'id'
    protected $primaryKey = 'id'; // Optional, since it defaults to 'id'

    // Define the fillable fields
    protected $fillable = [
        'episode_id', // Foreign key linking to episodes
        'position',   // Position of the part in the episode
    ];

    // Optionally, define the timestamps
    public $timestamps = true; // This will enable created_at and updated_at

    // Define relationships
    public function episode()
    {
        return $this->belongsTo(Episode::class); // Assuming a many-to-one relationship with the Episode model
    }

    public function operationLogs()
    {
        return $this->hasMany(OperationLog::class); // Assuming a one-to-many relationship with the OperationLog model
    }
}
