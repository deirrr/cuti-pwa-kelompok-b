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
        Schema::rename('departemen', 'bagian_organisasi');

        Schema::table('pegawai', function (Blueprint $table) {
            $table->renameColumn('departemen_id', 'bagian_organisasi_id');
        });

        Schema::table('jabatan', function (Blueprint $table) {
            $table->renameColumn('departemen_id', 'bagian_organisasi_id');
        });

        Schema::table('bagian_organisasi', function (Blueprint $table) {
            $table->dropUnique('departemen_kode_unique');
            $table->dropUnique('departemen_nama_unique');
            $table->foreignId('induk_id')
                ->nullable()
                ->after('unit_bisnis_id')
                ->constrained('bagian_organisasi')
                ->restrictOnDelete();
            $table->string('jenis', 20)->default('bagian')->after('induk_id');
            $table->unique(['unit_bisnis_id', 'kode']);
            $table->index(['unit_bisnis_id', 'jenis', 'aktif']);
        });

        DB::table('bagian_organisasi')
            ->whereIn('unit_bisnis_id', DB::table('unit_bisnis')->where('kategori', 'head_office')->select('id'))
            ->update(['jenis' => 'departemen']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bagian_organisasi', function (Blueprint $table) {
            $table->dropForeign(['induk_id']);
            $table->dropUnique(['unit_bisnis_id', 'kode']);
            $table->dropIndex(['unit_bisnis_id', 'jenis', 'aktif']);
            $table->dropColumn(['induk_id', 'jenis']);
            $table->unique('kode', 'departemen_kode_unique');
            $table->unique('nama', 'departemen_nama_unique');
        });

        Schema::table('pegawai', function (Blueprint $table) {
            $table->renameColumn('bagian_organisasi_id', 'departemen_id');
        });

        Schema::table('jabatan', function (Blueprint $table) {
            $table->renameColumn('bagian_organisasi_id', 'departemen_id');
        });

        Schema::rename('bagian_organisasi', 'departemen');
    }
};
