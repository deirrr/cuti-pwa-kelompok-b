<?php

namespace App\Http\Requests;

use App\Models\Pegawai;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class MasukRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nomor_induk' => ['required', 'string', 'max:30'],
            'kata_sandi' => ['required', 'string'],
            'ingat_saya' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nomor_induk.required' => 'NIK wajib diisi.',
            'nomor_induk.max' => 'NIK tidak boleh lebih dari 30 karakter.',
            'kata_sandi.required' => 'Kata sandi wajib diisi.',
            'ingat_saya.boolean' => 'Pilihan ingat saya tidak valid.',
        ];
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $pegawai = Pegawai::query()
            ->with('pengguna')
            ->where('nomor_induk', $this->string('nomor_induk')->toString())
            ->first();
        $pengguna = $pegawai?->pengguna;
        $credentials = ['password' => $this->string('kata_sandi')->toString()];

        if (
            ! $pegawai?->aktif
            || ! $pengguna?->aktif
            || ! Auth::getProvider()->validateCredentials($pengguna, $credentials)
        ) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'nomor_induk' => 'NIK atau kata sandi tidak sesuai, atau akun tidak aktif.',
            ]);
        }

        Auth::getProvider()->rehashPasswordIfRequired($pengguna, $credentials);
        Auth::login($pengguna, $this->boolean('ingat_saya'));

        $pengguna->update(['terakhir_masuk_pada' => now()]);
        RateLimiter::clear($this->throttleKey());
    }

    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'nomor_induk' => "Terlalu banyak percobaan masuk. Coba lagi dalam {$seconds} detik.",
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('nomor_induk')->toString())).'|'.$this->ip();
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'nomor_induk' => Str::upper($this->string('nomor_induk')->trim()->toString()),
        ]);
    }
}
