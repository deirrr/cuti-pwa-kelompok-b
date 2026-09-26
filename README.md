# Rancang Bangun Sistem Informasi Pengajuan dan Persetujuan Cuti Karyawan Berbasis Progressive Web App di PT Medika Antapani

> **Status proyek:** Dalam tahap pengembangan.

Proyek capstone ini dikerjakan oleh tim beranggotakan lima orang. Aplikasi dirancang sebagai sistem mandiri untuk mengelola pengajuan dan persetujuan cuti karyawan melalui web yang dapat dipasang sebagai Progressive Web App (PWA).

## Kondisi Proyek Saat Ini

Repository saat ini telah memiliki fondasi database bisnis, autentikasi berbasis NIK, dan pembatasan akses dasar berdasarkan peran. Fitur pengajuan cuti, alur persetujuan, pengelolaan data, dan kemampuan PWA masih dalam tahap perencanaan serta pengembangan.

Rancangan organisasi terbaru mencakup enam unit bisnis, pegawai dengan beberapa jabatan, serta persetujuan dinamis melalui Atasan, Manager Operasional, dan Admin HR. Rancangan tersebut belum diterapkan pada database atau fitur aplikasi.

Yang sudah tersedia:

- Kerangka aplikasi Laravel 13 beserta Blade, JavaScript, Vite, dan Tailwind CSS.
- Migration, model, factory, dan seeder untuk fondasi data cuti.
- Autentikasi menggunakan NIK dan kata sandi, termasuk pembatasan percobaan masuk dan tindakan keluar.
- Pemeriksaan akun aktif dan middleware pembatasan akses Karyawan, Atasan, serta Admin HR.
- Halaman masuk dan dashboard dasar yang responsif.
- Pengujian otomatis untuk fondasi database, autentikasi, dan hak akses dasar.

Yang masih direncanakan antara lain formulir pengajuan, validasi tanggal dan saldo, proses persetujuan berjenjang, pengelolaan data oleh Admin HR, serta komponen PWA.

## Deskripsi Singkat

Sistem ini direncanakan untuk membantu karyawan mengajukan cuti, atasan memberikan persetujuan awal, dan Admin HR memberikan keputusan akhir. Sistem juga akan mencatat tanggal cuti, riwayat keputusan, hari libur, dan saldo cuti secara terpusat.

Aplikasi ini berdiri sendiri dan khusus menangani proses cuti. Aplikasi tidak bergantung pada sistem, API, atau backend eksternal.

## Latar Belakang Masalah

Proses pengajuan cuti yang dilakukan melalui formulir kertas atau percakapan pribadi dapat menyebabkan data tercecer, status pengajuan sulit dipantau, dan riwayat persetujuan tidak terdokumentasi dengan baik. Pemeriksaan saldo serta tanggal cuti secara manual juga berisiko menimbulkan kesalahan.

Karena itu, diperlukan aplikasi yang menyatukan proses pengajuan, pemeriksaan, persetujuan, dan pencatatan saldo cuti. Pendekatan PWA dipilih agar aplikasi nyaman digunakan melalui komputer maupun perangkat seluler dan dapat dipasang dari browser yang mendukung.

## Tujuan Pengembangan

- Membuat proses pengajuan dan persetujuan cuti lebih terstruktur.
- Memudahkan karyawan memantau status pengajuan dan saldo cuti.
- Membantu atasan dan Admin HR mengambil keputusan berdasarkan data yang tercatat.
- Menyimpan riwayat pengajuan serta persetujuan secara terpusat.
- Menghasilkan aplikasi web responsif yang dapat dikembangkan menjadi PWA.
- Menjadi sarana penerapan Laravel, basis data, kolaborasi GitHub, dan pengujian perangkat lunak dalam proyek capstone.

## Manfaat Aplikasi

- Mengurangi penggunaan formulir manual.
- Mempercepat penyampaian dan pemeriksaan pengajuan cuti.
- Meningkatkan keterlacakan status dan riwayat keputusan.
- Membantu menjaga ketepatan pencatatan saldo cuti.
- Memberikan pengalaman penggunaan yang konsisten pada desktop dan perangkat seluler.

## Ruang Lingkup

Ruang lingkup yang direncanakan meliputi:

- Autentikasi pengguna dengan NIK dan pembatasan akses berdasarkan peran.
- Pengelolaan data pegawai, unit bisnis, departemen atau bagian, jabatan, jenis cuti, saldo cuti, dan hari libur.
- Pengajuan cuti untuk satu atau beberapa tanggal.
- Validasi tanggal pengajuan dan ketersediaan saldo.
- Persetujuan berjenjang oleh Atasan langsung, MO bila diperlukan, dan Admin HR.
- Pencatatan alasan penolakan serta riwayat keputusan.
- Pembaruan saldo setelah pengajuan memperoleh persetujuan akhir.
- Penyajian status dan riwayat pengajuan kepada pengguna terkait.
- Penyediaan kemampuan dasar PWA setelah komponen PWA dikembangkan.

## Batasan Sistem

- Aplikasi hanya menangani pengajuan dan persetujuan cuti karyawan.
- Aplikasi tidak mencakup integrasi dengan sistem lain, absensi, penggajian, atau layanan eksternal.
- Data pegawai dan aturan cuti dikelola di dalam aplikasi ini.
- Keputusan akhir berada pada Admin HR setelah seluruh tahap organisasi pada rute selesai.
- Perhitungan saldo mengikuti jenis cuti dan kebijakan yang nantinya ditetapkan oleh tim.
- Notifikasi melalui email, WhatsApp, atau layanan pihak ketiga berada di luar ruang lingkup proyek.
- Kemampuan offline terbatas dan tidak berarti seluruh proses bisnis dapat dijalankan tanpa internet.

## Aktor dan Hak Akses

| Aktor | Hak akses yang direncanakan |
| --- | --- |
| Karyawan | Melihat profil dan saldo cuti sendiri, membuat pengajuan, melihat status, serta melihat riwayat pengajuan sendiri. |
| Atasan | Melihat pengajuan yang menjadi tanggung jawabnya, termasuk tahap Atasan langsung atau MO, lalu menyetujui atau menolak. |
| Admin HR | Mengelola data pendukung, meninjau pengajuan yang telah menyelesaikan tahap organisasi, memberikan keputusan akhir, dan memantau data cuti. |

Satu akun nantinya dapat memiliki beberapa peran. Pembatasan peran tunggal dasar telah tersedia, sedangkan peran jamak dan policy berdasarkan kepemilikan, penugasan jabatan, rute, serta status pengajuan masih direncanakan.

## Alur Utama Pengajuan Cuti

1. Karyawan mengisi dan mengirim pengajuan cuti.
2. Sistem memvalidasi tanggal yang dipilih, hari libur yang tercatat, dan ketersediaan saldo cuti.
3. Sistem membentuk rute persetujuan berdasarkan unit dan penugasan utama pegawai.
4. Atasan langsung memeriksa pengajuan jika tahap tersebut diperlukan.
5. Pada unit operasional, MO memeriksa pengajuan jika belum bertindak sebagai Atasan langsung.
6. Admin HR yang bukan pemohon memberikan keputusan akhir.
7. Setelah persetujuan akhir diberikan, sistem memperbarui saldo cuti sesuai jumlah hari yang disetujui.
8. Jika pengajuan ditolak pada salah satu tahap, saldo cuti tidak dikurangi dan alasan penolakan dicatat.

Alur bisnis pengajuan tersebut masih berupa rancangan dan belum diimplementasikan. Autentikasi serta pembatasan akses dasar telah tersedia sebagai fondasinya.

## Fitur Utama

Fitur masuk dengan NIK, keluar, pemeriksaan akun aktif, dan dashboard dasar sesuai peran sudah tersedia. Fitur bisnis berikut masih direncanakan.

### Karyawan

- Masuk menggunakan NIK dan kata sandi serta keluar dari aplikasi. **Sudah tersedia.**
- Melihat profil serta saldo cuti.
- Membuat pengajuan cuti beserta alasan dan tanggal yang dipilih.
- Melihat status dan riwayat pengajuan.
- Membatalkan pengajuan selama belum diproses, apabila aturan bisnis mengizinkan.

### Atasan

- Melihat daftar pengajuan dari karyawan yang berada di bawah tanggung jawabnya.
- Melihat detail pengajuan dan saldo yang berkaitan.
- Menyetujui atau menolak pengajuan pada tahap Atasan atau MO sesuai penugasan.
- Memberikan catatan atau alasan keputusan.
- Melihat riwayat keputusan yang pernah diberikan.

### Admin HR

- Mengelola data pengguna dan pegawai.
- Mengelola jenis cuti, saldo cuti, dan hari libur.
- Memeriksa pengajuan yang telah menyelesaikan seluruh tahap organisasi.
- Memberikan persetujuan atau penolakan akhir.
- Melihat serta menyaring riwayat pengajuan dan persetujuan.
- Memantau perubahan saldo cuti.

## Teknologi yang Digunakan

| Teknologi | Kegunaan |
| --- | --- |
| Laravel 13 | Framework utama aplikasi web. |
| PHP 8.3 | Bahasa pemrograman backend. |
| MySQL | Basis data aplikasi yang direncanakan. |
| Blade | Pembuatan tampilan pada sisi server. |
| JavaScript | Interaksi pada antarmuka dan dukungan PWA. |
| Tailwind CSS | Penyusunan gaya antarmuka; sudah tersedia pada kerangka proyek. |
| Vite | Pengembangan dan bundling aset frontend. |
| Node.js dan npm | Pengelolaan dependensi serta proses build frontend. |
| Progressive Web App | Pendekatan agar aplikasi dapat dipasang dan memiliki kemampuan web modern. |
| Git dan GitHub | Kolaborasi kode, riwayat perubahan, dan dokumentasi. |
| PHPUnit | Pengujian otomatis aplikasi Laravel. |

## Konsep PWA dan Penggunaan Offline

Progressive Web App adalah aplikasi web yang dapat memberikan pengalaman menyerupai aplikasi perangkat, misalnya dapat dipasang dari browser, memiliki ikon aplikasi, dan memanfaatkan penyimpanan sementara melalui service worker.

Komponen PWA **belum dibuat**. Pada tahap pengembangan, PWA direncanakan mencakup web app manifest, service worker, ikon aplikasi, dan strategi cache untuk aset dasar.

Batasan offline yang direncanakan:

- Mode offline hanya ditujukan untuk memuat aset statis dan menampilkan halaman offline khusus.
- Data pengajuan, saldo, dan data pribadi tidak dirancang untuk tersedia dari cache ketika offline.
- Pengiriman pengajuan, pemberian persetujuan, pembaruan saldo, dan autentikasi tetap membutuhkan koneksi internet.
- Aplikasi harus menampilkan informasi yang jelas ketika koneksi tidak tersedia.

## Struktur Database

Nama tabel bisnis menggunakan bahasa Indonesia agar mudah dipahami oleh tim. Migration dan model fondasinya sudah tersedia.

| Tabel | Rencana fungsi |
| --- | --- |
| `departemen` | Fondasi saat ini menyimpan pengelompokan kerja; pada target akan berada di bawah unit bisnis. |
| `pengguna` | Menyimpan akun dan informasi autentikasi; relasi peran jamak masih direncanakan. |
| `pegawai` | Menyimpan identitas pegawai dan hubungan ke akun; struktur jabatan target akan dipisahkan. |
| `jenis_cuti` | Menyimpan kategori cuti dan ketentuan dasarnya. |
| `saldo_cuti` | Menyimpan saldo cuti setiap pegawai berdasarkan jenis dan periode. |
| `pengajuan_cuti` | Menyimpan data utama pengajuan, alasan, status, dan ringkasan durasi. |
| `tanggal_pengajuan_cuti` | Menyimpan rincian setiap tanggal yang diajukan dalam satu pengajuan. |
| `persetujuan_cuti` | Menyimpan tahap, keputusan, pemberi keputusan, catatan, dan waktu keputusan. |
| `hari_libur` | Menyimpan tanggal libur yang digunakan dalam validasi hari cuti. |

Rancangan target juga memerlukan tabel `unit_bisnis`, `jabatan`, `penugasan_jabatan`, `peran`, dan `pengguna_peran`. Tabel tersebut masih berupa rancangan karena fondasi sekarang hanya mendukung satu jabatan dan satu peran per akun.

Struktur kolom, relasi, indeks, serta aturan penghapusan dasar sudah diterapkan melalui migration. Tabel teknis Laravel, seperti `migrations`, `cache`, `cache_locks`, `jobs`, `job_batches`, dan tabel teknis lain, tetap boleh menggunakan nama bawaan framework.

## Rencana Pembagian Tanggung Jawab Tim

Pembagian berikut merupakan titik awal dan dapat disesuaikan berdasarkan kesepakatan tim.

| Anggota | Tanggung jawab utama |
| --- | --- |
| Anggota 1 | Analisis sistem dan koordinasi: kebutuhan, ruang lingkup, jadwal, dan koordinasi integrasi. |
| Anggota 2 | Backend dan database: rancangan data, migration, model, validasi, dan logika pengajuan. |
| Anggota 3 | Alur persetujuan dan hak akses: peran, otorisasi, tahapan keputusan, dan riwayat persetujuan. |
| Anggota 4 | Frontend dan UI/UX: rancangan antarmuka, Blade, responsivitas, dan pengalaman pengguna. |
| Anggota 5 | PWA, pengujian, dan dokumentasi: manifest, service worker, skenario uji, serta dokumentasi proyek. |

Walaupun memiliki fokus masing-masing, seluruh anggota tetap melakukan ulasan kode dan membantu pengujian fitur yang terintegrasi.

## Tahapan Pengembangan

1. **Analisis kebutuhan** — menyepakati aktor, aturan cuti, kebutuhan data, dan kriteria keberhasilan.
2. **Perancangan sistem** — menyusun alur proses, rancangan UI/UX, struktur database, dan hak akses.
3. **Persiapan proyek** — menyamakan lingkungan kerja, strategi branch, dan aturan kontribusi.
4. **Pengembangan dasar** — membangun autentikasi, data pengguna, pegawai, jenis cuti, saldo, dan hari libur.
5. **Pengembangan proses bisnis** — membangun pengajuan, validasi, persetujuan berjenjang, dan pembaruan saldo.
6. **Pengembangan frontend dan PWA** — menyelesaikan tampilan responsif serta komponen PWA.
7. **Pengujian dan perbaikan** — menguji fungsi, hak akses, alur utama, tampilan, dan perilaku PWA.
8. **Dokumentasi dan presentasi** — merapikan panduan, bukti pengujian, laporan, dan bahan demonstrasi.

## Instalasi Lokal

### Prasyarat

- PHP 8.3 atau versi kompatibel yang dipersyaratkan proyek.
- Composer.
- MySQL.
- Node.js dan npm.
- Git.

### Langkah Singkat

```bash
git clone <alamat-repository>
cd cuti-pwa
composer install
npm install
```

Salin file konfigurasi lingkungan tanpa membagikan isinya:

```bash
cp .env.example .env
php artisan key:generate
```

Pada Windows PowerShell, penyalinan dapat dilakukan dengan:

```powershell
Copy-Item .env.example .env
```

Buat database MySQL lokal, lalu atur koneksi database pada `.env` milik masing-masing anggota. Jangan memasukkan `.env`, kata sandi, token, atau data rahasia ke Git.

Setelah koneksi siap, jalankan:

```bash
php artisan migrate
php artisan serve
```

Data contoh untuk lingkungan lokal dapat dibuat dengan:

```bash
php artisan db:seed
```

Pada terminal lain, jalankan frontend development server:

```bash
npm run dev
```

Perintah migration membuat tabel teknis Laravel dan tabel fondasi bisnis cuti. Data produksi tetap harus disiapkan melalui proses yang disepakati tim, bukan menggunakan data contoh lokal.

## Dokumentasi

Dokumen berikut menjadi sumber rancangan dan keputusan proyek. Status implementasi tetap dibedakan dari rancangan pada README dan pengujian terkait:

1. [Kebutuhan Sistem](docs/01-kebutuhan-sistem.md)
2. [Alur Bisnis](docs/02-alur-bisnis.md)
3. [Hak Akses](docs/03-hak-akses.md)
4. [Rancangan Database](docs/04-rancangan-database.md)
5. [Rancangan Progressive Web App](docs/05-rancangan-pwa.md)
6. [Rencana Pengujian](docs/06-rencana-pengujian.md)
7. [Pembagian Tugas Tim](docs/07-pembagian-tugas.md)
8. [Pedoman Pengembangan](docs/08-pedoman-pengembangan.md)
9. [Keputusan Proyek](docs/09-keputusan-proyek.md)
10. [Matriks Ketertelusuran](docs/10-matriks-ketertelusuran.md)
11. [Rancangan Antarmuka dan Wireframe](docs/11-rancangan-antarmuka.md)
12. [Rancangan Organisasi dan Persetujuan Berjenjang](docs/12-rancangan-organisasi-dan-persetujuan.md)

## Aturan Kontribusi Dasar

1. Perbarui branch utama lokal sebelum mulai bekerja.
2. Buat branch baru untuk setiap tugas, misalnya `feature/pengajuan-cuti`, `fix/validasi-tanggal`, atau `docs/perbarui-readme`.
3. Kerjakan hanya perubahan yang berkaitan dengan tugas pada branch tersebut.
4. Gunakan pesan commit yang singkat dan menjelaskan perubahan.
5. Dorong branch ke GitHub dan buat pull request menuju branch utama.
6. Jelaskan tujuan, perubahan, serta cara menguji pada deskripsi pull request.
7. Minta sedikitnya satu anggota tim meninjau pull request sebelum digabungkan.
8. Selesaikan konflik dan pastikan pengujian yang berkaitan berhasil sebelum merge.
9. Jangan melakukan commit langsung ke branch utama, kecuali tim telah menyepakati pengecualian.

## Status Pengembangan

🚧 **Proyek masih dalam tahap pengembangan.**

Isi README ini menjelaskan tujuan dan rencana aplikasi. Daftar fitur bukan pernyataan bahwa seluruh fitur telah tersedia. Status akan diperbarui secara bertahap seiring implementasi, pengujian, dan kesepakatan tim.
