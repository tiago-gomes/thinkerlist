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
        if (!Schema::hasTable('schedule_rules')) {
            Schema::create('schedule_rules', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('description');
                $table->unsignedBigInteger('user_id'); // Assuming you want to associate with users (managers)
                $table->tinyInteger('recurring_type');
                $table->integer('recurring_type_duration');
                $table->time('recurring_start_time');
                $table->time('recurring_end_time');
                $table->time('recurring_start_lunch_time')->nullable();
                $table->time('recurring_end_lunch_time')->nullable();
                $table->boolean('recurring_ignore_weekends')->default(true);
                $table->integer('recurring_duration_minutes')->default(25);
                $table->integer('recurring_interval_minutes')->default(5);
                $table->boolean('recurring_has_lunch_time')->default(false);
                $table->boolean('recurring_has_weekdays')->default(false);
                $table->json('recurring_weekdays');
                $table->date('recurring_duration_start_date')->nullable();
                $table->json('custom_date_times')->nullable();
                $table->boolean('is_recurring')->default(false);
                $table->boolean('is_custom')->default(false);
                $table->timestamps();

                // Foreign key constraint to link manager_id with the users table
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_rules');
    }
};
