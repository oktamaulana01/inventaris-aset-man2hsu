-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 24, 2026 at 11:51 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_inventaris_man2hsu`
--

-- --------------------------------------------------------

--
-- Table structure for table `aset`
--

CREATE TABLE `aset` (
  `id` int NOT NULL,
  `kode_aset` varchar(50) NOT NULL,
  `nama_aset` varchar(150) NOT NULL,
  `id_kategori` int DEFAULT NULL,
  `id_lokasi` int DEFAULT NULL,
  `jumlah` int NOT NULL DEFAULT '1',
  `kondisi` enum('Baik','Rusak Ringan','Rusak Berat') NOT NULL DEFAULT 'Baik',
  `tahun_perolehan` year DEFAULT NULL,
  `nilai_perolehan` decimal(15,2) DEFAULT '0.00',
  `sumber_dana` varchar(100) DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `keterangan` text,
  `qr_code_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `aset`
--

INSERT INTO `aset` (`id`, `kode_aset`, `nama_aset`, `id_kategori`, `id_lokasi`, `jumlah`, `kondisi`, `tahun_perolehan`, `nilai_perolehan`, `sumber_dana`, `gambar`, `keterangan`, `qr_code_path`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'AST-2024-001', 'Meja Guru', 1, 2, 15, 'Baik', '2024', 750000.00, 'Dana BOS', NULL, 'Meja guru kayu jati', 'qr_AST-2024-001.png', '2026-03-12 12:51:46', '2026-03-12 13:09:58', NULL),
(2, 'AST-2024-002', 'Kursi Guru', 1, 2, 15, 'Baik', '2024', 500000.00, 'Dana BOS', NULL, 'Kursi guru sandaran', 'qr_AST-2024-002.png', '2026-03-12 12:51:46', '2026-03-13 15:11:19', NULL),
(3, 'AST-2024-003', 'Komputer Desktop', 2, 11, 20, 'Baik', '2024', 8000000.00, 'Dana BOS', NULL, 'PC Desktop Core i5', NULL, '2026-03-12 12:51:46', '2026-03-12 12:51:46', NULL),
(4, 'AST-2023-004', 'Proyektor LCD', 2, 14, 3, 'Baik', '2023', 5000000.00, 'Dana BOS', NULL, 'Proyektor Epson', NULL, '2026-03-12 12:51:46', '2026-03-12 12:51:46', NULL),
(5, 'AST-2023-005', 'Meja Siswa', 1, 4, 30, 'Rusak Ringan', '2023', 350000.00, 'Dana BOS', NULL, 'Meja siswa kayu', NULL, '2026-03-12 12:51:46', '2026-03-12 12:51:46', NULL),
(6, 'AST-2022-006', 'Printer LaserJet', 2, 3, 2, 'Baik', '2022', 3500000.00, 'APBD', NULL, 'Printer HP LaserJet', NULL, '2026-03-12 12:51:46', '2026-03-12 12:51:46', NULL),
(7, 'AST-2022-007', 'Lemari Arsip', 1, 3, 5, 'Baik', '2022', 2000000.00, 'APBD', NULL, 'Lemari besi 4 pintu', NULL, '2026-03-12 12:51:46', '2026-03-12 12:51:46', NULL),
(8, 'AST-2021-008', 'Mikroskop', 3, 12, 10, 'Rusak Ringan', '2021', 1500000.00, 'Dana BOS', NULL, 'Mikroskop binokuler', NULL, '2026-03-12 12:51:46', '2026-03-12 12:51:46', NULL),
(9, 'AST-2024-009', 'Bola Basket', 5, 15, 5, 'Baik', '2024', 250000.00, 'Dana BOS', NULL, 'Bola basket Molten', NULL, '2026-03-12 12:51:46', '2026-03-12 12:51:46', NULL),
(10, 'AST-2023-010', 'AC Split', 2, 1, 2, 'Baik', '2023', 4500000.00, 'APBD', NULL, 'AC 1.5 PK Daikin', NULL, '2026-03-12 12:51:46', '2026-03-12 12:51:46', NULL),
(11, 'AST-2026-011', 'Bola Futsal', 5, 15, 5, 'Baik', '2026', 300000.00, 'APBD', 'aset_1773414927.png', 'alat olahraga', NULL, '2026-03-13 15:15:27', '2026-03-13 15:15:27', NULL),
(12, 'AST-2026-012', 'Bola Voli', 5, 15, 2, 'Baik', '2026', 650000000000.00, 'APBD', 'aset_1773415043.png', '', 'qr_AST-2026-012.png', '2026-03-13 15:17:23', '2026-03-14 11:22:15', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id` int NOT NULL,
  `nama_kategori` varchar(100) NOT NULL,
  `keterangan` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id`, `nama_kategori`, `keterangan`, `created_at`) VALUES
(1, 'Mebeler', 'Perabot/furniture seperti meja, kursi, lemari', '2026-03-12 12:51:46'),
(2, 'Elektronik', 'Perangkat elektronik seperti komputer, printer, proyektor', '2026-03-12 12:51:46'),
(3, 'Alat Laboratorium', 'Peralatan laboratorium IPA, bahasa, komputer', '2026-03-12 12:51:46'),
(4, 'Buku & Pustaka', 'Koleksi buku perpustakaan dan bahan ajar', '2026-03-12 12:51:46'),
(5, 'Alat Olahraga', 'Peralatan olahraga dan kegiatan ekstrakurikuler', '2026-03-12 12:51:46'),
(6, 'Kendaraan', 'Kendaraan dinas dan operasional', '2026-03-12 12:51:46'),
(7, 'Alat Kantor', 'Peralatan kantor seperti ATK, mesin fotokopi', '2026-03-12 12:51:46'),
(8, 'Bangunan', 'Gedung dan komponen bangunan', '2026-03-12 12:51:46');

-- --------------------------------------------------------

--
-- Table structure for table `lokasi`
--

CREATE TABLE `lokasi` (
  `id` int NOT NULL,
  `nama_lokasi` varchar(100) NOT NULL,
  `keterangan` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `lokasi`
--

INSERT INTO `lokasi` (`id`, `nama_lokasi`, `keterangan`, `created_at`) VALUES
(1, 'Ruang Kepala Madrasah', 'Ruang kerja kepala sekolah', '2026-03-12 12:51:46'),
(2, 'Ruang Guru', 'Ruang kerja guru', '2026-03-12 12:51:46'),
(3, 'Ruang TU', 'Ruang tata usaha', '2026-03-12 12:51:46'),
(4, 'Ruang Kelas X-A', 'Kelas X jurusan A', '2026-03-12 12:51:46'),
(5, 'Ruang Kelas X-B', 'Kelas X jurusan B', '2026-03-12 12:51:46'),
(6, 'Ruang Kelas XI-A', 'Kelas XI jurusan A', '2026-03-12 12:51:46'),
(7, 'Ruang Kelas XI-B', 'Kelas XI jurusan B', '2026-03-12 12:51:46'),
(8, 'Ruang Kelas XII-A', 'Kelas XII jurusan A', '2026-03-12 12:51:46'),
(9, 'Ruang Kelas XII-B', 'Kelas XII jurusan B', '2026-03-12 12:51:46'),
(10, 'Perpustakaan', 'Ruang perpustakaan', '2026-03-12 12:51:46'),
(11, 'Lab Komputer', 'Laboratorium komputer', '2026-03-12 12:51:46'),
(12, 'Lab IPA', 'Laboratorium ilmu pengetahuan alam', '2026-03-12 12:51:46'),
(13, 'Mushalla', 'Tempat ibadah', '2026-03-12 12:51:46'),
(14, 'Aula', 'Ruang serba guna', '2026-03-12 12:51:46'),
(15, 'Gudang', 'Tempat penyimpanan barang', '2026-03-12 12:51:46');

-- --------------------------------------------------------

--
-- Table structure for table `mutasi_aset`
--

CREATE TABLE `mutasi_aset` (
  `id` int NOT NULL,
  `id_aset` int NOT NULL,
  `id_lokasi_asal` int DEFAULT NULL,
  `id_lokasi_tujuan` int DEFAULT NULL,
  `tanggal_mutasi` date NOT NULL,
  `keterangan` text,
  `id_user` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `mutasi_aset`
--

INSERT INTO `mutasi_aset` (`id`, `id_aset`, `id_lokasi_asal`, `id_lokasi_tujuan`, `tanggal_mutasi`, `keterangan`, `id_user`, `created_at`) VALUES
(1, 12, 15, 9, '2026-03-13', 'Perpindahan via edit aset', 2, '2026-03-13 15:34:38'),
(2, 12, 9, 15, '2026-03-13', 'Perpindahan via edit aset', 2, '2026-03-13 15:35:02');

-- --------------------------------------------------------

--
-- Table structure for table `peminjaman`
--

CREATE TABLE `peminjaman` (
  `id` int NOT NULL,
  `id_aset` int NOT NULL,
  `nama_peminjam` varchar(100) NOT NULL,
  `tanggal_pinjam` date NOT NULL,
  `tanggal_kembali_rencana` date NOT NULL,
  `tanggal_kembali_aktual` date DEFAULT NULL,
  `status` enum('Dipinjam','Dikembalikan') NOT NULL DEFAULT 'Dipinjam',
  `keterangan` text,
  `id_user` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `peminjaman`
--

INSERT INTO `peminjaman` (`id`, `id_aset`, `nama_peminjam`, `tanggal_pinjam`, `tanggal_kembali_rencana`, `tanggal_kembali_aktual`, `status`, `keterangan`, `id_user`, `created_at`) VALUES
(1, 9, 'Kurniawa', '2026-03-13', '2026-03-20', '2026-03-13', 'Dikembalikan', 'Latihan murid XB', 1, '2026-03-13 15:18:53');

-- --------------------------------------------------------

--
-- Table structure for table `riwayat_aktivitas`
--

CREATE TABLE `riwayat_aktivitas` (
  `id` int NOT NULL,
  `id_user` int DEFAULT NULL,
  `aktivitas` varchar(100) NOT NULL,
  `keterangan` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `riwayat_aktivitas`
--

INSERT INTO `riwayat_aktivitas` (`id`, `id_user`, `aktivitas`, `keterangan`, `created_at`) VALUES
(1, 1, 'Login', 'Admin melakukan login', '2026-03-12 12:51:46'),
(2, 1, 'Tambah Aset', 'Menambah aset: Meja Guru', '2026-03-12 12:51:46'),
(3, 1, 'Tambah Aset', 'Menambah aset: Komputer Desktop', '2026-03-12 12:51:46'),
(4, 1, 'Login', 'Administrator berhasil login', '2026-03-12 13:08:34'),
(5, 1, 'Logout', 'Administrator melakukan logout', '2026-03-12 13:09:32'),
(6, 2, 'Login', 'Petugas Inventaris berhasil login', '2026-03-12 13:09:38'),
(7, 2, 'Generate QR', 'Generate QR Code untuk: Meja Guru (AST-2024-001)', '2026-03-12 13:09:58'),
(8, 2, 'Generate QR', 'Generate QR Code untuk: Meja Guru (AST-2024-001)', '2026-03-12 13:13:41'),
(9, 2, 'Logout', 'Petugas Inventaris melakukan logout', '2026-03-12 13:31:41'),
(10, 1, 'Login', 'Administrator berhasil login', '2026-03-12 13:33:11'),
(11, 1, 'Logout', 'Administrator melakukan logout', '2026-03-12 13:37:01'),
(12, 1, 'Login', 'Administrator berhasil login', '2026-03-12 13:43:17'),
(13, 1, 'Logout', 'Administrator melakukan logout', '2026-03-12 13:43:25'),
(14, 1, 'Login', 'Administrator berhasil login', '2026-03-12 13:45:30'),
(15, 1, 'Logout', 'Administrator melakukan logout', '2026-03-12 13:46:29'),
(16, 1, 'Login', 'Administrator berhasil login', '2026-03-12 13:46:46'),
(17, 1, 'Logout', 'Administrator melakukan logout', '2026-03-12 13:56:51'),
(18, 1, 'Login', 'Administrator berhasil login', '2026-03-12 13:57:09'),
(19, 1, 'Logout', 'Administrator melakukan logout', '2026-03-12 14:30:47'),
(20, 1, 'Login', 'Administrator berhasil login', '2026-03-12 14:30:57'),
(21, 1, 'Logout', 'Administrator melakukan logout', '2026-03-12 14:34:19'),
(22, 1, 'Login', 'Administrator berhasil login', '2026-03-12 14:35:39'),
(23, 1, 'Logout', 'Administrator melakukan logout', '2026-03-12 15:02:31'),
(24, 1, 'Login', 'Administrator berhasil login', '2026-03-12 15:02:39'),
(25, 1, 'Logout', 'Administrator melakukan logout', '2026-03-12 15:03:32'),
(26, 1, 'Login', 'Administrator berhasil login', '2026-03-12 15:04:22'),
(27, 1, 'Logout', 'Administrator melakukan logout', '2026-03-12 15:17:25'),
(28, 1, 'Login', 'Administrator berhasil login', '2026-03-12 15:17:37'),
(29, 1, 'Login', 'Administrator berhasil login', '2026-03-12 16:06:41'),
(30, 1, 'Login', 'Administrator berhasil login', '2026-03-12 16:29:27'),
(31, 1, 'Login', 'Administrator berhasil login', '2026-03-12 16:53:24'),
(32, 1, 'Login', 'Administrator berhasil login', '2026-03-12 16:54:32'),
(33, 1, 'Login', 'Administrator berhasil login', '2026-03-12 17:36:19'),
(34, 1, 'Logout', 'Administrator melakukan logout', '2026-03-12 17:36:23'),
(35, 1, 'Login', 'Administrator berhasil login', '2026-03-12 17:38:22'),
(36, 1, 'Login', 'Administrator berhasil login', '2026-03-13 13:22:22'),
(37, 1, 'Login', 'Administrator berhasil login', '2026-03-13 14:26:14'),
(38, 1, 'Generate QR', 'Generate QR Code untuk: Kursi Guru (AST-2024-002)', '2026-03-13 15:11:19'),
(39, 1, 'Tambah Aset', 'Menambah aset: Bola Futsal (AST-2026-011)', '2026-03-13 15:15:27'),
(40, 1, 'Tambah Aset', 'Menambah aset: Bola Voli (AST-2026-012)', '2026-03-13 15:17:23'),
(41, 1, 'Peminjaman', 'Peminjaman aset: Bola Basket oleh Kurniawa', '2026-03-13 15:18:53'),
(42, 1, 'Pengembalian', 'Pengembalian aset: Bola Basket oleh Kurniawa', '2026-03-13 15:20:03'),
(43, 1, 'Logout', 'Administrator melakukan logout', '2026-03-13 15:21:35'),
(44, 2, 'Login', 'Petugas Inventaris berhasil login', '2026-03-13 15:21:41'),
(45, 2, 'Edit Aset', 'Mengedit aset: Bola Voli (AST-2026-012)', '2026-03-13 15:34:18'),
(46, 2, 'Edit Aset', 'Mengedit aset: Bola Voli (AST-2026-012)', '2026-03-13 15:34:38'),
(47, 2, 'Edit Aset', 'Mengedit aset: Bola Voli (AST-2026-012)', '2026-03-13 15:35:02'),
(48, 1, 'Login', 'Administrator berhasil login', '2026-03-14 11:21:53'),
(49, 1, 'Generate QR', 'Generate QR Code untuk: Bola Voli (AST-2026-012)', '2026-03-14 11:22:15'),
(50, 1, 'Login', 'Administrator berhasil login', '2026-03-21 07:33:28'),
(51, 1, 'Login', 'Administrator berhasil login', '2026-03-22 03:21:25'),
(52, 1, 'Login', 'Administrator berhasil login', '2026-03-24 01:57:06'),
(53, 1, 'Generate QR', 'Generate QR Code untuk: Bola Voli (AST-2026-012)', '2026-03-24 03:00:12'),
(54, 1, 'Generate QR', 'Generate QR Code untuk: Bola Voli (AST-2026-012)', '2026-03-24 03:02:59'),
(55, 1, 'Generate QR', 'Generate QR Code untuk: Bola Voli (AST-2026-012)', '2026-03-24 03:06:43'),
(56, 1, 'Login', 'Administrator berhasil login', '2026-03-24 03:32:08'),
(57, 1, 'Edit Profil', 'Administrator memperbarui profil', '2026-03-24 03:35:04');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','petugas') NOT NULL DEFAULT 'petugas',
  `foto` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `username`, `password`, `role`, `foto`, `created_at`) VALUES
(1, 'Administrator', 'admin', '$2y$10$X4bWmP8SZIhpJXIctRA9lu9zNoTFm4rff4By.3ZmH4uXNzIIv2d8y', 'admin', 'user_1_1774323304.jpg', '2026-03-12 12:51:46'),
(2, 'Petugas Inventaris', 'petugas', '$2y$10$X4bWmP8SZIhpJXIctRA9lu9zNoTFm4rff4By.3ZmH4uXNzIIv2d8y', 'petugas', NULL, '2026-03-12 12:51:46');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `aset`
--
ALTER TABLE `aset`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_aset` (`kode_aset`),
  ADD KEY `id_kategori` (`id_kategori`),
  ADD KEY `id_lokasi` (`id_lokasi`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lokasi`
--
ALTER TABLE `lokasi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mutasi_aset`
--
ALTER TABLE `mutasi_aset`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_aset` (`id_aset`),
  ADD KEY `id_lokasi_asal` (`id_lokasi_asal`),
  ADD KEY `id_lokasi_tujuan` (`id_lokasi_tujuan`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_aset` (`id_aset`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `riwayat_aktivitas`
--
ALTER TABLE `riwayat_aktivitas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `aset`
--
ALTER TABLE `aset`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `lokasi`
--
ALTER TABLE `lokasi`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `mutasi_aset`
--
ALTER TABLE `mutasi_aset`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `peminjaman`
--
ALTER TABLE `peminjaman`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `riwayat_aktivitas`
--
ALTER TABLE `riwayat_aktivitas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `aset`
--
ALTER TABLE `aset`
  ADD CONSTRAINT `aset_ibfk_1` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `aset_ibfk_2` FOREIGN KEY (`id_lokasi`) REFERENCES `lokasi` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `mutasi_aset`
--
ALTER TABLE `mutasi_aset`
  ADD CONSTRAINT `mutasi_aset_ibfk_1` FOREIGN KEY (`id_aset`) REFERENCES `aset` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mutasi_aset_ibfk_2` FOREIGN KEY (`id_lokasi_asal`) REFERENCES `lokasi` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `mutasi_aset_ibfk_3` FOREIGN KEY (`id_lokasi_tujuan`) REFERENCES `lokasi` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `mutasi_aset_ibfk_4` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD CONSTRAINT `peminjaman_ibfk_1` FOREIGN KEY (`id_aset`) REFERENCES `aset` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `peminjaman_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `riwayat_aktivitas`
--
ALTER TABLE `riwayat_aktivitas`
  ADD CONSTRAINT `riwayat_aktivitas_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
