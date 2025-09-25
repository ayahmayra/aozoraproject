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
        Schema::table('students', function (Blueprint $table) {
            // Remove unnecessary parent fields since we have parent_id relationship
            $table->dropColumn([
                'parent_name',
                'parent_phone', 
                'parent_address'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Add back parent fields
            $table->string('parent_name')->nullable();
            $table->string('parent_phone')->nullable();
            $table->text('parent_address')->nullable();
        });
    }
};
