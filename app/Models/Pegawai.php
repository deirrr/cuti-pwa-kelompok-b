<?php

namespace App\Models;

use Database\Factories\PegawaiFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use InvalidArgumentException;

#[Table('pegawai')]
#[Fillable([
    'pengguna_id',
    'departemen_id',
    'atasan_id',
    'nomor_induk',
    'nama',
    'jabatan',
    'tanggal_masuk',
    'aktif',
])]
class Pegawai extends Model
{
    /** @use HasFactory<PegawaiFactory> */
    use HasFactory;

    public const CREATED_AT = 'dibuat_pada';

    public const UPDATED_AT = 'diperbarui_pada';

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class);
    }

    public function departemen(): BelongsTo
    {
        return $this->belongsTo(Departemen::class);
    }

    public function atasan(): BelongsTo
    {
        return $this->belongsTo(self::class, 'atasan_id');
    }

    public function bawahan(): HasMany
    {
        return $this->hasMany(self::class, 'atasan_id');
    }

    public function saldoCuti(): HasMany
    {
        return $this->hasMany(SaldoCuti::class);
    }

    public function pengajuanCuti(): HasMany
    {
        return $this->hasMany(PengajuanCuti::class);
    }

    protected static function booted(): void
    {
        static::saving(function (Pegawai $pegawai): void {
            if ($pegawai->getKey() !== null && $pegawai->atasan_id === $pegawai->getKey()) {
                throw new InvalidArgumentException('Pegawai tidak dapat menjadi atasan bagi dirinya sendiri.');
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_masuk' => 'date',
            'aktif' => 'boolean',
        ];
    }
}
