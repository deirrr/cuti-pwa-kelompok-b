# Rancangan Database

Dokumen ini mencatat fondasi database yang sudah tersedia dan arah rancangan berikutnya. Migration fondasi bisnis sudah dibuat, tetapi masih memakai satu jabatan, satu Atasan, dan satu peran per akun. Bagian tersebut merupakan kondisi saat ini, bukan rancangan akhir untuk struktur organisasi terbaru.

> Rancangan target pada [Rancangan Organisasi dan Persetujuan Berjenjang](12-rancangan-organisasi-dan-persetujuan.md) menggantikan asumsi organisasi lama di dokumen ini. Migration, model, dan data belum diubah untuk menerapkan rancangan target.

## Prinsip Rancangan

- Nama tabel dan kolom bisnis menggunakan bahasa Indonesia.
- Setiap pengguna terhubung dengan tepat satu pegawai.
- Fondasi saat ini menghubungkan pegawai dengan satu Atasan; rancangan target memindahkan hierarki ke jabatan dan penugasan.
- Saldo dicatat per pegawai, jenis cuti, dan tahun.
- Satu pengajuan dapat memiliki beberapa tanggal dan beberapa catatan persetujuan.
- Keputusan dan perubahan saldo akhir dilakukan dalam transaksi database.
- Data yang sudah menjadi bagian riwayat dinonaktifkan, bukan dihapus sembarangan.
- Tabel teknis Laravel seperti `migrations`, `cache`, `cache_locks`, `jobs`, dan `job_batches` boleh tetap menggunakan nama bawaan framework.

## Tambahan Tabel `departemen`

Tabel `departemen` ditambahkan agar hubungan organisasi tidak disimpan sebagai teks berulang pada setiap pegawai. Tabel ini membantu penyaringan rekap dan pengelompokan pegawai tanpa membuat rancangan terlalu rumit.

## Revisi Target Struktur Organisasi

Rancangan berikut akan digunakan sebelum fitur pengajuan dan persetujuan dibangun:

| Tabel target | Perubahan utama |
| --- | --- |
| `unit_bisnis` | Menyimpan HO, KUMA, KPMA, Apotek, PMB, dan Medlab beserta kategori unit. |
| `departemen` | Ditautkan ke `unit_bisnis`; nama tidak lagi diasumsikan unik secara global. |
| `jabatan` | Menyimpan nama, kategori, unit, departemen, dan jabatan Atasan. |
| `penugasan_jabatan` | Relasi banyak-ke-banyak pegawai dan jabatan, masa berlaku, serta penanda penugasan utama. |
| `peran` dan `pengguna_peran` | Menggantikan satu enum peran agar satu akun dapat menjadi Karyawan, Atasan, dan Admin HR sekaligus. |
| `pengajuan_cuti` | Menyimpan konteks penugasan utama yang dipakai ketika pengajuan dikirim. |
| `persetujuan_cuti` | Menyimpan urutan tahap Atasan, MO, dan HR yang dibekukan beserta target dan keputusan. |

Kolom `pegawai.departemen_id`, `pegawai.atasan_id`, teks `pegawai.jabatan`, dan `pengguna.peran` tetap menggambarkan fondasi yang ada, tetapi tidak akan menjadi sumber kebenaran struktur organisasi setelah revisi target diterapkan.

## ERD Fondasi Saat Ini

```mermaid
erDiagram
    departemen ||--o{ pegawai : memiliki
    pengguna ||--|| pegawai : digunakan_oleh
    pegawai ||--o{ pegawai : membawahi
    pegawai ||--o{ saldo_cuti : mempunyai
    jenis_cuti ||--o{ saldo_cuti : dikelompokkan
    pegawai ||--o{ pengajuan_cuti : mengajukan
    jenis_cuti ||--o{ pengajuan_cuti : dipilih
    pengajuan_cuti ||--|{ tanggal_pengajuan_cuti : memuat
    pengajuan_cuti ||--o{ persetujuan_cuti : memiliki
    pengguna ||--o{ persetujuan_cuti : memutuskan

    departemen {
        bigint id PK
        varchar kode UK
        varchar nama UK
        boolean aktif
    }
    pengguna {
        bigint id PK
        varchar email UK
        varchar kata_sandi
        enum peran
        boolean aktif
    }
    pegawai {
        bigint id PK
        bigint pengguna_id FK,UK
        bigint departemen_id FK
        bigint atasan_id FK
        varchar nomor_induk UK
        varchar nama
        varchar jabatan
    }
    jenis_cuti {
        bigint id PK
        varchar kode UK
        varchar nama UK
        integer jatah_bawaan
        boolean mengurangi_saldo
        boolean aktif
    }
    saldo_cuti {
        bigint id PK
        bigint pegawai_id FK
        bigint jenis_cuti_id FK
        smallint tahun
        integer jatah_awal
        integer saldo_tersedia
    }
    pengajuan_cuti {
        bigint id PK
        varchar nomor_pengajuan UK
        bigint pegawai_id FK
        bigint jenis_cuti_id FK
        enum status
        integer jumlah_hari
        text alasan
    }
    tanggal_pengajuan_cuti {
        bigint id PK
        bigint pengajuan_cuti_id FK
        date tanggal
    }
    persetujuan_cuti {
        bigint id PK
        bigint pengajuan_cuti_id FK
        bigint pemberi_keputusan_id FK
        enum tahap
        enum keputusan
        timestamp diputuskan_pada
    }
    hari_libur {
        bigint id PK
        date tanggal UK
        varchar nama
        boolean aktif
    }
```

`hari_libur` tidak memiliki relasi langsung ke pengajuan. Tanggalnya digunakan oleh layanan validasi ketika pengajuan dikirim dan ditinjau.

## Konvensi Kamus Data

- **PK** menunjukkan primary key.
- **FK** menunjukkan foreign key.
- **UK** menunjukkan unique key, termasuk unique gabungan yang disebutkan pada aturan tabel.
- `timestamp` konseptual disimpan dalam zona waktu aplikasi dan ditampilkan sesuai zona waktu yang disepakati.
- Kolom `dibuat_pada` dan `diperbarui_pada` adalah padanan konseptual timestamp pencatatan. Nama teknis akhir dapat diselaraskan dengan kebutuhan Laravel sebelum migration dibuat.

## Tabel `departemen`

**Tujuan:** menyimpan unit organisasi untuk pengelompokan pegawai dan rekap.

| Kolom | Tipe konseptual | Boleh kosong | PK | FK | Nilai bawaan | Unik | Penjelasan |
| --- | --- | --- | --- | --- | --- | --- | --- |
| `id` | bigint unsigned | Tidak | Ya | - | otomatis | Ya | Identitas departemen. |
| `kode` | varchar(20) | Tidak | Tidak | - | - | Ya | Kode singkat departemen. |
| `nama` | varchar(100) | Tidak | Tidak | - | - | Ya | Nama departemen. |
| `aktif` | boolean | Tidak | Tidak | - | true | Tidak | Menentukan apakah departemen dapat dipilih. |
| `dibuat_pada` | timestamp | Tidak | Tidak | - | waktu saat ini | Tidak | Waktu pembuatan data. |
| `diperbarui_pada` | timestamp | Tidak | Tidak | - | waktu saat ini | Tidak | Waktu perubahan terakhir. |

## Tabel `pengguna`

**Tujuan:** menyimpan akun autentikasi dan peran akses.

| Kolom | Tipe konseptual | Boleh kosong | PK | FK | Nilai bawaan | Unik | Penjelasan |
| --- | --- | --- | --- | --- | --- | --- | --- |
| `id` | bigint unsigned | Tidak | Ya | - | otomatis | Ya | Identitas akun. |
| `email` | varchar(255) | Tidak | Tidak | - | - | Ya | Alamat email akun untuk identitas dan kebutuhan administrasi; bukan kolom login. |
| `kata_sandi` | varchar(255) | Tidak | Tidak | - | - | Tidak | Hash kata sandi, bukan kata sandi asli. |
| `peran` | enum | Tidak | Tidak | - | `karyawan` | Tidak | Nilai awal: `karyawan`, `atasan`, atau `admin_hr`. |
| `aktif` | boolean | Tidak | Tidak | - | true | Tidak | Menentukan apakah akun dapat digunakan. |
| `token_ingat` | varchar(100) | Ya | Tidak | - | null | Tidak | Token teknis untuk fitur ingat saya jika digunakan. |
| `terakhir_masuk_pada` | timestamp | Ya | Tidak | - | null | Tidak | Waktu masuk terakhir yang berhasil. |
| `dibuat_pada` | timestamp | Tidak | Tidak | - | waktu saat ini | Tidak | Waktu pembuatan akun. |
| `diperbarui_pada` | timestamp | Tidak | Tidak | - | waktu saat ini | Tidak | Waktu perubahan terakhir. |

**Aturan:** email dibandingkan secara tidak peka huruf besar-kecil. Satu akun wajib terhubung dengan tepat satu baris `pegawai` melalui aturan unik `pegawai.pengguna_id`. Login menggunakan `pegawai.nomor_induk` dan memvalidasi `pengguna.kata_sandi`; NIK tidak diduplikasi ke tabel `pengguna`.

## Tabel `pegawai`

**Tujuan:** menyimpan identitas pegawai dan struktur pelaporan.

| Kolom | Tipe konseptual | Boleh kosong | PK | FK | Nilai bawaan | Unik | Penjelasan |
| --- | --- | --- | --- | --- | --- | --- | --- |
| `id` | bigint unsigned | Tidak | Ya | - | otomatis | Ya | Identitas pegawai. |
| `pengguna_id` | bigint unsigned | Tidak | Tidak | `pengguna.id` | - | Ya | Akun yang dimiliki pegawai. |
| `departemen_id` | bigint unsigned | Tidak | Tidak | `departemen.id` | - | Tidak | Departemen pegawai. |
| `atasan_id` | bigint unsigned | Ya | Tidak | `pegawai.id` | null | Tidak | Atasan langsung; null untuk pimpinan tertinggi atau kondisi awal. |
| `nomor_induk` | varchar(30) | Tidak | Tidak | - | - | Ya | Nomor identitas internal pegawai. |
| `nama` | varchar(150) | Tidak | Tidak | - | - | Tidak | Nama lengkap pegawai. |
| `jabatan` | varchar(100) | Tidak | Tidak | - | - | Tidak | Jabatan pegawai. |
| `tanggal_masuk` | date | Ya | Tidak | - | null | Tidak | Tanggal mulai bekerja jika tersedia. |
| `aktif` | boolean | Tidak | Tidak | - | true | Tidak | Status aktif pegawai. |
| `dibuat_pada` | timestamp | Tidak | Tidak | - | waktu saat ini | Tidak | Waktu pembuatan data. |
| `diperbarui_pada` | timestamp | Tidak | Tidak | - | waktu saat ini | Tidak | Waktu perubahan terakhir. |

**Aturan:** `nomor_induk` disimpan dalam huruf kapital dan digunakan sebagai NIK untuk login. `atasan_id` tidak boleh sama dengan `id`. Validasi juga harus mencegah rantai Atasan melingkar. Penghapusan pegawai yang telah memiliki riwayat pengajuan tidak diperbolehkan; gunakan `aktif = false`.

## Tabel `jenis_cuti`

**Tujuan:** menyimpan kategori cuti dan aturan dasarnya.

| Kolom | Tipe konseptual | Boleh kosong | PK | FK | Nilai bawaan | Unik | Penjelasan |
| --- | --- | --- | --- | --- | --- | --- | --- |
| `id` | bigint unsigned | Tidak | Ya | - | otomatis | Ya | Identitas jenis cuti. |
| `kode` | varchar(20) | Tidak | Tidak | - | - | Ya | Kode singkat jenis cuti. |
| `nama` | varchar(100) | Tidak | Tidak | - | - | Ya | Nama jenis cuti. |
| `deskripsi` | text | Ya | Tidak | - | null | Tidak | Penjelasan singkat jenis cuti. |
| `jatah_bawaan` | integer unsigned | Tidak | Tidak | - | 0 | Tidak | Jatah awal yang dapat digunakan saat penerbitan saldo. |
| `mengurangi_saldo` | boolean | Tidak | Tidak | - | true | Tidak | Menentukan apakah persetujuan mengurangi saldo. |
| `aktif` | boolean | Tidak | Tidak | - | true | Tidak | Menentukan apakah jenis dapat dipilih pada pengajuan baru. |
| `dibuat_pada` | timestamp | Tidak | Tidak | - | waktu saat ini | Tidak | Waktu pembuatan data. |
| `diperbarui_pada` | timestamp | Tidak | Tidak | - | waktu saat ini | Tidak | Waktu perubahan terakhir. |

**Aturan:** jenis cuti yang sudah digunakan dinonaktifkan, bukan dihapus. Nilai `jatah_bawaan` tidak boleh negatif.

## Tabel `saldo_cuti`

**Tujuan:** menyimpan jatah dan saldo tersedia setiap pegawai per jenis cuti dan tahun.

| Kolom | Tipe konseptual | Boleh kosong | PK | FK | Nilai bawaan | Unik | Penjelasan |
| --- | --- | --- | --- | --- | --- | --- | --- |
| `id` | bigint unsigned | Tidak | Ya | - | otomatis | Ya | Identitas saldo. |
| `pegawai_id` | bigint unsigned | Tidak | Tidak | `pegawai.id` | - | Gabungan | Pemilik saldo. |
| `jenis_cuti_id` | bigint unsigned | Tidak | Tidak | `jenis_cuti.id` | - | Gabungan | Jenis cuti saldo. |
| `tahun` | smallint unsigned | Tidak | Tidak | - | - | Gabungan | Tahun berlakunya saldo. |
| `jatah_awal` | integer unsigned | Tidak | Tidak | - | 0 | Tidak | Jatah yang diberikan pada awal periode. |
| `saldo_tersedia` | integer unsigned | Tidak | Tidak | - | 0 | Tidak | Saldo yang masih dapat digunakan. |
| `catatan` | varchar(255) | Ya | Tidak | - | null | Tidak | Catatan penyesuaian terakhir jika diperlukan. |
| `dibuat_pada` | timestamp | Tidak | Tidak | - | waktu saat ini | Tidak | Waktu pembuatan saldo. |
| `diperbarui_pada` | timestamp | Tidak | Tidak | - | waktu saat ini | Tidak | Waktu perubahan saldo terakhir. |

**Aturan unik:** kombinasi (`pegawai_id`, `jenis_cuti_id`, `tahun`).

**Aturan saldo:** `jatah_awal` dan `saldo_tersedia` tidak boleh negatif. Pengurangan atau pengembalian saldo harus mengunci baris saldo di dalam transaksi, memeriksa status pengajuan, dan bersifat idempoten agar permintaan berulang tidak mengubah saldo dua kali.

## Tabel `pengajuan_cuti`

**Tujuan:** menyimpan data utama dan status terkini pengajuan.

| Kolom | Tipe konseptual | Boleh kosong | PK | FK | Nilai bawaan | Unik | Penjelasan |
| --- | --- | --- | --- | --- | --- | --- | --- |
| `id` | bigint unsigned | Tidak | Ya | - | otomatis | Ya | Identitas pengajuan. |
| `nomor_pengajuan` | varchar(40) | Tidak | Tidak | - | dibuat sistem | Ya | Nomor yang mudah dirujuk pengguna. |
| `pegawai_id` | bigint unsigned | Tidak | Tidak | `pegawai.id` | - | Tidak | Pegawai yang mengajukan. |
| `jenis_cuti_id` | bigint unsigned | Tidak | Tidak | `jenis_cuti.id` | - | Tidak | Jenis cuti yang diajukan. |
| `status` | enum | Tidak | Tidak | - | `draf` | Tidak | Rancangan target menambah `menunggu_mo` pada status yang tersedia. |
| `alasan` | text | Tidak | Tidak | - | - | Tidak | Alasan pengajuan dari Karyawan. |
| `jumlah_hari` | integer unsigned | Tidak | Tidak | - | 0 | Tidak | Jumlah tanggal kerja valid dalam pengajuan. |
| `diajukan_pada` | timestamp | Ya | Tidak | - | null | Tidak | Waktu draf dikirim kepada Atasan. |
| `dibatalkan_pada` | timestamp | Ya | Tidak | - | null | Tidak | Waktu pengajuan dibatalkan. |
| `dibuat_pada` | timestamp | Tidak | Tidak | - | waktu saat ini | Tidak | Waktu pembuatan draf. |
| `diperbarui_pada` | timestamp | Tidak | Tidak | - | waktu saat ini | Tidak | Waktu perubahan terakhir. |

**Aturan:** `jumlah_hari` harus sama dengan jumlah rincian tanggal yang sah dan lebih dari nol sebelum pengajuan dikirim. Perubahan status hanya mengikuti tabel transisi pada dokumen alur bisnis.

`nomor_pengajuan` dibuat oleh server dengan format `CUTI-YYYY-NNNNNN`. Nomor urut bersifat unik, menggunakan enam digit, dan dimulai kembali pada setiap tahun. Pembuatan nomor harus dilindungi transaksi serta unique key dan mengulang proses secara aman apabila terjadi benturan.

## Tabel `tanggal_pengajuan_cuti`

**Tujuan:** menyimpan setiap tanggal cuti dalam satu pengajuan.

| Kolom | Tipe konseptual | Boleh kosong | PK | FK | Nilai bawaan | Unik | Penjelasan |
| --- | --- | --- | --- | --- | --- | --- | --- |
| `id` | bigint unsigned | Tidak | Ya | - | otomatis | Ya | Identitas rincian tanggal. |
| `pengajuan_cuti_id` | bigint unsigned | Tidak | Tidak | `pengajuan_cuti.id` | - | Gabungan | Pengajuan induk. |
| `tanggal` | date | Tidak | Tidak | - | - | Gabungan | Tanggal cuti yang diminta. |
| `dibuat_pada` | timestamp | Tidak | Tidak | - | waktu saat ini | Tidak | Waktu rincian dibuat. |

**Aturan unik:** kombinasi (`pengajuan_cuti_id`, `tanggal`) mencegah tanggal berulang dalam pengajuan yang sama.

**Aturan tumpang tindih:** karena pegawai berada pada tabel induk dan status dapat berubah, tumpang tindih antar-pengajuan tidak cukup dicegah oleh unique key sederhana. Saat pengajuan dikirim dan disetujui akhir, sistem harus mencari tanggal yang sama pada pengajuan aktif (`menunggu_atasan`, `menunggu_mo`, `menunggu_hr`, atau `disetujui`). Indeks pada `tanggal` dan indeks status serta pegawai pada tabel induk diperlukan agar pemeriksaan efisien.

## Tabel `persetujuan_cuti`

**Tujuan:** menyimpan jejak keputusan setiap tahap, termasuk pembatalan yang diproses Admin HR.

| Kolom | Tipe konseptual | Boleh kosong | PK | FK | Nilai bawaan | Unik | Penjelasan |
| --- | --- | --- | --- | --- | --- | --- | --- |
| `id` | bigint unsigned | Tidak | Ya | - | otomatis | Ya | Identitas catatan keputusan. |
| `pengajuan_cuti_id` | bigint unsigned | Tidak | Tidak | `pengajuan_cuti.id` | - | Gabungan | Pengajuan yang diproses. |
| `pemberi_keputusan_id` | bigint unsigned | Tidak | Tidak | `pengguna.id` | - | Tidak | Pengguna yang melakukan tindakan. |
| `tahap` | enum | Tidak | Tidak | - | - | Gabungan | Target: `atasan`, `manager_operasional`, `admin_hr`, atau `pembatalan`. |
| `keputusan` | enum | Tidak | Tidak | - | - | Tidak | `disetujui`, `ditolak`, atau `dibatalkan` sesuai tahap. |
| `catatan` | text | Ya | Tidak | - | null | Tidak | Catatan keputusan; wajib untuk penolakan dan pembatalan. |
| `diputuskan_pada` | timestamp | Tidak | Tidak | - | waktu saat ini | Tidak | Waktu keputusan diberikan. |
| `dibuat_pada` | timestamp | Tidak | Tidak | - | waktu saat ini | Tidak | Waktu catatan disimpan. |

**Revisi target:** baris tahap dibuat ketika pengajuan dikirim dan menyimpan `urutan`, target jabatan atau penyetuju, status tahap, serta pemberi keputusan yang masih boleh kosong sebelum diputus. Kombinasi (`pengajuan_cuti_id`, `urutan`) harus unik.

**Aturan:** pemberi keputusan tidak boleh diambil dari input browser. Sistem mengisinya dari pengguna yang sedang masuk. Pada seluruh tahap keputusan, pemberi keputusan tidak boleh merupakan pemilik pengajuan.

## Tabel `hari_libur`

**Tujuan:** menyimpan hari libur yang dikecualikan dari perhitungan hari cuti.

| Kolom | Tipe konseptual | Boleh kosong | PK | FK | Nilai bawaan | Unik | Penjelasan |
| --- | --- | --- | --- | --- | --- | --- | --- |
| `id` | bigint unsigned | Tidak | Ya | - | otomatis | Ya | Identitas hari libur. |
| `tanggal` | date | Tidak | Tidak | - | - | Ya | Tanggal libur. |
| `nama` | varchar(150) | Tidak | Tidak | - | - | Tidak | Nama atau keterangan hari libur. |
| `jenis` | enum | Tidak | Tidak | - | `nasional` | Tidak | Rancangan nilai: `nasional` atau `perusahaan`. |
| `aktif` | boolean | Tidak | Tidak | - | true | Tidak | Menentukan apakah tanggal dipakai dalam validasi. |
| `dibuat_pada` | timestamp | Tidak | Tidak | - | waktu saat ini | Tidak | Waktu pembuatan data. |
| `diperbarui_pada` | timestamp | Tidak | Tidak | - | waktu saat ini | Tidak | Waktu perubahan terakhir. |

**Aturan:** satu tanggal hanya boleh memiliki satu catatan aktif pada rancangan awal. Jika diperlukan beberapa keterangan untuk tanggal yang sama, struktur perlu disesuaikan sebelum implementasi.

## Aturan Integritas dan Transaksi

### Pengiriman Pengajuan

1. Validasi kepemilikan, status, jenis cuti, Atasan, hari libur, akhir pekan, dan rincian tanggal.
2. Mulai transaksi dan kunci saldo terkait untuk menyusun urutan pemeriksaan pengajuan pegawai.
3. Periksa kembali saldo dan tanggal yang tumpang tindih.
4. Bekukan rute persetujuan lalu ubah status mengikuti tahap pertama.
5. Batalkan seluruh perubahan jika salah satu langkah gagal.

### Persetujuan Akhir

1. Pastikan status `menunggu_hr` dan belum ada keputusan tahap Admin HR.
2. Mulai transaksi dan kunci pengajuan serta saldo terkait.
3. Periksa ulang tanggal, hari libur, tumpang tindih, dan saldo.
4. Kurangi `saldo_tersedia` jika jenis cuti memang mengurangi saldo.
5. Simpan `persetujuan_cuti` dan ubah status menjadi `disetujui`.
6. Commit hanya jika seluruh langkah berhasil.

### Pembatalan Setelah Disetujui

1. Hanya Admin HR yang memproses berdasarkan alasan yang telah diverifikasi.
2. Kunci pengajuan dan saldo dalam satu transaksi.
3. Pastikan belum ada catatan tahap `pembatalan`.
4. Kembalikan saldo sebesar `jumlah_hari` jika sebelumnya dikurangi.
5. Simpan jejak pembatalan dan ubah status menjadi `dibatalkan`.

## Indeks Awal yang Disarankan

- `pengguna.email` unik.
- `pegawai.pengguna_id` unik dan `pegawai.nomor_induk` unik.
- Indeks pada `pegawai.atasan_id` dan `pegawai.departemen_id`.
- Kombinasi unik saldo (`pegawai_id`, `jenis_cuti_id`, `tahun`).
- `pengajuan_cuti.nomor_pengajuan` unik.
- Indeks gabungan `pengajuan_cuti` pada (`pegawai_id`, `status`) dan (`status`, `dibuat_pada`).
- Kombinasi unik tanggal (`pengajuan_cuti_id`, `tanggal`) serta indeks pada `tanggal`.
- Kombinasi unik persetujuan (`pengajuan_cuti_id`, `tahap`).
- `hari_libur.tanggal` unik.

## Ketentuan yang Telah Ditetapkan

- Sabtu dan Minggu diperlakukan sebagai akhir pekan.
- Jenis cuti serta aturan pengurangan saldo dikelola sebagai data oleh Admin HR.
- Saldo tidak dibawa otomatis ke tahun berikutnya pada versi awal.
- Atasan, MO, dan Admin HR tetap dapat mengajukan cuti sebagai pegawai, tetapi tidak boleh memutus pengajuan sendiri atau dua tahap pada pengajuan yang sama.
- Pegawai dapat memiliki beberapa penugasan jabatan, dengan tepat satu penugasan utama untuk menentukan rute cuti.
- Pembatalan pengajuan yang disetujui hanya dapat diproses Admin HR sebelum tanggal cuti pertama.
- Nomor pengajuan menggunakan format `CUTI-YYYY-NNNNNN` dan nomor urut dimulai kembali setiap tahun.
- Riwayat yang telah digunakan tidak dihapus; data master dinonaktifkan melalui status `aktif`.
