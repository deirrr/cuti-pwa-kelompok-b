<?php

namespace App\Http\Requests\AdminHr;

use App\Enums\PeranPengguna;
use App\Models\HariLibur;
use App\Models\Pengguna;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class PerbaruiHariLiburRequest extends FormRequest
{
    public function authorize(): bool
    {
        $pengguna = $this->user();

        return $pengguna instanceof Pengguna && $pengguna->memilikiPeran(PeranPengguna::AdminHr);
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'tanggal' => ['required', 'date'],
            'nama' => ['required', 'string', 'max:150'],
            'aktif' => ['required', 'boolean'],
        ];
    }

    /** @return array<callable(Validator): void> */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->has('tanggal')) {
                    return;
                }

                /** @var HariLibur $hariLibur */
                $hariLibur = $this->route('hariLibur');

                $sudahTerdaftar = HariLibur::query()
                    ->whereDate('tanggal', $this->date('tanggal'))
                    ->whereKeyNot($hariLibur->getKey())
                    ->exists();

                if ($sudahTerdaftar) {
                    $validator->errors()->add('tanggal', 'Tanggal tersebut sudah terdaftar sebagai hari libur.');
                }
            },
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'tanggal.required' => 'Tanggal hari libur wajib dipilih.',
            'tanggal.date' => 'Tanggal hari libur tidak valid.',
            'tanggal.unique' => 'Tanggal tersebut sudah terdaftar sebagai hari libur.',
            'nama.required' => 'Nama hari libur wajib diisi.',
            'nama.max' => 'Nama hari libur maksimal 150 karakter.',
            'aktif.boolean' => 'Status hari libur tidak valid.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'nama' => $this->string('nama')->trim()->toString(),
        ]);
    }
}
