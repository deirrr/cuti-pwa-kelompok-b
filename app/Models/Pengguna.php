<?php

namespace App\Models;

use App\Enums\PeranPengguna;
use Database\Factories\PenggunaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Table('pengguna')]
#[Fillable(['email', 'kata_sandi', 'peran', 'aktif', 'terakhir_masuk_pada'])]
#[Hidden(['kata_sandi', 'token_ingat'])]
class Pengguna extends Authenticatable
{
    /** @use HasFactory<PenggunaFactory> */
    use HasFactory, Notifiable;

    public const CREATED_AT = 'dibuat_pada';

    public const UPDATED_AT = 'diperbarui_pada';

    public function pegawai(): HasOne
    {
        return $this->hasOne(Pegawai::class);
    }

    public function persetujuanDiberikan(): HasMany
    {
        return $this->hasMany(PersetujuanCuti::class, 'pemberi_keputusan_id');
    }

    public function peranSistem(): BelongsToMany
    {
        return $this->belongsToMany(Peran::class, 'pengguna_peran')
            ->withPivot('ditetapkan_pada');
    }

    public function memilikiPeran(PeranPengguna|string $peran): bool
    {
        $kodePeran = $peran instanceof PeranPengguna ? $peran->value : $peran;

        if ($this->peran?->value === $kodePeran) {
            return true;
        }

        if ($this->relationLoaded('peranSistem')) {
            return $this->peranSistem->contains('kode', $kodePeran);
        }

        return $this->peranSistem()->where('kode', $kodePeran)->exists();
    }

    /**
     * @param  array<int, string>  $peranDiizinkan
     */
    public function memilikiSalahSatuPeran(array $peranDiizinkan): bool
    {
        if (in_array($this->peran?->value, $peranDiizinkan, true)) {
            return true;
        }

        return $this->peranSistem()->whereIn('kode', $peranDiizinkan)->exists();
    }

    public function getAuthPasswordName(): string
    {
        return 'kata_sandi';
    }

    public function getRememberTokenName(): string
    {
        return 'token_ingat';
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'peran' => PeranPengguna::class,
            'aktif' => 'boolean',
            'terakhir_masuk_pada' => 'datetime',
            'kata_sandi' => 'hashed',
        ];
    }
}
