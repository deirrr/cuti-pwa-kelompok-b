<?php

namespace App\Http\Requests;

use App\Enums\KeputusanPersetujuan;
use App\Enums\PeranPengguna;
use App\Models\Pengguna;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SimpanKeputusanCutiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() instanceof Pengguna
            && $this->user()->memilikiSalahSatuPeran([
                PeranPengguna::Atasan->value,
                PeranPengguna::AdminHr->value,
            ]);
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'keputusan' => ['required', Rule::in([
                KeputusanPersetujuan::Disetujui->value,
                KeputusanPersetujuan::Ditolak->value,
            ])],
            'catatan' => ['nullable', 'required_if:keputusan,ditolak', 'string', 'max:1000'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'keputusan.required' => 'Keputusan wajib dipilih.',
            'keputusan.in' => 'Keputusan tidak valid.',
            'catatan.required_if' => 'Catatan wajib diisi saat pengajuan ditolak.',
            'catatan.max' => 'Catatan maksimal 1000 karakter.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'catatan' => Str::squish($this->string('catatan')->toString()),
        ]);
    }
}
