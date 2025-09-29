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
        Schema::table('invoices', function (Blueprint $table) {
            // Payment information fields
            $table->string('payment_reference')->nullable()->after('paid_amount');
            $table->string('payment_proof')->nullable()->after('payment_reference');
            $table->timestamp('payment_date')->nullable()->after('payment_proof');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['payment_reference', 'payment_proof', 'payment_date']);
        });
    }
};
