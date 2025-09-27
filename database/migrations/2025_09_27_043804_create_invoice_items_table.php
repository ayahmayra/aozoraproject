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
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id');
            
            // Item Details
            $table->string('item_name');
            $table->text('item_description')->nullable();
            $table->enum('item_type', ['tuition', 'fee', 'penalty', 'discount'])->default('tuition');
            
            // Amount
            $table->decimal('quantity', 8, 2)->default(1);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            
            $table->timestamps();
            
            // Foreign Keys
            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            
            // Indexes
            $table->index('invoice_id');
            $table->index('item_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};