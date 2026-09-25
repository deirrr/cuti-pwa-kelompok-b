<?php

namespace App\Models;

use Database\Factories\JenisCutiFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table('jenis_cuti')]
#[Fillable(['kode', 'nama', 'deskripsi', 'jatah_bawaan', 'mengurangi_saldo', 'aktif'])]
class JenisCuti extends Model
{
    /** @use HasFactory<JenisCutiFactory> */
    use HasFactory;

    public const CREATED_AT = 'dibuat_pada';

    public const UPDATED_AT = 'diperbarui_pada';

    public function saldoCuti(): HasMany
    {
        return $this->hasMany(SaldoCuti::class);
    }

    public function pengajuanCuti(): HasMany
    {
        return $this->hasMany(PengajuanCuti::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'jatah_bawaan' => 'integer',
            'mengurangi_saldo' => 'boolean',
            'aktif' => 'boolean',
        ];
    }
}
