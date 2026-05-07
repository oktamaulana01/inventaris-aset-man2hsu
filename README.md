# Sistem Informasi Inventarisasi Aset MAN 2 HSU

Aplikasi ini adalah sistem informasi berbasis web untuk mengelola data inventaris aset pada MAN 2 Hulu Sungai Utara. Sistem dibangun menggunakan PHP native, MySQL, HTML, CSS, JavaScript, serta beberapa pustaka Composer untuk pembuatan QR Code dan ekspor PDF.

## Tujuan Sistem

Sistem ini membantu petugas sekolah dalam mencatat aset, mengelompokkan aset berdasarkan kategori dan lokasi, memantau kondisi aset, mencatat peminjaman, membuat QR Code aset, serta menghasilkan laporan inventaris.

## Teknologi

- PHP native
- MySQL / MariaDB
- PDO untuk koneksi database
- HTML, CSS, JavaScript
- chillerlan/php-qrcode untuk generate QR Code
- dompdf/dompdf untuk export laporan PDF

## Struktur Folder

```text
inventaris-aset-man2hsu/
|-- assets/
|   |-- css/
|   |-- js/
|   `-- uploads/
|-- config/
|   `-- database.php
|-- includes/
|   |-- auth_check.php
|   |-- header.php
|   |-- sidebar.php
|   |-- footer.php
|   `-- kop_surat.php
|-- pages/
|   |-- aset/
|   |-- guru/
|   |-- kategori/
|   |-- laporan/
|   |-- lokasi/
|   |-- peminjaman/
|   `-- pengguna/
|-- qrcodes/
|-- vendor/
|-- composer.json
|-- db_inventaris_man2hsu.sql
|-- index.php
`-- login.php
```

## Modul Utama

- Login dan manajemen sesi pengguna.
- Dashboard admin/petugas untuk ringkasan aset, peminjaman, dan aktivitas.
- Dashboard guru untuk melihat aset tersedia dan riwayat peminjaman.
- Master data aset, kategori, lokasi, dan pengguna.
- Peminjaman dan pengembalian aset.
- QR Code aset dan scan QR.
- Laporan inventaris, peminjaman, aset masuk, mutasi, dan penghapusan.
- Export laporan ke PDF dan CSV.

## Role Pengguna

- Admin: mengelola seluruh data, termasuk pengguna dan riwayat aktivitas.
- Petugas: mengelola aset, kategori, lokasi, peminjaman, laporan, dan QR Code.
- Guru: melihat katalog aset dan mengajukan peminjaman aset.

## Instalasi Lokal

1. Salin folder proyek ke direktori web server, misalnya:

   ```text
   C:\laragon\www\inventaris-aset-man2hsu
   ```

2. Buat database MySQL dengan nama:

   ```text
   db_inventaris_man2hsu
   ```

3. Import file database:

   ```text
   db_inventaris_man2hsu.sql
   ```

4. Pastikan konfigurasi database sesuai di:

   ```text
   config/database.php
   ```

5. Jalankan Composer jika folder vendor belum tersedia:

   ```bash
   composer install
   ```

6. Buka aplikasi melalui browser:

   ```text
   http://localhost/inventaris-aset-man2hsu/login.php
   ```

## Dokumentasi Skripsi

Penjelasan struktur dan alur sistem tersedia di:

```text
docs/STRUKTUR_DAN_ALUR.md
docs/CATATAN_RAPI_SKRIPSI.md
```

