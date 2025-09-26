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
        Schema::create('document_numbering_configs', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type')->unique(); // user, teacher, parent, student, enrollment, etc.
            $table->string('prefix')->nullable(); // STU, TCH, PRT, etc.
            $table->string('suffix')->nullable(); // -2024, /A, etc.
            $table->integer('current_number')->default(1); // Current running number
            $table->integer('number_length')->default(4); // Length of number (0001, 00001, etc.)
            $table->string('separator')->default(''); // Separator between prefix and number
            $table->boolean('include_year')->default(false); // Include year in format
            $table->boolean('include_month')->default(false); // Include month in format
            $table->boolean('include_day')->default(false); // Include day in format
            $table->string('year_format')->default('Y'); // Y, y, YY, etc.
            $table->string('month_format')->default('m'); // m, M, etc.
            $table->string('day_format')->default('d'); // d, D, etc.
            $table->boolean('reset_yearly')->default(false); // Reset number each year
            $table->boolean('reset_monthly')->default(false); // Reset number each month
            $table->boolean('reset_daily')->default(false); // Reset number each day
            $table->text('description')->nullable(); // Description of the numbering format
            $table->boolean('is_active')->default(true); // Whether this config is active
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_numbering_configs');
    }
};