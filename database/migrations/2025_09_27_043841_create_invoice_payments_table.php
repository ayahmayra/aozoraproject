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
        Schema::create('invoice_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id');
            
            // Payment Details
            $table->date('payment_date');
            $table->decimal('payment_amount', 10, 2);
            $table->enum('payment_method', ['cash', 'transfer', 'credit_card', 'debit_card']);
            $table->string('payment_reference')->nullable(); // Payment reference number
            $table->text('payment_notes')->nullable();
            
            // Verification
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');
            
            $table->timestamps();
            
            // Foreign Keys
            $table->foreign('invoice_id')->references('id')->on('invoices');
            $table->foreign('verified_by')->references('id')->on('users');
            
            // Indexes
            $table->index('invoice_id');
            $table->index('payment_date');
            $table->index('status');
            $table->index('payment_reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_payments');
    }
};