# Hak Akses

Dokumen ini mendefinisikan hak akses Karyawan, Atasan, dan Admin HR. MO merupakan jabatan dengan kewenangan persetujuan Atasan, bukan peran login keempat. Relasi peran jamak dan middleware dasarnya sudah tersedia; otorisasi berdasarkan rute pengajuan masih direncanakan.

## Prinsip Dasar

- Satu akun dapat memiliki beberapa peran sistem secara bersamaan.
- Setiap pegawai aktif memiliki fungsi Karyawan untuk pengajuan pribadi.
- Hak Atasan ditentukan dari penugasan jabatan dan snapshot rute persetujuan.
- Hak Admin HR diberikan terpisah dari jabatan organisasi.
- Kepemilikan, tahap aktif, cakupan organisasi, dan konflik kepentingan selalu diperiksa di server.
- Hak melihat tidak otomatis memberi hak mengubah atau memutus.
- Akses yang tidak dinyatakan diizinkan dianggap ditolak.

## Matriks Hak Akses

| Fitur | Karyawan | Atasan atau MO | Admin HR |
| --- | --- | --- | --- |
| Dashboard dan pengajuan pribadi | Ya | Ya sebagai Karyawan | Ya sebagai Karyawan |
| Mengubah draf pribadi | Ya | Ya sebagai Karyawan | Ya sebagai Karyawan |
| Melihat antrean persetujuan | Tidak | Terbatas pada tahap yang ditugaskan | Terbatas pada tahap akhir dan audit |
| Memutus tahap Atasan | Tidak | Terbatas pada snapshot rute aktif | Tidak hanya karena hak HR |
| Memutus tahap MO | Tidak | Terbatas bagi MO yang ditetapkan | Tidak hanya karena hak HR |
| Memutus tahap akhir | Tidak | Tidak | Ya, jika bukan pemohon atau penyetuju sebelumnya |
| Mengelola organisasi dan akun | Tidak | Tidak | Ya |
| Mengelola jenis, saldo, dan hari libur | Tidak | Tidak | Ya |
| Melihat rekap | Data pribadi | Cakupan tanggung jawab | Sesuai kebutuhan HR |
| Mengekspor rekap | Tidak | Tidak | Ya |

## Karyawan

Identitas pemohon selalu berasal dari akun yang sedang masuk. Karyawan hanya melihat pengajuan dan saldo miliknya, dapat mengubah draf, serta dapat membatalkan pengajuan sebelum keputusan pertama. Pengguna tidak dapat memilih pegawai lain melalui parameter formulir.

## Atasan dan MO

Atasan atau MO dapat memutus pengajuan jika:

- tahap aktif ditujukan kepadanya atau posisi yang menjadi cakupannya;
- penugasannya masih aktif;
- ia bukan pemohon;
- ia belum memberikan keputusan pada tahap lain dalam pengajuan tersebut; dan
- tahap belum diputus oleh penyetuju sah lainnya.

Pengguna yang menjabat MO tetap dapat mengajukan cuti sebagai Karyawan. Jika ia juga Admin HR, kedua fungsi tersedia sesuai konteks, tetapi aturan konflik kepentingan tetap berlaku.

## Admin HR

Admin HR mengelola organisasi, akun, jenis cuti, saldo, hari libur, rekap, dan keputusan akhir. Hak Admin HR tidak dapat digunakan untuk melewati tahap Atasan atau MO yang tersimpan.

Saat Admin HR mengajukan cuti, pengajuan mengikuti penugasan utama seperti pegawai lain. Ia tidak dapat memutus pengajuannya sendiri dan keputusan akhir harus diberikan Admin HR lain. Jika belum ada Admin HR lain yang aktif, proses ditahan sampai tersedia penyetuju yang sah; sistem tidak menyetujui otomatis.

## Batas Data

| Data | Batas akses |
| --- | --- |
| Profil dan penugasan | Pegawai melihat miliknya; Admin HR mengelola data yang diperlukan. |
| Saldo cuti | Pegawai melihat miliknya; penyetuju melihat informasi relevan; Admin HR mengelola seluruh saldo. |
| Pengajuan | Pemilik melihat miliknya; penyetuju melihat antreannya; Admin HR melihat untuk tugas HR. |
| Persetujuan | Pemilik melihat riwayatnya; penyetuju melihat proses dalam cakupannya; Admin HR melakukan audit. |
| Rekap | Dibatasi menurut peran dan cakupan sebelum ditampilkan atau diekspor. |

## Pencegahan Akses Tidak Sah

- Gunakan autentikasi untuk seluruh halaman internal dan policy untuk tindakan bisnis.
- Cari data melalui akun, penugasan, dan snapshot rute, bukan hanya ID dari URL.
- Jangan mempercayai `pegawai_id`, `status`, `saldo`, `tahap`, atau pemberi keputusan dari browser.
- Periksa kembali peran, cakupan, tahap, dan konflik kepentingan saat formulir dikirim.
- Terapkan CSRF, pembatasan mass assignment, serta batas yang sama pada halaman, endpoint JSON, dan ekspor.
- Catat keputusan serta kegagalan akses penting tanpa menyimpan kata sandi atau rahasia.

## Skenario Otorisasi Penting

- Karyawan membuka pengajuan orang lain: ditolak.
- Atasan atau MO memproses pengajuan di luar snapshot rutenya: ditolak.
- Pemohon memproses pengajuannya sendiri dengan peran apa pun: ditolak.
- Penyetuju yang sama mencoba memutus tahap kedua: ditolak.
- Admin HR mencoba melewati tahap Atasan atau MO: ditolak.
- Admin HR memutus pengajuannya sendiri: ditolak dan Admin HR lain diperlukan.
- MO dari unit atau cakupan berbeda mencoba memutus: ditolak.
- Permintaan keputusan dikirim dua kali: keputusan kedua tidak mengubah status atau saldo.

## Catatan Implementasi

Tabel peran jamak dan penugasan jabatan sudah tersedia. Kolom peran dan struktur lama masih dipertahankan sementara untuk kompatibilitas, lalu akan dihentikan penggunaannya setelah seluruh autentikasi, pengelolaan organisasi, dan alur persetujuan berpindah ke struktur baru.
