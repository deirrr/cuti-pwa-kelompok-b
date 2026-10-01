<?php

namespace App\Http\Requests;

use App\Enums\JenisHariLibur;
use App\Enums\PeranPengguna;
use App\Enums\StatusPengajuanCuti;
use App\Models\HariLibur;
use App\Models\PengajuanCuti;
use App\Models\Pengguna;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;

class SimpanPengajuanCutiRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (! $this->user() instanceof Pengguna || ! $this->user()->memilikiSalahSatuPeran([
            PeranPengguna::Karyawan->value,
            PeranPengguna::Atasan->value,
        ])) {
            return false;
        }

        $pengajuan = $this->pengajuanCuti();

        return $pengajuan === null
            || ($pengajuan->pegawai_id === $this->user()->pegawai?->getKey()
                && in_array($pengajuan->status, [
                    StatusPengajuanCuti::MenungguAtasan,
                    StatusPengajuanCuti::MenungguHr,
                ], true)
                && ! $pengajuan->persetujuan()->exists());
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'tanggal_cuti' => [
                'required',
                'array',
                'min:1',
                $this->pengajuanCuti() === null ? 'max:31' : 'max:1',
            ],
            'tanggal_cuti.*' => ['required', 'date', 'after_or_equal:today', 'distinct:strict'],
            'alasan' => ['required', 'string', 'max:1000'],
        ];
    }

    /** @return array<int, callable(Validator): void> */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->hasAny(['tanggal_cuti', 'tanggal_cuti.*'])) {
                    return;
                }

                $tanggalCuti = $this->tanggalCuti();
                $tanggalMinggu = $tanggalCuti->first(
                    fn (string $tanggal): bool => CarbonImmutable::parse($tanggal)->isSunday(),
                );

                if ($tanggalMinggu !== null) {
                    $validator->errors()->add('tanggal_cuti', 'Hari Minggu tidak dapat dipilih sebagai tanggal cuti.');

                    return;
                }

                $hariLibur = HariLibur::query()
                    ->where('aktif', true)
                    ->where('jenis', JenisHariLibur::Nasional->value)
                    ->where(function ($query) use ($tanggalCuti): void {
                        foreach ($tanggalCuti as $tanggal) {
                            $query->orWhereDate('tanggal', $tanggal);
                        }
                    })
                    ->orderBy('tanggal')
                    ->first();

                if ($hariLibur !== null) {
                    $validator->errors()->add(
                        'tanggal_cuti',
                        "{$hariLibur->tanggal->translatedFormat('d F Y')} merupakan {$hariLibur->nama}.",
                    );

                    return;
                }

                $pegawai = $this->user()?->pegawai;

                if ($pegawai === null) {
                    return;
                }

                if (
                    $this->user()?->peran === PeranPengguna::Karyawan
                    && (! $pegawai->atasan?->aktif || ! $pegawai->atasan?->pengguna?->aktif)
                ) {
                    $validator->errors()->add('tanggal_cuti', 'Atasan langsung belum tersedia. Hubungi Admin HR.');

                    return;
                }

                $pengajuanSaatIni = $this->pengajuanCuti();
                $bertumpuk = PengajuanCuti::query()
                    ->whereBelongsTo($pegawai)
                    ->when($pengajuanSaatIni !== null, fn ($query) => $query->whereKeyNot($pengajuanSaatIni->getKey()))
                    ->whereIn('status', [
                        StatusPengajuanCuti::MenungguAtasan->value,
                        StatusPengajuanCuti::MenungguHr->value,
                        StatusPengajuanCuti::Disetujui->value,
                    ])
                    ->whereIn('tanggal_cuti', $tanggalCuti)
                    ->exists();

                if ($bertumpuk) {
                    $validator->errors()->add('tanggal_cuti', 'Salah satu tanggal sudah memiliki pengajuan cuti aktif.');

                    return;
                }

                foreach ($tanggalCuti->groupBy(fn (string $tanggal): int => CarbonImmutable::parse($tanggal)->year) as $tahun => $tanggalPerTahun) {
                    $saldoTersedia = $pegawai->saldoCuti()
                        ->where('tahun', $tahun)
                        ->value('saldo_tersedia') ?? $pegawai->jatah_cuti;
                    $cutiMenunggu = PengajuanCuti::query()
                        ->whereBelongsTo($pegawai)
                        ->when($pengajuanSaatIni !== null, fn ($query) => $query->whereKeyNot($pengajuanSaatIni->getKey()))
                        ->whereIn('status', [
                            StatusPengajuanCuti::MenungguAtasan->value,
                            StatusPengajuanCuti::MenungguHr->value,
                        ])
                        ->whereYear('tanggal_cuti', $tahun)
                        ->count();

                    if ($tanggalPerTahun->count() > $saldoTersedia - $cutiMenunggu) {
                        $validator->errors()->add('tanggal_cuti', "Jumlah tanggal cuti tahun {$tahun} melebihi saldo yang tersedia.");

                        return;
                    }
                }
            },
        ];
    }

    /** @return Collection<int, string> */
    public function tanggalCuti(): Collection
    {
        return collect($this->input('tanggal_cuti', []))
            ->map(fn (string $tanggal): string => CarbonImmutable::parse($tanggal)->toDateString())
            ->unique()
            ->sort()
            ->values();
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'tanggal_cuti.required' => 'Pilih minimal satu tanggal cuti.',
            'tanggal_cuti.array' => 'Pilihan tanggal cuti tidak valid.',
            'tanggal_cuti.min' => 'Pilih minimal satu tanggal cuti.',
            'tanggal_cuti.max' => $this->pengajuanCuti() === null
                ? 'Maksimal 31 tanggal dalam satu kali pengajuan.'
                : 'Satu pengajuan hanya dapat memiliki satu tanggal.',
            'tanggal_cuti.*.date' => 'Salah satu tanggal cuti tidak valid.',
            'tanggal_cuti.*.after_or_equal' => 'Tanggal cuti tidak boleh sebelum hari ini.',
            'tanggal_cuti.*.distinct' => 'Tanggal cuti tidak boleh dipilih dua kali.',
            'alasan.required' => 'Alasan cuti wajib diisi.',
            'alasan.max' => 'Alasan cuti maksimal 1000 karakter.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $tanggalCuti = collect($this->input('tanggal_cuti', []))
            ->filter(fn ($tanggal): bool => is_string($tanggal) && $tanggal !== '')
            ->values()
            ->all();

        $this->merge([
            'tanggal_cuti' => $tanggalCuti,
            'alasan' => Str::squish($this->string('alasan')->toString()),
        ]);
    }

    private function pengajuanCuti(): ?PengajuanCuti
    {
        $pengajuan = $this->route('pengajuanCuti');

        return $pengajuan instanceof PengajuanCuti ? $pengajuan : null;
    }
}
