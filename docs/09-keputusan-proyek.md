# Keputusan Proyek

Dokumen ini mencatat keputusan yang mengikat rancangan dan pengembangan proyek. Keputusan yang berstatus **Berlaku** menjadi acuan ketika muncul perbedaan pendapat atau ketidaksesuaian antardokumen.

## Daftar Keputusan

| ID keputusan | Tanggal | Keputusan | Alasan | Dampak | Status |
| --- | --- | --- | --- | --- | --- |
| KP-001 | 2026-09-26 | Menggunakan arsitektur Laravel monolitik. | Cakupan capstone dapat ditangani oleh satu aplikasi dan satu tim kecil. | Backend, Blade, autentikasi, serta proses bisnis dikembangkan dalam repository yang sama. | Berlaku |
| KP-002 | 2026-09-26 | Menggunakan MySQL sebagai database aplikasi. | MySQL sesuai teknologi yang dipilih tim dan mendukung relasi serta transaksi yang diperlukan. | Rancangan tipe data, indeks, constraint, dan lingkungan pengembangan mengacu pada MySQL. | Berlaku |
| KP-003 | 2026-09-26 | Menggunakan nama tabel dan kolom bisnis berbahasa Indonesia. | Istilah bisnis lebih mudah dipahami dan konsisten bagi anggota tim. | Migration bisnis, model, query, dan dokumentasi mengikuti kamus data Indonesia; tabel teknis Laravel boleh tetap memakai nama bawaan. | Berlaku |
| KP-004 | 2026-09-26 | Menggunakan persetujuan dua tahap: Atasan lalu Admin HR. | Alur ini memisahkan penilaian operasional dan keputusan akhir administrasi. | Pengajuan harus melewati `menunggu_atasan` lalu `menunggu_hr`; Admin HR tidak dapat melewati tahap Atasan. | Berlaku |
| KP-005 | 2026-09-26 | Aplikasi berdiri sendiri dan tidak terhubung dengan sistem eksternal. | Fokus proyek adalah proses cuti dan integrasi akan menambah ketergantungan serta ruang lingkup. | Tidak dibuat integrasi API, backend lain, layanan pihak ketiga, atau sinkronisasi data eksternal. | Berlaku |
| KP-006 | 2026-09-26 | Saldo dikurangi setelah persetujuan akhir Admin HR. | Saldo tidak boleh berkurang untuk pengajuan yang belum diputuskan atau akhirnya ditolak. | Persetujuan akhir dan pengurangan saldo dilakukan dalam satu transaksi serta harus idempoten. | Berlaku |
| KP-007 | 2026-09-26 | Transaksi utama tetap membutuhkan internet. | Server harus memvalidasi hak akses, status, tanggal, dan saldo terbaru sebelum menyimpan perubahan. | Autentikasi, pengajuan, persetujuan, penolakan, pembatalan, dan perubahan saldo tidak diproses secara offline. | Berlaku |
| KP-008 | 2026-09-26 | PWA berfokus pada installability, cache aset statis, dan halaman offline. | Cakupan tersebut memberi pengalaman PWA dasar tanpa risiko sinkronisasi transaksi. | Manifest, ikon, service worker, HTTPS, cache aset, halaman offline, dan responsivitas menjadi fokus; data transaksi tidak dicache untuk operasi offline. | Berlaku |
| KP-009 | 2026-09-26 | Fitur di luar dokumentasi tidak langsung dikembangkan. | Setiap pekerjaan harus dapat ditelusuri dan diuji agar tim tidak keluar dari ruang lingkup. | Fitur baru harus melewati pembaruan kebutuhan, alur, data, kriteria penerimaan, dan matriks ketertelusuran. | Berlaku |
| KP-010 | 2026-09-26 | Proyek dibatasi sebagai sistem pengajuan cuti, bukan sistem HR lengkap. | Tim perlu mempertahankan cakupan yang realistis untuk capstone lima orang. | Modul absensi, payroll, slip gaji, rekrutmen, penilaian kinerja, dan modul HR lain tidak dikembangkan. | Berlaku |
| KP-011 | 2026-09-26 | Sabtu dan Minggu diperlakukan sebagai akhir pekan. | Proyek memerlukan aturan perhitungan awal yang seragam dan sederhana. | Tanggal Sabtu, Minggu, dan hari libur aktif tidak dapat dipilih sebagai hari cuti. | Berlaku |
| KP-012 | 2026-09-26 | Jenis cuti dan jatah bawaan dikelola Admin HR, bukan ditanam tetap dalam kode. | Kebijakan operasional dapat berubah tanpa memerlukan perubahan program. | Nilai awal jenis dan jatah diisi sebagai data master; validasi menggunakan data aktif pada database. | Berlaku |
| KP-013 | 2026-09-26 | Saldo tidak otomatis dibawa ke tahun berikutnya pada versi awal. | Aturan carry-over belum menjadi kebutuhan wajib dan dapat menambah kerumitan periode saldo. | Admin HR menerbitkan saldo per tahun; fitur carry-over memerlukan keputusan baru jika kelak dibutuhkan. | Berlaku |
| KP-014 | 2026-09-26 | Pembatalan pengajuan yang sudah disetujui hanya diproses Admin HR sebelum tanggal cuti pertama. | Batas ini mudah dipahami dan mencegah perubahan saldo setelah cuti mulai digunakan. | Admin HR wajib mencatat alasan; saldo dikembalikan tepat satu kali dalam transaksi. | Berlaku |
| KP-015 | 2026-09-26 | Atasan dan Admin HR dapat mengajukan cuti sebagai pegawai, tetapi tidak boleh memutus pengajuannya sendiri. | Semua akun terhubung dengan pegawai dan konflik kepentingan harus dicegah. | Pengajuan Atasan diproses Atasan langsungnya; keputusan akhir pengajuan Admin HR harus diberikan akun Admin HR lain. | Berlaku |
| KP-016 | 2026-09-26 | Setiap pegawai memiliki paling banyak satu Atasan langsung. | Struktur tunggal cukup untuk alur capstone dan menyederhanakan otorisasi. | Pegawai yang akan mengajukan wajib memiliki Atasan aktif yang bukan dirinya; rantai pelaporan melingkar ditolak. | Berlaku |
| KP-017 | 2026-09-26 | Nomor pengajuan menggunakan format `CUTI-YYYY-NNNNNN`. | Format mudah dibaca, unik per tahun, dan cukup untuk skala capstone. | Nomor dibuat server dan nomor urut dimulai kembali setiap tahun tanpa mengubah primary key. | Berlaku |
| KP-018 | 2026-09-26 | Format ekspor rekap wajib versi awal adalah CSV. | CSV dapat dibuka dengan aplikasi spreadsheet dan tidak memerlukan pengolah dokumen tambahan. | PDF dan XLSX tetap menjadi pengembangan lanjutan, bukan syarat versi awal. | Berlaku |
| KP-019 | 2026-09-26 | Satuan cuti versi awal adalah satu hari penuh. | Cuti setengah hari memerlukan perhitungan serta antarmuka tambahan yang belum menjadi kebutuhan wajib. | Setiap tanggal valid bernilai satu hari; cuti setengah hari memerlukan keputusan baru. | Berlaku |

## Status Keputusan

- **Diusulkan:** belum disepakati dan belum menjadi acuan implementasi.
- **Berlaku:** telah disepakati dan menjadi acuan aktif.
- **Digantikan:** tidak lagi aktif karena digantikan keputusan baru.
- **Dicabut:** dibatalkan tanpa pengganti dan alasannya telah dicatat.

## Aturan Perubahan Keputusan

- Jangan menghapus atau mengubah isi keputusan lama untuk menyembunyikan riwayat.
- Perubahan keputusan dicatat sebagai entri baru dengan ID berikutnya, tanggal, alasan, dan dampaknya.
- Entri baru harus menyebut ID keputusan yang digantikan atau dicabut.
- Status keputusan lama diperbarui menjadi `Digantikan` atau `Dicabut` setelah keputusan baru disepakati.
- Dokumen kebutuhan, alur bisnis, hak akses, database, PWA, pengujian, dan matriks ketertelusuran diperbarui sesuai dampaknya.
- Keputusan baru harus ditinjau oleh tim sebelum implementasi dimulai.

## Data Operasional yang Masih Perlu Diisi

Keputusan rancangan di atas belum menentukan isi data operasional. Sebelum demonstrasi, tim dan pihak terkait masih perlu mengisi daftar departemen, pegawai, hubungan Atasan, jenis cuti, besar jatah, saldo awal, serta hari libur. Perubahan isi data tersebut tidak mengubah keputusan proyek selama tetap mengikuti struktur dan alur yang berlaku.
