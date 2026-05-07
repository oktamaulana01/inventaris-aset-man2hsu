# Struktur dan Alur Sistem

Dokumen ini menjelaskan struktur project dan alur kerja Sistem Informasi Inventarisasi Aset MAN 2 HSU. Isi dokumen dapat digunakan sebagai bahan penjelasan pada bab analisis, perancangan, implementasi, atau lampiran skripsi.

## Gambaran Umum

Sistem ini merupakan aplikasi web inventaris aset sekolah. Aplikasi digunakan oleh tiga jenis pengguna, yaitu admin, petugas, dan guru. Admin dan petugas mengelola data inventaris, sedangkan guru dapat melihat katalog aset dan mengajukan peminjaman.

Arsitektur aplikasi bersifat PHP native procedural. Setiap fitur ditempatkan dalam file PHP berdasarkan modul. Koneksi database dan helper umum diletakkan pada folder `config`, sedangkan komponen tampilan bersama seperti header, sidebar, dan footer diletakkan pada folder `includes`.

## Struktur Project

```text
assets/
```

Berisi file pendukung tampilan aplikasi.

- `assets/css/style.css`: stylesheet utama aplikasi.
- `assets/js/script.js`: fungsi JavaScript umum seperti sidebar, konfirmasi hapus, preview gambar, dan cetak QR.
- `assets/uploads/`: menyimpan gambar aset, logo, dan foto pengguna.

```text
config/
```

Berisi konfigurasi sistem.

- `config/database.php`: konfigurasi koneksi PDO, helper session, flash message, format rupiah, log aktivitas, dan generate kode aset.

```text
includes/
```

Berisi komponen yang digunakan berulang.

- `includes/auth_check.php`: pengecekan login dan role pengguna.
- `includes/header.php`: bagian awal HTML, topbar, dan proses logout.
- `includes/sidebar.php`: menu navigasi sesuai role.
- `includes/footer.php`: bagian akhir layout dan pemanggilan JavaScript.
- `includes/kop_surat.php`: kop surat untuk laporan.

```text
pages/
```

Berisi halaman fitur utama aplikasi.

- `pages/aset/`: CRUD aset, detail aset, hapus aset, dan generate QR Code.
- `pages/kategori/`: CRUD kategori aset.
- `pages/lokasi/`: CRUD lokasi atau ruangan.
- `pages/peminjaman/`: pencatatan peminjaman, pengembalian, dan hapus peminjaman.
- `pages/pengguna/`: manajemen pengguna.
- `pages/guru/`: dashboard guru, katalog aset, pengajuan pinjam, profil, dan riwayat.
- `pages/laporan/`: laporan dan export PDF/CSV.
- `pages/dashboard.php`: dashboard admin/petugas.
- `pages/scan_qr.php`: scan QR Code dan pencarian manual aset.
- `pages/profil.php`: profil admin/petugas.
- `pages/riwayat.php`: riwayat aktivitas.

```text
qrcodes/
```

Berisi file QR Code aset yang dihasilkan oleh sistem.

```text
vendor/
```

Berisi pustaka Composer.

## Alur Login dan Hak Akses

1. Pengguna membuka `login.php`.
2. Sistem menerima input username dan password.
3. Sistem mencari username pada tabel `users`.
4. Password diverifikasi menggunakan `password_verify`.
5. Jika valid, data pengguna disimpan pada `$_SESSION`.
6. Sistem mengarahkan pengguna berdasarkan role:
   - Role `guru` diarahkan ke `pages/guru/dashboard.php`.
   - Role `admin` dan `petugas` diarahkan ke `pages/dashboard.php`.
7. Setiap halaman yang membutuhkan login memanggil `includes/auth_check.php`.
8. Fungsi role guard yang tersedia:
   - `requireAdmin()` untuk halaman khusus admin.
   - `requireGuru()` untuk halaman khusus guru.
   - `requireStaff()` untuk membatasi akses guru ke halaman staff.

## Alur Pengelolaan Aset

1. Admin atau petugas membuka modul Data Aset.
2. Sistem menampilkan daftar aset dari tabel `aset` dengan data kategori dan lokasi.
3. Pengguna dapat menambah aset baru.
4. Sistem membuat kode aset otomatis melalui fungsi `generateKodeAset`.
5. Data aset disimpan ke tabel `aset`.
6. Jika pengguna mengedit lokasi aset, sistem mencatat mutasi ke tabel `mutasi_aset`.
7. Jika aset dihapus, sistem melakukan soft delete dengan mengisi kolom `deleted_at`.
8. Aktivitas tambah, edit, hapus, dan generate QR dicatat pada tabel `riwayat_aktivitas`.

## Alur QR Code

1. Pengguna membuka halaman generate QR pada modul aset.
2. Sistem mengambil data aset berdasarkan ID.
3. Data aset dikemas ke dalam format JSON.
4. Library `chillerlan/php-qrcode` membuat file QR dalam format PNG.
5. File disimpan pada folder `qrcodes`.
6. Nama file QR disimpan pada kolom `qr_code_path` di tabel `aset`.
7. QR Code dapat dicetak atau discan melalui halaman `pages/scan_qr.php`.

## Alur Peminjaman Aset

1. Guru membuka katalog aset atau halaman pengajuan peminjaman.
2. Guru memilih aset yang kondisinya baik.
3. Guru mengisi tanggal pinjam, tanggal rencana kembali, dan keterangan.
4. Sistem menyimpan data ke tabel `peminjaman`.
5. Status awal peminjaman adalah `Dipinjam`.
6. Petugas dapat menandai peminjaman sebagai dikembalikan.
7. Sistem mengisi `tanggal_kembali_aktual` dan mengubah status menjadi `Dikembalikan`.
8. Riwayat peminjaman dapat dilihat oleh guru dan petugas.

## Alur Laporan

1. Pengguna memilih jenis laporan.
2. Sistem mengambil data dari database sesuai filter.
3. Laporan dapat ditampilkan sebagai halaman web.
4. Laporan dapat diekspor ke:
   - PDF menggunakan `dompdf/dompdf`.
   - CSV menggunakan output `text/csv`.

Jenis laporan yang tersedia:

- Inventaris aset keseluruhan.
- Aset per kategori.
- Aset per lokasi.
- Kondisi aset.
- Peminjaman aset.
- Aset masuk.
- Mutasi aset.
- Penghapusan aset.

## Struktur Database

Tabel utama sistem:

- `users`: menyimpan akun pengguna dan role.
- `aset`: menyimpan data aset inventaris.
- `kategori`: menyimpan kategori aset.
- `lokasi`: menyimpan lokasi atau ruangan aset.
- `peminjaman`: menyimpan transaksi peminjaman aset.
- `mutasi_aset`: menyimpan riwayat perpindahan lokasi aset.
- `riwayat_aktivitas`: menyimpan log aktivitas pengguna.

## Relasi Data

- Satu kategori dapat memiliki banyak aset.
- Satu lokasi dapat memiliki banyak aset.
- Satu aset dapat memiliki banyak riwayat peminjaman.
- Satu aset dapat memiliki banyak riwayat mutasi.
- Satu pengguna dapat membuat banyak aktivitas.
- Guru sebagai pengguna dapat memiliki banyak peminjaman.

## Ringkasan Alur Sistem

```text
Login
  |
  |-- Admin/Petugas
  |     |-- Dashboard
  |     |-- Kelola Aset
  |     |-- Kelola Kategori
  |     |-- Kelola Lokasi
  |     |-- Kelola Peminjaman
  |     |-- Generate/Scan QR
  |     |-- Cetak Laporan
  |     `-- Riwayat Aktivitas
  |
  `-- Guru
        |-- Dashboard Guru
        |-- Lihat Katalog Aset
        |-- Ajukan Peminjaman
        |-- Lihat Riwayat Peminjaman
        `-- Kelola Profil
```

