<?php

namespace App\Http\Requests\AdminHr;

use App\Enums\KategoriUnitBisnis;
use App\Enums\PeranPengguna;
use App\Models\Pengguna;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SimpanUnitBisnisRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $pengguna = $this->user();

        return $pengguna instanceof Pengguna && $pengguna->memilikiPeran(PeranPengguna::AdminHr);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'kode' => ['required', 'string', 'max:20', 'regex:/^[A-Z0-9-]+$/', Rule::unique('unit_bisnis', 'kode')],
            'nama' => ['required', 'string', 'max:150', Rule::unique('unit_bisnis', 'nama')],
            'kategori' => ['required', Rule::enum(KategoriUnitBisnis::class)],
            'aktif' => ['required', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'kode.required' => 'Kode unit wajib diisi.',
            'kode.regex' => 'Kode hanya boleh berisi huruf kapital, angka, dan tanda hubung.',
            'kode.unique' => 'Kode unit sudah digunakan.',
            'nama.required' => 'Nama unit wajib diisi.',
            'nama.unique' => 'Nama unit sudah digunakan.',
            'kategori.required' => 'Kategori unit wajib dipilih.',
            'kategori.enum' => 'Kategori unit tidak valid.',
            'aktif.boolean' => 'Status unit tidak valid.',
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
