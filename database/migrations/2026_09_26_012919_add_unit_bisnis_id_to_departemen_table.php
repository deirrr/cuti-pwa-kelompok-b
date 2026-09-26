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
        Schema::table('departemen', function (Blueprint $table) {
            $table->foreignId('unit_bisnis_id')
                ->nullable()
                ->after('id')
                ->constrained('unit_bisnis')
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('departemen', function (Blueprint $table) {
            $table->dropConstrainedForeignId('unit_bisnis_id');
        });
    }
};
