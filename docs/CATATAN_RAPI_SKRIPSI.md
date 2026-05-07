# Catatan Perapihan untuk Skripsi

Dokumen ini berisi catatan teknis agar project lebih siap dipresentasikan sebagai aplikasi skripsi. Catatan dibagi menjadi bagian yang sudah baik, bagian yang perlu dijelaskan, dan rekomendasi perapihan lanjutan.

## Kekuatan Sistem

- Struktur folder sudah dipisahkan berdasarkan fungsi: `config`, `includes`, `pages`, `assets`, dan `qrcodes`.
- Koneksi database menggunakan PDO.
- Password pengguna sudah menggunakan hash dan diverifikasi dengan `password_verify`.
- Sistem memiliki pembagian role: admin, petugas, dan guru.
- Modul utama inventaris sudah lengkap: aset, kategori, lokasi, peminjaman, laporan, QR Code, dan riwayat aktivitas.
- Aset menggunakan soft delete melalui kolom `deleted_at`.
- Ada pencatatan aktivitas pengguna pada tabel `riwayat_aktivitas`.
- Laporan mendukung export PDF dan CSV.

## Hal yang Perlu Ditekankan di Skripsi

Sistem ini dapat dijelaskan sebagai aplikasi inventaris aset berbasis web yang bertujuan untuk:

- Mengurangi pencatatan aset secara manual.
- Mempermudah pencarian data aset.
- Memantau kondisi dan lokasi aset.
- Mendukung proses peminjaman aset oleh guru atau karyawan.
- Menyediakan laporan inventaris yang dapat dicetak.
- Memanfaatkan QR Code untuk identifikasi aset.

## Catatan Teknis

### 1. Pola Arsitektur

Aplikasi menggunakan PHP native procedural, bukan framework. Pola ini masih layak untuk skripsi selama dijelaskan bahwa pemisahan modul dilakukan berdasarkan folder dan file fitur.

Contoh pembagian:

- Konfigurasi: `config/database.php`
- Autentikasi: `includes/auth_check.php`
- Tampilan bersama: `includes/header.php`, `includes/sidebar.php`, `includes/footer.php`
- Modul fitur: `pages/aset`, `pages/peminjaman`, dan seterusnya.

### 2. Hak Akses

Sistem sudah memiliki fungsi role guard:

- `requireAdmin()`
- `requireGuru()`
- `requireStaff()`

Untuk perapihan lanjutan, pastikan semua halaman admin/petugas memanggil guard yang sesuai. Ini penting agar akses langsung melalui URL tetap aman.

### 3. Keamanan Form

Beberapa aksi seperti hapus data, pengembalian, generate QR, dan logout masih dipicu melalui parameter GET. Untuk standar keamanan yang lebih baik, aksi perubahan data sebaiknya memakai method POST dan dilengkapi CSRF token.

### 4. Upload File

Upload gambar aset saat ini memeriksa ekstensi file. Untuk validasi lebih kuat, sebaiknya ditambah pemeriksaan MIME dan isi gambar menggunakan `finfo` atau `getimagesize`.

### 5. Peminjaman Aset

Sistem sudah dapat mencatat peminjaman dan pengembalian. Untuk versi lanjutan, sistem dapat menambahkan validasi ketersediaan aset agar jumlah peminjaman aktif tidak melebihi jumlah aset yang tersedia.

### 6. Dokumentasi

Dokumentasi yang disarankan untuk lampiran skripsi:

- Struktur project.
- Alur login.
- Alur pengelolaan aset.
- Alur peminjaman.
- Alur laporan.
- Struktur database.
- Daftar tabel dan relasi.
- Screenshot setiap modul.

## Saran Penamaan Bab Implementasi

Bagian implementasi dapat disusun seperti berikut:

1. Implementasi Database
2. Implementasi Login dan Hak Akses
3. Implementasi Dashboard
4. Implementasi Pengelolaan Data Aset
5. Implementasi Peminjaman Aset
6. Implementasi QR Code
7. Implementasi Laporan
8. Pengujian Sistem

## Skenario Pengujian yang Disarankan

| No | Fitur | Skenario | Hasil yang Diharapkan |
|---|---|---|---|
| 1 | Login | Username dan password benar | Pengguna masuk ke dashboard sesuai role |
| 2 | Login | Password salah | Sistem menampilkan pesan kesalahan |
| 3 | Aset | Tambah data aset valid | Data tersimpan dan muncul di daftar aset |
| 4 | Aset | Edit lokasi aset | Data aset berubah dan mutasi tercatat |
| 5 | Aset | Hapus aset | Aset tidak tampil pada daftar aktif |
| 6 | QR Code | Generate QR aset | File QR dibuat dan dapat dicetak |
| 7 | Scan QR | Scan QR aset valid | Sistem menampilkan data aset |
| 8 | Peminjaman | Guru mengajukan peminjaman | Data masuk ke riwayat peminjaman |
| 9 | Pengembalian | Petugas mengembalikan aset | Status berubah menjadi Dikembalikan |
| 10 | Laporan | Export PDF/CSV | File laporan berhasil diunduh |

## Prioritas Perapihan Lanjutan

1. Konsistenkan hak akses di semua halaman.
2. Ubah aksi perubahan data dari GET menjadi POST.
3. Tambahkan CSRF token pada form penting.
4. Tambahkan validasi upload yang lebih kuat.
5. Tambahkan validasi stok pada peminjaman.
6. Tambahkan halaman dokumentasi atau panduan pengguna.
7. Rapikan encoding karakter agar tanda baca tampil normal.

