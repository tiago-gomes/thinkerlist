<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('operation_logs', function (Blueprint $table) {
            $table->id(); // Unique log ID
            $table->string('operation'); // Type of operation ('add', 'delete', 'update')
            $table->foreignId('episode_id')->constrained()->onDelete('cascade'); // Foreign key linking to episodes
            $table->foreignId('part_id')->constrained()->onDelete('cascade'); // Foreign key linking to parts
            $table->integer('position'); // Current position
            $table->timestamp('timestamp'); // Time the operation was performed
            $table->tinyInteger('status'); // Status of the operation ('pending', 'completed')
            $table->timestamps(); // Created at and updated at timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operation_logs');
    }
};
