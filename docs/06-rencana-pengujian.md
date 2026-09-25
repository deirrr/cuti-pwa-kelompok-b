# Rencana Pengujian

Dokumen ini berisi rencana pengujian untuk fitur yang akan dikembangkan. Belum ada pernyataan bahwa skenario berikut telah dijalankan atau lulus.

## Pendekatan

- Utamakan feature test untuk perilaku yang dapat dicapai melalui permintaan HTTP.
- Gunakan unit test hanya untuk aturan perhitungan atau objek yang benar-benar terpisah dari framework.
- Uji perilaku yang terlihat oleh pengguna, perubahan database, dan efek samping penting.
- Buat data uji melalui factory dan state yang bermakna; setiap pengujian menyiapkan datanya sendiri.
- Gunakan database pengujian, kendalikan waktu pada skenario tanggal, dan jangan memanggil layanan jaringan nyata.
- Uji kegagalan autentikasi, otorisasi, validasi, dan kepemilikan selain jalur berhasil.
- Untuk operasi tulis, periksa respons sekaligus keadaan database setelah tindakan.
- Jalankan pengujian terkecil yang relevan selama pengembangan, kemudian seluruh suite sebelum pull request digabungkan.

## Data dan Kondisi Uji

Data uji minimal direncanakan mencakup:

- akun Karyawan, Atasan, Admin HR, pengguna tidak aktif, dan pengguna tanpa hak;
- dua departemen dengan hubungan Atasan-bawahan yang berbeda;
- jenis cuti aktif dan tidak aktif;
- saldo cukup, tepat pada batas, tidak cukup, dan nol;
- hari kerja, akhir pekan, dan hari libur;
- pengajuan pada setiap status;
- tanggal tetap agar hasil tidak berubah mengikuti waktu eksekusi.

Data tersebut dibuat khusus di lingkungan pengujian dan tidak menggunakan data produksi.

## Matriks Skenario

| ID pengujian | Skenario | Prasyarat | Langkah ringkas | Hasil yang diharapkan | Jenis pengujian |
| --- | --- | --- | --- | --- | --- |
| AUT-001 | Pengguna aktif masuk dengan NIK dan kata sandi benar | Akun aktif tersedia | Kirim formulir masuk menggunakan NIK yang valid | Pengguna terautentikasi dan diarahkan ke dashboard yang sesuai | Feature - autentikasi |
| AUT-002 | Kredensial masuk salah | Akun aktif tersedia | Kirim kata sandi yang salah | Akses ditolak, sesi tidak dibuat, dan pesan aman ditampilkan | Feature - autentikasi |
| AUT-003 | Pengguna belum masuk membuka halaman internal | Tidak ada sesi | Buka dashboard | Pengguna diarahkan ke halaman masuk | Feature - autentikasi |
| AUT-004 | Akun tidak aktif mencoba masuk | Akun tidak aktif tersedia | Kirim kredensial yang benar | Sesi aplikasi tidak diberikan dan pesan yang sesuai ditampilkan | Feature - autentikasi |
| DBH-001 | Karyawan melihat ringkasan pada dashboard | Karyawan memiliki saldo dan beberapa pengajuan | Buka dashboard Karyawan | Saldo dan ringkasan status hanya menampilkan data miliknya | Feature - dashboard |
| DFT-001 | Karyawan menyimpan pengajuan sebagai draf | Karyawan aktif dan jenis cuti tersedia | Isi data awal lalu simpan tanpa mengirim | Pengajuan tersimpan sebagai `draf` dan belum dapat diproses Atasan | Feature - pengajuan |
| DFT-002 | Karyawan mengubah draf miliknya | Draf dan pengajuan terkirim tersedia | Ubah keduanya sebagai pemilik | Draf dapat diperbarui, sedangkan pengajuan terkirim tidak dapat diubah | Feature - status/otorisasi |
| RWT-001 | Karyawan melihat riwayat pribadinya | Dua Karyawan dengan beberapa pengajuan tersedia | Buka riwayat sebagai salah satu Karyawan | Hanya detail, status, dan riwayat milik pengguna yang ditampilkan | Feature - kepemilikan |
| RWT-002 | Atasan melihat riwayat keputusan yang diberikannya | Keputusan dari dua Atasan tersedia | Buka riwayat keputusan salah satu Atasan | Hanya keputusan dalam cakupannya yang ditampilkan | Feature - kepemilikan |
| RWT-003 | Admin HR melihat jejak keputusan | Pengajuan dengan keputusan lengkap tersedia | Buka detail riwayat sebagai Admin HR | Pemberi keputusan, tahap, keputusan, catatan, dan waktu ditampilkan dengan benar | Feature - audit |
| AKS-001 | Karyawan membuka pengajuan milik Karyawan lain | Dua Karyawan dan satu pengajuan tersedia | Ubah ID pada URL detail | Data tidak ditampilkan; respons tidak membocorkan keberadaan data | Feature - otorisasi |
| AKS-002 | Atasan melihat daftar pengajuan bawahan | Atasan memiliki satu bawahan dan satu nonbawahan | Buka daftar pengajuan | Hanya pengajuan bawahan dalam cakupan yang ditampilkan | Feature - otorisasi |
| AKS-003 | Atasan mencoba memproses pengajuan sendiri | Atasan memiliki pengajuan `menunggu_atasan` | Kirim keputusan setuju | Akses ditolak, status dan saldo tidak berubah, keputusan tidak tercatat | Feature - otorisasi |
| AKS-004 | Pengguna non-HR membuka pengelolaan saldo | Akun Karyawan atau Atasan tersedia | Buka halaman atau kirim perubahan saldo | Akses ditolak dan saldo tidak berubah | Feature dan policy |
| AKS-005 | Admin HR mencoba melewati tahap Atasan | Pengajuan `menunggu_atasan` tersedia | Kirim persetujuan akhir | Tindakan ditolak, status dan saldo tidak berubah | Feature - otorisasi/status |
| AKS-006 | Admin HR mencoba memutus pengajuan sendiri | Pengajuan milik Admin HR berstatus `menunggu_hr` | Kirim persetujuan atau penolakan dengan akun pemilik | Tindakan ditolak; Admin HR lain tetap dapat memproses pengajuan | Feature - otorisasi |
| VAL-001 | Pengajuan tidak memiliki data wajib | Karyawan aktif dan saldo tersedia | Kirim formulir kosong | Pesan validasi muncul dan pengajuan tidak dikirim | Feature - validasi |
| VAL-002 | Tanggal yang sama dipilih dua kali dalam satu pengajuan | Draf milik Karyawan tersedia | Kirim daftar tanggal berulang | Pengiriman ditolak dengan pesan yang jelas | Feature - validasi |
| VAL-003 | Tanggal tumpang tindih dengan pengajuan aktif | Pengajuan aktif pada tanggal yang sama tersedia | Kirim pengajuan kedua | Pengiriman ditolak dan tidak ada rincian aktif duplikat | Feature - validasi/database |
| VAL-004 | Tanggal pernah dipakai pengajuan yang ditolak | Pengajuan `ditolak` pada tanggal yang sama tersedia | Kirim pengajuan baru | Tanggal dapat digunakan jika aturan lain terpenuhi | Feature - validasi |
| VAL-005 | Tanggal jatuh pada akhir pekan | Waktu dan kalender uji ditetapkan | Kirim pengajuan untuk Sabtu atau Minggu | Tanggal ditolak dan tidak dihitung sebagai hari cuti | Feature dengan data provider |
| VAL-006 | Tanggal tercatat sebagai hari libur aktif | Hari libur aktif tersedia | Kirim pengajuan pada tanggal tersebut | Tanggal ditolak dan saldo tidak berubah | Feature - validasi |
| VAL-007 | Jenis cuti tidak aktif dipilih melalui permintaan buatan | Jenis cuti tidak aktif tersedia | Kirim ID jenis cuti secara langsung | Pengajuan ditolak dengan pesan validasi | Feature - validasi |
| VAL-008 | Saldo tidak mencukupi | Saldo lebih kecil dari jumlah hari | Kirim pengajuan | Pengiriman ditolak dan saldo tetap | Feature - validasi |
| ALR-001 | Pengajuan valid dikirim kepada Atasan | Draf valid, saldo cukup, dan Atasan aktif | Kirim pengajuan | Status menjadi `menunggu_atasan`, waktu pengiriman dan rincian tersimpan | Feature - alur |
| NOM-001 | Nomor pengajuan dibuat oleh server | Beberapa pengajuan pada tahun yang sama dan berbeda tersedia | Kirim pengajuan baru dengan nomor buatan pada payload | Nomor payload diabaikan dan server membuat nomor unik berformat `CUTI-YYYY-NNNNNN` sesuai tahun | Feature - penomoran |
| ALR-002 | Atasan menyetujui pengajuan bawahan | Pengajuan bawahan `menunggu_atasan` | Kirim keputusan setuju | Keputusan Atasan tercatat dan status menjadi `menunggu_hr`; saldo belum berkurang | Feature - alur |
| ALR-003 | Atasan menolak tanpa alasan | Pengajuan bawahan `menunggu_atasan` | Kirim keputusan tolak tanpa catatan | Validasi gagal, status tetap, dan keputusan tidak tercatat | Feature - validasi/alur |
| ALR-004 | Atasan menolak dengan alasan | Pengajuan bawahan `menunggu_atasan` | Kirim keputusan tolak dan alasan | Status menjadi `ditolak`, keputusan tercatat, dan saldo tidak berubah | Feature - alur |
| ALR-005 | Admin HR memberi persetujuan akhir | Pengajuan `menunggu_hr` dan saldo cukup | Kirim keputusan setuju | Status menjadi `disetujui`, keputusan tercatat, dan saldo berkurang sesuai jumlah hari | Feature - transaksi |
| ALR-006 | Admin HR menolak pengajuan | Pengajuan `menunggu_hr` tersedia | Kirim keputusan tolak dan alasan | Status menjadi `ditolak`, keputusan tercatat, dan saldo tidak berubah | Feature - alur |
| SLD-001 | Saldo tepat sama dengan jumlah hari | Pengajuan `menunggu_hr` dan saldo pada batas | Setujui pengajuan | Persetujuan berhasil dan saldo menjadi nol, bukan negatif | Feature - batas saldo |
| SLD-002 | Saldo berubah menjadi tidak cukup sebelum keputusan akhir | Pengajuan `menunggu_hr`; saldo dikurangi oleh proses sah lain | Setujui pengajuan | Transaksi ditolak, status tetap `menunggu_hr`, dan tidak ada pengurangan tambahan | Feature - transaksi |
| SLD-003 | Permintaan persetujuan akhir dikirim dua kali | Pengajuan dapat disetujui | Kirim permintaan yang sama dua kali | Hanya satu keputusan tersimpan dan saldo hanya berkurang satu kali | Feature - idempotensi |
| SLD-004 | Dua persetujuan bersamaan menggunakan saldo yang sama | Dua pengajuan `menunggu_hr` dengan saldo hanya cukup untuk satu | Jalankan kedua proses secara terkendali | Maksimal satu proses berhasil dan saldo tidak negatif | Integration - konkurensi |
| SLD-005 | Admin HR menyesuaikan saldo dengan alasan | Saldo pegawai dan Admin HR tersedia | Simpan penyesuaian saldo yang valid | Saldo berubah sesuai input sah dan alasan perubahan dapat ditelusuri | Feature - pengelolaan saldo |
| SLD-006 | Saldo tahun lalu tidak otomatis dibawa | Saldo tahun sebelumnya tersedia | Terbitkan periode saldo tahun baru | Saldo baru mengikuti nilai yang ditetapkan Admin HR dan tidak menyalin sisa tahun lalu otomatis | Feature - periode saldo |
| BTL-001 | Karyawan membatalkan draf miliknya | Draf tersedia | Kirim pembatalan | Status menjadi `dibatalkan` dan saldo tidak berubah | Feature - pembatalan |
| BTL-002 | Karyawan membatalkan sebelum keputusan Atasan | Pengajuan `menunggu_atasan` tersedia | Kirim pembatalan | Status menjadi `dibatalkan` dan tidak dapat diproses Atasan | Feature - pembatalan |
| BTL-003 | Karyawan mencoba membatalkan setelah masuk tahap HR | Pengajuan `menunggu_hr` tersedia | Kirim pembatalan sebagai Karyawan | Tindakan ditolak dan status tidak berubah | Feature - otorisasi |
| BTL-004 | Admin HR membatalkan pengajuan yang disetujui | Pengajuan `disetujui` dan saldo sudah dikurangi | Kirim pembatalan dengan alasan | Status menjadi `dibatalkan`, jejak tercatat, dan saldo kembali tepat satu kali | Feature - transaksi |
| BTL-005 | Pembatalan pengajuan disetujui dikirim dua kali | Pengajuan telah dibatalkan dan saldo dipulihkan | Ulangi permintaan pembatalan | Permintaan kedua ditolak dan saldo tidak bertambah lagi | Feature - idempotensi |
| BTL-006 | Pengajuan disetujui dibatalkan setelah tanggal cuti dimulai | Pengajuan `disetujui` dengan tanggal pertama hari ini atau telah lewat | Kirim pembatalan sebagai Admin HR | Pembatalan ditolak, status tetap `disetujui`, dan saldo tidak berubah | Feature - batas waktu |
| LIB-001 | Admin HR membuat hari libur baru | Admin HR terautentikasi | Simpan tanggal dan nama valid | Hari libur tersimpan dan digunakan pada validasi berikutnya | Feature - pengelolaan |
| LIB-002 | Admin HR membuat tanggal hari libur duplikat | Tanggal hari libur sudah ada | Simpan tanggal yang sama | Validasi gagal dan hanya satu data tanggal tersimpan | Feature - validasi |
| LIB-003 | Pengguna non-HR mengubah hari libur | Hari libur dan akun non-HR tersedia | Kirim perubahan | Akses ditolak dan data tidak berubah | Feature - otorisasi |
| DAT-001 | Admin HR mengelola pengguna dan pegawai | Admin HR terautentikasi | Buat atau perbarui data yang valid | Data tersimpan, akun terhubung dengan satu pegawai, dan field terlarang tidak berubah | Feature - pengelolaan data |
| DAT-002 | Admin HR menetapkan hubungan Atasan | Data pegawai tersedia | Tetapkan Atasan valid lalu coba Atasan diri sendiri atau rantai melingkar | Hubungan valid tersimpan; hubungan diri sendiri atau melingkar ditolak | Feature - validasi data |
| JCT-001 | Admin HR mengelola jenis cuti | Admin HR dan jenis cuti tersedia | Buat, ubah, lalu nonaktifkan jenis cuti | Data tersimpan dan jenis tidak aktif tidak dapat dipilih pada pengajuan baru | Feature - pengelolaan data |
| SEC-001 | Alasan mengandung skrip berbahaya | Pengajuan dengan teks `<script>` dibuat | Buka halaman detail | Teks ditampilkan dalam bentuk aman dan skrip tidak dijalankan | Feature - keamanan tampilan |
| SEC-002 | Payload memuat `status`, `pegawai_id`, atau `saldo` yang tidak boleh dikendalikan | Karyawan aktif tersedia | Kirim field tambahan pada formulir | Field tidak dipercaya; kepemilikan, status, dan saldo tetap ditentukan server | Feature - mass assignment |
| SEC-003 | Filter rekap berisi kolom urut yang tidak diizinkan | Admin HR terautentikasi | Kirim nama kolom buatan atau input injeksi | Permintaan ditolak atau memakai nilai aman tanpa menjalankan query berbahaya | Feature - keamanan query |
| SEC-004 | Kata sandi akun disimpan dengan aman | Akun baru akan dibuat | Buat akun dengan kata sandi contoh | Database menyimpan hash yang dapat diverifikasi dan tidak menyimpan teks asli | Feature - keamanan autentikasi |
| SEC-005 | Lingkungan produksi menggunakan koneksi aman | Konfigurasi lingkungan produksi atau staging tersedia | Periksa HTTPS, pengalihan HTTP, dan atribut cookie sesi | Aplikasi dilayani melalui HTTPS dan cookie sesi memakai pengaturan aman yang disepakati | Pengujian keamanan konfigurasi |
| RKP-001 | Admin HR mengekspor rekap dengan filter | Data lintas periode dan departemen tersedia | Terapkan filter lalu ekspor | Berkas CSV hanya memuat data sesuai filter dan hak akses | Feature - rekap |
| RSP-001 | Formulir dan daftar digunakan pada layar ponsel | Aplikasi berjalan dan data contoh tersedia | Uji ukuran layar ponsel yang disepakati | Kontrol dapat digunakan, teks terbaca, dan tidak ada elemen utama terpotong | Manual responsif |
| RSP-002 | Tabel digunakan pada tablet dan desktop | Data cukup untuk tabel tersedia | Uji ukuran tablet dan desktop | Tabel atau pola penggantinya dapat dibaca dan dioperasikan | Manual responsif |
| PWA-001 | Aplikasi dipasang dari browser pendukung | Manifest, ikon, HTTPS atau localhost tersedia | Jalankan proses instalasi | Aplikasi terpasang dengan nama, ikon, dan mode tampilan yang benar | Manual PWA |
| PWA-002 | Aplikasi dibuka tanpa jaringan setelah kunjungan awal | Service worker aktif dan halaman offline telah dicache | Matikan jaringan lalu buka aplikasi | Halaman offline tampil dan tidak mengklaim data sebagai data terbaru | Manual/otomatis PWA |
| PWA-003 | Pengajuan dicoba saat offline | Karyawan masuk sebelum jaringan dimatikan | Matikan jaringan lalu kirim pengajuan | Tidak ada pesan sukses palsu; pengguna diminta kembali online | Manual PWA |
| PWA-004 | Cache diperbarui setelah versi aplikasi berubah | Cache versi lama tersedia | Pasang versi baru dan muat ulang | Aset baru digunakan dan cache lama yang tidak diperlukan dibersihkan | Manual PWA |
| PWA-005 | Pengguna keluar lalu akun lain memakai perangkat sama | Dua akun dan PWA terpasang tersedia | Buka data akun pertama, keluar, lalu masuk akun kedua | Cache tidak menampilkan data pribadi akun pertama kepada akun kedua | Manual keamanan PWA |
| PRF-001 | Halaman utama merespons pada beban capstone | Data uji dengan jumlah yang disepakati tersedia | Ukur halaman daftar dan dashboard pada lingkungan uji | Waktu respons memenuhi batas yang disepakati dan query bermasalah dicatat | Pengujian performa |
| CMP-001 | Alur utama berjalan pada browser sasaran | Daftar browser modern telah disepakati | Jalankan alur utama pada setiap browser | Tampilan dan fungsi wajib dapat digunakan tanpa kesalahan penghalang | Manual kompatibilitas |
| OPS-001 | Cadangan database dapat dipulihkan | Database uji dan prosedur cadangan tersedia | Buat cadangan lalu pulihkan ke lingkungan uji terpisah | Data penting kembali konsisten tanpa menyentuh lingkungan produksi | Pengujian operasional |
| REV-001 | Perubahan memenuhi standar pemeliharaan | Pull request fitur tersedia | Jalankan review kode, formatter, dan pengujian relevan | Konvensi proyek dipenuhi dan tidak ada temuan wajib tersisa | Review dan pemeriksaan otomatis |

## Cakupan Policy dan Endpoint

- Policy test memuat matriks lengkap peran, kepemilikan, hubungan Atasan-bawahan, dan status.
- Feature test membuktikan setiap endpoint benar-benar memanggil otorisasi, setidaknya untuk satu kasus penolakan yang bernilai tinggi.
- Akses silang ke data pengguna lain sebaiknya tidak mengungkap apakah data tersebut ada.
- Setiap aturan validasi diuji melalui perilaku dan pesan yang diterima pengguna, bukan dengan memeriksa isi deklarasi aturan.

## Struktur dan Penamaan Saat Implementasi

- Gunakan PHPUnit sesuai konfigurasi proyek.
- Nama file mengikuti kelas atau tindakan yang diuji dan berakhiran `Test.php`.
- Nama metode menyatakan hasil dan kondisinya, misalnya `test_atasan_tidak_dapat_menyetujui_pengajuan_sendiri`.
- Pisahkan susunan data, tindakan, dan pemeriksaan agar mudah dibaca.
- Gunakan data provider untuk nilai yang memakai susunan serta hasil sama, seperti variasi akhir pekan atau peran yang ditolak.
- Gunakan assertion Laravel yang sesuai untuk respons dan database setelah sintaksnya dikonfirmasi dari dokumentasi versi terpasang.

## Urutan Pelaksanaan

1. Pengujian autentikasi dan policy hak akses.
2. Pengujian validasi pembuatan serta pengiriman pengajuan.
3. Pengujian keputusan Atasan dan Admin HR.
4. Pengujian transaksi, batas saldo, idempotensi, dan pembatalan.
5. Pengujian pengelolaan jenis cuti, hari libur, pengguna, dan pegawai.
6. Pengujian rekap dan keamanan input/output.
7. Pengujian responsif dan PWA pada browser atau perangkat sasaran.
8. Pengujian regresi seluruh suite sebelum merge dan demonstrasi.

## Pencatatan Hasil

Setelah implementasi tersedia, tim mencatat tanggal pengujian, versi commit, lingkungan, hasil aktual, status lulus/gagal, dan bukti yang relevan. Kegagalan dibuat sebagai tugas perbaikan dan diuji ulang. Dokumen ini hanya mendefinisikan rencana, sehingga belum memuat hasil lulus.
