# Kebutuhan Sistem

Dokumen ini menjabarkan kebutuhan awal untuk proyek **Rancang Bangun Sistem Informasi Pengajuan dan Persetujuan Cuti Karyawan Berbasis Progressive Web App di PT Medika Antapani**. Seluruh kebutuhan masih berupa rancangan dan belum menunjukkan bahwa fitur telah diimplementasikan.

## Tujuan Sistem

Sistem dirancang untuk menyediakan proses pengajuan cuti yang terpusat, mudah ditelusuri, dan sesuai alur persetujuan PT Medika Antapani. Karyawan dapat mengajukan cuti, Atasan memberikan keputusan tahap pertama, dan Admin HR memberikan keputusan akhir. Saldo cuti diperbarui hanya setelah persetujuan akhir.

## Ruang Lingkup

- Autentikasi pengguna dan pembatasan akses berdasarkan peran.
- Pengelolaan data pengguna, pegawai, jenis cuti, saldo cuti, dan hari libur.
- Pengajuan cuti untuk satu atau beberapa tanggal.
- Validasi tanggal, pengajuan yang tumpang tindih, hari libur, akhir pekan, dan saldo.
- Persetujuan berjenjang oleh Atasan dan Admin HR.
- Penolakan dan pembatalan pengajuan dengan pencatatan alasan.
- Pembaruan saldo setelah persetujuan akhir.
- Riwayat pengajuan, riwayat keputusan, dan rekap pengajuan.
- Antarmuka responsif dan kemampuan dasar PWA.

## Kebutuhan Fungsional

### Karyawan

| Kode | Kebutuhan |
| --- | --- |
| KF-KAR-001 | Karyawan dapat masuk dan keluar dari aplikasi menggunakan akun yang aktif. |
| KF-KAR-002 | Karyawan dapat melihat dashboard yang memuat ringkasan saldo dan status pengajuan miliknya. |
| KF-KAR-003 | Karyawan dapat membuat dan menyimpan pengajuan sebagai `draf`. |
| KF-KAR-004 | Karyawan dapat memilih jenis cuti, satu atau beberapa tanggal, dan mengisi alasan. |
| KF-KAR-005 | Karyawan dapat mengirim draf sehingga status berubah menjadi `menunggu_atasan` jika seluruh validasi berhasil. |
| KF-KAR-006 | Karyawan dapat melihat detail, status, dan riwayat pengajuan miliknya. |
| KF-KAR-007 | Karyawan dapat mengubah draf miliknya sebelum dikirim. |
| KF-KAR-008 | Karyawan dapat membatalkan draf atau pengajuan yang belum diberi keputusan Atasan. |
| KF-KAR-009 | Karyawan menerima penjelasan validasi ketika tanggal atau saldo tidak memenuhi ketentuan. |

### Atasan

| Kode | Kebutuhan |
| --- | --- |
| KF-ATS-001 | Atasan dapat melihat pengajuan berstatus `menunggu_atasan` dari pegawai yang menjadi bawahannya. |
| KF-ATS-002 | Atasan dapat melihat detail pengajuan, tanggal cuti, dan informasi saldo yang relevan. |
| KF-ATS-003 | Atasan dapat menyetujui pengajuan sehingga status berubah menjadi `menunggu_hr`. |
| KF-ATS-004 | Atasan dapat menolak pengajuan dengan alasan sehingga status berubah menjadi `ditolak`. |
| KF-ATS-005 | Atasan dapat melihat riwayat keputusan yang pernah diberikannya. |
| KF-ATS-006 | Sistem mencegah Atasan memproses pengajuan miliknya sendiri. |
| KF-ATS-007 | Atasan hanya dapat memproses pengajuan yang masih menunggu keputusannya. |

### Admin HR

| Kode | Kebutuhan |
| --- | --- |
| KF-HR-001 | Admin HR dapat mengelola akun pengguna dan data pegawai. |
| KF-HR-002 | Admin HR dapat menetapkan hubungan Atasan dan bawahan. |
| KF-HR-003 | Admin HR dapat mengelola jenis cuti dan status aktifnya. |
| KF-HR-004 | Admin HR dapat mengelola saldo cuti pegawai per jenis cuti dan tahun. |
| KF-HR-005 | Admin HR dapat mengelola daftar hari libur. |
| KF-HR-006 | Admin HR dapat melihat pengajuan berstatus `menunggu_hr`. |
| KF-HR-007 | Admin HR dapat memberikan persetujuan akhir sehingga status berubah menjadi `disetujui`. |
| KF-HR-008 | Admin HR dapat menolak pengajuan dengan alasan sehingga status berubah menjadi `ditolak`. |
| KF-HR-009 | Sistem mengurangi saldo secara aman setelah persetujuan akhir dan tidak menguranginya ketika pengajuan ditolak. |
| KF-HR-010 | Admin HR dapat memproses pembatalan pengajuan yang sudah disetujui dan mengembalikan saldo sesuai aturan. |
| KF-HR-011 | Admin HR dapat melihat, menyaring, dan mengekspor rekap pengajuan. |
| KF-HR-012 | Admin HR dapat melihat jejak pemberi keputusan dan waktu keputusan. |

## Kebutuhan Nonfungsional

| Kode | Kebutuhan |
| --- | --- |
| KNF-001 | Aplikasi menggunakan HTTPS pada lingkungan produksi. |
| KNF-002 | Kata sandi disimpan dalam bentuk hash dan tidak pernah ditampilkan kembali. |
| KNF-003 | Setiap halaman dan tindakan dilindungi autentikasi serta otorisasi di sisi server. |
| KNF-004 | Antarmuka dapat digunakan pada layar ponsel, tablet, dan desktop. |
| KNF-005 | Operasi keputusan akhir dan perubahan saldo berjalan dalam transaksi database agar data tetap konsisten. |
| KNF-006 | Sistem menyimpan jejak keputusan beserta aktor dan waktunya. |
| KNF-007 | Pesan validasi disajikan dengan bahasa yang jelas dan tidak membuka informasi sensitif. |
| KNF-008 | Halaman utama yang umum digunakan ditargetkan merespons dalam waktu yang wajar pada jaringan normal dan data capstone. |
| KNF-009 | Kode mengikuti struktur Laravel, mudah diuji, dan dapat dipelihara oleh seluruh anggota tim. |
| KNF-010 | Aplikasi mendukung browser modern yang menyediakan fitur dasar PWA. |
| KNF-011 | Cache PWA tidak boleh menyebabkan transaksi atau data sensitif lama dianggap sebagai data terbaru. |
| KNF-012 | Data aplikasi dapat dicadangkan dan dipulihkan sesuai prosedur lingkungan penerapan yang disepakati tim. |

## Asumsi Sistem

Poin berikut masih merupakan **asumsi awal** dan perlu dikonfirmasi bersama pembimbing atau pihak PT Medika Antapani:

- Setiap pengguna terhubung dengan tepat satu data pegawai.
- Setiap pegawai memiliki paling banyak satu Atasan langsung.
- Admin HR menyiapkan saldo cuti untuk setiap jenis cuti dan tahun yang berlaku.
- Akhir pekan adalah Sabtu dan Minggu; ketentuan ini perlu dikonfirmasi apabila jadwal kerja berbeda.
- Satu hari cuti dihitung sebagai satu hari penuh; cuti setengah hari belum direncanakan.
- Atasan yang juga berstatus pegawai tetap dapat mengajukan cuti, tetapi tidak dapat menyetujui pengajuannya sendiri.
- Admin HR memiliki kewenangan memproses pembatalan pengajuan yang sudah disetujui.
- Ekspor rekap awal menggunakan format yang mudah dibuat, misalnya CSV; format akhir perlu disepakati.

## Batasan Sistem

- Sistem berdiri sendiri dan tidak bergantung pada sistem, API, atau backend eksternal.
- Pengajuan dan keputusan persetujuan memerlukan koneksi internet.
- Push notification dan background sync bukan fitur wajib tahap awal.
- Sistem tidak menghitung kehadiran, lembur, penggajian, atau potongan gaji.
- Sistem tidak mengatur pergantian jadwal kerja atau penunjukan pengganti selama cuti.
- Kebijakan hukum dan ketenagakerjaan tetap harus diterjemahkan menjadi aturan bisnis yang dikonfirmasi oleh tim; dokumen ini bukan dokumen kebijakan perusahaan.

## Prioritas Fitur

### Wajib

- Autentikasi dan otorisasi tiga aktor.
- Data pengguna, pegawai, Atasan, jenis cuti, saldo, dan hari libur.
- Pengajuan dan validasi cuti.
- Persetujuan Atasan dan keputusan akhir Admin HR.
- Pencegahan persetujuan pengajuan sendiri.
- Perubahan saldo yang konsisten setelah persetujuan akhir.
- Riwayat pengajuan dan keputusan.
- Pembatalan beserta aturan pengembalian saldo.
- Rekap dasar pengajuan.
- Tampilan responsif dan PWA dasar dengan halaman offline.
- Pengujian alur utama dan hak akses.

### Pengembangan Lanjutan

- Push notification.
- Background sync.
- Ekspor rekap dengan format tambahan seperti PDF atau XLSX.
- Persetujuan pengganti ketika Atasan tidak tersedia.
- Kalender cuti bersama dengan pengaturan privasi.
- Cuti setengah hari atau perhitungan berdasarkan jadwal kerja khusus.

### Di Luar Ruang Lingkup

- Integrasi sistem absensi, backend lain, atau API eksternal.
- Notifikasi melalui email, WhatsApp, atau layanan pihak ketiga.
- Penggajian dan data slip gaji.
- Penilaian kinerja pegawai.
- Rekrutmen dan modul sumber daya manusia lainnya.
- Transaksi pengajuan atau persetujuan secara offline.

## Kriteria Keberhasilan Sistem

- Karyawan dapat mengirim pengajuan valid dan melihat statusnya sampai keputusan akhir.
- Tanggal hari libur, akhir pekan, duplikat, atau tumpang tindih dapat ditolak sesuai aturan.
- Atasan hanya dapat memproses pengajuan bawahannya dan tidak dapat menyetujui pengajuannya sendiri.
- Admin HR hanya dapat memproses pengajuan yang telah disetujui Atasan.
- Saldo berkurang tepat satu kali setelah persetujuan akhir dan tidak pernah negatif.
- Penolakan tidak mengurangi saldo; pembatalan yang sah mengembalikan saldo tepat satu kali.
- Pengguna tidak dapat melihat atau mengubah data yang berada di luar kewenangannya.
- Riwayat keputusan menyimpan aktor, keputusan, catatan, dan waktu keputusan.
- Antarmuka dapat digunakan pada ukuran layar utama yang disepakati tim.
- Aplikasi dapat dipasang sebagai PWA pada browser pendukung, menampilkan halaman offline, dan tetap mewajibkan internet untuk transaksi.
- Skenario pengujian wajib telah dijalankan dan hasil aktualnya didokumentasikan setelah implementasi tersedia.
