<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('penugasan_jabatan');
        Schema::dropIfExists('jabatan');

        Schema::table('pegawai', function (Blueprint $table) {
            $table->dropColumn('jabatan');
        });
    }

    public function down(): void
    {
        Schema::table('pegawai', function (Blueprint $table) {
            $table->string('jabatan', 100)->default('Staff')->after('nama');
        });

        Schema::create('jabatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bagian_organisasi_id')->nullable()->constrained('bagian_organisasi')->restrictOnDelete();
            $table->foreignId('atasan_jabatan_id')->nullable()->constrained('jabatan')->restrictOnDelete();
            $table->string('kode', 40);
            $table->string('nama', 150);
            $table->enum('kategori', [
                'direktur_utama',
                'direktur',
                'kepala_departemen',
                'manager_operasional',
                'supervisor',
                'staf',
                'lainnya',
            ]);
            $table->boolean('langsung_ke_hr')->default(false);
            $table->boolean('aktif')->default(true);
            $table->timestamp('dibuat_pada')->useCurrent();
            $table->timestamp('diperbarui_pada')->useCurrent();

            $table->unique(['bagian_organisasi_id', 'kode']);
        });

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
};
