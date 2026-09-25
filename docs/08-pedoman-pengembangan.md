# Pedoman Pengembangan

Dokumen ini menjadi pagar pengembangan agar proyek tetap konsisten dengan kebutuhan capstone. Dokumentasi dalam repository merupakan sumber kebenaran utama. Kode, database, antarmuka, dan pengujian harus dapat ditelusuri kembali ke kebutuhan yang telah disepakati.

## Tujuan Pedoman

- Menjaga pengembangan tetap berada dalam ruang lingkup sistem pengajuan cuti.
- Mencegah fitur dibuat hanya berdasarkan ide spontan.
- Menyamakan cara tim menilai kesiapan dan penyelesaian pekerjaan.
- Menjaga istilah, alur, data, dan pengujian tetap konsisten.
- Memudahkan setiap anggota meninjau dampak perubahan sebelum kode dibuat.

## Dokumen Acuan

Urutan acuan ketika ditemukan perbedaan adalah:

1. [Keputusan Proyek](09-keputusan-proyek.md) yang berstatus berlaku.
2. [Kebutuhan Sistem](01-kebutuhan-sistem.md).
3. [Alur Bisnis](02-alur-bisnis.md) dan [Hak Akses](03-hak-akses.md).
4. [Rancangan Database](04-rancangan-database.md) dan [Rancangan PWA](05-rancangan-pwa.md).
5. [Rencana Pengujian](06-rencana-pengujian.md).
6. [Pembagian Tugas](07-pembagian-tugas.md) dan pedoman ini.
7. [Matriks Ketertelusuran](10-matriks-ketertelusuran.md) sebagai indeks hubungan antardokumen dan implementasi.

Jika kode berbeda dari keputusan dan kebutuhan yang masih berlaku, perbedaan tersebut harus diselesaikan. Tim tidak boleh diam-diam menjadikan perilaku kode sebagai aturan baru.

## Prinsip Pengembangan

- Kerjakan kebutuhan bernilai paling penting lebih dahulu.
- Buat solusi sesederhana mungkin untuk kebutuhan capstone lima orang.
- Gunakan pola dan fasilitas Laravel yang sesuai versi terpasang.
- Terapkan otorisasi dan validasi di sisi server.
- Pisahkan fitur wajib, pengembangan lanjutan, dan hal di luar ruang lingkup.
- Sertakan pengujian yang membuktikan perilaku dan kegagalan penting.
- Perbarui dokumentasi ketika keputusan atau perilaku yang direncanakan berubah.
- Jangan memasukkan kata sandi, token, isi `.env`, atau data pribadi nyata ke repository.

## Ruang Lingkup yang Dikunci

- Aplikasi Laravel monolitik yang berdiri sendiri.
- Teknologi utama Laravel 13, PHP 8.3, MySQL, Blade, JavaScript, dan PWA.
- Aktor hanya Karyawan, Atasan, dan Admin HR.
- Persetujuan berlangsung dua tahap: Atasan, kemudian Admin HR.
- Status pengajuan hanya `draf`, `menunggu_atasan`, `menunggu_hr`, `disetujui`, `ditolak`, dan `dibatalkan` sampai ada keputusan baru yang terdokumentasi.
- Saldo dikurangi setelah persetujuan akhir, bukan ketika draf dibuat atau ketika Atasan menyetujui.
- Nama tabel dan kolom bisnis menggunakan bahasa Indonesia.
- Tabel teknis bawaan Laravel boleh mempertahankan nama framework.
- Pengajuan dan persetujuan memerlukan koneksi internet.
- PWA wajib berfokus pada instalasi, cache aset statis, halaman offline, HTTPS produksi, dan tampilan responsif.

## Hal yang Tidak Boleh Masuk Proyek

- Sistem absensi, penggajian, slip gaji, rekrutmen, atau penilaian kinerja.
- Integrasi API, backend, atau layanan pihak ketiga.
- Transaksi pengajuan atau persetujuan secara offline.
- Push notification dan background sync sebagai fitur wajib.
- Penambahan aktor, tingkat persetujuan, atau status tanpa keputusan proyek baru.
- Fitur pengembangan lanjutan sebelum fitur wajib yang menjadi prasyaratnya stabil.
- Perubahan di luar kebutuhan hanya karena menarik secara teknis.

## Aturan Penambahan Fitur

Sebuah fitur hanya boleh mulai dikembangkan jika seluruh syarat berikut terpenuhi:

1. Memiliki kode kebutuhan fungsional.
2. Aktor dan hak aksesnya jelas.
3. Alur bisnisnya terdokumentasi.
4. Data yang digunakan sudah ada dalam rancangan database.
5. Kriteria penerimaannya dapat diuji.
6. Tidak bertentangan dengan batasan sistem.

Usulan fitur yang belum memenuhi syarat dicatat sebagai usulan atau pengembangan lanjutan. Tim tidak langsung membuat branch implementasi. Jika fitur diterima, urutan perubahan adalah keputusan bila diperlukan, kebutuhan, alur dan akses, database, pengujian, matriks ketertelusuran, kemudian implementasi.

## Aturan Perubahan Database

- Setiap tabel dan kolom bisnis harus memiliki alasan yang terkait dengan kebutuhan.
- Perubahan relasi, nullability, nilai bawaan, unique key, atau aturan penghapusan harus diperbarui lebih dahulu pada rancangan database.
- Nama bisnis tetap menggunakan bahasa Indonesia dan konsisten dengan istilah antarmuka.
- Migration yang sudah dipakai bersama tidak diubah sembarangan; buat migration lanjutan ketika tahap implementasi telah berjalan.
- Foreign key, indeks, transaksi, dan aturan integritas dirancang sebelum kode bergantung padanya.
- Data riwayat tidak dihapus hanya untuk mempermudah implementasi.
- Perubahan saldo dan keputusan akhir harus aman terhadap permintaan berulang dan kegagalan di tengah transaksi.
- Perubahan database harus ditinjau oleh penanggung jawab backend serta sedikitnya satu anggota lain.

## Aturan Perubahan Alur Bisnis

- Perubahan status hanya boleh mengikuti transisi yang terdokumentasi.
- Perubahan urutan persetujuan, kewenangan aktor, pembatalan, atau waktu pengurangan saldo membutuhkan entri keputusan proyek baru.
- Alur, matriks hak akses, database, rencana pengujian, dan matriks ketertelusuran diperbarui bersama-sama.
- Atasan tidak boleh memproses pengajuannya sendiri.
- Admin HR tidak boleh melewati tahap Atasan.
- Penolakan tidak mengurangi saldo.
- Pembatalan setelah persetujuan akhir harus mengembalikan saldo tepat satu kali sesuai aturan yang disepakati.

## Aturan Konsistensi Istilah

- Gunakan `Karyawan`, `Atasan`, dan `Admin HR` untuk nama aktor dalam teks.
- Gunakan nilai status persis seperti yang tercantum dalam ruang lingkup terkunci.
- Gunakan nama tabel dan kolom dari rancangan database.
- Gunakan "persetujuan akhir" untuk keputusan Admin HR.
- Gunakan "saldo tersedia" untuk jumlah yang masih dapat digunakan.
- Gunakan "hari libur" untuk tanggal yang dikecualikan selain akhir pekan.
- Hindari sinonim baru yang dapat dianggap sebagai aktor, status, atau tahap tambahan.

## Aturan Keamanan Dasar

- Seluruh halaman internal memerlukan autentikasi.
- Setiap tindakan memerlukan otorisasi berdasarkan peran, kepemilikan, cakupan bawahan, dan status.
- Identitas pemilik, status, saldo, serta pemberi keputusan ditentukan server, bukan dipercaya dari browser.
- Validasi dilakukan di server dan menampilkan pesan yang aman.
- Gunakan perlindungan CSRF, hashing kata sandi, pembatasan mass assignment, dan escaping output.
- Jangan cache respons yang memuat data pribadi untuk penggunaan offline.
- Gunakan HTTPS pada produksi dan konfigurasi cookie yang sesuai.
- Jangan mencatat kata sandi, token, atau isi data rahasia ke log dan repository.
- Uji akses silang, peningkatan hak, input berbahaya, dan permintaan keputusan berulang.

## Aturan Branch dan Pull Request

- Satu branch mewakili satu tugas yang telah siap dikerjakan.
- Gunakan awalan yang jelas, misalnya `feature/`, `fix/`, `docs/`, atau `test/`.
- Contoh: `feature/pengajuan-cuti`, `fix/validasi-saldo`, `docs/perbarui-alur`, dan `test/hak-akses-atasan`.
- Jangan mencampurkan refactor atau perubahan lain yang tidak diperlukan.
- Commit harus kecil, bermakna, dan tidak memuat kredensial.
- Pull request menjelaskan kebutuhan terkait, perubahan, cara menguji, risiko, dan dokumentasi yang diperbarui.
- Jangan melakukan push langsung ke branch utama kecuali tim memiliki aturan khusus yang telah disepakati.

## Aturan Code Review

- Sedikitnya satu anggota selain pembuat melakukan review.
- Reviewer memeriksa keterkaitan dengan kode kebutuhan dan matriks ketertelusuran.
- Reviewer memeriksa hak akses, validasi, transaksi, keamanan, pengujian, serta dampak pada aktor lain.
- Perubahan database atau saldo wajib ditinjau penanggung jawab backend.
- Perubahan persetujuan atau policy wajib ditinjau penanggung jawab hak akses.
- Perubahan PWA wajib diperiksa agar tidak mengklaim transaksi offline.
- Temuan penting harus diselesaikan sebelum merge; persetujuan tidak diberikan hanya karena kode dapat dijalankan.

## Definition of Ready

Sebuah fitur siap dikerjakan apabila:

- kode dan isi kebutuhan tersedia;
- aktor, hak akses, dan batas kepemilikan jelas;
- alur berhasil serta gagal telah dibahas;
- status dan transisinya telah ditentukan;
- tabel dan data terkait telah dirancang;
- kriteria penerimaan dan skenario pengujian tersedia;
- ketergantungan pada pekerjaan lain diketahui;
- asumsi penting telah dikonfirmasi atau ditandai;
- ukuran tugas cukup kecil untuk satu pull request; dan
- tidak bertentangan dengan keputusan proyek yang berlaku.

## Definition of Done

Sebuah fitur selesai apabila:

- implementasi memenuhi kebutuhan dan tidak menambah ruang lingkup tersembunyi;
- otorisasi, validasi, alur status, dan perubahan data sesuai dokumentasi;
- pengujian jalur berhasil serta kegagalan penting telah ditulis dan lulus;
- pemeriksaan format dan pengujian relevan telah dijalankan;
- tidak ada kredensial atau data rahasia dalam perubahan;
- tampilan terkait telah diperiksa secara responsif;
- dokumentasi dan status matriks ketertelusuran diperbarui berdasarkan bukti;
- pull request telah ditinjau dan semua temuan wajib diselesaikan; dan
- perubahan dapat digabungkan tanpa konflik.

## Checklist Sebelum Fitur Mulai Dibuat

- [ ] Kode kebutuhan fungsional sudah ada.
- [ ] Fitur termasuk kategori wajib atau telah disetujui sebagai perubahan ruang lingkup.
- [ ] Aktor dan hak akses telah ditentukan.
- [ ] Alur utama, kegagalan, penolakan, dan pembatalan yang relevan telah jelas.
- [ ] Status yang digunakan berasal dari daftar yang dikunci.
- [ ] Tabel dan kolom yang diperlukan sudah ada dalam rancangan.
- [ ] Kriteria penerimaan dan skenario pengujian telah ditulis.
- [ ] Ketergantungan dan penanggung jawab telah diketahui.
- [ ] Matriks ketertelusuran memuat kebutuhan tersebut.
- [ ] Branch dibuat dengan nama yang sesuai.

## Checklist Sebelum Pull Request Digabungkan

- [ ] Pull request merujuk kode kebutuhan.
- [ ] Diff hanya berisi perubahan yang diperlukan.
- [ ] Hak akses dan kepemilikan data telah diperiksa.
- [ ] Validasi jalur berhasil dan gagal telah diuji.
- [ ] Perubahan saldo atau status aman terhadap pengulangan permintaan.
- [ ] Pengujian relevan telah lulus dan hasilnya dicantumkan.
- [ ] Format kode dan pemeriksaan proyek telah lulus.
- [ ] Tidak ada kredensial, `.env`, atau data pribadi nyata.
- [ ] Dokumentasi dan matriks ketertelusuran telah diperbarui.
- [ ] Sedikitnya satu reviewer telah menyetujui.
- [ ] Tidak ada konflik atau komentar wajib yang belum selesai.
