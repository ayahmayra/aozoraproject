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
        Schema::table('student_subject', function (Blueprint $table) {
            // Add enrollment fields if they don't exist
            if (!Schema::hasColumn('student_subject', 'enrollment_date')) {
                $table->date('enrollment_date')->nullable();
            }
            if (!Schema::hasColumn('student_subject', 'start_date')) {
                $table->date('start_date')->nullable();
            }
            if (!Schema::hasColumn('student_subject', 'end_date')) {
                $table->date('end_date')->nullable();
            }
            if (!Schema::hasColumn('student_subject', 'payment_method')) {
                $table->enum('payment_method', ['monthly', 'semester', 'yearly'])->nullable();
            }
            if (!Schema::hasColumn('student_subject', 'payment_amount')) {
                $table->decimal('payment_amount', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('student_subject', 'payment_status')) {
                $table->enum('payment_status', ['pending', 'paid', 'overdue', 'cancelled'])->default('pending');
            }
            if (!Schema::hasColumn('student_subject', 'enrollment_status')) {
                $table->enum('enrollment_status', ['pending', 'active', 'completed', 'cancelled'])->default('pending');
            }
            if (!Schema::hasColumn('student_subject', 'parent_id')) {
                $table->foreignId('parent_id')->nullable()->constrained('users')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_subject', function (Blueprint $table) {
            if (Schema::hasColumn('student_subject', 'parent_id')) {
                $table->dropForeign(['parent_id']);
            }
            $table->dropColumn([
                'enrollment_date',
                'start_date', 
                'end_date',
                'payment_method',
                'payment_amount',
                'payment_status',
                'enrollment_status',
                'parent_id'
            ]);
        });
    }
};
