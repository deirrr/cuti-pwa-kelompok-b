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
        Schema::create('penugasan_jabatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawai')->restrictOnDelete();
            $table->foreignId('jabatan_id')->constrained('jabatan')->restrictOnDelete();
            $table->boolean('utama')->default(false);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamp('dibuat_pada')->useCurrent();
            $table->timestamp('diperbarui_pada')->useCurrent();

            $table->unique(['pegawai_id', 'jabatan_id', 'tanggal_mulai']);
            $table->index(['pegawai_id', 'aktif', 'utama']);
            $table->index(['jabatan_id', 'aktif']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penugasan_jabatan');
    }
};
