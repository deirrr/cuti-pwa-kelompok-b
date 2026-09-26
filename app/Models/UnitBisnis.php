<?php

namespace App\Models;

use App\Enums\KategoriUnitBisnis;
use Database\Factories\UnitBisnisFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table('unit_bisnis')]
#[Fillable(['kode', 'nama', 'kategori', 'aktif'])]
class UnitBisnis extends Model
{
    /** @use HasFactory<UnitBisnisFactory> */
    use HasFactory;

    public const CREATED_AT = 'dibuat_pada';

    public const UPDATED_AT = 'diperbarui_pada';

    public function departemen(): HasMany
    {
        return $this->hasMany(Departemen::class);
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
            'kategori' => KategoriUnitBisnis::class,
            'aktif' => 'boolean',
        ];
    }
}
