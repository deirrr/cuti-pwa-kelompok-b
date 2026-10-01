<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pegawai', function (Blueprint $table) {
            $table->unsignedSmallInteger('jatah_cuti')->default(12)->after('tanggal_masuk');
        });

        DB::table('saldo_cuti')
            ->orderBy('pegawai_id')
            ->orderByDesc('tahun')
            ->get(['pegawai_id', 'jatah_awal'])
            ->unique('pegawai_id')
            ->each(function (object $saldoCuti): void {
                DB::table('pegawai')
                    ->where('id', $saldoCuti->pegawai_id)
                    ->update(['jatah_cuti' => $saldoCuti->jatah_awal]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pegawai', function (Blueprint $table) {
            $table->dropColumn('jatah_cuti');
        });
    }
};
