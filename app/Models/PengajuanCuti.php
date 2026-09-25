<?php

namespace App\Models;

use App\Enums\StatusPengajuanCuti;
use Database\Factories\PengajuanCutiFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table('pengajuan_cuti')]
#[Fillable([
    'nomor_pengajuan',
    'pegawai_id',
    'jenis_cuti_id',
    'status',
    'alasan',
    'jumlah_hari',
    'diajukan_pada',
    'dibatalkan_pada',
])]
class PengajuanCuti extends Model
{
    /** @use HasFactory<PengajuanCutiFactory> */
    use HasFactory;

    public const CREATED_AT = 'dibuat_pada';

    public const UPDATED_AT = 'diperbarui_pada';

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function jenisCuti(): BelongsTo
    {
        return $this->belongsTo(JenisCuti::class);
    }

    public function tanggalCuti(): HasMany
    {
        return $this->hasMany(TanggalPengajuanCuti::class);
    }

    public function persetujuan(): HasMany
    {
        return $this->hasMany(PersetujuanCuti::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => StatusPengajuanCuti::class,
            'jumlah_hari' => 'integer',
            'diajukan_pada' => 'datetime',
            'dibatalkan_pada' => 'datetime',
        ];
    }
}
