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
-- Current Database: `db_inventaris_man2hsu`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `db_inventaris_man2hsu` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `db_inventaris_man2hsu`;

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
  `bukti_hapus` varchar(255) DEFAULT NULL,
  `alasan_hapus` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_aset` (`kode_aset`),
  KEY `id_kategori` (`id_kategori`),
  KEY `id_lokasi` (`id_lokasi`),
  CONSTRAINT `aset_ibfk_1` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id`) ON DELETE SET NULL,
  CONSTRAINT `aset_ibfk_2` FOREIGN KEY (`id_lokasi`) REFERENCES `lokasi` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aset`
--

LOCK TABLES `aset` WRITE;
/*!40000 ALTER TABLE `aset` DISABLE KEYS */;
INSERT INTO `aset` VALUES (3,'AST-2024-003','Komputer Desktop',2,11,10,'Baik',2024,8950000.00,'Dana BOS','aset_1780621971.png','PC Desktop Core i5','qr_AST-2024-003.png','2026-03-12 12:51:46','2026-06-05 01:21:03',NULL,NULL,NULL),(4,'AST-2023-004','Projector Epson',2,3,2,'Baik',2023,5890000.00,'Dana BOS','aset_1780622380.png','EB-X600 TKDN XGA 3600 Lumens HDMI 3LCD',NULL,'2026-03-12 12:51:46','2026-06-17 06:26:46','2026-06-17 06:26:46','bukti_AST-2023-004_1781677606.png','sudah tidak bisa di perbaiki'),(6,'AST-2022-006','Printer LaserJet',2,3,2,'Baik',2022,3800000.00,'APBD','aset_1780621770.png','50NW Laser Jet 150 NW COLOR Warna Wireless Network LAN Wifi',NULL,'2026-03-12 12:51:46','2026-06-10 03:49:33','2026-06-10 03:49:33','bukti_AST-2022-006_1781063373.jpeg','barang sudah tidak bisa di perbaiki'),(8,'AST-2021-008','Mikroskop Monokuler 40X-2000X',3,12,2,'Baik',2021,1185000.00,'Dana BOS',NULL,'Mikroskop binokuler','qr_AST-2021-008.png','2026-03-12 12:51:46','2026-07-06 11:49:45',NULL,NULL,NULL),(9,'AST-2024-009','Bola Basket',5,15,1,'Baik',2024,250000.00,'Dana BOS',NULL,'Bola basket Molten','qr_AST-2024-009.png','2026-03-12 12:51:46','2026-07-21 05:32:12',NULL,NULL,NULL),(11,'AST-2026-011','Bola Futsal',5,15,1,'Baik',2026,300000.00,'APBD','aset_1773414927.png','alat olahraga','qr_AST-2026-011.png','2026-03-13 15:15:27','2026-06-10 02:58:00',NULL,NULL,NULL),(12,'AST-2026-012','Bola Voli',5,2,1,'Baik',2026,200000.00,'APBD','aset_1773415043.png','','qr_AST-2026-012.png','2026-03-13 15:17:23','2026-07-19 04:27:03',NULL,NULL,NULL),(13,'AST-2026-013','Bola Basket',5,15,1,'Rusak Ringan',2024,250000.00,'Dana BOS',NULL,'Pemisahan dari kode AST-2024-009 karena rusak.\n\nCatatan Rusak: mengalami kebocoran,harus di kompa terlebih dahulu','qr_AST-2026-013.png','2026-06-10 04:15:26','2026-08-20 07:22:02','2026-08-20 07:22:02','bukti_AST-2026-013_1787210522.png','bolanya bocor'),(14,'AST-2026-014','PROYEKTOR',2,3,2,'Baik',2025,7650000.00,'Dana BOS','aset_1783338529.png','EPSON EB-E500','qr_AST-2026-014.png','2026-07-06 11:48:49','2026-08-20 07:20:00','2026-08-20 07:20:00','bukti_AST-2026-014_1787210400.png','barang rusak'),(19,'AST-2026-015','Laptop ASUS ExpertBook B1400',2,11,15,'Baik',2026,8500000.00,'BOS Reguler',NULL,'Laptop pembelajaran dan ujian berbasis komputer di Lab Komputer',NULL,'2026-08-21 13:29:34','2026-08-21 13:29:34',NULL,NULL,NULL),(20,'AST-2026-016','Smart TV Samsung 55 Inch Crystal UHD',2,14,1,'Baik',2026,7200000.00,'Komite Madrasah',NULL,'Smart TV presentasi, video conference, dan acara di Aula',NULL,'2026-08-21 13:29:34','2026-08-21 13:29:34',NULL,NULL,NULL),(21,'AST-2025-017','Sound System Portable Baretone 15 Inch',2,2,2,'Baik',2025,3500000.00,'BOS Reguler',NULL,'Speaker portable + 2 wireless mic untuk upacara dan rapat',NULL,'2026-08-21 13:29:34','2026-08-21 13:29:34',NULL,NULL,NULL),(22,'AST-2025-018','Meja Rapat Kayu Jati Oval',1,1,1,'Baik',2025,4800000.00,'APBN',NULL,'Meja rapat pimpinan kayu jati ukuran 300x120 cm',NULL,'2026-08-21 13:29:34','2026-08-21 13:29:34',NULL,NULL,NULL),(23,'AST-2024-019','Set Kursi & Meja Siswa Ergonomis',1,4,36,'Baik',2024,350000.00,'BOS Reguler',NULL,'Satu set meja kursi belajar siswa bahan kayu lapis dan besi',NULL,'2026-08-21 13:29:34','2026-08-21 13:29:34',NULL,NULL,NULL),(24,'AST-2024-020','Lemari Arsip Besi 4 Pintu Lion',1,3,2,'Baik',2024,2750000.00,'APBN',NULL,'Lemari filing cabinet tahan api untuk arsip ijazah dan kepegawaian',NULL,'2026-08-21 13:29:34','2026-08-21 13:29:34',NULL,NULL,NULL),(25,'AST-2025-021','Mikroskop Binokuler Olympus CX23',3,12,5,'Baik',2025,12500000.00,'Hibah Kemenag',NULL,'Mikroskop praktikum biologi siswa resolusi tinggi',NULL,'2026-08-21 13:29:34','2026-08-21 13:29:34',NULL,NULL,NULL),(26,'AST-2024-022','Torso Model Anatomi Tubuh Manusia',3,12,2,'Baik',2024,1850000.00,'BOS Reguler',NULL,'Model organ tubuh manusia lengkap ukuran dewasa untuk peraga IPA',NULL,'2026-08-21 13:29:34','2026-08-21 13:29:34',NULL,NULL,NULL),(27,'AST-2025-023','Paket Ensiklopedia Islam Tematik (10 Jilid)',4,10,3,'Baik',2025,4500000.00,'Komite Madrasah',NULL,'Referensi sejarah peradaban Islam dan sains Islam perpustakaan',NULL,'2026-08-21 13:29:34','2026-08-21 13:29:34',NULL,NULL,NULL),(28,'AST-2026-024','Set Raket Badminton Yonex Nanoray + Net',5,15,8,'Baik',2026,450000.00,'BOS Reguler',NULL,'Raket dan net bulutangkis untuk ekstrakurikuler olahraga',NULL,'2026-08-21 13:29:34','2026-08-21 13:29:34',NULL,NULL,NULL),(29,'AST-2024-025','Matras Senam Lantai 2x1 Meter',5,15,4,'Rusak Ringan',2024,1200000.00,'BOS Reguler',NULL,'Busa matras senam ketebalan 10cm, jahitan tepi luar terkelupas sedikit',NULL,'2026-08-21 13:29:34','2026-08-21 13:29:34',NULL,NULL,NULL),(30,'AST-2025-026','Mesin Fotokopi Multifungsi Kyocera M2040dn',7,3,1,'Baik',2025,8900000.00,'APBN',NULL,'Mesin cetak & scan dokumen operasional administrasi madrasah',NULL,'2026-08-21 13:29:34','2026-08-21 13:29:34',NULL,NULL,NULL),(31,'AST-2023-027','Sepeda Motor Honda Vario 125 Operasional',6,3,1,'Baik',2023,22500000.00,'APBN',NULL,'Kendaraan dinas roda dua untuk dinas luar tata usaha (DA 4567 XY)',NULL,'2026-08-21 13:29:34','2026-08-21 13:29:34',NULL,NULL,NULL),(32,'AST-2025-028','Router Switch Mikrotik Cloud Router CRS328',2,11,2,'Baik',2025,5600000.00,'BOS Reguler',NULL,'Switch jaringan utama 24 Port Gigabit PoE untuk internet sekolah',NULL,'2026-08-21 13:29:34','2026-08-21 13:29:34',NULL,NULL,NULL),(33,'AST-2024-029','AC Split Daikin 1.5 PK Inverter',2,2,2,'Rusak Ringan',2024,5200000.00,'Komite Madrasah',NULL,'Pendingin udara ruang guru, pendinginan kurang maksimal perlu servis rutin',NULL,'2026-08-21 13:29:34','2026-08-21 13:29:34',NULL,NULL,NULL),(34,'AST-2023-030','Mimbar Podium Kayu Jati Ukir Jepara',1,13,1,'Baik',2023,3200000.00,'Hibah Kemenag',NULL,'Podium khutbah jumat dan ceramah di Mushalla madrasah',NULL,'2026-08-21 13:29:34','2026-08-21 13:29:34',NULL,NULL,NULL),(35,'AST-2022-031','Papan Tulis Whiteboard Magnetik 120x240cm',1,6,6,'Baik',2022,850000.00,'BOS Reguler',NULL,'Whiteboard gantung kelas XI-A dan kelas sekitarnya',NULL,'2026-08-21 13:29:34','2026-08-21 13:29:34',NULL,NULL,NULL),(36,'AST-2024-032','Genset Silent Perkins 15 KVA',2,15,1,'Rusak Berat',2024,38000000.00,'APBN',NULL,'Generator cadangan listrik darurat, modul dinamo starter terbakar',NULL,'2026-08-21 13:29:34','2026-08-21 13:29:34',NULL,NULL,NULL);
/*!40000 ALTER TABLE `aset` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `email_notifications`
--

DROP TABLE IF EXISTS `email_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `email_notifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_peminjaman` int NOT NULL,
  `tipe` enum('reminder','due','overdue','approve','reject','return','mutasi','submit') NOT NULL,
  `media` enum('email','telegram') NOT NULL DEFAULT 'email',
  `email_tujuan` varchar(255) NOT NULL,
  `status` enum('sent','failed') NOT NULL,
  `pesan_error` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_peminjaman` (`id_peminjaman`),
  CONSTRAINT `email_notifications_ibfk_1` FOREIGN KEY (`id_peminjaman`) REFERENCES `peminjaman` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `email_notifications`
--

LOCK TABLES `email_notifications` WRITE;
/*!40000 ALTER TABLE `email_notifications` DISABLE KEYS */;
INSERT INTO `email_notifications` VALUES (2,5,'reminder','email','m.oktamaulana6@gmail.com','sent',NULL,'2026-05-23 11:09:30'),(3,6,'reminder','email','m.oktamaulana6@gmail.com','failed','SMTP Error: Could not authenticate.','2026-06-03 00:39:56'),(4,6,'reminder','email','m.oktamaulana6@gmail.com','failed','SMTP Error: Could not authenticate.','2026-06-03 00:40:40'),(5,6,'reminder','email','m.oktamaulana6@gmail.com','failed','SMTP Error: Could not authenticate.','2026-06-03 00:40:42'),(6,6,'reminder','email','m.oktamaulana6@gmail.com','sent',NULL,'2026-06-03 01:02:20'),(7,6,'reminder','email','m.oktamaulana6@gmail.com','sent',NULL,'2026-06-03 01:03:24'),(8,6,'reminder','email','m.oktamaulana6@gmail.com','sent',NULL,'2026-06-03 01:18:05'),(9,6,'reminder','email','m.oktamaulana6@gmail.com','sent',NULL,'2026-06-03 05:58:19'),(10,7,'reminder','email','ocvaniespada@gmail.com','sent',NULL,'2026-06-03 06:09:31'),(11,12,'reminder','email','m.oktamaulana6@gmail.com','sent',NULL,'2026-06-09 01:46:30'),(12,12,'reminder','email','m.oktamaulana6@gmail.com','sent',NULL,'2026-06-09 01:51:56'),(13,12,'reminder','email','m.oktamaulana6@gmail.com','sent',NULL,'2026-06-09 01:53:30'),(14,12,'reminder','email','m.oktamaulana6@gmail.com','sent',NULL,'2026-06-09 01:56:46'),(15,12,'reminder','email','m.oktamaulana6@gmail.com','sent',NULL,'2026-06-09 02:00:29'),(16,12,'reminder','email','m.oktamaulana6@gmail.com','sent',NULL,'2026-06-09 02:05:44'),(17,12,'reminder','email','m.oktamaulana6@gmail.com','sent',NULL,'2026-06-09 02:07:50'),(18,19,'reminder','email','m.oktamaulana6@gmail.com','sent',NULL,'2026-07-19 04:23:57'),(19,22,'reminder','email','m.oktamaulana6@gmail.com','sent',NULL,'2026-08-20 07:30:06'),(20,22,'reminder','email','m.oktamaulana6@gmail.com','sent',NULL,'2026-08-20 07:30:49'),(21,22,'reminder','email','m.oktamaulana6@gmail.com','sent',NULL,'2026-08-20 12:16:48'),(22,23,'reminder','email','m.oktamaulana6@gmail.com','sent',NULL,'2026-08-20 12:47:22'),(23,23,'reminder','email','m.oktamaulana6@gmail.com','sent',NULL,'2026-08-20 12:52:44');
/*!40000 ALTER TABLE `email_notifications` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mutasi_aset`
--

LOCK TABLES `mutasi_aset` WRITE;
/*!40000 ALTER TABLE `mutasi_aset` DISABLE KEYS */;
INSERT INTO `mutasi_aset` VALUES (1,12,15,9,'2026-03-13','Perpindahan via edit aset',2,'2026-03-13 15:34:38'),(2,12,9,15,'2026-03-13','Perpindahan via edit aset',2,'2026-03-13 15:35:02'),(3,4,14,3,'2026-06-09','Perpindahan via edit aset',1,'2026-06-08 23:26:40'),(4,12,15,2,'2026-06-10','Perpindahan via edit aset',1,'2026-06-10 03:12:21'),(5,14,11,3,'2026-07-22',NULL,1,'2026-07-22 01:17:37'),(6,4,2,8,'2026-08-10','Pemindahan proyektor tetap untuk kebutuhan pembelajaran interaktif kelas XII-A',1,'2026-08-21 13:30:01'),(7,21,15,2,'2026-08-01','Penempatan sound system portabel di Ruang Guru agar mudah diakses saat rapat madrasah',1,'2026-08-21 13:30:01'),(8,24,1,3,'2026-07-25','Pemindahan lemari arsip ke Ruang Tata Usaha untuk integrasi data kepegawaian',1,'2026-08-21 13:30:01');
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
  `kondisi_saat_dikembalikan` enum('Baik','Rusak Ringan','Rusak Berat') DEFAULT NULL,
  `catatan_pengembalian` text,
  `status` enum('Menunggu Konfirmasi','Ditolak','Dipinjam','Dikembalikan') NOT NULL DEFAULT 'Dipinjam',
  `keterangan` text,
  `id_user` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_aset` (`id_aset`),
  KEY `id_user` (`id_user`),
  KEY `fk_peminjaman_peminjam` (`id_peminjam`),
  KEY `fk_peminjaman_lokasi` (`id_lokasi`),
  CONSTRAINT `fk_peminjaman_lokasi` FOREIGN KEY (`id_lokasi`) REFERENCES `lokasi` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_peminjaman_peminjam` FOREIGN KEY (`id_peminjam`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `peminjaman_ibfk_1` FOREIGN KEY (`id_aset`) REFERENCES `aset` (`id`) ON DELETE CASCADE,
  CONSTRAINT `peminjaman_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `peminjaman`
--

LOCK TABLES `peminjaman` WRITE;
/*!40000 ALTER TABLE `peminjaman` DISABLE KEYS */;
INSERT INTO `peminjaman` VALUES (5,9,'Muhamma okta Maulana',6,NULL,'2026-05-23','2026-05-24','2026-05-26',NULL,NULL,'Dikembalikan','untuk latihan',6,'2026-05-23 10:44:54'),(6,11,'Muhamma okta Maulana',6,NULL,'2026-06-03','2026-06-04','2026-06-05',NULL,NULL,'Dikembalikan','Untuk praktek Kelas X A',6,'2026-06-03 00:39:26'),(7,4,'Siti Rahmah, S.Ag',5,NULL,'2026-06-03','2026-06-05','2026-06-05',NULL,NULL,'Dikembalikan','untuk presentasi',5,'2026-06-03 06:00:48'),(8,4,'Ahmad Fauzi, S.Pd',NULL,NULL,'2026-06-05','2026-06-06','2026-06-05',NULL,NULL,'Dikembalikan','untuk presentasi murid dikelas',NULL,'2026-06-05 01:27:13'),(9,4,'Ahmad Fauzi, S.Pd',NULL,NULL,'2026-06-05','2026-06-07','2026-06-05',NULL,NULL,'Dikembalikan','untuk presentasi murid dikelas',NULL,'2026-06-05 01:32:14'),(10,4,'Siti Rahmah, S.Ag',5,4,'2026-06-05','2026-06-06','2026-06-09',NULL,NULL,'Dikembalikan','Untuk pembelajaran',5,'2026-06-05 01:44:58'),(11,4,'Ahmad Fauzi, S.Pd',NULL,5,'2026-06-05','2026-06-06','2026-06-09',NULL,NULL,'Dikembalikan','untuk presentasi murid dikelas',NULL,'2026-06-05 01:45:46'),(12,11,'Muhamma okta Maulana',6,4,'2026-06-09','2026-06-10','2026-06-10',NULL,NULL,'Dikembalikan','latihan praktek futsal',6,'2026-06-09 01:46:03'),(13,4,'Muhamma okta Maulana',6,5,'2026-06-10','2026-06-11','2026-06-10',NULL,NULL,'Dikembalikan','untuk presentasi',6,'2026-06-10 03:15:44'),(14,4,'Muhamma okta Maulana',6,4,'2026-06-10','2026-06-11','2026-06-14',NULL,NULL,'Dikembalikan','untuk presentasi',6,'2026-06-10 03:34:07'),(15,9,'Muhamma okta Maulana',6,4,'2026-06-17','2026-06-18','2026-06-27',NULL,NULL,'Dikembalikan','latihan praktek',6,'2026-06-17 06:28:14'),(19,3,'Muhamma okta Maulana',6,4,'2026-07-19','2026-07-20','2026-07-19',NULL,NULL,'Dikembalikan','membuat laporan excel',6,'2026-07-19 04:09:16'),(20,14,'Muhamma okta Maulana',6,14,'2026-07-19','2026-07-20','2026-07-19','Baik',NULL,'Dikembalikan','olahraga',6,'2026-07-19 05:34:42'),(21,14,'Muhamma okta Maulana',6,9,'2026-07-21','2026-07-22','2026-07-22','Baik',NULL,'Dikembalikan','Untuk pembelajaran kelas',6,'2026-07-21 05:39:48'),(22,9,'Muhamma okta Maulana',6,14,'2026-08-20','2026-08-21','2026-08-20','Baik',NULL,'Dikembalikan','latihan praktek',6,'2026-08-20 07:26:21'),(23,9,'Muhamma okta Maulana',6,14,'2026-08-20','2026-08-21','2026-08-20','Baik',NULL,'Dikembalikan','Praktek',6,'2026-08-20 12:46:24'),(24,21,'Ahmad Fauzi, S.Pd',8,14,'2026-08-22','2026-08-23',NULL,NULL,NULL,'Menunggu Konfirmasi','Peminjaman sound system untuk kegiatan Latihan Pidato Siswa di Aula',1,'2026-08-21 13:30:01'),(25,19,'Budi Santoso, S.Kom',10,11,'2026-08-22','2026-08-25',NULL,NULL,NULL,'Menunggu Konfirmasi','Peminjaman 5 unit laptop untuk bimbingan teknis Olimpiade Informatika',1,'2026-08-21 13:30:01'),(26,4,'Nurul Hidayah, M.Pd',9,8,'2026-08-20','2026-08-22',NULL,NULL,NULL,'Dipinjam','Digunakan untuk media tayang pembelajaran materi Gelombang Elektromagnetik',1,'2026-08-21 13:30:01'),(27,28,'Muhamma okta Maulana',6,14,'2026-08-21','2026-08-24',NULL,NULL,NULL,'Dipinjam','Peralatan latihan pertandingan bulutangkis antar madrasah',1,'2026-08-21 13:30:01'),(28,25,'Nurul Hidayah, M.Pd',9,12,'2026-08-15','2026-08-17','2026-08-17','Baik','Alat dikembalikan lengkap dengan kotak lensa dalam kondisi bersih dan berfungsi normal.','Dikembalikan','Praktikum pengamatan sel tumbuhan kelas XI IPA',1,'2026-08-21 13:30:01'),(29,3,'Budi Santoso, S.Kom',10,11,'2026-08-10','2026-08-12','2026-08-12','Baik','Komputer dikembalikan utuh dengan kabel power dan monitor.','Dikembalikan','Instalasi server lokal simulasi ANBK',1,'2026-08-21 13:30:01'),(30,26,'Siti Rahmah, S.Ag',5,6,'2026-08-05','2026-08-06','2026-08-06','Baik','Organ torso lengkap tanpa cacat.','Dikembalikan','Peraga pembelajaran struktur pencernaan biologi manusia',1,'2026-08-21 13:30:01'),(31,20,'Hendra Setiawan, S.Pd',12,4,'2026-08-18','2026-08-19',NULL,NULL,NULL,'Ditolak','Permintaan ditolak karena Smart TV Aula sudah dijadwalkan untuk gladi bersih pelantikan OSIM',1,'2026-08-21 13:30:01');
/*!40000 ALTER TABLE `peminjaman` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pengaturan`
--

DROP TABLE IF EXISTS `pengaturan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pengaturan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `kunci` varchar(100) NOT NULL,
  `nilai` text,
  `keterangan` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kunci` (`kunci`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pengaturan`
--

LOCK TABLES `pengaturan` WRITE;
/*!40000 ALTER TABLE `pengaturan` DISABLE KEYS */;
INSERT INTO `pengaturan` VALUES (1,'smtp_host','smtp.gmail.com','SMTP Server Host'),(2,'smtp_port','587','SMTP Server Port'),(3,'smtp_username','adminman2hsu@gmail.com','Email pengirim (Gmail)'),(4,'smtp_password','hjryjuxlfkodbxzp','App Password Gmail'),(5,'smtp_sender_name','MAN 2 HSU - Inventaris Aset','Nama pengirim'),(6,'smtp_sender_email','adminman2hsu@gmail.com','Alamat email pengirim'),(7,'notif_h_minus_1','1','Kirim reminder H-1 (1=aktif, 0=nonaktif)'),(8,'notif_h_0','1','Kirim reminder hari H (1=aktif, 0=nonaktif)'),(9,'notif_h_plus_1','1','Kirim reminder H+1/overdue (1=aktif, 0=nonaktif)'),(19,'telegram_bot_token','8752573346:AAHy3iO19bcPgNRpGCeWmDqH8YDuw34636I','Token Bot Telegram API'),(20,'telegram_chat_id','6873151654','Chat ID Penerima Notifikasi'),(21,'telegram_notif_aktif','1','Aktifkan notifikasi Telegram (1=aktif, 0=nonaktif)');
/*!40000 ALTER TABLE `pengaturan` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=420 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `riwayat_aktivitas`
--

LOCK TABLES `riwayat_aktivitas` WRITE;
/*!40000 ALTER TABLE `riwayat_aktivitas` DISABLE KEYS */;
INSERT INTO `riwayat_aktivitas` VALUES (1,1,'Login','Admin melakukan login','2026-03-12 12:51:46'),(2,1,'Tambah Aset','Menambah aset: Meja Guru','2026-03-12 12:51:46'),(3,1,'Tambah Aset','Menambah aset: Komputer Desktop','2026-03-12 12:51:46'),(4,1,'Login','Administrator berhasil login','2026-03-12 13:08:34'),(5,1,'Logout','Administrator melakukan logout','2026-03-12 13:09:32'),(6,2,'Login','Petugas Inventaris berhasil login','2026-03-12 13:09:38'),(7,2,'Generate QR','Generate QR Code untuk: Meja Guru (AST-2024-001)','2026-03-12 13:09:58'),(8,2,'Generate QR','Generate QR Code untuk: Meja Guru (AST-2024-001)','2026-03-12 13:13:41'),(9,2,'Logout','Petugas Inventaris melakukan logout','2026-03-12 13:31:41'),(10,1,'Login','Administrator berhasil login','2026-03-12 13:33:11'),(11,1,'Logout','Administrator melakukan logout','2026-03-12 13:37:01'),(12,1,'Login','Administrator berhasil login','2026-03-12 13:43:17'),(13,1,'Logout','Administrator melakukan logout','2026-03-12 13:43:25'),(14,1,'Login','Administrator berhasil login','2026-03-12 13:45:30'),(15,1,'Logout','Administrator melakukan logout','2026-03-12 13:46:29'),(16,1,'Login','Administrator berhasil login','2026-03-12 13:46:46'),(17,1,'Logout','Administrator melakukan logout','2026-03-12 13:56:51'),(18,1,'Login','Administrator berhasil login','2026-03-12 13:57:09'),(19,1,'Logout','Administrator melakukan logout','2026-03-12 14:30:47'),(20,1,'Login','Administrator berhasil login','2026-03-12 14:30:57'),(21,1,'Logout','Administrator melakukan logout','2026-03-12 14:34:19'),(22,1,'Login','Administrator berhasil login','2026-03-12 14:35:39'),(23,1,'Logout','Administrator melakukan logout','2026-03-12 15:02:31'),(24,1,'Login','Administrator berhasil login','2026-03-12 15:02:39'),(25,1,'Logout','Administrator melakukan logout','2026-03-12 15:03:32'),(26,1,'Login','Administrator berhasil login','2026-03-12 15:04:22'),(27,1,'Logout','Administrator melakukan logout','2026-03-12 15:17:25'),(28,1,'Login','Administrator berhasil login','2026-03-12 15:17:37'),(29,1,'Login','Administrator berhasil login','2026-03-12 16:06:41'),(30,1,'Login','Administrator berhasil login','2026-03-12 16:29:27'),(31,1,'Login','Administrator berhasil login','2026-03-12 16:53:24'),(32,1,'Login','Administrator berhasil login','2026-03-12 16:54:32'),(33,1,'Login','Administrator berhasil login','2026-03-12 17:36:19'),(34,1,'Logout','Administrator melakukan logout','2026-03-12 17:36:23'),(35,1,'Login','Administrator berhasil login','2026-03-12 17:38:22'),(36,1,'Login','Administrator berhasil login','2026-03-13 13:22:22'),(37,1,'Login','Administrator berhasil login','2026-03-13 14:26:14'),(38,1,'Generate QR','Generate QR Code untuk: Kursi Guru (AST-2024-002)','2026-03-13 15:11:19'),(39,1,'Tambah Aset','Menambah aset: Bola Futsal (AST-2026-011)','2026-03-13 15:15:27'),(40,1,'Tambah Aset','Menambah aset: Bola Voli (AST-2026-012)','2026-03-13 15:17:23'),(41,1,'Peminjaman','Peminjaman aset: Bola Basket oleh Kurniawa','2026-03-13 15:18:53'),(42,1,'Pengembalian','Pengembalian aset: Bola Basket oleh Kurniawa','2026-03-13 15:20:03'),(43,1,'Logout','Administrator melakukan logout','2026-03-13 15:21:35'),(44,2,'Login','Petugas Inventaris berhasil login','2026-03-13 15:21:41'),(45,2,'Edit Aset','Mengedit aset: Bola Voli (AST-2026-012)','2026-03-13 15:34:18'),(46,2,'Edit Aset','Mengedit aset: Bola Voli (AST-2026-012)','2026-03-13 15:34:38'),(47,2,'Edit Aset','Mengedit aset: Bola Voli (AST-2026-012)','2026-03-13 15:35:02'),(48,1,'Login','Administrator berhasil login','2026-03-14 11:21:53'),(49,1,'Generate QR','Generate QR Code untuk: Bola Voli (AST-2026-012)','2026-03-14 11:22:15'),(50,1,'Login','Administrator berhasil login','2026-03-21 07:33:28'),(51,1,'Login','Administrator berhasil login','2026-03-22 03:21:25'),(52,1,'Login','Administrator berhasil login','2026-03-24 01:57:06'),(53,1,'Generate QR','Generate QR Code untuk: Bola Voli (AST-2026-012)','2026-03-24 03:00:12'),(54,1,'Generate QR','Generate QR Code untuk: Bola Voli (AST-2026-012)','2026-03-24 03:02:59'),(55,1,'Generate QR','Generate QR Code untuk: Bola Voli (AST-2026-012)','2026-03-24 03:06:43'),(56,1,'Login','Administrator berhasil login','2026-03-24 03:32:08'),(57,1,'Edit Profil','Administrator memperbarui profil','2026-03-24 03:35:04'),(58,1,'Logout','Administrator melakukan logout','2026-03-24 05:50:00'),(59,2,'Login','Petugas Inventaris berhasil login','2026-03-24 05:50:08'),(60,2,'Edit Profil','Petugas Inventaris memperbarui profil','2026-03-24 05:50:28'),(61,2,'Logout','Petugas Inventaris melakukan logout','2026-03-24 05:50:33'),(62,1,'Login','Administrator berhasil login','2026-03-24 05:56:35'),(63,1,'Tambah User','Menambah pengguna: budi (petugas)','2026-03-24 05:56:54'),(64,1,'Login','Administrator berhasil login','2026-04-21 03:37:16'),(65,1,'Login','Administrator berhasil login','2026-04-23 05:25:18'),(66,1,'Generate QR','Generate QR Code untuk: Meja Guru (AST-2024-001)','2026-04-23 05:27:54'),(67,1,'Peminjaman','Peminjaman aset: Proyektor LCD oleh AMAT','2026-04-23 05:31:26'),(68,NULL,'Login','Ahmad Fauzi, S.Pd berhasil login','2026-04-23 06:22:51'),(69,NULL,'Peminjaman','Peminjaman aset: AC Split oleh Ahmad Fauzi, S.Pd','2026-04-23 06:24:23'),(70,NULL,'Logout','Ahmad Fauzi, S.Pd melakukan logout','2026-04-23 06:24:53'),(71,1,'Login','Administrator berhasil login','2026-04-23 06:25:55'),(72,1,'Logout','Administrator melakukan logout','2026-04-23 06:31:33'),(73,NULL,'Login','Ahmad Fauzi, S.Pd berhasil login','2026-04-23 06:31:44'),(74,1,'Login','Administrator berhasil login','2026-04-23 06:56:16'),(75,1,'Login','Administrator berhasil login','2026-05-07 02:26:29'),(76,1,'Login','Administrator berhasil login','2026-05-09 01:51:36'),(77,2,'Login','Petugas Inventaris berhasil login','2026-05-09 01:51:59'),(78,NULL,'Login','Ahmad Fauzi, S.Pd berhasil login','2026-05-09 01:53:43'),(79,1,'Pengembalian','Pengembalian aset: AC Split oleh Ahmad Fauzi, S.Pd','2026-05-09 01:55:48'),(80,1,'Pengembalian','Pengembalian aset: Proyektor LCD oleh AMAT','2026-05-09 02:02:08'),(81,1,'Login','Administrator berhasil login','2026-05-19 12:51:18'),(82,1,'Generate QR','Generate QR Code untuk: Bola Voli (AST-2026-012)','2026-05-19 12:51:37'),(83,1,'Login','Administrator berhasil login','2026-05-21 13:07:33'),(84,1,'Login','Administrator berhasil login','2026-05-21 23:50:13'),(85,1,'Edit User','Mengedit pengguna: Ahmad Fauzi, S.Pd','2026-05-21 23:50:47'),(86,1,'Login','Administrator berhasil login','2026-05-23 10:40:36'),(87,NULL,'Login','Ahmad Fauzi, S.Pd berhasil login','2026-05-23 10:41:10'),(88,NULL,'Peminjaman','Peminjaman aset: Proyektor LCD oleh Ahmad Fauzi, S.Pd','2026-05-23 10:41:51'),(89,1,'Tambah User','Menambah pengguna: Muhamma okta Maulana (guru)','2026-05-23 10:42:51'),(90,NULL,'Logout','Ahmad Fauzi, S.Pd melakukan logout','2026-05-23 10:44:30'),(91,6,'Login','Muhamma okta Maulana berhasil login','2026-05-23 10:44:37'),(92,6,'Peminjaman','Peminjaman aset: Bola Basket oleh Muhamma okta Maulana','2026-05-23 10:44:54'),(93,1,'Tambah User','Menambah pengguna: Okta (admin)','2026-05-23 11:00:07'),(94,1,'Logout','Administrator melakukan logout','2026-05-23 11:00:09'),(95,1,'Login','Administrator berhasil login','2026-05-23 11:00:52'),(96,1,'Logout','Administrator melakukan logout','2026-05-23 11:01:05'),(97,7,'Login','Okta berhasil login','2026-05-23 11:01:13'),(98,6,'Logout','Muhamma okta Maulana melakukan logout','2026-05-23 11:02:00'),(99,7,'Login','Okta berhasil login','2026-05-23 11:02:08'),(100,7,'Update Pengaturan','Memperbarui konfigurasi SMTP email','2026-05-23 11:05:52'),(102,7,'Logout','Okta melakukan logout','2026-05-23 11:10:46'),(103,NULL,'Login','Ahmad Fauzi, S.Pd berhasil login','2026-05-23 11:10:54'),(104,1,'Login','Administrator berhasil login','2026-05-25 21:22:23'),(105,1,'Pengembalian','Pengembalian aset: Bola Basket oleh Muhamma okta Maulana','2026-05-25 21:22:31'),(106,1,'Pengembalian','Pengembalian aset: Proyektor LCD oleh Ahmad Fauzi, S.Pd','2026-05-25 21:22:33'),(107,1,'Login','Administrator berhasil login','2026-05-25 23:50:30'),(108,1,'Login','Administrator berhasil login','2026-05-27 22:10:14'),(109,1,'Login','Administrator berhasil login','2026-05-27 22:28:21'),(110,1,'Login','Administrator berhasil login','2026-05-28 10:16:45'),(111,1,'Generate QR','Generate QR Code untuk: Bola Voli (AST-2026-012)','2026-05-28 10:16:52'),(112,1,'Generate QR','Generate QR Code untuk: Bola Voli (AST-2026-012)','2026-05-28 10:18:14'),(113,1,'Generate QR','Generate QR Code untuk: Bola Futsal (AST-2026-011)','2026-05-28 10:18:16'),(114,1,'Generate QR','Generate QR Code untuk: Bola Voli (AST-2026-012)','2026-05-28 10:23:10'),(115,1,'Generate QR','Generate QR Code untuk: Bola Voli (AST-2026-012)','2026-05-28 10:23:39'),(116,1,'Generate QR','Generate QR Code untuk: AC Split (AST-2023-010)','2026-05-28 10:26:56'),(117,1,'Edit Aset','Mengedit aset: Bola Voli (AST-2026-012)','2026-05-28 10:27:41'),(118,1,'Login','Administrator berhasil login','2026-06-03 00:31:49'),(119,1,'Logout','Administrator melakukan logout','2026-06-03 00:31:52'),(120,1,'Login','Administrator berhasil login','2026-06-03 00:33:05'),(121,1,'Logout','Administrator melakukan logout','2026-06-03 00:33:08'),(122,1,'Login','Administrator berhasil login','2026-06-03 00:34:54'),(123,7,'Login','Okta berhasil login','2026-06-03 00:35:26'),(124,7,'Logout','Okta melakukan logout','2026-06-03 00:36:04'),(125,6,'Login','Muhamma okta Maulana berhasil login','2026-06-03 00:36:46'),(126,6,'Ganti Password','Muhamma okta Maulana mengganti password','2026-06-03 00:38:21'),(127,6,'Peminjaman','Peminjaman aset: Bola Futsal oleh Muhamma okta Maulana','2026-06-03 00:39:26'),(128,1,'Kirim Notifikasi','Kirim email reminder untuk aset: Bola Futsal ke m.oktamaulana6@gmail.com','2026-06-03 00:39:56'),(129,1,'Update Pengaturan','Memperbarui konfigurasi SMTP email','2026-06-03 00:42:11'),(130,1,'Update Pengaturan','Memperbarui konfigurasi SMTP email','2026-06-03 01:01:32'),(131,1,'Kirim Notifikasi','Kirim email reminder untuk aset: Bola Futsal ke m.oktamaulana6@gmail.com','2026-06-03 01:02:20'),(132,1,'Kirim Notifikasi','Kirim email reminder untuk aset: Bola Futsal ke m.oktamaulana6@gmail.com','2026-06-03 01:03:24'),(133,1,'Kirim Notifikasi','Kirim email reminder untuk aset: Bola Futsal ke m.oktamaulana6@gmail.com','2026-06-03 01:18:05'),(134,6,'Logout','Muhamma okta Maulana melakukan logout','2026-06-03 04:03:25'),(135,2,'Login','Petugas Inventaris berhasil login','2026-06-03 04:03:32'),(136,1,'Logout','Administrator melakukan logout','2026-06-03 04:05:29'),(137,2,'Update Pengaturan','Memperbarui konfigurasi SMTP email','2026-06-03 05:07:57'),(138,2,'Login','Petugas Inventaris berhasil login','2026-06-03 05:51:40'),(139,2,'Update Pengaturan','Memperbarui konfigurasi SMTP email','2026-06-03 05:52:25'),(140,2,'Logout','Petugas Inventaris melakukan logout','2026-06-03 05:53:29'),(141,1,'Login','Administrator berhasil login','2026-06-03 05:53:36'),(142,1,'Edit User','Mengedit pengguna: Petugas Inventaris','2026-06-03 05:54:09'),(143,1,'Logout','Administrator melakukan logout','2026-06-03 05:54:16'),(144,2,'Login','Petugas Inventaris berhasil login','2026-06-03 05:54:36'),(145,2,'Update Pengaturan','Memperbarui konfigurasi SMTP email','2026-06-03 05:54:44'),(146,2,'Logout','Petugas Inventaris melakukan logout','2026-06-03 05:55:14'),(147,2,'Logout','Petugas Inventaris melakukan logout','2026-06-03 05:55:17'),(148,1,'Login','Administrator berhasil login','2026-06-03 05:55:25'),(149,1,'Update Pengaturan','Memperbarui konfigurasi SMTP email','2026-06-03 05:57:46'),(150,1,'Kirim Notifikasi','Kirim email reminder untuk aset: Bola Futsal ke m.oktamaulana6@gmail.com','2026-06-03 05:58:19'),(151,1,'Edit User','Mengedit pengguna: Siti Rahmah, S.Ag','2026-06-03 05:59:56'),(152,5,'Login','Siti Rahmah, S.Ag berhasil login','2026-06-03 06:00:12'),(153,5,'Peminjaman','Peminjaman aset: Proyektor LCD oleh Siti Rahmah, S.Ag','2026-06-03 06:00:48'),(154,1,'Update Pengaturan','Memperbarui konfigurasi SMTP email','2026-06-03 06:03:45'),(155,1,'Kirim Notifikasi','Kirim email reminder untuk aset: Proyektor LCD ke ocvaniespada@gmail.com','2026-06-03 06:09:31'),(156,1,'Login','Administrator berhasil login','2026-06-03 06:35:33'),(157,1,'Login','Administrator berhasil login','2026-06-05 00:50:32'),(158,1,'Edit Aset','Mengedit aset: Bola Basket (AST-2024-009)','2026-06-05 00:51:17'),(159,1,'Edit Aset','Mengedit aset: Mikroskop Monokuler 40X-2000X (AST-2021-008)','2026-06-05 00:52:52'),(160,1,'Edit Aset','Mengedit aset: Lemari Arsip Kantor GRAY-2 (AST-2022-007)','2026-06-05 00:54:45'),(161,1,'Edit Aset','Mengedit aset: Printer LaserJet (AST-2022-006)','2026-06-05 01:09:30'),(162,1,'Edit Aset','Mengedit aset: Lemari Arsip Kantor GRAY-2 (AST-2022-007)','2026-06-05 01:09:45'),(163,1,'Edit Aset','Mengedit aset: Bola Futsal (AST-2026-011)','2026-06-05 01:10:04'),(164,1,'Edit Aset','Mengedit aset: Meja Guru (AST-2024-001)','2026-06-05 01:11:27'),(165,1,'Edit Aset','Mengedit aset: Komputer Desktop (AST-2024-003)','2026-06-05 01:12:51'),(166,1,'Pengembalian','Pengembalian aset: Proyektor LCD oleh Siti Rahmah, S.Ag','2026-06-05 01:14:04'),(167,1,'Pengembalian','Pengembalian aset: Bola Futsal oleh Muhamma okta Maulana','2026-06-05 01:14:08'),(168,1,'Edit Aset','Mengedit aset: Kursi Guru (AST-2024-002)','2026-06-05 01:16:36'),(169,1,'Hapus Aset','Menghapus aset: AC Split (AST-2023-010)','2026-06-05 01:17:07'),(170,1,'Hapus Aset','Menghapus aset: Lemari Arsip Kantor GRAY-2 (AST-2022-007)','2026-06-05 01:17:24'),(171,1,'Hapus Aset','Menghapus aset: Meja Siswa (AST-2023-005)','2026-06-05 01:17:49'),(172,1,'Hapus Aset','Menghapus aset: Meja Guru (AST-2024-001)','2026-06-05 01:18:03'),(173,1,'Hapus Aset','Menghapus aset: Kursi Guru (AST-2024-002)','2026-06-05 01:18:07'),(174,1,'Edit Aset','Mengedit aset: Projector Epson (AST-2023-004)','2026-06-05 01:19:40'),(175,7,'Login','Okta berhasil login','2026-06-05 01:20:20'),(176,7,'Logout','Okta melakukan logout','2026-06-05 01:20:25'),(177,6,'Login','Muhamma okta Maulana berhasil login','2026-06-05 01:20:41'),(178,1,'Generate QR','Generate QR Code untuk: Komputer Desktop (AST-2024-003)','2026-06-05 01:21:03'),(179,1,'Edit Aset','Mengedit aset: Projector Epson (AST-2023-004)','2026-06-05 01:23:16'),(180,5,'Login','Siti Rahmah, S.Ag berhasil login','2026-06-05 01:25:42'),(181,1,'Edit User','Mengedit pengguna: Ahmad Fauzi, S.Pd','2026-06-05 01:26:39'),(182,NULL,'Login','Ahmad Fauzi, S.Pd berhasil login','2026-06-05 01:26:44'),(183,NULL,'Peminjaman','Peminjaman aset: Projector Epson oleh Ahmad Fauzi, S.Pd','2026-06-05 01:27:13'),(184,1,'Pengembalian','Pengembalian aset: Projector Epson oleh Ahmad Fauzi, S.Pd','2026-06-05 01:31:35'),(185,1,'Edit Aset','Mengedit aset: Projector Epson (AST-2023-004)','2026-06-05 01:31:49'),(186,NULL,'Peminjaman','Peminjaman aset: Projector Epson oleh Ahmad Fauzi, S.Pd','2026-06-05 01:32:14'),(187,1,'Pengembalian','Pengembalian aset: Projector Epson oleh Ahmad Fauzi, S.Pd','2026-06-05 01:37:47'),(188,5,'Peminjaman','Peminjaman aset: Projector Epson oleh Siti Rahmah, S.Ag','2026-06-05 01:44:58'),(189,NULL,'Peminjaman','Peminjaman aset: Projector Epson oleh Ahmad Fauzi, S.Pd','2026-06-05 01:45:46'),(190,1,'Logout','Administrator melakukan logout','2026-06-06 01:19:21'),(191,5,'Login','Siti Rahmah, S.Ag berhasil login','2026-06-06 01:22:33'),(192,1,'Login','Administrator berhasil login','2026-06-06 01:22:51'),(193,5,'Logout','Siti Rahmah, S.Ag melakukan logout','2026-06-06 01:23:03'),(194,5,'Login','Siti Rahmah, S.Ag berhasil login','2026-06-06 01:27:15'),(195,1,'Logout','Administrator melakukan logout','2026-06-06 01:27:33'),(196,2,'Login','Petugas Inventaris berhasil login','2026-06-06 01:27:43'),(197,2,'Logout','Petugas Inventaris melakukan logout','2026-06-06 01:27:59'),(198,2,'Login','Petugas Inventaris berhasil login','2026-06-06 01:28:58'),(199,1,'Login','Administrator berhasil login','2026-06-06 01:29:06'),(200,2,'Edit Aset','Mengedit aset: Projector Epson (AST-2023-004)','2026-06-06 01:31:50'),(201,2,'Edit Aset','Mengedit aset: Bola Futsal (AST-2026-011)','2026-06-06 01:32:36'),(202,2,'Login','Petugas Inventaris berhasil login','2026-06-08 23:23:55'),(203,2,'Pengembalian','Pengembalian aset: Projector Epson oleh Ahmad Fauzi, S.Pd','2026-06-08 23:24:05'),(204,2,'Pengembalian','Pengembalian aset: Projector Epson oleh Siti Rahmah, S.Ag','2026-06-08 23:24:08'),(205,2,'Logout','Petugas Inventaris melakukan logout','2026-06-08 23:24:32'),(206,1,'Login','Administrator berhasil login','2026-06-08 23:24:40'),(207,1,'Edit Aset','Mengedit aset: Projector Epson (AST-2023-004)','2026-06-08 23:26:40'),(208,6,'Logout','Muhamma okta Maulana melakukan logout','2026-06-09 01:44:00'),(209,7,'Login','Okta berhasil login','2026-06-09 01:44:10'),(210,6,'Login','Muhamma okta Maulana berhasil login','2026-06-09 01:45:24'),(211,6,'Peminjaman','Peminjaman aset: Bola Futsal oleh Muhamma okta Maulana','2026-06-09 01:46:03'),(212,7,'Kirim Notifikasi','Kirim email reminder untuk aset: Bola Futsal ke m.oktamaulana6@gmail.com','2026-06-09 01:46:30'),(213,7,'Kirim Notifikasi','Kirim email reminder untuk aset: Bola Futsal ke m.oktamaulana6@gmail.com','2026-06-09 01:51:56'),(214,7,'Kirim Notifikasi','Kirim email reminder untuk aset: Bola Futsal ke m.oktamaulana6@gmail.com','2026-06-09 01:53:30'),(215,7,'Kirim Notifikasi','Kirim email reminder untuk aset: Bola Futsal ke m.oktamaulana6@gmail.com','2026-06-09 01:56:46'),(216,7,'Kirim Notifikasi','Kirim email reminder untuk aset: Bola Futsal ke m.oktamaulana6@gmail.com','2026-06-09 02:00:29'),(219,7,'Logout','Okta melakukan logout','2026-06-09 02:03:38'),(220,1,'Login','Administrator berhasil login','2026-06-09 02:04:12'),(221,7,'Login','Okta berhasil login','2026-06-09 02:04:27'),(222,1,'Logout','Administrator melakukan logout','2026-06-09 02:05:02'),(223,6,'Login','Muhamma okta Maulana berhasil login','2026-06-09 02:05:07'),(224,6,'Logout','Muhamma okta Maulana melakukan logout','2026-06-09 02:05:09'),(225,7,'Kirim Notifikasi','Kirim email reminder untuk aset: Bola Futsal ke m.oktamaulana6@gmail.com','2026-06-09 02:05:44'),(226,7,'Kirim Notifikasi','Kirim email reminder untuk aset: Bola Futsal ke m.oktamaulana6@gmail.com','2026-06-09 02:07:50'),(227,1,'Login','Administrator berhasil login','2026-06-10 02:51:27'),(228,1,'Generate QR','Generate QR Code untuk: Bola Voli (AST-2026-012)','2026-06-10 03:09:25'),(229,1,'Edit Aset','Mengedit aset: Bola Voli (AST-2026-012)','2026-06-10 03:12:21'),(230,1,'Logout','Administrator melakukan logout','2026-06-10 03:14:50'),(231,6,'Login','Muhamma okta Maulana berhasil login','2026-06-10 03:15:03'),(232,6,'Peminjaman','Peminjaman aset: Projector Epson oleh Muhamma okta Maulana','2026-06-10 03:15:44'),(233,1,'Login','Administrator berhasil login','2026-06-10 03:19:37'),(234,1,'Edit Aset','Mengedit aset: Bola Futsal (AST-2026-011)','2026-06-10 03:24:32'),(235,1,'Edit Aset','Mengedit aset: Bola Voli (AST-2026-012)','2026-06-10 03:24:53'),(236,1,'Edit Aset','Mengedit aset: Komputer Desktop (AST-2024-003)','2026-06-10 03:25:04'),(237,1,'Pengembalian','Pengembalian aset: Projector Epson oleh Muhamma okta Maulana','2026-06-10 03:25:28'),(238,1,'Pengembalian','Pengembalian aset: Bola Futsal oleh Muhamma okta Maulana','2026-06-10 03:25:30'),(239,6,'Peminjaman','Pengajuan peminjaman aset: Projector Epson oleh Muhamma okta Maulana','2026-06-10 03:34:07'),(240,1,'Logout','Administrator melakukan logout','2026-06-10 03:34:28'),(241,2,'Login','Petugas Inventaris berhasil login','2026-06-10 03:34:38'),(242,2,'Konfirmasi','Menyetujui peminjaman aset: Projector Epson oleh Muhamma okta Maulana','2026-06-10 03:35:29'),(243,2,'Penghapusan','Penghapusan aset: Printer LaserJet (Kode: AST-2022-006)','2026-06-10 03:49:33'),(244,2,'Split Aset Rusak','Memisah 1 unit Bola Basket yang rusak menjadi kode baru AST-2026-013 (Rusak Ringan)','2026-06-10 04:15:26'),(245,1,'Login','Administrator berhasil login','2026-06-10 13:02:00'),(246,1,'Login','Administrator berhasil login','2026-06-14 05:34:41'),(247,1,'Pengembalian','Pengembalian aset: Projector Epson oleh Muhamma okta Maulana','2026-06-14 05:35:18'),(248,1,'Login','Administrator berhasil login','2026-06-14 09:08:42'),(249,1,'Hapus Peminjaman','Menghapus data peminjaman ID: 1','2026-06-16 04:13:11'),(250,1,'Hapus Peminjaman','Menghapus data peminjaman ID: 2','2026-06-16 04:13:14'),(251,6,'Login','Muhamma okta Maulana berhasil login','2026-06-16 04:13:32'),(252,1,'Penghapusan','Penghapusan aset: Projector Epson (Kode: AST-2023-004)','2026-06-17 06:26:46'),(253,1,'Logout','Administrator melakukan logout','2026-06-17 06:27:46'),(254,6,'Login','Muhamma okta Maulana berhasil login','2026-06-17 06:27:53'),(255,6,'Peminjaman','Pengajuan peminjaman aset: Bola Basket oleh Muhamma okta Maulana','2026-06-17 06:28:14'),(256,6,'Logout','Muhamma okta Maulana melakukan logout','2026-06-17 06:28:20'),(257,1,'Login','Administrator berhasil login','2026-06-17 06:28:24'),(258,1,'Konfirmasi','Menyetujui peminjaman aset: Bola Basket oleh Muhamma okta Maulana','2026-06-17 06:28:36'),(259,1,'Generate QR','Generate QR Code untuk: Bola Voli (AST-2026-012)','2026-06-17 06:29:12'),(260,1,'Login','Administrator berhasil login','2026-06-17 06:42:53'),(261,1,'Login','Administrator berhasil login','2026-06-24 23:45:21'),(262,1,'Hapus User','Menghapus pengguna: budi','2026-06-25 00:04:20'),(263,1,'Hapus User','Menghapus pengguna: Ahmad Fauzi, S.Pd','2026-06-25 00:04:23'),(264,1,'Login','Administrator berhasil login','2026-06-26 22:25:29'),(265,1,'Pengembalian','Pengembalian aset: Bola Basket oleh Muhamma okta Maulana','2026-06-26 22:26:25'),(266,1,'Login','Administrator berhasil login','2026-06-26 22:42:31'),(267,1,'Logout','Administrator melakukan logout','2026-06-26 22:44:22'),(268,1,'Login','Administrator berhasil login','2026-06-26 22:44:56'),(269,1,'Login','Administrator berhasil login','2026-06-26 22:47:45'),(270,1,'Login','Administrator berhasil login','2026-06-30 22:46:31'),(271,1,'Login','Administrator berhasil login','2026-06-30 22:59:03'),(272,1,'Login','Administrator berhasil login','2026-06-30 23:15:20'),(273,1,'Login','Administrator berhasil login','2026-06-30 23:41:37'),(274,1,'Login','Administrator berhasil login','2026-07-02 06:48:49'),(275,1,'Generate QR','Generate QR Code untuk: Bola Basket (AST-2026-013)','2026-07-02 06:48:56'),(276,1,'Login','Administrator berhasil login','2026-07-02 07:00:22'),(277,1,'Generate QR','Generate QR Code untuk: Bola Basket (AST-2026-013)','2026-07-02 07:00:25'),(278,1,'Generate QR','Generate QR Code untuk: Bola Basket (AST-2026-013)','2026-07-02 07:00:39'),(279,1,'Generate QR','Generate QR Code untuk: Bola Basket (AST-2026-013)','2026-07-02 07:06:52'),(280,1,'Generate QR','Generate QR Code untuk: Bola Basket (AST-2026-013)','2026-07-02 07:06:57'),(281,1,'Generate QR','Generate QR Code untuk: Bola Basket (AST-2026-013)','2026-07-02 07:07:01'),(282,1,'Generate QR','Generate QR Code untuk: Bola Basket (AST-2026-013)','2026-07-02 07:07:07'),(283,1,'Generate QR','Generate QR Code untuk: Bola Basket (AST-2026-013)','2026-07-02 07:07:08'),(284,1,'Generate QR','Generate QR Code untuk: Bola Basket (AST-2026-013)','2026-07-02 07:07:26'),(285,1,'Generate QR','Generate QR Code untuk: Mikroskop Monokuler 40X-2000X (AST-2021-008)','2026-07-02 07:07:34'),(286,1,'Generate QR','Generate QR Code untuk: Mikroskop Monokuler 40X-2000X (AST-2021-008)','2026-07-02 07:14:26'),(287,1,'Generate QR','Generate QR Code untuk: Mikroskop Monokuler 40X-2000X (AST-2021-008)','2026-07-02 07:15:53'),(288,1,'Login','Administrator berhasil login','2026-07-06 01:38:43'),(289,1,'Login','Administrator berhasil login','2026-07-06 11:11:51'),(290,1,'Login','Administrator berhasil login','2026-07-06 11:41:10'),(291,6,'Login','Muhamma okta Maulana berhasil login','2026-07-06 11:41:47'),(292,1,'Tambah Aset','Menambah aset: PROYEKTOR (AST-2026-014)','2026-07-06 11:48:49'),(293,1,'Edit Aset','Mengedit aset: Mikroskop Monokuler 40X-2000X (AST-2021-008)','2026-07-06 11:49:45'),(294,6,'Peminjaman','Pengajuan peminjaman aset: PROYEKTOR oleh Muhamma okta Maulana','2026-07-06 11:57:54'),(295,1,'Konfirmasi','Menyetujui peminjaman aset: PROYEKTOR oleh Muhamma okta Maulana','2026-07-06 12:01:21'),(296,1,'Login','Administrator berhasil login','2026-07-06 12:26:33'),(297,1,'Login','Administrator berhasil login','2026-07-15 00:57:58'),(298,1,'Logout','Administrator melakukan logout','2026-07-15 00:58:17'),(299,1,'Login','Administrator berhasil login','2026-07-15 00:58:51'),(300,1,'Logout','Administrator melakukan logout','2026-07-15 00:59:07'),(301,6,'Login','Muhamma okta Maulana berhasil login','2026-07-15 00:59:17'),(302,1,'Login','Administrator berhasil login','2026-07-18 02:51:59'),(303,1,'Login','Administrator berhasil login','2026-07-18 03:06:02'),(304,1,'Login','Administrator berhasil login','2026-07-18 03:27:34'),(305,1,'Login','Administrator berhasil login','2026-07-19 02:48:52'),(306,1,'Pengembalian','Pengembalian aset: PROYEKTOR oleh Muhamma okta Maulana','2026-07-19 02:49:00'),(307,6,'Login','Muhamma okta Maulana berhasil login','2026-07-19 02:49:30'),(308,6,'Peminjaman','Pengajuan peminjaman aset: Komputer Desktop oleh Muhamma okta Maulana','2026-07-19 02:50:22'),(309,6,'Peminjaman','Pengajuan peminjaman aset: Komputer Desktop oleh Muhamma okta Maulana','2026-07-19 02:51:07'),(310,6,'Login','Muhamma okta Maulana berhasil login','2026-07-19 04:07:52'),(311,1,'Login','Administrator berhasil login','2026-07-19 04:08:08'),(312,1,'Konfirmasi','Menolak peminjaman aset: Komputer Desktop oleh Muhamma okta Maulana','2026-07-19 04:08:14'),(313,1,'Konfirmasi','Menolak peminjaman aset: Komputer Desktop oleh Muhamma okta Maulana','2026-07-19 04:08:42'),(314,6,'Peminjaman','Pengajuan peminjaman aset: Komputer Desktop oleh Muhamma okta Maulana','2026-07-19 04:09:16'),(315,1,'Konfirmasi','Menyetujui peminjaman aset: Komputer Desktop oleh Muhamma okta Maulana','2026-07-19 04:09:24'),(316,1,'Hapus Peminjaman','Menghapus data peminjaman ID: 18','2026-07-19 04:09:53'),(317,1,'Hapus Peminjaman','Menghapus data peminjaman ID: 17','2026-07-19 04:10:03'),(318,1,'Login','Administrator berhasil login','2026-07-19 04:23:36'),(319,1,'Hapus Peminjaman','Menghapus data peminjaman ID: 16','2026-07-19 04:23:46'),(320,1,'Kirim Notifikasi','Kirim email reminder untuk aset: Komputer Desktop ke m.oktamaulana6@gmail.com','2026-07-19 04:23:57'),(321,1,'Pengembalian','Pengembalian aset: Komputer Desktop oleh Muhamma okta Maulana','2026-07-19 04:24:28'),(322,1,'Edit Lokasi','Mengedit lokasi: AULA','2026-07-19 04:25:20'),(323,1,'Edit Lokasi','Mengedit lokasi: Aula','2026-07-19 04:25:28'),(324,1,'Tambah Lokasi','Menambah lokasi: Aula 1','2026-07-19 04:26:12'),(325,1,'Hapus Lokasi','Menghapus lokasi: Aula 1','2026-07-19 04:26:15'),(326,1,'Tambah Kategori','Menambah kategori: kamus','2026-07-19 04:26:37'),(327,1,'Hapus Kategori','Menghapus kategori: kamus','2026-07-19 04:26:42'),(328,1,'Split Aset Rusak','Memisah 1 unit Bola Voli yang rusak menjadi kode baru AST-2026-015 (Rusak Ringan)','2026-07-19 04:27:03'),(329,1,'Penghapusan','Penghapusan aset: Bola Voli (Kode: AST-2026-015)','2026-07-19 04:27:31'),(330,1,'Login','Administrator berhasil login','2026-07-19 05:04:29'),(331,1,'Edit Profil','Administrator memperbarui profil','2026-07-19 05:08:39'),(332,1,'Edit Profil','Administrator memperbarui profil','2026-07-19 05:08:46'),(333,1,'Logout','Administrator melakukan logout','2026-07-19 05:08:49'),(334,6,'Logout','Muhamma okta Maulana melakukan logout','2026-07-19 05:18:47'),(335,1,'Login','Administrator berhasil login','2026-07-19 05:18:53'),(336,1,'Login','Administrator berhasil login','2026-07-19 05:32:19'),(337,1,'Logout','Administrator melakukan logout','2026-07-19 05:32:53'),(338,6,'Login','Muhamma okta Maulana berhasil login','2026-07-19 05:33:07'),(339,6,'Peminjaman','Pengajuan peminjaman aset: PROYEKTOR oleh Muhamma okta Maulana','2026-07-19 05:34:42'),(340,6,'Logout','Muhamma okta Maulana melakukan logout','2026-07-19 05:34:47'),(341,1,'Login','Administrator berhasil login','2026-07-19 05:34:53'),(342,1,'Konfirmasi','Menyetujui peminjaman aset: PROYEKTOR oleh Muhamma okta Maulana','2026-07-19 05:34:59'),(343,1,'Pengembalian','Pengembalian aset: PROYEKTOR oleh Muhamma okta Maulana (Kondisi: Baik)','2026-07-19 05:35:14'),(344,1,'Login','Administrator berhasil login','2026-07-21 04:25:24'),(345,1,'Login','Administrator berhasil login','2026-07-21 05:22:17'),(346,1,'Generate QR','Generate QR Code untuk: PROYEKTOR (AST-2026-014)','2026-07-21 05:31:30'),(347,1,'Generate QR','Generate QR Code untuk: Bola Basket (AST-2026-013)','2026-07-21 05:32:06'),(348,1,'Generate QR','Generate QR Code untuk: Bola Futsal (AST-2026-011)','2026-07-21 05:32:09'),(349,1,'Generate QR','Generate QR Code untuk: Bola Basket (AST-2024-009)','2026-07-21 05:32:12'),(350,1,'Logout','Administrator melakukan logout','2026-07-21 05:39:08'),(351,6,'Login','Muhamma okta Maulana berhasil login','2026-07-21 05:39:15'),(352,6,'Peminjaman','Pengajuan peminjaman aset: PROYEKTOR oleh Muhamma okta Maulana','2026-07-21 05:39:48'),(353,6,'Logout','Muhamma okta Maulana melakukan logout','2026-07-21 05:39:53'),(354,1,'Login','Administrator berhasil login','2026-07-21 05:40:01'),(355,1,'Konfirmasi','Menyetujui peminjaman aset: PROYEKTOR oleh Muhamma okta Maulana','2026-07-21 05:40:46'),(356,1,'Login','Administrator berhasil login','2026-07-21 06:15:09'),(357,1,'Logout','Administrator melakukan logout','2026-07-21 06:21:48'),(358,6,'Login','Muhamma okta Maulana berhasil login','2026-07-21 06:21:53'),(359,6,'Login','Muhamma okta Maulana berhasil login','2026-07-22 00:22:05'),(360,6,'Login','Muhamma okta Maulana berhasil login','2026-07-22 00:35:12'),(361,6,'Logout','Muhamma okta Maulana melakukan logout','2026-07-22 00:40:12'),(362,1,'Login','Administrator berhasil login','2026-07-22 00:40:17'),(363,1,'Pengembalian','Pengembalian aset: PROYEKTOR oleh Muhamma okta Maulana (Kondisi: Baik)','2026-07-22 00:42:52'),(364,1,'Logout','Administrator melakukan logout','2026-07-22 00:42:54'),(365,6,'Login','Muhamma okta Maulana berhasil login','2026-07-22 00:43:12'),(366,6,'Logout','Muhamma okta Maulana melakukan logout','2026-07-22 00:58:44'),(367,1,'Login','Administrator berhasil login','2026-07-22 00:58:48'),(368,1,'Mutasi Aset','Mutasi aset PROYEKTOR (AST-2026-014) dari Lab Komputer ke Ruang TU','2026-07-22 01:17:37'),(369,1,'Login','Administrator berhasil login','2026-08-18 22:12:18'),(370,1,'Login','Administrator berhasil login','2026-08-18 23:16:20'),(371,1,'Login','Administrator berhasil login','2026-08-19 07:58:52'),(372,1,'Logout','Administrator melakukan logout','2026-08-19 08:06:35'),(373,1,'Login','Administrator berhasil login','2026-08-19 08:06:39'),(374,1,'Tambah Supplier','Menambah supplier baru: Epson','2026-08-19 08:11:34'),(375,1,'Login','Administrator berhasil login','2026-08-20 00:40:35'),(376,1,'Edit Profil','Administrator memperbarui profil','2026-08-20 00:41:34'),(377,1,'Logout','Administrator melakukan logout','2026-08-20 00:41:44'),(378,6,'Login','Muhamma okta Maulana berhasil login','2026-08-20 00:41:51'),(379,6,'Logout','Muhamma okta Maulana melakukan logout','2026-08-20 00:43:57'),(380,1,'Login','Administrator berhasil login','2026-08-20 01:36:51'),(381,1,'Generate QR','Generate QR Code untuk: PROYEKTOR (AST-2026-014)','2026-08-20 01:37:01'),(382,1,'Generate QR','Generate QR Code untuk: PROYEKTOR (AST-2026-014)','2026-08-20 01:37:16'),(383,1,'Generate QR','Generate QR Code untuk: PROYEKTOR (AST-2026-014)','2026-08-20 01:38:05'),(384,1,'Generate QR','Generate QR Code untuk: PROYEKTOR (AST-2026-014)','2026-08-20 01:38:55'),(385,1,'Logout','Administrator melakukan logout','2026-08-20 02:06:46'),(386,1,'Login','Administrator berhasil login','2026-08-20 07:12:54'),(387,1,'Penghapusan','Penghapusan aset: PROYEKTOR (Kode: AST-2026-014)','2026-08-20 07:20:00'),(388,1,'Penghapusan','Penghapusan aset: Bola Basket (Kode: AST-2026-013)','2026-08-20 07:22:02'),(389,1,'Generate QR','Generate QR Code untuk: Bola Voli (AST-2026-012)','2026-08-20 07:24:31'),(390,1,'Logout','Administrator melakukan logout','2026-08-20 07:25:11'),(391,6,'Login','Muhamma okta Maulana berhasil login','2026-08-20 07:25:17'),(392,6,'Peminjaman','Pengajuan peminjaman aset: Bola Basket oleh Muhamma okta Maulana','2026-08-20 07:26:21'),(393,6,'Logout','Muhamma okta Maulana melakukan logout','2026-08-20 07:29:02'),(394,1,'Login','Administrator berhasil login','2026-08-20 07:29:08'),(395,1,'Konfirmasi','Menyetujui peminjaman aset: Bola Basket oleh Muhamma okta Maulana','2026-08-20 07:29:58'),(396,1,'Kirim Notifikasi','Kirim email reminder untuk aset: Bola Basket ke m.oktamaulana6@gmail.com','2026-08-20 07:30:06'),(397,1,'Kirim Notifikasi','Kirim email reminder untuk aset: Bola Basket ke m.oktamaulana6@gmail.com','2026-08-20 07:30:49'),(398,1,'Login','Administrator berhasil login','2026-08-20 11:42:57'),(399,1,'Edit User','Mengedit pengguna: Okta','2026-08-20 11:54:56'),(400,1,'Kirim Notifikasi','Kirim email reminder untuk aset: Bola Basket ke m.oktamaulana6@gmail.com','2026-08-20 12:16:48'),(401,1,'Pengembalian','Pengembalian aset: Bola Basket oleh Muhamma okta Maulana (Kondisi: Baik)','2026-08-20 12:43:45'),(402,1,'Hapus Peminjaman','Menghapus data peminjaman ID: 4','2026-08-20 12:44:11'),(403,1,'Logout','Administrator melakukan logout','2026-08-20 12:44:36'),(404,6,'Login','Muhamma okta Maulana berhasil login','2026-08-20 12:44:43'),(405,6,'Edit Profil','Muhamma okta Maulana memperbarui profil','2026-08-20 12:45:36'),(406,6,'Peminjaman','Pengajuan peminjaman aset: Bola Basket oleh Muhamma okta Maulana','2026-08-20 12:46:24'),(407,6,'Logout','Muhamma okta Maulana melakukan logout','2026-08-20 12:46:27'),(408,1,'Login','Administrator berhasil login','2026-08-20 12:46:38'),(409,1,'Konfirmasi','Menyetujui peminjaman aset: Bola Basket oleh Muhamma okta Maulana','2026-08-20 12:47:07'),(410,1,'Kirim Notifikasi','Kirim email reminder untuk aset: Bola Basket ke m.oktamaulana6@gmail.com','2026-08-20 12:47:22'),(411,1,'Logout','Administrator melakukan logout','2026-08-20 12:49:45'),(412,2,'Login','Petugas Inventaris berhasil login','2026-08-20 12:49:52'),(413,2,'Kirim Notifikasi','Kirim email reminder untuk aset: Bola Basket ke m.oktamaulana6@gmail.com','2026-08-20 12:52:45'),(414,2,'Pengembalian','Pengembalian aset: Bola Basket oleh Muhamma okta Maulana (Kondisi: Baik)','2026-08-20 12:57:37'),(415,1,'Tambah Aset','Menambah aset baru: Laptop ASUS ExpertBook B1400 (AST-2026-015)','2026-08-21 13:30:01'),(416,1,'Tambah Aset','Menambah aset baru: Smart TV Samsung 55 Inch Crystal UHD (AST-2026-016)','2026-08-21 13:30:01'),(417,1,'Tambah Aset','Menambah aset baru: Sound System Portable Baretone 15 Inch (AST-2025-017)','2026-08-21 13:30:01'),(418,1,'Mutasi Aset','Mutasi aset: Projector Epson dari Ruang Guru ke Ruang Kelas XII-A','2026-08-21 13:30:01'),(419,1,'Konfirmasi','Menyetujui peminjaman aset: Projector Epson oleh Nurul Hidayah, M.Pd','2026-08-21 13:30:01');
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
  `telegram_chat_id` varchar(50) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `remember_token` varchar(255) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_token_expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Administrator','admin',NULL,'$2y$10$X4bWmP8SZIhpJXIctRA9lu9zNoTFm4rff4By.3ZmH4uXNzIIv2d8y','admin',NULL,NULL,NULL,NULL,'user_1_1787186494.jpeg','2026-03-12 12:51:46',NULL,NULL,NULL),(2,'Petugas Inventaris','petugas','adminman2hsu@gmail.com','$2y$10$NArtLnYat829Kr3ezg2Q1uS08a.c.cAP/ZlZhLQBCXqji4Jvtlcmi','petugas',NULL,NULL,NULL,NULL,'user_2_1774331428.jpeg','2026-03-12 12:51:46',NULL,NULL,NULL),(5,'Siti Rahmah, S.Ag','rahmah','ocvaniespada@gmail.com','$2y$10$lPSnq4dNk.BGkMTHGJn7kul5gx3YhgotI1ECVn//VWgQ0qUljrqqi','guru','199001152012012345','Guru PAI','08567891234',NULL,NULL,'2026-04-23 06:15:55',NULL,NULL,NULL),(6,'Muhamma okta Maulana','okta123','m.oktamaulana6@gmail.com','$2y$10$Cme9X9BXBr360jEGGF0ZI.ohAmefJ3btfoYhnjOp3xhDew9LFlByK','guru','083635628383726','Guru olahraga','082237175894','1477944735',NULL,'2026-05-23 10:42:51',NULL,NULL,NULL),(7,'Okta','Oka111','okta.maulana01@gmail.com','$2y$10$IqcCpK5/ZLbUoGeQk6Ut5OgTmX09JkJ32LZPkNncYVRXvBTiRnrqK','admin',NULL,NULL,'082237175894',NULL,NULL,'2026-05-23 11:00:07',NULL,NULL,NULL),(8,'Ahmad Fauzi, S.Pd','fauzi','fauzi.man2@gmail.com','$2y$10$QhLxkPJiTmYqPh.QWyIOzeztgJy/tX/In/51ELVPBgGoxn4JZuxzG','guru','198504122010011015','Guru Matematika','081234567890',NULL,NULL,'2026-08-21 13:29:34',NULL,NULL,NULL),(9,'Nurul Hidayah, M.Pd','nurul','nurul.man2@gmail.com','$2y$10$gDtoVLBSUJOGg4KfWu1r4OqK6zhD3b8ohdr9YVqoEIQX.XySGz5fS','guru','198807202014022003','Guru Fisika','082198765432',NULL,NULL,'2026-08-21 13:29:34',NULL,NULL,NULL),(10,'Budi Santoso, S.Kom','budi','budi.man2@gmail.com','$2y$10$NWP9yL1/XF7lomOT0MQJ5eWpRcdgSAouOzTGgUP54KW/xVgvBmxWK','guru','199203152019031008','Guru Informatika','085712345678',NULL,NULL,'2026-08-21 13:29:34',NULL,NULL,NULL),(11,'Hj. Mardiana, S.Pd','mardiana','mardiana.man2@gmail.com','$2y$10$OUL.mcYcB2h4rBvrFDxzPOY5AGflCt/g9xAjr8kzxKn8HJ61bHSwW','guru','197811052005012004','Guru Bahasa Indonesia','081345678901',NULL,NULL,'2026-08-21 13:29:34',NULL,NULL,NULL),(12,'Hendra Setiawan, S.Pd','hendra','hendra.man2@gmail.com','$2y$10$vINQASn7HErmBpFmUKHsb.RAaG.sHWk3S5bKAgGE1orKDCMmXsXSO','guru','199406182020121002','Guru Bahasa Inggris','087812345678',NULL,NULL,'2026-08-21 13:29:34',NULL,NULL,NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-08-21 21:30:31
