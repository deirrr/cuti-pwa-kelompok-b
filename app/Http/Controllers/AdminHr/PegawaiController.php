<?php

namespace App\Http\Controllers\AdminHr;

use App\Enums\PeranPengguna;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdminHr\PerbaruiPegawaiRequest;
use App\Http\Requests\AdminHr\SimpanPegawaiRequest;
use App\Models\BagianOrganisasi;
use App\Models\Pegawai;
use App\Models\Pengguna;
use App\Models\Peran;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PegawaiController extends Controller
{
    public function index(Request $request): View
    {
        $pencarian = $request->string('cari')->trim()->toString();
        $filterPeran = $request->string('peran')->toString();
        $peranTerpilih = PeranPengguna::tryFrom($filterPeran);
        $bagianOrganisasi = BagianOrganisasi::query()
            ->with(['pegawai' => function ($query) use ($pencarian, $peranTerpilih): void {
                $query
                    ->with([
                        'pengguna',
                        'atasan',
                    ])
                    ->when($pencarian !== '', function ($query) use ($pencarian): void {
                        $query->where(function ($query) use ($pencarian): void {
                            $query->where('nama', 'like', "%{$pencarian}%")
                                ->orWhere('nomor_induk', 'like', "%{$pencarian}%")
                                ->orWhereHas('pengguna', fn ($query) => $query->where('email', 'like', "%{$pencarian}%"));
                        });
                    })
                    ->when(
                        $peranTerpilih !== null,
                        fn ($query) => $query->whereHas(
                            'pengguna',
                            fn ($query) => $query->where('peran', $peranTerpilih->value),
                        ),
                    )
                    ->orderByRaw(
                        'CASE WHEN EXISTS (SELECT 1 FROM pengguna WHERE pengguna.id = pegawai.pengguna_id AND pengguna.peran = ?) THEN 0 ELSE 1 END',
                        [PeranPengguna::Atasan->value],
                    )
                    ->orderBy('nama')
                    ->orderBy('id');
            }])
            ->orderByDesc('aktif')
            ->orderBy('nama')
            ->get();

        if ($pencarian !== '' || $peranTerpilih !== null) {
            $bagianOrganisasi = $bagianOrganisasi
                ->filter(fn (BagianOrganisasi $bagian) => $bagian->pegawai->isNotEmpty())
                ->values();
        }

        return view('admin-hr.pegawai.index', [
            'bagianOrganisasi' => $bagianOrganisasi,
            'jumlahKaryawan' => $bagianOrganisasi->sum(fn (BagianOrganisasi $bagian) => $bagian->pegawai->count()),
            'pencarian' => $pencarian,
            'filterPeran' => $filterPeran,
            'daftarPeran' => PeranPengguna::cases(),
        ]);
    }

    public function create(): View
    {
        return view('admin-hr.pegawai.create', [
            'daftarBagian' => $this->daftarBagian(),
            'daftarAtasan' => $this->daftarAtasan(),
            'daftarPeran' => PeranPengguna::cases(),
        ]);
    }

    public function store(SimpanPegawaiRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $pegawai = DB::transaction(function () use ($data): Pegawai {
            $peran = PeranPengguna::from($data['peran']);
            $pengguna = Pengguna::query()->create([
                'email' => $data['email'],
                'kata_sandi' => $data['kata_sandi'],
                'peran' => $peran,
                'aktif' => $data['aktif'],
            ]);

            $this->sinkronkanPeran($pengguna, $peran);

            $pegawai = Pegawai::query()->create([
                'pengguna_id' => $pengguna->getKey(),
                'bagian_organisasi_id' => $data['bagian_organisasi_id'],
                'atasan_id' => $peran === PeranPengguna::Karyawan ? $data['atasan_id'] : null,
                'nomor_induk' => $data['nomor_induk'],
                'nama' => $data['nama'],
                'tanggal_masuk' => $data['tanggal_masuk'] ?? null,
                'jatah_cuti' => $peran === PeranPengguna::AdminHr ? 0 : $data['jatah_cuti'],
                'aktif' => $data['aktif'],
            ]);

            if ($peran !== PeranPengguna::AdminHr) {
                $this->sinkronkanSaldoCuti($pegawai, (int) $data['jatah_cuti']);
            }

            return $pegawai;
        });

        return redirect()
            ->route('admin_hr.pegawai.index')
            ->with('sukses', "Karyawan {$pegawai->nama} berhasil ditambahkan.");
    }

    public function edit(Pegawai $pegawai): View
    {
        $pegawai->load('pengguna');

        return view('admin-hr.pegawai.edit', [
            'pegawai' => $pegawai,
            'daftarBagian' => $this->daftarBagian($pegawai),
            'daftarAtasan' => $this->daftarAtasan($pegawai),
            'daftarPeran' => PeranPengguna::cases(),
        ]);
    }

    public function update(PerbaruiPegawaiRequest $request, Pegawai $pegawai): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data, $pegawai): void {
            $peran = PeranPengguna::from($data['peran']);
            $dataPengguna = [
                'email' => $data['email'],
                'peran' => $peran,
                'aktif' => $data['aktif'],
            ];

            if (! empty($data['kata_sandi'])) {
                $dataPengguna['kata_sandi'] = $data['kata_sandi'];
            }

            $pegawai->pengguna->update($dataPengguna);
            $this->sinkronkanPeran($pegawai->pengguna, $peran);

            $pegawai->update([
                'bagian_organisasi_id' => $data['bagian_organisasi_id'],
                'atasan_id' => $peran === PeranPengguna::Karyawan ? $data['atasan_id'] : null,
                'nomor_induk' => $data['nomor_induk'],
                'nama' => $data['nama'],
                'tanggal_masuk' => $data['tanggal_masuk'] ?? null,
                'jatah_cuti' => $peran === PeranPengguna::AdminHr ? 0 : $data['jatah_cuti'],
                'aktif' => $data['aktif'],
            ]);

            if ($peran === PeranPengguna::AdminHr) {
                $pegawai->saldoCuti()->where('tahun', now()->year)->delete();
            } else {
                $this->sinkronkanSaldoCuti($pegawai, (int) $data['jatah_cuti']);
            }
        });

        return redirect()
            ->route('admin_hr.pegawai.index')
            ->with('sukses', "Data karyawan {$pegawai->nama} berhasil diperbarui.");
    }

    /** @return Collection<int, BagianOrganisasi> */
    private function daftarBagian(?Pegawai $pegawai = null): Collection
    {
        return BagianOrganisasi::query()
            ->where(function ($query) use ($pegawai): void {
                $query->where('aktif', true)
                    ->when(
                        $pegawai !== null,
                        fn ($query) => $query->orWhereKey($pegawai->bagian_organisasi_id),
                    );
            })
            ->orderBy('nama')
            ->get();
    }

    /** @return Collection<int, Pegawai> */
    private function daftarAtasan(?Pegawai $pegawai = null): Collection
    {
        return Pegawai::query()
            ->with('bagianOrganisasi')
            ->where('aktif', true)
            ->whereHas('pengguna', fn ($query) => $query
                ->where('peran', PeranPengguna::Atasan->value)
                ->where('aktif', true))
            ->when($pegawai !== null, fn ($query) => $query->whereKeyNot($pegawai->getKey()))
            ->orderBy('nama')
            ->get();
    }

    private function sinkronkanPeran(Pengguna $pengguna, PeranPengguna $peran): void
    {
        $peranModel = Peran::query()->where('kode', $peran->value)->firstOrFail();

        $pengguna->peranSistem()->sync([$peranModel->getKey()]);
    }

    private function sinkronkanSaldoCuti(Pegawai $pegawai, int $jatahCuti): void
    {
        $saldoCuti = $pegawai->saldoCuti()->firstOrNew(
            ['tahun' => now()->year],
            ['jatah_awal' => 0, 'saldo_tersedia' => 0],
        );
        $cutiTerpakai = max(0, $saldoCuti->jatah_awal - $saldoCuti->saldo_tersedia);

        $saldoCuti->fill([
            'jatah_awal' => $jatahCuti,
            'saldo_tersedia' => $jatahCuti - $cutiTerpakai,
            'catatan' => 'Diperbarui melalui Master Karyawan.',
        ])->save();
    }
}
