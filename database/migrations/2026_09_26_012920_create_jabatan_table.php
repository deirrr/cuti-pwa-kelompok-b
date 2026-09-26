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
        Schema::create('jabatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_bisnis_id')->constrained('unit_bisnis')->restrictOnDelete();
            $table->foreignId('departemen_id')->nullable()->constrained('departemen')->restrictOnDelete();
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

            $table->unique(['unit_bisnis_id', 'kode']);
            $table->index(['unit_bisnis_id', 'kategori', 'aktif']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jabatan');
    }
};
