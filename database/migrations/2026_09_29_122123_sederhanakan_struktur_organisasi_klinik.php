<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $databaseDriver = DB::connection()->getDriverName();

        Schema::table('jabatan', function (Blueprint $table) {
            $table->dropForeign(['unit_bisnis_id']);
            $table->dropUnique(['unit_bisnis_id', 'kode']);
            $table->dropIndex(['unit_bisnis_id', 'kategori', 'aktif']);
            $table->dropColumn('unit_bisnis_id');
        });

        Schema::table('bagian_organisasi', function (Blueprint $table) use ($databaseDriver) {
            $table->dropForeign(['induk_id']);

            if ($databaseDriver === 'sqlite') {
                $table->dropForeign(['unit_bisnis_id']);
            } else {
                $table->dropForeign('departemen_unit_bisnis_id_foreign');
            }

            $table->dropUnique(['unit_bisnis_id', 'kode']);
            $table->dropIndex(['unit_bisnis_id', 'jenis', 'aktif']);
            $table->dropColumn(['unit_bisnis_id', 'induk_id', 'jenis']);
        });

        Schema::dropIfExists('unit_bisnis');
    }

    public function down(): void
    {
        Schema::create('unit_bisnis', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique();
            $table->string('nama', 100)->unique();
            $table->string('kategori', 20);
            $table->boolean('aktif')->default(true);
            $table->timestamp('dibuat_pada')->useCurrent();
            $table->timestamp('diperbarui_pada')->useCurrent();
        });

        Schema::table('bagian_organisasi', function (Blueprint $table) {
            $table->foreignId('unit_bisnis_id')->nullable()->after('id')->constrained('unit_bisnis')->restrictOnDelete();
            $table->foreignId('induk_id')->nullable()->after('unit_bisnis_id')->constrained('bagian_organisasi')->restrictOnDelete();
            $table->string('jenis', 20)->default('bagian')->after('induk_id');
            $table->unique(['unit_bisnis_id', 'kode']);
            $table->index(['unit_bisnis_id', 'jenis', 'aktif']);
        });

        Schema::table('jabatan', function (Blueprint $table) {
            $table->foreignId('unit_bisnis_id')->nullable()->after('id')->constrained('unit_bisnis')->restrictOnDelete();
            $table->unique(['unit_bisnis_id', 'kode']);
            $table->index(['unit_bisnis_id', 'kategori', 'aktif']);
        });
    }
};
