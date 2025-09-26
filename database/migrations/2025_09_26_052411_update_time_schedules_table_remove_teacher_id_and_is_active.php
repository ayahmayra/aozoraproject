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
        Schema::table('time_schedules', function (Blueprint $table) {
            // Drop foreign key constraint first
            $table->dropForeign(['teacher_id']);
            
            // Drop the columns
            $table->dropColumn(['teacher_id', 'is_active']);
            
            // Drop the unique constraint that included teacher_id
            $table->dropUnique('unique_teacher_schedule');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('time_schedules', function (Blueprint $table) {
            // Add back the columns
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
            $table->boolean('is_active')->default(true);
            
            // Add back the unique constraint
            $table->unique(['teacher_id', 'day_of_week', 'start_time'], 'unique_teacher_schedule');
        });
    }
};