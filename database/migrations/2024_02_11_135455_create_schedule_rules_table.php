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

                $table->boolean('is_recurring')->default(0);
                $table->integer('recurring_type_duration')->default(1);

                $table->date('recurring_duration_start_date')->default(now());
                $table->time('recurring_start_time')->default("09:00");
                $table->time('recurring_end_time')->default('17:00');

                $table->integer('recurring_duration_minutes')->default(15);
                $table->integer('recurring_interval_minutes')->default(5);

                $table->boolean('recurring_has_lunch_time')->default(0);
                $table->time('recurring_start_lunch_time')->nullable();
                $table->time('recurring_end_lunch_time')->nullable();

                $table->boolean('recurring_has_weekdays')->default(0);
                $table->json('recurring_weekdays')->default(json_encode(["monday","tuesday","wednesday","thursday","friday"]));

                $table->boolean('recurring_ignore_weekends')->default(1);

                $table->boolean('is_custom')->default(0);
                $table->json('custom_date_times')->nullable();

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
