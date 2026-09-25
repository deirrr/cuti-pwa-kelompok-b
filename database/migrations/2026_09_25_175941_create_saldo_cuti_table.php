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
        Schema::create('saldo_cuti', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawai')->restrictOnDelete();
            $table->foreignId('jenis_cuti_id')->constrained('jenis_cuti')->restrictOnDelete();
            $table->unsignedSmallInteger('tahun');
            $table->unsignedInteger('jatah_awal')->default(0);
            $table->unsignedInteger('saldo_tersedia')->default(0);
            $table->string('catatan')->nullable();
            $table->timestamp('dibuat_pada')->useCurrent();
            $table->timestamp('diperbarui_pada')->useCurrent();

            $table->unique(
                ['pegawai_id', 'jenis_cuti_id', 'tahun'],
                'saldo_cuti_pegawai_jenis_tahun_unik',
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saldo_cuti');
    }
};
