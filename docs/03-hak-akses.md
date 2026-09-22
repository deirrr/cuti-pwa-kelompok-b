# Hak Akses

Dokumen ini mendefinisikan rancangan hak akses untuk Karyawan, Atasan, dan Admin HR. Pemeriksaan hak akses harus dilakukan di sisi server pada setiap permintaan, bukan hanya dengan menyembunyikan tombol pada antarmuka.

## Prinsip Dasar

- Setiap pengguna harus masuk menggunakan akun aktif.
- Akses ditentukan oleh peran, kepemilikan data, hubungan Atasan-bawahan, dan status pengajuan.
- Hak melihat data tidak otomatis memberikan hak mengubah data.
- Setiap perubahan penting harus menyimpan aktor dan waktu tindakan.
- Akses yang tidak dinyatakan diizinkan dianggap ditolak.
- Atasan tidak boleh menyetujui atau menolak pengajuannya sendiri.

## Matriks Hak Akses

Keterangan: **Ya** berarti diizinkan, **Terbatas** berarti hanya pada data atau kondisi tertentu, dan **Tidak** berarti tidak diizinkan.

| Fitur | Karyawan | Atasan | Admin HR |
| --- | --- | --- | --- |
| Melihat dashboard | Ya, data pribadi | Ya, data pribadi dan ringkasan bawahan | Ya, ringkasan operasional HR |
| Mengajukan cuti | Ya, untuk diri sendiri | Ya, untuk diri sendiri sebagai pegawai | Ya, untuk diri sendiri sebagai pegawai |
| Mengubah pengajuan | Terbatas, hanya `draf` milik sendiri | Terbatas, hanya `draf` milik sendiri | Tidak sebagai HR; mengikuti hak Karyawan untuk pengajuan sendiri |
| Membatalkan pengajuan | Terbatas, milik sendiri sebelum keputusan Atasan | Terbatas, milik sendiri sebelum keputusan Atasan | Ya, untuk pembatalan yang memerlukan proses HR |
| Melihat riwayat pribadi | Ya | Ya | Ya |
| Melihat pengajuan bawahan | Tidak | Terbatas, bawahan langsung | Ya, sesuai kebutuhan operasional HR |
| Menyetujui atau menolak tahap Atasan | Tidak | Terbatas, pengajuan bawahan pada `menunggu_atasan` | Tidak |
| Menyetujui atau menolak tahap akhir | Tidak | Tidak | Terbatas, pengajuan pada `menunggu_hr` |
| Mengelola pengguna dan pegawai | Tidak | Tidak | Ya |
| Mengelola jenis cuti | Tidak | Tidak | Ya |
| Mengelola saldo | Tidak | Tidak | Ya |
| Mengelola hari libur | Tidak | Tidak | Ya |
| Melihat rekap | Riwayat pribadi | Terbatas, data bawahan | Ya |
| Mengekspor rekap | Tidak | Terbatas, jika fitur diberikan pada tahap implementasi | Ya |
| Melihat jejak persetujuan | Terbatas, pengajuan sendiri | Terbatas, pengajuan bawahan yang berwenang dilihat | Ya |

## Rincian per Aktor

### Karyawan

Karyawan hanya dapat melihat dan mengelola data pengajuan miliknya. Identitas pegawai harus diperoleh dari akun yang sedang masuk, bukan dari parameter yang dikirim pengguna. Karyawan tidak dapat memilih pegawai lain ketika membuat pengajuan.

Perubahan hanya diizinkan ketika status `draf`. Pembatalan mandiri hanya diizinkan untuk `draf` atau `menunggu_atasan`. Setelah pengajuan masuk ke tahap HR, proses pembatalan mengikuti kewenangan Admin HR.

### Atasan

Atasan dapat melihat pengajuan jika pemilik pengajuan tercatat sebagai bawahan langsungnya. Saat memberi keputusan, sistem harus memeriksa kembali bahwa:

- hubungan Atasan-bawahan masih aktif;
- status pengajuan adalah `menunggu_atasan`;
- pengajuan belum memiliki keputusan tahap Atasan; dan
- pegawai pemilik pengajuan bukan dirinya sendiri.

Jika Atasan juga mengajukan cuti sebagai pegawai, kemampuan tersebut mengikuti hak Karyawan. Hak Atasan tidak boleh digunakan untuk memproses pengajuan pribadinya.

### Admin HR

Admin HR dapat mengelola data utama dan melihat pengajuan yang dibutuhkan untuk proses HR. Ketika mengajukan cuti untuk dirinya sendiri, Admin HR mengikuti aturan Karyawan. Keputusan akhir hanya dapat diberikan terhadap pengajuan `menunggu_hr`. Admin HR tidak dapat melewati tahap Atasan dengan langsung menyetujui pengajuan `menunggu_atasan`.

Perubahan manual saldo harus memiliki alasan dan dapat ditelusuri. Penghapusan data yang telah dipakai pada riwayat sebaiknya diganti dengan penonaktifan agar hubungan data tetap utuh.

## Batas Kepemilikan Data

| Data | Batas akses |
| --- | --- |
| Profil pegawai | Karyawan melihat profil sendiri; Admin HR mengelola data yang diperlukan. |
| Saldo cuti | Karyawan melihat saldo sendiri; Atasan hanya melihat informasi relevan saat memproses bawahan; Admin HR mengelola seluruh saldo. |
| Pengajuan cuti | Pemilik melihat miliknya; Atasan melihat milik bawahan; Admin HR melihat sesuai tugas HR. |
| Persetujuan | Pemilik melihat riwayat pengajuannya; pemberi keputusan melihat proses yang menjadi kewenangannya; Admin HR dapat melakukan audit. |
| Rekap | Selalu dibatasi berdasarkan cakupan aktor sebelum ditampilkan atau diekspor. |

## Pencegahan Akses Data Pengguna Lain

- Gunakan middleware autentikasi untuk seluruh halaman internal.
- Gunakan policy atau mekanisme otorisasi Laravel untuk tindakan melihat, mengubah, membatalkan, menyetujui, menolak, dan mengekspor.
- Cari data melalui relasi pengguna yang sedang masuk atau cakupan bawahan, bukan hanya berdasarkan ID dari URL.
- Tolak permintaan ketika ID valid tetapi data bukan milik atau bukan cakupan pengguna.
- Validasi peran dan status kembali ketika formulir dikirim karena tampilan dapat kedaluwarsa.
- Terapkan perlindungan CSRF pada formulir dan batasi mass assignment pada data yang boleh diubah.
- Jangan mempercayai nilai `pegawai_id`, `status`, `saldo`, atau `pemberi_keputusan` dari browser.
- Catat percobaan atau kegagalan akses penting secukupnya tanpa menyimpan kata sandi atau data rahasia.
- Terapkan pembatasan yang sama pada ekspor, endpoint JSON, dan tampilan biasa.

## Skenario Otorisasi Penting

- Karyawan A mencoba membuka URL detail pengajuan Karyawan B: akses ditolak.
- Atasan mencoba memproses pengajuan pegawai yang bukan bawahannya: akses ditolak.
- Atasan mencoba memproses pengajuan sendiri: akses ditolak.
- Atasan mencoba menyetujui pengajuan yang sudah `menunggu_hr`: akses ditolak.
- Admin HR mencoba menyetujui pengajuan `menunggu_atasan`: akses ditolak.
- Pengguna non-HR mencoba mengubah saldo atau hari libur: akses ditolak.
- Pengguna mengubah ID pada permintaan ekspor: hasil tetap dibatasi sesuai cakupan akses.
- Permintaan keputusan dikirim dua kali: keputusan kedua tidak mengubah status atau saldo.

## Asumsi yang Perlu Dikonfirmasi

- Hubungan organisasi awal hanya menggunakan satu Atasan langsung per pegawai.
- Admin HR dapat melihat seluruh data pengajuan untuk kebutuhan operasional dan audit.
- Hak ekspor untuk Atasan dapat diaktifkan bila memang diperlukan; fitur wajib hanya mewajibkan Admin HR mengekspor rekap.
- Pengguna dapat memiliki fungsi Atasan atau Admin HR sekaligus tetap memiliki profil pegawai. Hak untuk pengajuan pribadi selalu mengikuti aturan Karyawan.
