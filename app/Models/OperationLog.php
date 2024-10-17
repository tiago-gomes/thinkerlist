<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperationLog extends Model
{
    use HasFactory;

    // Define the table associated with the model
    protected $table = 'operation_logs'; // If your table name is not plural

    // Specify the primary key if it's not the default 'id'
    protected $primaryKey = 'id'; // Optional, since it defaults to 'id'

    // Define the fillable fields
    protected $fillable = [
        'operation',   // Type of operation ('add', 'delete', 'update')
        'episode_id',  // Foreign key linking to episodes
        'part_id',     // Foreign key linking to parts
        'position',     // Current position of the part
        'status',      // Status of the operation ('pending', 'completed')
    ];

    // Optionally, define the timestamps
    public $timestamps = true; // This will enable created_at and updated_at

    // Define relationships
    public function episode()
    {
        return $this->belongsTo(Episode::class); // Assuming a many-to-one relationship with the Episode model
    }

    public function part()
    {
        return $this->belongsTo(Part::class); // Assuming a many-to-one relationship with the Part model
    }
}
