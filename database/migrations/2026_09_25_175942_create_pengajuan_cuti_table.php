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
        Schema::create('pengajuan_cuti', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pengajuan', 40)->unique();
            $table->foreignId('pegawai_id')->constrained('pegawai')->restrictOnDelete();
            $table->foreignId('jenis_cuti_id')->constrained('jenis_cuti')->restrictOnDelete();
            $table->enum('status', [
                'draf',
                'menunggu_atasan',
                'menunggu_hr',
                'disetujui',
                'ditolak',
                'dibatalkan',
            ])->default('draf');
            $table->text('alasan');
            $table->unsignedInteger('jumlah_hari')->default(0);
            $table->timestamp('diajukan_pada')->nullable();
            $table->timestamp('dibatalkan_pada')->nullable();
            $table->timestamp('dibuat_pada')->useCurrent();
            $table->timestamp('diperbarui_pada')->useCurrent();

            $table->index(['pegawai_id', 'status'], 'pengajuan_pegawai_status_idx');
            $table->index(['status', 'dibuat_pada'], 'pengajuan_status_dibuat_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_cuti');
    }
};
