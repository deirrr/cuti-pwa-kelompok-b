<?php

namespace App\Models;

use Database\Factories\PenugasanJabatanFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use InvalidArgumentException;

#[Table('penugasan_jabatan')]
#[Fillable([
    'pegawai_id',
    'jabatan_id',
    'utama',
    'tanggal_mulai',
    'tanggal_selesai',
    'aktif',
])]
class PenugasanJabatan extends Model
{
    /** @use HasFactory<PenugasanJabatanFactory> */
    use HasFactory;

    public const CREATED_AT = 'dibuat_pada';

    public const UPDATED_AT = 'diperbarui_pada';

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function jabatan(): BelongsTo
    {
        return $this->belongsTo(Jabatan::class);
    }

    protected static function booted(): void
    {
        static::saving(function (PenugasanJabatan $penugasan): void {
            if ($penugasan->tanggal_selesai !== null && $penugasan->tanggal_selesai->isBefore($penugasan->tanggal_mulai)) {
                throw new InvalidArgumentException('Tanggal selesai penugasan tidak boleh sebelum tanggal mulai.');
            }

            if (! $penugasan->aktif || ! $penugasan->utama || $penugasan->pegawai_id === null) {
                return;
            }

            $sudahMemilikiPenugasanUtama = self::query()
                ->where('pegawai_id', $penugasan->pegawai_id)
                ->where('aktif', true)
                ->where('utama', true)
                ->when(
                    $penugasan->exists,
                    fn ($query) => $query->whereKeyNot($penugasan->getKey()),
                )
                ->exists();

            if ($sudahMemilikiPenugasanUtama) {
                throw new InvalidArgumentException('Pegawai hanya boleh memiliki satu penugasan utama yang aktif.');
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'utama' => 'boolean',
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'aktif' => 'boolean',
        ];
    }
}
