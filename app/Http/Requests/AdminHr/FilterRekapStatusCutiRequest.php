<?php

namespace App\Http\Requests\AdminHr;

use App\Enums\StatusPengajuanCuti;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class FilterRekapStatusCutiRequest extends FormRequest
{
    /** @return list<StatusPengajuanCuti> */
    public static function statusLaporan(): array
    {
        return [StatusPengajuanCuti::MenungguAtasan, StatusPengajuanCuti::MenungguHr, StatusPengajuanCuti::Disetujui, StatusPengajuanCuti::Ditolak, StatusPengajuanCuti::Dibatalkan];
    }

    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'tanggal_mulai' => ['required', 'date_format:Y-m-d'],
            'tanggal_selesai' => ['required', 'date_format:Y-m-d', 'after_or_equal:tanggal_mulai'],
            'status' => ['required', 'array', 'min:1'],
            'status.*' => ['required', 'distinct', Rule::in(array_map(fn (StatusPengajuanCuti $status): string => $status->value, self::statusLaporan()))],
            'bagian_organisasi_id' => ['nullable', 'integer', 'exists:bagian_organisasi,id'],
            'cari' => ['nullable', 'string', 'max:100'],
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
            'status.required' => 'Pilih minimal satu status pengajuan.',
            'status.min' => 'Pilih minimal satu status pengajuan.',
            'status.*.in' => 'Status pengajuan yang dipilih tidak valid.',
            'bagian_organisasi_id.exists' => 'Bagian organisasi yang dipilih tidak tersedia.',
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
        $statusBawaan = array_map(fn (StatusPengajuanCuti $status): string => $status->value, self::statusLaporan());

        $this->merge([
            'tanggal_mulai' => $this->input('tanggal_mulai') ?: now()->startOfYear()->toDateString(),
            'tanggal_selesai' => $this->input('tanggal_selesai') ?: now()->toDateString(),
            'status' => $this->boolean('filter') ? (array) $this->input('status', []) : $statusBawaan,
            'bagian_organisasi_id' => $this->input('bagian_organisasi_id') ?: null,
            'cari' => Str::squish($this->string('cari')->toString()),
        ]);
    }
}
