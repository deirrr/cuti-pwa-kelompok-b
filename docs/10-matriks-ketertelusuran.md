# Matriks Ketertelusuran

Dokumen ini menghubungkan kebutuhan dengan aktor, alur bisnis, data, modul yang direncanakan, dan skenario pengujian. Matriks harus diperbarui ketika rancangan atau implementasi berubah.

Status implementasi yang digunakan:

- **Direncanakan:** kebutuhan telah terdokumentasi, tetapi implementasi belum dimulai.
- **Sedang dikerjakan:** terdapat branch atau pull request aktif yang dapat dirujuk.
- **Selesai:** implementasi tersedia, pengujian relevan lulus, dan bukti telah ditinjau.
- **Ditunda:** kebutuhan tetap tercatat, tetapi pengerjaannya ditunda dengan alasan yang jelas.

Seluruh kebutuhan saat dokumen ini dibuat berstatus **Direncanakan** karena aplikasi masih berupa kerangka awal dan belum memiliki bukti implementasi fitur bisnis.

## Kebutuhan Karyawan

| ID kebutuhan | Nama kebutuhan | Aktor | Alur bisnis terkait | Tabel terkait | Halaman atau modul yang direncanakan | Skenario pengujian | Status implementasi |
| --- | --- | --- | --- | --- | --- | --- | --- |
| KF-KAR-001 | Masuk dan keluar aplikasi | Karyawan | Autentikasi sebelum mengakses alur cuti | `pengguna`, `pegawai` | Halaman masuk dan tindakan keluar | AUT-001 sampai AUT-004 | Direncanakan |
| KF-KAR-002 | Melihat dashboard pribadi | Karyawan | Ringkasan saldo dan pengajuan pribadi | `pegawai`, `saldo_cuti`, `pengajuan_cuti` | Dashboard Karyawan | DBH-001, AKS-001 | Direncanakan |
| KF-KAR-003 | Membuat dan menyimpan draf | Karyawan | [Pengajuan dan validasi](02-alur-bisnis.md#pengajuan-dan-validasi) | `pengajuan_cuti`, `tanggal_pengajuan_cuti` | Formulir pengajuan | DFT-001 | Direncanakan |
| KF-KAR-004 | Memilih jenis, tanggal, dan alasan | Karyawan | [Pengajuan dan validasi](02-alur-bisnis.md#pengajuan-dan-validasi) | `jenis_cuti`, `pengajuan_cuti`, `tanggal_pengajuan_cuti`, `hari_libur` | Formulir pengajuan | VAL-001 sampai VAL-007 | Direncanakan |
| KF-KAR-005 | Mengirim draf dan membentuk rute | Karyawan | `draf` ke tahap pertama | `pengajuan_cuti`, `tanggal_pengajuan_cuti`, `persetujuan_cuti`, `penugasan_jabatan` | Tindakan kirim pengajuan | RTE-001 sampai RTE-007, NOM-001, VAL-008 | Direncanakan |
| KF-KAR-006 | Melihat detail, status, dan riwayat pribadi | Karyawan | [Data utama dan rekap](02-alur-bisnis.md#data-utama-dan-rekap) | `pengajuan_cuti`, `tanggal_pengajuan_cuti`, `persetujuan_cuti` | Daftar, detail, dan riwayat pribadi | RWT-001, AKS-001 | Direncanakan |
| KF-KAR-007 | Mengubah draf | Karyawan | Perubahan sebelum pengiriman | `pengajuan_cuti`, `tanggal_pengajuan_cuti` | Formulir edit draf | DFT-002 | Direncanakan |
| KF-KAR-008 | Membatalkan pengajuan sebelum keputusan Atasan | Karyawan | [Penolakan, pembatalan, dan saldo](02-alur-bisnis.md#penolakan-pembatalan-dan-saldo) | `pengajuan_cuti`, `persetujuan_cuti` | Tindakan pembatalan pribadi | BTL-001 sampai BTL-003 | Direncanakan |
| KF-KAR-009 | Menerima penjelasan validasi | Karyawan | [Pengajuan dan validasi](02-alur-bisnis.md#pengajuan-dan-validasi) | `jenis_cuti`, `saldo_cuti`, `pengajuan_cuti`, `tanggal_pengajuan_cuti`, `hari_libur` | Pesan validasi formulir | VAL-001 sampai VAL-008 | Direncanakan |

## Kebutuhan Atasan

| ID kebutuhan | Nama kebutuhan | Aktor | Alur bisnis terkait | Tabel terkait | Halaman atau modul yang direncanakan | Skenario pengujian | Status implementasi |
| --- | --- | --- | --- | --- | --- | --- | --- |
| KF-ATS-001 | Melihat pengajuan bawahan yang menunggu | Atasan | [Keputusan Atasan dan MO](02-alur-bisnis.md#keputusan-atasan-dan-mo) | `pegawai`, `pengajuan_cuti` | Daftar persetujuan Atasan | AKS-002 | Direncanakan |
| KF-ATS-002 | Melihat detail dan saldo relevan | Atasan | Pemeriksaan sebelum keputusan Atasan | `pegawai`, `jenis_cuti`, `saldo_cuti`, `pengajuan_cuti`, `tanggal_pengajuan_cuti` | Detail pengajuan bawahan | AKS-002, AKS-003 | Direncanakan |
| KF-ATS-003 | Menyetujui tahap organisasi | Atasan atau MO | Melanjutkan ke tahap berikutnya pada snapshot rute | `pengajuan_cuti`, `persetujuan_cuti` | Form keputusan Atasan atau MO | ALR-002, RTE-002 sampai RTE-004, AKS-003 | Direncanakan |
| KF-ATS-004 | Menolak dengan alasan | Atasan | `menunggu_atasan` ke `ditolak` | `pengajuan_cuti`, `persetujuan_cuti` | Form keputusan Atasan | ALR-003, ALR-004 | Direncanakan |
| KF-ATS-005 | Melihat riwayat keputusan | Atasan | [Data utama dan rekap](02-alur-bisnis.md#data-utama-dan-rekap) | `pengajuan_cuti`, `persetujuan_cuti` | Riwayat keputusan Atasan | RWT-002 | Direncanakan |
| KF-ATS-006 | Mencegah pemrosesan pengajuan sendiri | Atasan | [Keputusan Atasan dan MO](02-alur-bisnis.md#keputusan-atasan-dan-mo) | `pegawai`, `pengajuan_cuti`, `persetujuan_cuti` | Otorisasi keputusan Atasan | AKS-003 | Direncanakan |
| KF-ATS-007 | Memproses hanya status yang sah | Atasan | [Transisi status](02-alur-bisnis.md#transisi-status-dan-wewenang) | `pengajuan_cuti`, `persetujuan_cuti` | Otorisasi dan layanan keputusan | AKS-003, ALR-002 | Direncanakan |

## Kebutuhan Admin HR

| ID kebutuhan | Nama kebutuhan | Aktor | Alur bisnis terkait | Tabel terkait | Halaman atau modul yang direncanakan | Skenario pengujian | Status implementasi |
| --- | --- | --- | --- | --- | --- | --- | --- |
| KF-HR-001 | Mengelola pengguna, pegawai, dan peran jamak | Admin HR | Pengelolaan data utama | `pengguna`, `pegawai`, `peran`, `pengguna_peran` | Manajemen pengguna dan pegawai | DAT-001, AKS-004 | Direncanakan |
| KF-HR-002 | Mengelola organisasi dan penugasan | Admin HR | Penyiapan kewenangan persetujuan | `unit_bisnis`, `departemen`, `jabatan`, `penugasan_jabatan` | Form struktur organisasi | ORG-001, ORG-002, DAT-002 | Direncanakan |
| KF-HR-003 | Mengelola jenis cuti | Admin HR | [Data utama dan rekap](02-alur-bisnis.md#data-utama-dan-rekap) | `jenis_cuti` | Manajemen jenis cuti | JCT-001 | Direncanakan |
| KF-HR-004 | Mengelola saldo per jenis dan tahun | Admin HR | [Penolakan, pembatalan, dan saldo](02-alur-bisnis.md#penolakan-pembatalan-dan-saldo) | `pegawai`, `jenis_cuti`, `saldo_cuti` | Manajemen saldo | SLD-005, SLD-006, AKS-004 | Direncanakan |
| KF-HR-005 | Mengelola hari libur | Admin HR | [Data utama dan rekap](02-alur-bisnis.md#data-utama-dan-rekap) | `hari_libur` | Manajemen hari libur | LIB-001 sampai LIB-003 | Direncanakan |
| KF-HR-006 | Melihat pengajuan yang menunggu HR | Admin HR | [Keputusan Admin HR](02-alur-bisnis.md#keputusan-admin-hr) | `pengajuan_cuti`, `tanggal_pengajuan_cuti`, `persetujuan_cuti` | Daftar persetujuan Admin HR | AKS-005, ALR-005 | Direncanakan |
| KF-HR-007 | Memberikan persetujuan akhir | Admin HR | `menunggu_hr` ke `disetujui` | `pengajuan_cuti`, `persetujuan_cuti`, `saldo_cuti` | Form keputusan Admin HR | ALR-005, AKS-006, SLD-001 sampai SLD-004 | Direncanakan |
| KF-HR-008 | Menolak pengajuan dengan alasan | Admin HR | `menunggu_hr` ke `ditolak` | `pengajuan_cuti`, `persetujuan_cuti` | Form keputusan Admin HR | ALR-006 | Direncanakan |
| KF-HR-009 | Mengurangi saldo setelah persetujuan akhir | Admin HR dan sistem | [Penolakan, pembatalan, dan saldo](02-alur-bisnis.md#penolakan-pembatalan-dan-saldo) | `saldo_cuti`, `pengajuan_cuti`, `persetujuan_cuti` | Layanan transaksi persetujuan akhir | ALR-005, SLD-001 sampai SLD-004 | Direncanakan |
| KF-HR-010 | Memproses pembatalan setelah persetujuan | Admin HR | [Penolakan, pembatalan, dan saldo](02-alur-bisnis.md#penolakan-pembatalan-dan-saldo) | `saldo_cuti`, `pengajuan_cuti`, `persetujuan_cuti` | Form pembatalan Admin HR | BTL-004 sampai BTL-006 | Direncanakan |
| KF-HR-011 | Melihat, menyaring, dan mengekspor rekap | Admin HR | [Data utama dan rekap](02-alur-bisnis.md#data-utama-dan-rekap) | `departemen`, `pegawai`, `jenis_cuti`, `pengajuan_cuti`, `tanggal_pengajuan_cuti` | Rekap dan ekspor pengajuan | RKP-001, SEC-003 | Direncanakan |
| KF-HR-012 | Melihat jejak keputusan dan waktunya | Admin HR | Audit persetujuan | `pengajuan_cuti`, `persetujuan_cuti`, `pengguna` | Riwayat persetujuan | RWT-003 | Direncanakan |

## Kebutuhan Nonfungsional

| ID kebutuhan | Nama kebutuhan | Aktor | Alur bisnis terkait | Tabel terkait | Halaman atau modul yang direncanakan | Skenario pengujian | Status implementasi |
| --- | --- | --- | --- | --- | --- | --- | --- |
| KNF-001 | HTTPS pada produksi | Semua aktor | Seluruh akses aplikasi | Tabel teknis sesi bila digunakan | Konfigurasi deployment | SEC-005 | Direncanakan |
| KNF-002 | Penyimpanan kata sandi dalam bentuk hash | Semua aktor | Autentikasi | `pengguna` | Modul autentikasi | SEC-004 | Direncanakan |
| KNF-003 | Autentikasi dan otorisasi sisi server | Semua aktor | Seluruh alur terproteksi | Seluruh tabel bisnis sesuai tindakan | Middleware dan policy | AUT-003, AKS-001 sampai AKS-005 | Direncanakan |
| KNF-004 | Tampilan responsif | Semua aktor | Seluruh alur antarmuka | - | Layout dan komponen antarmuka | RSP-001, RSP-002 | Direncanakan |
| KNF-005 | Transaksi keputusan akhir dan saldo | Admin HR dan sistem | Persetujuan akhir serta pembatalan | `saldo_cuti`, `pengajuan_cuti`, `persetujuan_cuti` | Layanan transaksi saldo | SLD-001 sampai SLD-004, BTL-004, BTL-005 | Direncanakan |
| KNF-006 | Jejak keputusan | Atasan dan Admin HR | Persetujuan, penolakan, pembatalan | `pengajuan_cuti`, `persetujuan_cuti`, `pengguna` | Riwayat persetujuan | RWT-002, RWT-003 | Direncanakan |
| KNF-007 | Pesan validasi aman dan jelas | Semua aktor | Validasi formulir dan keputusan | Tabel sesuai formulir | Komponen pesan validasi | VAL-001 sampai VAL-008, ALR-003 | Direncanakan |
| KNF-008 | Waktu respons yang wajar | Semua aktor | Dashboard, daftar, dan detail | Tabel sesuai halaman | Pemantauan query dan performa | PRF-001 | Direncanakan |
| KNF-009 | Kode terpelihara dan dapat diuji | Tim pengembang | Seluruh pengembangan | - | Struktur aplikasi dan suite pengujian | REV-001 | Direncanakan |
| KNF-010 | Dukungan browser modern dan PWA dasar | Semua aktor | Instalasi dan penggunaan aplikasi | - | Manifest, service worker, dan antarmuka | CMP-001, PWA-001 sampai PWA-004 | Direncanakan |
| KNF-011 | Cache tidak menampilkan transaksi atau data sensitif lama | Semua aktor | Logout, pergantian akun, dan kondisi offline | - | Strategi cache dan halaman offline | PWA-002, PWA-003, PWA-005 | Direncanakan |
| KNF-012 | Data dapat dicadangkan dan dipulihkan | Admin HR dan pengelola lingkungan | Operasional aplikasi | Seluruh tabel aplikasi | Prosedur backup dan restore | OPS-001 | Direncanakan |

## Aturan Pemeliharaan Matriks

- Ketika kebutuhan ditambah atau diubah, perbarui baris terkait sebelum implementasi dimulai.
- Status `Sedang dikerjakan` harus disertai referensi branch, issue, atau pull request pada alat kolaborasi tim.
- Status `Selesai` hanya digunakan setelah kode tersedia, skenario pengujian relevan lulus, dan hasil review diterima.
- Jika implementasi menggunakan tabel atau modul berbeda dari rancangan, perbarui dokumen sumber terlebih dahulu dan jelaskan alasannya.
- Kebutuhan yang ditunda tetap dipertahankan dalam matriks beserta alasan pada backlog atau catatan keputusan.
- Jangan menghapus baris hanya karena kebutuhan berubah; gunakan catatan keputusan untuk menjaga riwayat perubahan penting.
