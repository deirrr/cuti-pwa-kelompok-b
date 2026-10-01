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
        Schema::table('pengajuan_cuti', function (Blueprint $table) {
            $table->date('tanggal_cuti')->nullable()->after('alasan');
        });

        DB::table('pengajuan_cuti')
            ->orderBy('id')
            ->get()
            ->each(function (object $pengajuan): void {
                $tanggalCuti = DB::table('tanggal_pengajuan_cuti')
                    ->where('pengajuan_cuti_id', $pengajuan->id)
                    ->orderBy('tanggal')
                    ->pluck('tanggal');

                if ($tanggalCuti->isEmpty()) {
                    return;
                }

                DB::table('pengajuan_cuti')
                    ->where('id', $pengajuan->id)
                    ->update([
                        'tanggal_cuti' => $tanggalCuti->first(),
                        'jumlah_hari' => 1,
                    ]);

                $persetujuan = DB::table('persetujuan_cuti')
                    ->where('pengajuan_cuti_id', $pengajuan->id)
                    ->get();

                foreach ($tanggalCuti->skip(1)->values() as $indeks => $tanggal) {
                    $nomorUrut = $indeks + 2;
                    $pengajuanBaruId = DB::table('pengajuan_cuti')->insertGetId([
                        'nomor_pengajuan' => mb_substr($pengajuan->nomor_pengajuan, 0, 36).'-'.str_pad((string) $nomorUrut, 2, '0', STR_PAD_LEFT),
                        'pegawai_id' => $pengajuan->pegawai_id,
                        'atasan_penyetuju_id' => $pengajuan->atasan_penyetuju_id,
                        'status' => $pengajuan->status,
                        'alasan' => $pengajuan->alasan,
                        'tanggal_cuti' => $tanggal,
                        'jumlah_hari' => 1,
                        'diajukan_pada' => $pengajuan->diajukan_pada,
                        'dibatalkan_pada' => $pengajuan->dibatalkan_pada,
                        'dibuat_pada' => $pengajuan->dibuat_pada,
                        'diperbarui_pada' => $pengajuan->diperbarui_pada,
                    ]);

                    foreach ($persetujuan as $keputusan) {
                        DB::table('persetujuan_cuti')->insert([
                            'pengajuan_cuti_id' => $pengajuanBaruId,
                            'pemberi_keputusan_id' => $keputusan->pemberi_keputusan_id,
                            'tahap' => $keputusan->tahap,
                            'keputusan' => $keputusan->keputusan,
                            'catatan' => $keputusan->catatan,
                            'diputuskan_pada' => $keputusan->diputuskan_pada,
                            'dibuat_pada' => $keputusan->dibuat_pada,
                        ]);
                    }
                }
            });

        Schema::drop('tanggal_pengajuan_cuti');

        Schema::table('pengajuan_cuti', function (Blueprint $table) {
            $table->date('tanggal_cuti')->nullable(false)->change();
            $table->dropColumn('jumlah_hari');
            $table->index(['pegawai_id', 'tanggal_cuti'], 'pengajuan_pegawai_tanggal_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuan_cuti', function (Blueprint $table) {
            $table->unsignedInteger('jumlah_hari')->default(1)->after('tanggal_cuti');
        });

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

        DB::table('pengajuan_cuti')
            ->whereNotNull('tanggal_cuti')
            ->orderBy('id')
            ->each(function (object $pengajuan): void {
                DB::table('tanggal_pengajuan_cuti')->insert([
                    'pengajuan_cuti_id' => $pengajuan->id,
                    'tanggal' => $pengajuan->tanggal_cuti,
                    'dibuat_pada' => $pengajuan->dibuat_pada,
                ]);
            });

        Schema::table('pengajuan_cuti', function (Blueprint $table) {
            $table->dropIndex('pengajuan_pegawai_tanggal_idx');
            $table->dropColumn('tanggal_cuti');
        });
    }
};
