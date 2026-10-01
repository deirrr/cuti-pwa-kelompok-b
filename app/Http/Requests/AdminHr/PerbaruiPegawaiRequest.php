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

class PerbaruiPegawaiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() instanceof Pengguna
            && $this->user()->memilikiPeran(PeranPengguna::AdminHr);
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        $pegawai = $this->pegawai();

        return [
            'nomor_induk' => ['required', 'string', 'max:30', Rule::unique('pegawai', 'nomor_induk')->ignore($pegawai)],
            'nama' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email:rfc', 'max:255', Rule::unique('pengguna', 'email')->ignore($pegawai?->pengguna_id)],
            'kata_sandi' => ['nullable', 'string', 'min:8', 'max:255', 'confirmed'],
            'bagian_organisasi_id' => ['required', 'integer', Rule::exists((new BagianOrganisasi)->getTable(), 'id')],
            'peran' => ['required', Rule::enum(PeranPengguna::class)],
            'atasan_id' => [
                'nullable',
                'required_if:peran,karyawan',
                'integer',
                Rule::exists('pegawai', 'id'),
                Rule::notIn(array_filter([$pegawai?->getKey()])),
            ],
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
                $pegawai = $this->pegawai();

                if (! $pegawai instanceof Pegawai) {
                    return;
                }

                $peran = PeranPengguna::tryFrom($this->string('peran')->toString());

                if ($peran === PeranPengguna::Karyawan) {
                    $atasan = Pegawai::query()->with('pengguna')->find($this->integer('atasan_id'));

                    if (
                        ! $atasan?->aktif
                        || ! $atasan->pengguna?->aktif
                        || $atasan->pengguna->peran !== PeranPengguna::Atasan
                    ) {
                        $validator->errors()->add('atasan_id', 'Atasan langsung harus merupakan pegawai aktif dengan peran Atasan.');
                    }
                }

                if (
                    ($peran !== PeranPengguna::Atasan || ! $this->boolean('aktif'))
                    && $pegawai->bawahan()->where('aktif', true)->exists()
                ) {
                    $validator->errors()->add('peran', 'Atasan yang masih memiliki Staff aktif tidak dapat diubah peran atau dinonaktifkan.');
                }

                if (
                    $this->user()?->is($pegawai->pengguna)
                    && ($peran !== PeranPengguna::AdminHr || ! $this->boolean('aktif'))
                ) {
                    $validator->errors()->add('peran', 'Admin HR tidak dapat mengubah peran atau menonaktifkan akunnya sendiri.');
                }

                if ($peran === PeranPengguna::AdminHr || ! $this->filled('jatah_cuti')) {
                    return;
                }

                $saldoCuti = $pegawai->saldoCuti()->where('tahun', now()->year)->first();
                $cutiTerpakai = $saldoCuti === null
                    ? 0
                    : max(0, $saldoCuti->jatah_awal - $saldoCuti->saldo_tersedia);

                if ($this->integer('jatah_cuti') < $cutiTerpakai) {
                    $validator->errors()->add('jatah_cuti', "Jatah cuti tidak boleh lebih kecil dari {$cutiTerpakai} hari yang sudah digunakan.");
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
            'kata_sandi.min' => 'Kata sandi minimal 8 karakter.',
            'kata_sandi.confirmed' => 'Konfirmasi kata sandi tidak sesuai.',
            'bagian_organisasi_id.required' => 'Bagian organisasi wajib dipilih.',
            'bagian_organisasi_id.exists' => 'Bagian organisasi tidak tersedia.',
            'peran.required' => 'Peran wajib dipilih.',
            'peran.enum' => 'Peran yang dipilih tidak valid.',
            'atasan_id.required_if' => 'Atasan langsung wajib dipilih untuk Staff.',
            'atasan_id.not_in' => 'Pegawai tidak dapat menjadi atasan bagi dirinya sendiri.',
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

    private function pegawai(): ?Pegawai
    {
        $pegawai = $this->route('pegawai');

        return $pegawai instanceof Pegawai ? $pegawai : null;
    }
}
