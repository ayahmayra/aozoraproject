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
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('employee_number')->unique();
            $table->date('date_of_birth');
            $table->enum('gender', ['male', 'female']);
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('subject_specialization')->nullable();
            $table->string('education_level')->nullable();
            $table->string('institution')->nullable();
            $table->year('graduation_year')->nullable();
            $table->date('hire_date');
            $table->enum('employment_status', ['full-time', 'part-time', 'contract', 'substitute'])->default('full-time');
            $table->text('certifications')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
