<?php

namespace Database\Factories;

use App\Enums\PeranPengguna;
use App\Models\Pengguna;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<Pengguna>
 */
class PenggunaFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $kataSandi;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail(),
            'kata_sandi' => static::$kataSandi ??= Hash::make('password'),
            'peran' => PeranPengguna::Karyawan,
            'aktif' => true,
            'token_ingat' => Str::random(10),
            'terakhir_masuk_pada' => null,
        ];
    }

    public function atasan(): static
    {
        return $this->state(fn (array $attributes): array => [
            'peran' => PeranPengguna::Atasan,
        ]);
    }

    public function adminHr(): static
    {
        return $this->state(fn (array $attributes): array => [
            'peran' => PeranPengguna::AdminHr,
        ]);
    }

    public function nonaktif(): static
    {
        return $this->state(fn (array $attributes): array => [
            'aktif' => false,
        ]);
    }
}
