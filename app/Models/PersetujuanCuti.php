<?php

namespace App\Models;

use App\Enums\KeputusanPersetujuan;
use App\Enums\TahapPersetujuan;
use Database\Factories\PersetujuanCutiFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table('persetujuan_cuti')]
#[Fillable([
    'pengajuan_cuti_id',
    'pemberi_keputusan_id',
    'tahap',
    'keputusan',
    'catatan',
    'diputuskan_pada',
])]
class PersetujuanCuti extends Model
{
    /** @use HasFactory<PersetujuanCutiFactory> */
    use HasFactory;

    public const CREATED_AT = 'dibuat_pada';

    public const UPDATED_AT = null;

    public function pengajuanCuti(): BelongsTo
    {
        return $this->belongsTo(PengajuanCuti::class);
    }

    public function pemberiKeputusan(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'pemberi_keputusan_id');
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tahap' => TahapPersetujuan::class,
            'keputusan' => KeputusanPersetujuan::class,
            'diputuskan_pada' => 'datetime',
        ];
    }
}
