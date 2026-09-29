<?php

namespace App\Http\Requests\AdminHr;

use App\Enums\PeranPengguna;
use App\Models\Pengguna;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SimpanBagianOrganisasiRequest extends FormRequest
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
            'kode' => ['required', 'string', 'max:20', 'regex:/^[A-Z0-9-]+$/', Rule::unique('bagian_organisasi', 'kode')],
            'nama' => ['required', 'string', 'max:100', Rule::unique('bagian_organisasi', 'nama')],
            'aktif' => ['required', 'boolean'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'kode.required' => 'Kode bagian organisasi wajib diisi.',
            'kode.regex' => 'Kode hanya boleh berisi huruf kapital, angka, dan tanda hubung.',
            'kode.unique' => 'Kode bagian organisasi sudah digunakan.',
            'nama.required' => 'Nama bagian organisasi wajib diisi.',
            'nama.unique' => 'Nama bagian organisasi sudah digunakan.',
            'aktif.boolean' => 'Status bagian organisasi tidak valid.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'kode' => $this->string('kode')->trim()->upper()->toString(),
            'nama' => $this->string('nama')->trim()->toString(),
        ]);
    }
}
