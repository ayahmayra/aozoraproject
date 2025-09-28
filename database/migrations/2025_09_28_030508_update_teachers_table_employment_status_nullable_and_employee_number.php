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
        Schema::table('teachers', function (Blueprint $table) {
            // Make employment_status nullable
            $table->enum('employment_status', ['full-time', 'part-time', 'contract', 'substitute'])->nullable()->change();
            
            // Make employee_number nullable temporarily to allow auto-generation
            $table->string('employee_number')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            // Revert employment_status to not nullable with default
            $table->enum('employment_status', ['full-time', 'part-time', 'contract', 'substitute'])->default('full-time')->change();
            
            // Revert employee_number to not nullable
            $table->string('employee_number')->nullable(false)->change();
        });
    }
};