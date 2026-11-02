<?php

namespace App\Http\Requests;

use App\Enums\StatusPengajuanCuti;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class FilterRekapStaffRequest extends FormRequest
{
    /** @return list<StatusPengajuanCuti> */
    public static function statusLaporan(): array
    {
        return [
            StatusPengajuanCuti::MenungguAtasan,
            StatusPengajuanCuti::MenungguHr,
            StatusPengajuanCuti::Disetujui,
            StatusPengajuanCuti::Ditolak,
            StatusPengajuanCuti::Dibatalkan,
        ];
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
            'status.required' => 'Pilih minimal satu status pengajuan.',
            'status.min' => 'Pilih minimal satu status pengajuan.',
            'status.*.in' => 'Status pengajuan yang dipilih tidak valid.',
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
            'cari' => Str::squish($this->string('cari')->toString()),
        ]);
    }
}
