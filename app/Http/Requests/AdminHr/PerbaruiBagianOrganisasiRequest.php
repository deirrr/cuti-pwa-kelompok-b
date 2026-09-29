<?php

namespace App\Http\Requests\AdminHr;

use App\Enums\JenisBagianOrganisasi;
use App\Enums\KategoriUnitBisnis;
use App\Enums\PeranPengguna;
use App\Models\BagianOrganisasi;
use App\Models\Pengguna;
use App\Models\UnitBisnis;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class PerbaruiBagianOrganisasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        $pengguna = $this->user();

        return $pengguna instanceof Pengguna && $pengguna->memilikiPeran(PeranPengguna::AdminHr);
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        /** @var BagianOrganisasi $bagianOrganisasi */
        $bagianOrganisasi = $this->route('bagianOrganisasi');

        return [
            'unit_bisnis_id' => ['required', 'integer', Rule::exists('unit_bisnis', 'id')],
            'induk_id' => [
                'nullable', 'integer', Rule::exists('bagian_organisasi', 'id'), Rule::notIn([$bagianOrganisasi->getKey()]),
            ],
            'jenis' => ['required', Rule::enum(JenisBagianOrganisasi::class)],
            'kode' => [
                'required', 'string', 'max:20', 'regex:/^[A-Z0-9-]+$/',
                Rule::unique('bagian_organisasi', 'kode')
                    ->where('unit_bisnis_id', $this->integer('unit_bisnis_id'))
                    ->ignore($bagianOrganisasi),
            ],
            'nama' => ['required', 'string', 'max:100'],
            'aktif' => ['required', 'boolean'],
        ];
    }

    /** @return array<int, callable(Validator): void> */
    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->hasAny(['unit_bisnis_id', 'induk_id', 'jenis'])) {
                return;
            }

            /** @var BagianOrganisasi $bagianOrganisasi */
            $bagianOrganisasi = $this->route('bagianOrganisasi');
            $unitBisnis = UnitBisnis::query()->find($this->integer('unit_bisnis_id'));
            $induk = $this->filled('induk_id')
                ? BagianOrganisasi::query()->find($this->integer('induk_id'))
                : null;

            if ($induk !== null && ! $induk->unitBisnis()->is($unitBisnis)) {
                $validator->errors()->add('induk_id', 'Induk harus berada pada unit bisnis yang sama.');
            }

            $unitBerubah = $bagianOrganisasi->unit_bisnis_id !== $this->integer('unit_bisnis_id');
            $sudahDigunakan = $unitBerubah && (
                $bagianOrganisasi->anak()->exists()
                || $bagianOrganisasi->jabatan()->exists()
                || $bagianOrganisasi->pegawai()->exists()
            );

            if ($unitBerubah && $sudahDigunakan) {
                $validator->errors()->add('unit_bisnis_id', 'Unit bisnis tidak dapat diubah karena bagian organisasi sudah digunakan.');
            }

            for ($calonInduk = $induk; $calonInduk !== null; $calonInduk = $calonInduk->induk) {
                if ($calonInduk->is($bagianOrganisasi)) {
                    $validator->errors()->add('induk_id', 'Induk tidak boleh membentuk hubungan melingkar.');
                    break;
                }
            }

            $this->validasiJenisUntukUnit($validator, $unitBisnis);
        }];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'unit_bisnis_id.required' => 'Unit bisnis wajib dipilih.',
            'unit_bisnis_id.exists' => 'Unit bisnis tidak valid.',
            'induk_id.exists' => 'Induk bagian organisasi tidak valid.',
            'induk_id.not_in' => 'Bagian organisasi tidak dapat menjadi induk dirinya sendiri.',
            'jenis.required' => 'Jenis bagian organisasi wajib dipilih.',
            'jenis.enum' => 'Jenis bagian organisasi tidak valid.',
            'kode.required' => 'Kode bagian organisasi wajib diisi.',
            'kode.regex' => 'Kode hanya boleh berisi huruf kapital, angka, dan tanda hubung.',
            'kode.unique' => 'Kode sudah digunakan pada unit bisnis tersebut.',
            'nama.required' => 'Nama bagian organisasi wajib diisi.',
            'aktif.boolean' => 'Status bagian organisasi tidak valid.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'induk_id' => $this->input('induk_id') ?: null,
            'kode' => $this->string('kode')->trim()->upper()->toString(),
            'nama' => $this->string('nama')->trim()->toString(),
        ]);
    }

    private function validasiJenisUntukUnit(Validator $validator, ?UnitBisnis $unitBisnis): void
    {
        $jenis = JenisBagianOrganisasi::tryFrom($this->string('jenis')->toString());

        if ($unitBisnis?->kategori === KategoriUnitBisnis::Operasional && $jenis !== JenisBagianOrganisasi::Bagian) {
            $validator->errors()->add('jenis', 'Unit operasional hanya dapat menggunakan jenis Bagian.');
        }

        if ($unitBisnis?->kategori === KategoriUnitBisnis::HeadOffice && $jenis === JenisBagianOrganisasi::Bagian) {
            $validator->errors()->add('jenis', 'Head Office menggunakan jenis Direktorat atau Departemen.');
        }
    }
}
