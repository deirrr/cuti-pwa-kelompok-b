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
        Schema::table('pengajuan_cuti', function (Blueprint $table) {
            $table->foreignId('atasan_penyetuju_id')
                ->nullable()
                ->after('pegawai_id')
                ->constrained('pengguna')
                ->restrictOnDelete();

            $table->index(
                ['atasan_penyetuju_id', 'status'],
                'pengajuan_atasan_status_idx',
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuan_cuti', function (Blueprint $table) {
            $table->dropIndex('pengajuan_atasan_status_idx');
            $table->dropConstrainedForeignId('atasan_penyetuju_id');
        });
    }
};
