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
        Schema::create('persetujuan_cuti', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_cuti_id')->constrained('pengajuan_cuti')->cascadeOnDelete();
            $table->foreignId('pemberi_keputusan_id')->constrained('pengguna')->restrictOnDelete();
            $table->enum('tahap', ['atasan', 'admin_hr', 'pembatalan']);
            $table->enum('keputusan', ['disetujui', 'ditolak', 'dibatalkan']);
            $table->text('catatan')->nullable();
            $table->timestamp('diputuskan_pada')->useCurrent();
            $table->timestamp('dibuat_pada')->useCurrent();

            $table->unique(
                ['pengajuan_cuti_id', 'tahap'],
                'persetujuan_cuti_pengajuan_tahap_unik',
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('persetujuan_cuti');
    }
};
