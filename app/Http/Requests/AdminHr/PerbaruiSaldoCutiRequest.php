<?php

namespace App\Http\Requests\AdminHr;

use App\Enums\PeranPengguna;
use App\Models\Pegawai;
use App\Models\Pengguna;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PerbaruiSaldoCutiRequest extends FormRequest
{
    public function authorize(): bool
    {
        $pengguna = $this->user();

        $pegawai = $this->route('pegawai');

        return $pengguna instanceof Pengguna
            && $pengguna->memilikiPeran(PeranPengguna::AdminHr)
            && $pegawai instanceof Pegawai
            && in_array($pegawai->pengguna?->peran, [PeranPengguna::Karyawan, PeranPengguna::Atasan], true);
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'tahun' => ['required', 'integer', 'between:2000,2100'],
            'jatah_awal' => ['required', 'integer', 'between:0,365'],
            'saldo_tersedia' => ['required', 'integer', 'between:0,365', 'lte:jatah_awal'],
            'catatan' => ['nullable', 'string', 'max:255'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'tahun.between' => 'Tahun harus berada antara 2000 dan 2100.',
            'jatah_awal.between' => 'Jatah cuti harus berada antara 0 dan 365 hari.',
            'saldo_tersedia.between' => 'Saldo tersedia harus berada antara 0 dan 365 hari.',
            'saldo_tersedia.lte' => 'Saldo tersedia tidak boleh melebihi jatah awal.',
        ];
    }
}
