<?php

namespace App\Models;

use App\Enums\JenisHariLibur;
use Database\Factories\HariLiburFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Table('hari_libur')]
#[Fillable(['tanggal', 'nama', 'jenis', 'aktif'])]
class HariLibur extends Model
{
    /** @use HasFactory<HariLiburFactory> */
    use HasFactory;

    public const CREATED_AT = 'dibuat_pada';

    public const UPDATED_AT = 'diperbarui_pada';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'jenis' => JenisHariLibur::class,
            'aktif' => 'boolean',
        ];
    }
}
