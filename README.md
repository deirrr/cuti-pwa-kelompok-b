# Rancang Bangun Sistem Informasi Pengajuan dan Persetujuan Cuti Karyawan Berbasis Progressive Web App di Klinik Utama Medika Antapani

> **Status proyek:** Dalam tahap pengembangan.

## Latar Belakang

Proses pengajuan cuti di Klinik Utama Medika Antapani sebelumnya dilakukan menggunakan formulir kertas. Staff perlu mengisi formulir dan menemui atasan secara langsung untuk memperoleh persetujuan sebelum pengajuan diteruskan kepada HR.

Proses tersebut membutuhkan waktu, menggunakan banyak kertas, dan membuat status serta riwayat pengajuan sulit dipantau. Dokumen juga berisiko terlambat disampaikan, tercecer, atau tidak tercatat dengan baik.

Sistem informasi ini dibangun untuk memindahkan proses pengajuan dan persetujuan cuti ke dalam aplikasi web yang terpusat. Aplikasi dikembangkan menggunakan pendekatan Progressive Web App (PWA) agar dapat diakses melalui komputer maupun perangkat seluler dan dapat dipasang dari browser yang mendukung.

## Tujuan Sistem

- Mengurangi penggunaan kertas dalam proses pengajuan dan persetujuan cuti.
- Memudahkan Staff mengajukan cuti tanpa harus menemui Atasan atau HR secara langsung.
- Memudahkan Atasan dan HR memeriksa serta memproses pengajuan cuti.
- Membantu pengguna memantau status pengajuan cuti.
- Menyimpan riwayat pengajuan dan persetujuan secara terpusat.
- Membantu HR mengelola saldo cuti dan menyusun laporan cuti karyawan.
- Menyediakan aplikasi yang responsif dan dapat dipasang sebagai PWA.

## Ruang Lingkup

Sistem ini digunakan di lingkungan Klinik Utama Medika Antapani dengan ruang lingkup:

- Autentikasi dan pembatasan akses berdasarkan peran pengguna.
- Pengelolaan data Staff, Atasan, bagian organisasi, jenis cuti, saldo cuti, dan hari libur.
- Pengajuan cuti oleh Staff dan Atasan.
- Persetujuan pengajuan Staff oleh Atasan dan HR.
- Persetujuan pengajuan Atasan oleh HR.
- Validasi tanggal pengajuan dan ketersediaan saldo cuti.
- Pencatatan keputusan, catatan persetujuan, status, dan riwayat pengajuan.
- Pembaruan saldo setelah pengajuan memperoleh persetujuan akhir.
- Penyediaan laporan pengajuan dan penggunaan cuti.
- Dukungan dasar PWA untuk pemasangan aplikasi dan tampilan yang responsif.

Sistem tidak mencakup:

- Pengajuan cuti untuk HR dan Kepala Klinik.
- Struktur organisasi berbasis Unit Bisnis.
- Pengelolaan absensi dan penggajian.
- Integrasi dengan sistem atau layanan eksternal.
- Notifikasi melalui WhatsApp, email, atau layanan pihak ketiga.

## Aktor Sistem

| Aktor | Tanggung jawab dan hak akses |
| --- | --- |
| Staff | Mengajukan cuti, melihat saldo, memantau status, membatalkan pengajuan yang masih dapat dibatalkan, dan melihat riwayat pengajuan sendiri. |
| Atasan | Memiliki hak pengajuan pribadi serta memeriksa, menyetujui, atau menolak pengajuan Staff yang menjadi bawahannya. Pengajuan pribadi Atasan diteruskan langsung kepada HR. |
| HR | Mengelola data pendukung cuti, memberikan persetujuan akhir, memperbarui saldo melalui proses sistem, dan melihat laporan cuti karyawan. |

## Alur Singkat

```text
Staff  -> Atasan -> HR
Atasan -> HR
```

Pengajuan yang disetujui HR menjadi pengajuan final dan diperhitungkan pada saldo serta laporan cuti.
