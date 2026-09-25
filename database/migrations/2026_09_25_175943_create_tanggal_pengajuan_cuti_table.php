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
        Schema::create('tanggal_pengajuan_cuti', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_cuti_id')->constrained('pengajuan_cuti')->cascadeOnDelete();
            $table->date('tanggal')->index();
            $table->timestamp('dibuat_pada')->useCurrent();

            $table->unique(
                ['pengajuan_cuti_id', 'tanggal'],
                'tanggal_pengajuan_cuti_unik',
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tanggal_pengajuan_cuti');
    }
};
