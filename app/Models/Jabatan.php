<?php

namespace App\Models;

use App\Enums\KategoriJabatan;
use Database\Factories\JabatanFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table('jabatan')]
#[Fillable([
    'bagian_organisasi_id',
    'atasan_jabatan_id',
    'kode',
    'nama',
    'kategori',
    'langsung_ke_hr',
    'aktif',
])]
class Jabatan extends Model
{
    /** @use HasFactory<JabatanFactory> */
    use HasFactory;

    public const CREATED_AT = 'dibuat_pada';

    public const UPDATED_AT = 'diperbarui_pada';

    public function bagianOrganisasi(): BelongsTo
    {
        return $this->belongsTo(BagianOrganisasi::class);
    }

    public function atasanJabatan(): BelongsTo
    {
        return $this->belongsTo(self::class, 'atasan_jabatan_id');
    }

    public function bawahanJabatan(): HasMany
    {
        return $this->hasMany(self::class, 'atasan_jabatan_id');
    }

    public function penugasan(): HasMany
    {
        return $this->hasMany(PenugasanJabatan::class);
    }

    public function pegawai(): BelongsToMany
    {
        return $this->belongsToMany(Pegawai::class, 'penugasan_jabatan')
            ->withPivot(['id', 'utama', 'tanggal_mulai', 'tanggal_selesai', 'aktif']);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'kategori' => KategoriJabatan::class,
            'langsung_ke_hr' => 'boolean',
            'aktif' => 'boolean',
        ];
    }
}
