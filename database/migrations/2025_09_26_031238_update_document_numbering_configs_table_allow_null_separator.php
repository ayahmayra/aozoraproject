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
        Schema::table('document_numbering_configs', function (Blueprint $table) {
            // Make separator column nullable
            $table->string('separator')->nullable()->change();
            
            // Also make other optional fields nullable to be safe
            $table->string('prefix')->nullable()->change();
            $table->string('suffix')->nullable()->change();
            $table->text('description')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_numbering_configs', function (Blueprint $table) {
            // Revert back to not nullable
            $table->string('separator')->nullable(false)->change();
            $table->string('prefix')->nullable(false)->change();
            $table->string('suffix')->nullable(false)->change();
            $table->text('description')->nullable(false)->change();
        });
    }
};