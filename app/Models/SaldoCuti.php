<?php

namespace App\Models;

use Database\Factories\SaldoCutiFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use InvalidArgumentException;

#[Table('saldo_cuti')]
#[Fillable([
    'pegawai_id',
    'jenis_cuti_id',
    'tahun',
    'jatah_awal',
    'saldo_tersedia',
    'catatan',
])]
class SaldoCuti extends Model
{
    /** @use HasFactory<SaldoCutiFactory> */
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

    protected static function booted(): void
    {
        static::saving(function (SaldoCuti $saldoCuti): void {
            if ($saldoCuti->jatah_awal < 0 || $saldoCuti->saldo_tersedia < 0) {
                throw new InvalidArgumentException('Saldo cuti tidak boleh bernilai negatif.');
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tahun' => 'integer',
            'jatah_awal' => 'integer',
            'saldo_tersedia' => 'integer',
        ];
    }
}
