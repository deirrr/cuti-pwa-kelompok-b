<?php

namespace App\Http\Requests\AdminHr;

use App\Enums\PeranPengguna;
use App\Models\BagianOrganisasi;
use App\Models\Pegawai;
use App\Models\Pengguna;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class SimpanPegawaiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() instanceof Pengguna
            && $this->user()->memilikiPeran(PeranPengguna::AdminHr);
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'nomor_induk' => ['required', 'string', 'max:30', Rule::unique('pegawai', 'nomor_induk')],
            'nama' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email:rfc', 'max:255', Rule::unique('pengguna', 'email')],
            'kata_sandi' => ['required', 'string', 'min:8', 'max:255', 'confirmed'],
            'bagian_organisasi_id' => [
                'required',
                'integer',
                Rule::exists((new BagianOrganisasi)->getTable(), 'id')->where('aktif', true),
            ],
            'peran' => ['required', Rule::enum(PeranPengguna::class)],
            'atasan_id' => ['nullable', 'required_if:peran,karyawan', 'integer', Rule::exists('pegawai', 'id')],
            'jatah_cuti' => ['nullable', 'required_unless:peran,admin_hr', 'integer', 'between:0,365'],
            'tanggal_masuk' => ['nullable', 'date'],
            'aktif' => ['required', 'boolean'],
        ];
    }

    /** @return array<int, callable(Validator): void> */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($this->string('peran')->toString() !== PeranPengguna::Karyawan->value) {
                    return;
                }

                $atasan = Pegawai::query()->with('pengguna')->find($this->integer('atasan_id'));

                if (
                    ! $atasan?->aktif
                    || ! $atasan->pengguna?->aktif
                    || $atasan->pengguna->peran !== PeranPengguna::Atasan
                ) {
                    $validator->errors()->add('atasan_id', 'Atasan langsung harus merupakan pegawai aktif dengan peran Atasan.');
                }
            },
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'nomor_induk.required' => 'NIK wajib diisi.',
            'nomor_induk.unique' => 'NIK sudah digunakan.',
            'nama.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'kata_sandi.required' => 'Kata sandi awal wajib diisi.',
            'kata_sandi.min' => 'Kata sandi minimal 8 karakter.',
            'kata_sandi.confirmed' => 'Konfirmasi kata sandi tidak sesuai.',
            'bagian_organisasi_id.required' => 'Bagian organisasi wajib dipilih.',
            'bagian_organisasi_id.exists' => 'Bagian organisasi tidak tersedia atau tidak aktif.',
            'peran.required' => 'Peran wajib dipilih.',
            'peran.enum' => 'Peran yang dipilih tidak valid.',
            'atasan_id.required_if' => 'Atasan langsung wajib dipilih untuk Staff.',
            'jatah_cuti.required_unless' => 'Jatah cuti wajib diisi untuk Staff dan Atasan.',
            'jatah_cuti.between' => 'Jatah cuti harus berada antara 0 dan 365 hari.',
            'tanggal_masuk.date' => 'Tanggal masuk tidak valid.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'nomor_induk' => Str::upper($this->string('nomor_induk')->trim()->toString()),
            'nama' => Str::squish($this->string('nama')->toString()),
            'email' => Str::lower($this->string('email')->trim()->toString()),
            'aktif' => $this->boolean('aktif'),
        ]);
    }
}
