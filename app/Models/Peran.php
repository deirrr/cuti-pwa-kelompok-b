<?php

namespace App\Models;

use Database\Factories\PeranFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Table('peran')]
#[Fillable(['kode', 'nama', 'aktif'])]
class Peran extends Model
{
    /** @use HasFactory<PeranFactory> */
    use HasFactory;

    public const CREATED_AT = 'dibuat_pada';

    public const UPDATED_AT = 'diperbarui_pada';

    public function pengguna(): BelongsToMany
    {
        return $this->belongsToMany(Pengguna::class, 'pengguna_peran')
            ->withPivot('ditetapkan_pada');
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
