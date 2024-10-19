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
        Schema::create('parts', function (Blueprint $table) {
            $table->id(); // This will create an auto-incrementing ID as the primary key
            $table->foreignId('episode_id')->constrained()->onDelete('cascade'); // Foreign key linking to episodes
            $table->integer('position'); // Position of the part in the episode
            $table->timestamps(); // This will create created_at and updated_at timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parts');
    }
};