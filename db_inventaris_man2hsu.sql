-- MySQL dump 10.13  Distrib 8.0.30, for Win64 (x86_64)
--
-- Host: localhost    Database: db_inventaris_man2hsu
-- ------------------------------------------------------
-- Server version	8.0.30

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `aset`
--

DROP TABLE IF EXISTS `aset`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `aset` (
  `id` int NOT NULL AUTO_INCREMENT,
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
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_aset` (`kode_aset`),
  KEY `id_kategori` (`id_kategori`),
  KEY `id_lokasi` (`id_lokasi`),
  CONSTRAINT `aset_ibfk_1` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id`) ON DELETE SET NULL,
  CONSTRAINT `aset_ibfk_2` FOREIGN KEY (`id_lokasi`) REFERENCES `lokasi` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aset`
--

LOCK TABLES `aset` WRITE;
/*!40000 ALTER TABLE `aset` DISABLE KEYS */;
INSERT INTO `aset` VALUES (1,'AST-2024-001','Meja Guru',1,2,15,'Baik',2024,750000.00,'Dana BOS',NULL,'Meja guru kayu jati','qr_AST-2024-001.png','2026-03-12 12:51:46','2026-03-12 13:09:58',NULL),(2,'AST-2024-002','Kursi Guru',1,2,15,'Baik',2024,500000.00,'Dana BOS',NULL,'Kursi guru sandaran','qr_AST-2024-002.png','2026-03-12 12:51:46','2026-03-13 15:11:19',NULL),(3,'AST-2024-003','Komputer Desktop',2,11,20,'Baik',2024,8000000.00,'Dana BOS',NULL,'PC Desktop Core i5',NULL,'2026-03-12 12:51:46','2026-03-12 12:51:46',NULL),(4,'AST-2023-004','Proyektor LCD',2,14,3,'Baik',2023,5000000.00,'Dana BOS',NULL,'Proyektor Epson',NULL,'2026-03-12 12:51:46','2026-03-12 12:51:46',NULL),(5,'AST-2023-005','Meja Siswa',1,4,30,'Rusak Ringan',2023,350000.00,'Dana BOS',NULL,'Meja siswa kayu',NULL,'2026-03-12 12:51:46','2026-03-12 12:51:46',NULL),(6,'AST-2022-006','Printer LaserJet',2,3,2,'Baik',2022,3500000.00,'APBD',NULL,'Printer HP LaserJet',NULL,'2026-03-12 12:51:46','2026-03-12 12:51:46',NULL),(7,'AST-2022-007','Lemari Arsip',1,3,5,'Baik',2022,2000000.00,'APBD',NULL,'Lemari besi 4 pintu',NULL,'2026-03-12 12:51:46','2026-03-12 12:51:46',NULL),(8,'AST-2021-008','Mikroskop',3,12,10,'Rusak Ringan',2021,1500000.00,'Dana BOS',NULL,'Mikroskop binokuler',NULL,'2026-03-12 12:51:46','2026-03-12 12:51:46',NULL),(9,'AST-2024-009','Bola Basket',5,15,5,'Baik',2024,250000.00,'Dana BOS',NULL,'Bola basket Molten',NULL,'2026-03-12 12:51:46','2026-03-12 12:51:46',NULL),(10,'AST-2023-010','AC Split',2,1,2,'Baik',2023,4500000.00,'APBD',NULL,'AC 1.5 PK Daikin',NULL,'2026-03-12 12:51:46','2026-03-12 12:51:46',NULL),(11,'AST-2026-011','Bola Futsal',5,15,5,'Baik',2026,300000.00,'APBD','aset_1773414927.png','alat olahraga',NULL,'2026-03-13 15:15:27','2026-03-13 15:15:27',NULL),(12,'AST-2026-012','Bola Voli',5,15,2,'Baik',2026,650000000000.00,'APBD','aset_1773415043.png','','qr_AST-2026-012.png','2026-03-13 15:17:23','2026-03-14 11:22:15',NULL);
/*!40000 ALTER TABLE `aset` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kategori`
--

DROP TABLE IF EXISTS `kategori`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kategori` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(100) NOT NULL,
  `keterangan` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kategori`
--

LOCK TABLES `kategori` WRITE;
/*!40000 ALTER TABLE `kategori` DISABLE KEYS */;
INSERT INTO `kategori` VALUES (1,'Mebeler','Perabot/furniture seperti meja, kursi, lemari','2026-03-12 12:51:46'),(2,'Elektronik','Perangkat elektronik seperti komputer, printer, proyektor','2026-03-12 12:51:46'),(3,'Alat Laboratorium','Peralatan laboratorium IPA, bahasa, komputer','2026-03-12 12:51:46'),(4,'Buku & Pustaka','Koleksi buku perpustakaan dan bahan ajar','2026-03-12 12:51:46'),(5,'Alat Olahraga','Peralatan olahraga dan kegiatan ekstrakurikuler','2026-03-12 12:51:46'),(6,'Kendaraan','Kendaraan dinas dan operasional','2026-03-12 12:51:46'),(7,'Alat Kantor','Peralatan kantor seperti ATK, mesin fotokopi','2026-03-12 12:51:46'),(8,'Bangunan','Gedung dan komponen bangunan','2026-03-12 12:51:46');
/*!40000 ALTER TABLE `kategori` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lokasi`
--

DROP TABLE IF EXISTS `lokasi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lokasi` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_lokasi` varchar(100) NOT NULL,
  `keterangan` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lokasi`
--

LOCK TABLES `lokasi` WRITE;
/*!40000 ALTER TABLE `lokasi` DISABLE KEYS */;
INSERT INTO `lokasi` VALUES (1,'Ruang Kepala Madrasah','Ruang kerja kepala sekolah','2026-03-12 12:51:46'),(2,'Ruang Guru','Ruang kerja guru','2026-03-12 12:51:46'),(3,'Ruang TU','Ruang tata usaha','2026-03-12 12:51:46'),(4,'Ruang Kelas X-A','Kelas X jurusan A','2026-03-12 12:51:46'),(5,'Ruang Kelas X-B','Kelas X jurusan B','2026-03-12 12:51:46'),(6,'Ruang Kelas XI-A','Kelas XI jurusan A','2026-03-12 12:51:46'),(7,'Ruang Kelas XI-B','Kelas XI jurusan B','2026-03-12 12:51:46'),(8,'Ruang Kelas XII-A','Kelas XII jurusan A','2026-03-12 12:51:46'),(9,'Ruang Kelas XII-B','Kelas XII jurusan B','2026-03-12 12:51:46'),(10,'Perpustakaan','Ruang perpustakaan','2026-03-12 12:51:46'),(11,'Lab Komputer','Laboratorium komputer','2026-03-12 12:51:46'),(12,'Lab IPA','Laboratorium ilmu pengetahuan alam','2026-03-12 12:51:46'),(13,'Mushalla','Tempat ibadah','2026-03-12 12:51:46'),(14,'Aula','Ruang serba guna','2026-03-12 12:51:46'),(15,'Gudang','Tempat penyimpanan barang','2026-03-12 12:51:46');
/*!40000 ALTER TABLE `lokasi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mutasi_aset`
--

DROP TABLE IF EXISTS `mutasi_aset`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mutasi_aset` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_aset` int NOT NULL,
  `id_lokasi_asal` int DEFAULT NULL,
  `id_lokasi_tujuan` int DEFAULT NULL,
  `tanggal_mutasi` date NOT NULL,
  `keterangan` text,
  `id_user` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_aset` (`id_aset`),
  KEY `id_lokasi_asal` (`id_lokasi_asal`),
  KEY `id_lokasi_tujuan` (`id_lokasi_tujuan`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `mutasi_aset_ibfk_1` FOREIGN KEY (`id_aset`) REFERENCES `aset` (`id`) ON DELETE CASCADE,
  CONSTRAINT `mutasi_aset_ibfk_2` FOREIGN KEY (`id_lokasi_asal`) REFERENCES `lokasi` (`id`) ON DELETE SET NULL,
  CONSTRAINT `mutasi_aset_ibfk_3` FOREIGN KEY (`id_lokasi_tujuan`) REFERENCES `lokasi` (`id`) ON DELETE SET NULL,
  CONSTRAINT `mutasi_aset_ibfk_4` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mutasi_aset`
--

LOCK TABLES `mutasi_aset` WRITE;
/*!40000 ALTER TABLE `mutasi_aset` DISABLE KEYS */;
INSERT INTO `mutasi_aset` VALUES (1,12,15,9,'2026-03-13','Perpindahan via edit aset',2,'2026-03-13 15:34:38'),(2,12,9,15,'2026-03-13','Perpindahan via edit aset',2,'2026-03-13 15:35:02');
/*!40000 ALTER TABLE `mutasi_aset` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `peminjaman`
--

DROP TABLE IF EXISTS `peminjaman`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `peminjaman` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_aset` int NOT NULL,
  `nama_peminjam` varchar(100) NOT NULL,
  `id_peminjam` int DEFAULT NULL,
  `id_lokasi` int DEFAULT NULL,
  `tanggal_pinjam` date NOT NULL,
  `tanggal_kembali_rencana` date NOT NULL,
  `tanggal_kembali_aktual` date DEFAULT NULL,
  `status` enum('Dipinjam','Dikembalikan') NOT NULL DEFAULT 'Dipinjam',
  `keterangan` text,
  `id_user` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_aset` (`id_aset`),
  KEY `id_user` (`id_user`),
  KEY `fk_peminjaman_peminjam` (`id_peminjam`),
  KEY `fk_peminjaman_lokasi` (`id_lokasi`),
  CONSTRAINT `fk_peminjaman_peminjam` FOREIGN KEY (`id_peminjam`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_peminjaman_lokasi` FOREIGN KEY (`id_lokasi`) REFERENCES `lokasi` (`id`) ON DELETE SET NULL,
  CONSTRAINT `peminjaman_ibfk_1` FOREIGN KEY (`id_aset`) REFERENCES `aset` (`id`) ON DELETE CASCADE,
  CONSTRAINT `peminjaman_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `peminjaman`
--

LOCK TABLES `peminjaman` WRITE;
/*!40000 ALTER TABLE `peminjaman` DISABLE KEYS */;
INSERT INTO `peminjaman` VALUES (1,9,'Kurniawa',NULL,'2026-03-13','2026-03-20','2026-03-13','Dikembalikan','Latihan murid XB',1,'2026-03-13 15:18:53'),(2,4,'AMAT',NULL,'2026-04-23','2026-04-29',NULL,'Dipinjam','PRESENTASI KELAS',1,'2026-04-23 05:31:26'),(3,10,'Ahmad Fauzi, S.Pd',4,'2026-04-23','2026-05-01',NULL,'Dipinjam','Untuk pembelajaran kelas',4,'2026-04-23 06:24:23');
/*!40000 ALTER TABLE `peminjaman` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `riwayat_aktivitas`
--

DROP TABLE IF EXISTS `riwayat_aktivitas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `riwayat_aktivitas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_user` int DEFAULT NULL,
  `aktivitas` varchar(100) NOT NULL,
  `keterangan` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `riwayat_aktivitas_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `riwayat_aktivitas`
--

LOCK TABLES `riwayat_aktivitas` WRITE;
/*!40000 ALTER TABLE `riwayat_aktivitas` DISABLE KEYS */;
INSERT INTO `riwayat_aktivitas` VALUES (1,1,'Login','Admin melakukan login','2026-03-12 12:51:46'),(2,1,'Tambah Aset','Menambah aset: Meja Guru','2026-03-12 12:51:46'),(3,1,'Tambah Aset','Menambah aset: Komputer Desktop','2026-03-12 12:51:46'),(4,1,'Login','Administrator berhasil login','2026-03-12 13:08:34'),(5,1,'Logout','Administrator melakukan logout','2026-03-12 13:09:32'),(6,2,'Login','Petugas Inventaris berhasil login','2026-03-12 13:09:38'),(7,2,'Generate QR','Generate QR Code untuk: Meja Guru (AST-2024-001)','2026-03-12 13:09:58'),(8,2,'Generate QR','Generate QR Code untuk: Meja Guru (AST-2024-001)','2026-03-12 13:13:41'),(9,2,'Logout','Petugas Inventaris melakukan logout','2026-03-12 13:31:41'),(10,1,'Login','Administrator berhasil login','2026-03-12 13:33:11'),(11,1,'Logout','Administrator melakukan logout','2026-03-12 13:37:01'),(12,1,'Login','Administrator berhasil login','2026-03-12 13:43:17'),(13,1,'Logout','Administrator melakukan logout','2026-03-12 13:43:25'),(14,1,'Login','Administrator berhasil login','2026-03-12 13:45:30'),(15,1,'Logout','Administrator melakukan logout','2026-03-12 13:46:29'),(16,1,'Login','Administrator berhasil login','2026-03-12 13:46:46'),(17,1,'Logout','Administrator melakukan logout','2026-03-12 13:56:51'),(18,1,'Login','Administrator berhasil login','2026-03-12 13:57:09'),(19,1,'Logout','Administrator melakukan logout','2026-03-12 14:30:47'),(20,1,'Login','Administrator berhasil login','2026-03-12 14:30:57'),(21,1,'Logout','Administrator melakukan logout','2026-03-12 14:34:19'),(22,1,'Login','Administrator berhasil login','2026-03-12 14:35:39'),(23,1,'Logout','Administrator melakukan logout','2026-03-12 15:02:31'),(24,1,'Login','Administrator berhasil login','2026-03-12 15:02:39'),(25,1,'Logout','Administrator melakukan logout','2026-03-12 15:03:32'),(26,1,'Login','Administrator berhasil login','2026-03-12 15:04:22'),(27,1,'Logout','Administrator melakukan logout','2026-03-12 15:17:25'),(28,1,'Login','Administrator berhasil login','2026-03-12 15:17:37'),(29,1,'Login','Administrator berhasil login','2026-03-12 16:06:41'),(30,1,'Login','Administrator berhasil login','2026-03-12 16:29:27'),(31,1,'Login','Administrator berhasil login','2026-03-12 16:53:24'),(32,1,'Login','Administrator berhasil login','2026-03-12 16:54:32'),(33,1,'Login','Administrator berhasil login','2026-03-12 17:36:19'),(34,1,'Logout','Administrator melakukan logout','2026-03-12 17:36:23'),(35,1,'Login','Administrator berhasil login','2026-03-12 17:38:22'),(36,1,'Login','Administrator berhasil login','2026-03-13 13:22:22'),(37,1,'Login','Administrator berhasil login','2026-03-13 14:26:14'),(38,1,'Generate QR','Generate QR Code untuk: Kursi Guru (AST-2024-002)','2026-03-13 15:11:19'),(39,1,'Tambah Aset','Menambah aset: Bola Futsal (AST-2026-011)','2026-03-13 15:15:27'),(40,1,'Tambah Aset','Menambah aset: Bola Voli (AST-2026-012)','2026-03-13 15:17:23'),(41,1,'Peminjaman','Peminjaman aset: Bola Basket oleh Kurniawa','2026-03-13 15:18:53'),(42,1,'Pengembalian','Pengembalian aset: Bola Basket oleh Kurniawa','2026-03-13 15:20:03'),(43,1,'Logout','Administrator melakukan logout','2026-03-13 15:21:35'),(44,2,'Login','Petugas Inventaris berhasil login','2026-03-13 15:21:41'),(45,2,'Edit Aset','Mengedit aset: Bola Voli (AST-2026-012)','2026-03-13 15:34:18'),(46,2,'Edit Aset','Mengedit aset: Bola Voli (AST-2026-012)','2026-03-13 15:34:38'),(47,2,'Edit Aset','Mengedit aset: Bola Voli (AST-2026-012)','2026-03-13 15:35:02'),(48,1,'Login','Administrator berhasil login','2026-03-14 11:21:53'),(49,1,'Generate QR','Generate QR Code untuk: Bola Voli (AST-2026-012)','2026-03-14 11:22:15'),(50,1,'Login','Administrator berhasil login','2026-03-21 07:33:28'),(51,1,'Login','Administrator berhasil login','2026-03-22 03:21:25'),(52,1,'Login','Administrator berhasil login','2026-03-24 01:57:06'),(53,1,'Generate QR','Generate QR Code untuk: Bola Voli (AST-2026-012)','2026-03-24 03:00:12'),(54,1,'Generate QR','Generate QR Code untuk: Bola Voli (AST-2026-012)','2026-03-24 03:02:59'),(55,1,'Generate QR','Generate QR Code untuk: Bola Voli (AST-2026-012)','2026-03-24 03:06:43'),(56,1,'Login','Administrator berhasil login','2026-03-24 03:32:08'),(57,1,'Edit Profil','Administrator memperbarui profil','2026-03-24 03:35:04'),(58,1,'Logout','Administrator melakukan logout','2026-03-24 05:50:00'),(59,2,'Login','Petugas Inventaris berhasil login','2026-03-24 05:50:08'),(60,2,'Edit Profil','Petugas Inventaris memperbarui profil','2026-03-24 05:50:28'),(61,2,'Logout','Petugas Inventaris melakukan logout','2026-03-24 05:50:33'),(62,1,'Login','Administrator berhasil login','2026-03-24 05:56:35'),(63,1,'Tambah User','Menambah pengguna: budi (petugas)','2026-03-24 05:56:54'),(64,1,'Login','Administrator berhasil login','2026-04-21 03:37:16'),(65,1,'Login','Administrator berhasil login','2026-04-23 05:25:18'),(66,1,'Generate QR','Generate QR Code untuk: Meja Guru (AST-2024-001)','2026-04-23 05:27:54'),(67,1,'Peminjaman','Peminjaman aset: Proyektor LCD oleh AMAT','2026-04-23 05:31:26'),(68,4,'Login','Ahmad Fauzi, S.Pd berhasil login','2026-04-23 06:22:51'),(69,4,'Peminjaman','Peminjaman aset: AC Split oleh Ahmad Fauzi, S.Pd','2026-04-23 06:24:23'),(70,4,'Logout','Ahmad Fauzi, S.Pd melakukan logout','2026-04-23 06:24:53'),(71,1,'Login','Administrator berhasil login','2026-04-23 06:25:55');
/*!40000 ALTER TABLE `riwayat_aktivitas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','petugas','guru') NOT NULL DEFAULT 'petugas',
  `nip` varchar(30) DEFAULT NULL,
  `jabatan` varchar(100) DEFAULT NULL,
  `no_telepon` varchar(20) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `Ingat_Token` varchar(255) DEFAULT NULL,
  `remember_token` varchar(255) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_token_expires_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Administrator','admin','$2y$10$X4bWmP8SZIhpJXIctRA9lu9zNoTFm4rff4By.3ZmH4uXNzIIv2d8y','admin',NULL,NULL,NULL,'user_1_1774323304.jpg','2026-03-12 12:51:46'),(2,'Petugas Inventaris','petugas','$2y$10$X4bWmP8SZIhpJXIctRA9lu9zNoTFm4rff4By.3ZmH4uXNzIIv2d8y','petugas',NULL,NULL,NULL,'user_2_1774331428.jpeg','2026-03-12 12:51:46'),(3,'budi','budi01','$2y$10$HQQC.VvSSVDYDL5FQGpTuOHyiTYIY2Pudg9Ne0qCZPUkUxHzOGdPe','petugas',NULL,NULL,NULL,NULL,'2026-03-24 05:56:54'),(4,'Ahmad Fauzi, S.Pd','fauzi','$2y$10$X4bWmP8SZIhpJXIctRA9lu9zNoTFm4rff4By.3ZmH4uXNzIIv2d8y','guru','198505012010011234','Guru Matematika','08123456789',NULL,'2026-04-23 06:15:55'),(5,'Siti Rahmah, S.Ag','rahmah','$2y$10$X4bWmP8SZIhpJXIctRA9lu9zNoTFm4rff4By.3ZmH4uXNzIIv2d8y','guru','199001152012012345','Guru PAI','08567891234',NULL,'2026-04-23 06:15:55');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pengaturan`
--

DROP TABLE IF EXISTS `pengaturan`;
CREATE TABLE `pengaturan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `kunci` varchar(100) NOT NULL,
  `nilai` text,
  `keterangan` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kunci` (`kunci`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pengaturan`
--

LOCK TABLES `pengaturan` WRITE;
INSERT INTO `pengaturan` VALUES 
(1,'smtp_host','smtp.gmail.com','SMTP Server Host'),
(2,'smtp_port','587','SMTP Server Port'),
(3,'smtp_username','','Email pengirim (Gmail)'),
(4,'smtp_password','','App Password Gmail'),
(5,'smtp_sender_name','MAN 2 HSU - Inventaris Aset','Nama pengirim'),
(6,'smtp_sender_email','','Alamat email pengirim'),
(7,'notif_h_minus_1','1','Kirim reminder H-1 (1=aktif, 0=nonaktif)'),
(8,'notif_h_0','1','Kirim reminder hari H (1=aktif, 0=nonaktif)'),
(9,'notif_h_plus_1','1','Kirim reminder H+1/overdue (1=aktif, 0=nonaktif)');
UNLOCK TABLES;

--
-- Table structure for table `email_notifications`
--

DROP TABLE IF EXISTS `email_notifications`;
CREATE TABLE `email_notifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_peminjaman` int NOT NULL,
  `tipe` enum('reminder','due','overdue') NOT NULL,
  `email_tujuan` varchar(255) NOT NULL,
  `status` enum('sent','failed') NOT NULL,
  `pesan_error` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_peminjaman` (`id_peminjaman`),
  CONSTRAINT `email_notifications_ibfk_1` FOREIGN KEY (`id_peminjaman`) REFERENCES `peminjaman` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-04-23 14:27:59
