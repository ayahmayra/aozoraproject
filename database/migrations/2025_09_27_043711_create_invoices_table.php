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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('subject_id');
            $table->unsignedBigInteger('enrollment_id'); // Reference to student_subject
            
            // Invoice Details
            $table->date('invoice_date');
            $table->date('due_date');
            $table->date('billing_period_start');
            $table->date('billing_period_end');
            
            // Amount Details
            $table->decimal('amount', 10, 2);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->string('currency', 3)->default('IDR');
            
            // Payment Details
            $table->enum('payment_method', ['monthly', 'semester', 'yearly']);
            $table->enum('payment_status', ['pending', 'paid', 'overdue', 'cancelled'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->decimal('paid_amount', 10, 2)->default(0);
            
            // Additional Info
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable(); // Admin who created
            $table->timestamps();
            
            // Foreign Keys
            $table->foreign('student_id')->references('id')->on('students');
            $table->foreign('subject_id')->references('id')->on('subjects');
            $table->foreign('enrollment_id')->references('id')->on('student_subject');
            $table->foreign('created_by')->references('id')->on('users');
            
            // Indexes
            $table->index('invoice_number');
            $table->index('student_id');
            $table->index('due_date');
            $table->index('payment_status');
            $table->index(['student_id', 'subject_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};