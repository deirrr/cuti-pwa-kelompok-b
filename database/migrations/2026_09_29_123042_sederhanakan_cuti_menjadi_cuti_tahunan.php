<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $cutiTahunanId = DB::table('jenis_cuti')->where('kode', 'TAHUNAN')->value('id');

        if ($cutiTahunanId === null) {
            DB::table('saldo_cuti')->delete();
            DB::table('pengajuan_cuti')->delete();
        } else {
            DB::table('saldo_cuti')->where('jenis_cuti_id', '!=', $cutiTahunanId)->delete();
            DB::table('pengajuan_cuti')->where('jenis_cuti_id', '!=', $cutiTahunanId)->delete();
        }

        Schema::table('saldo_cuti', function (Blueprint $table) {
            $table->dropUnique('saldo_cuti_pegawai_jenis_tahun_unik');
            $table->dropForeign(['jenis_cuti_id']);
            $table->dropColumn('jenis_cuti_id');
            $table->unique(['pegawai_id', 'tahun'], 'saldo_cuti_pegawai_tahun_unik');
        });

        Schema::table('pengajuan_cuti', function (Blueprint $table) {
            $table->dropForeign(['jenis_cuti_id']);
            $table->dropColumn('jenis_cuti_id');
        });

        Schema::dropIfExists('jenis_cuti');
    }

    public function down(): void
    {
        Schema::create('jenis_cuti', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique();
            $table->string('nama', 100)->unique();
            $table->text('deskripsi')->nullable();
            $table->unsignedInteger('jatah_bawaan')->default(12);
            $table->boolean('mengurangi_saldo')->default(true);
            $table->boolean('aktif')->default(true);
            $table->timestamp('dibuat_pada')->useCurrent();
            $table->timestamp('diperbarui_pada')->useCurrent();
        });

        $cutiTahunanId = DB::table('jenis_cuti')->insertGetId([
            'kode' => 'TAHUNAN',
            'nama' => 'Cuti Tahunan',
            'jatah_bawaan' => 12,
            'mengurangi_saldo' => true,
            'aktif' => true,
        ]);

        Schema::table('saldo_cuti', function (Blueprint $table) {
            $table->dropUnique('saldo_cuti_pegawai_tahun_unik');
            $table->foreignId('jenis_cuti_id')->nullable()->after('pegawai_id')->constrained('jenis_cuti')->restrictOnDelete();
        });
        DB::table('saldo_cuti')->update(['jenis_cuti_id' => $cutiTahunanId]);
        Schema::table('saldo_cuti', function (Blueprint $table) {
            $table->unique(['pegawai_id', 'jenis_cuti_id', 'tahun'], 'saldo_cuti_pegawai_jenis_tahun_unik');
        });

        Schema::table('pengajuan_cuti', function (Blueprint $table) {
            $table->foreignId('jenis_cuti_id')->nullable()->after('pegawai_id')->constrained('jenis_cuti')->restrictOnDelete();
        });
        DB::table('pengajuan_cuti')->update(['jenis_cuti_id' => $cutiTahunanId]);
    }
};
