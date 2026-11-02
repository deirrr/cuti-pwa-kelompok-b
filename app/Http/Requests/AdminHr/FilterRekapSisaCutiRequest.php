<?php

namespace App\Http\Requests\AdminHr;

use Carbon\CarbonImmutable;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class FilterRekapSisaCutiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tanggal_mulai' => ['required', 'date_format:Y-m-d'],
            'tanggal_selesai' => ['required', 'date_format:Y-m-d', 'after_or_equal:tanggal_mulai'],
            'bagian_organisasi_id' => ['nullable', 'integer', 'exists:bagian_organisasi,id'],
            'status_pegawai' => ['required', Rule::in(['semua', 'aktif', 'nonaktif'])],
            'cari' => ['nullable', 'string', 'max:100'],
        ];
    }

    /** @return array<int, callable(Validator): void> */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->hasAny(['tanggal_mulai', 'tanggal_selesai'])) {
                    return;
                }

                if ($this->tanggalMulai()->year !== $this->tanggalSelesai()->year) {
                    $validator->errors()->add(
                        'tanggal_selesai',
                        'Periode laporan harus berada dalam tahun yang sama.',
                    );
                }
            },
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'tanggal_mulai.required' => 'Tanggal mulai wajib dipilih.',
            'tanggal_mulai.date_format' => 'Format tanggal mulai tidak valid.',
            'tanggal_selesai.required' => 'Tanggal selesai wajib dipilih.',
            'tanggal_selesai.date_format' => 'Format tanggal selesai tidak valid.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus sama dengan atau setelah tanggal mulai.',
            'bagian_organisasi_id.exists' => 'Bagian organisasi yang dipilih tidak tersedia.',
            'status_pegawai.in' => 'Status pegawai yang dipilih tidak valid.',
            'cari.max' => 'Pencarian maksimal 100 karakter.',
        ];
    }

    public function tanggalMulai(): CarbonImmutable
    {
        return CarbonImmutable::parse($this->string('tanggal_mulai')->toString());
    }

    public function tanggalSelesai(): CarbonImmutable
    {
        return CarbonImmutable::parse($this->string('tanggal_selesai')->toString());
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'tanggal_mulai' => $this->input('tanggal_mulai') ?: now()->startOfYear()->toDateString(),
            'tanggal_selesai' => $this->input('tanggal_selesai') ?: now()->toDateString(),
            'bagian_organisasi_id' => $this->input('bagian_organisasi_id') ?: null,
            'status_pegawai' => $this->input('status_pegawai') ?: 'semua',
            'cari' => Str::squish($this->string('cari')->toString()),
        ]);
    }
}
