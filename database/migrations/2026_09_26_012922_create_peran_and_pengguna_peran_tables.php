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
        Schema::create('peran', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 30)->unique();
            $table->string('nama', 100);
            $table->boolean('aktif')->default(true);
            $table->timestamp('dibuat_pada')->useCurrent();
            $table->timestamp('diperbarui_pada')->useCurrent();
        });

        Schema::create('pengguna_peran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengguna_id')->constrained('pengguna')->cascadeOnDelete();
            $table->foreignId('peran_id')->constrained('peran')->cascadeOnDelete();
            $table->timestamp('ditetapkan_pada')->useCurrent();

            $table->unique(['pengguna_id', 'peran_id']);
        });

        DB::table('peran')->insert([
            ['kode' => 'karyawan', 'nama' => 'Karyawan'],
            ['kode' => 'atasan', 'nama' => 'Atasan'],
            ['kode' => 'admin_hr', 'nama' => 'Admin HR'],
        ]);

        DB::table('pengguna_peran')->insertUsing(
            ['pengguna_id', 'peran_id'],
            DB::table('pengguna')
                ->join('peran', 'peran.kode', '=', 'pengguna.peran')
                ->select(['pengguna.id', 'peran.id']),
        );

        DB::table('pengguna_peran')->insertUsing(
            ['pengguna_id', 'peran_id'],
            DB::table('pengguna')
                ->crossJoin('peran')
                ->where('pengguna.peran', '!=', 'karyawan')
                ->where('peran.kode', 'karyawan')
                ->select(['pengguna.id', 'peran.id']),
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengguna_peran');
        Schema::dropIfExists('peran');
    }
};
