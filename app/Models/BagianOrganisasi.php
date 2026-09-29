<?php

namespace App\Models;

use Database\Factories\BagianOrganisasiFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table('bagian_organisasi')]
#[Fillable(['kode', 'nama', 'aktif'])]
class BagianOrganisasi extends Model
{
    /** @use HasFactory<BagianOrganisasiFactory> */
    use HasFactory;

    public const CREATED_AT = 'dibuat_pada';

    public const UPDATED_AT = 'diperbarui_pada';

    public function pegawai(): HasMany
    {
        return $this->hasMany(Pegawai::class);
    }

    public function jabatan(): HasMany
    {
        return $this->hasMany(Jabatan::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'aktif' => 'boolean',
        ];
    }
}
