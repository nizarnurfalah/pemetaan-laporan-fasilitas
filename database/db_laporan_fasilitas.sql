-- MySQL dump 10.13  Distrib 8.0.30, for Win64 (x86_64)
--
-- Host: localhost    Database: db_laporan_fasilitas
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
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admin` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin`
--

LOCK TABLES `admin` WRITE;
/*!40000 ALTER TABLE `admin` DISABLE KEYS */;
INSERT INTO `admin` VALUES (1,'admin','$2y$10$MxOYq7T/q0YLHMg3oDNXD.oxpeP7//E5ZEBoEttwP88XtuY.4vy2y');
/*!40000 ALTER TABLE `admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `laporan`
--

DROP TABLE IF EXISTS `laporan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `laporan` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `email_pelapor` varchar(100) NOT NULL,
  `jenis_kerusakan` varchar(100) NOT NULL,
  `deskripsi` text NOT NULL,
  `foto_lokasi` varchar(255) NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `status` enum('Baru','Diproses','Dijadwalkan','Selesai') NOT NULL DEFAULT 'Baru',
  `prioritas` enum('tinggi','sedang','rendah') NOT NULL DEFAULT 'sedang',
  `is_verified` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=Belum diverifikasi, 1=Terverifikasi, 2=Ditolak',
  `verified_at` datetime DEFAULT NULL,
  `verified_by` varchar(100) DEFAULT NULL,
  `rejection_reason` text,
  `tgl_lapor` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tgl_perbaikan_dijadwalkan` datetime DEFAULT NULL,
  `catatan_admin` text,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `laporan`
--

LOCK TABLES `laporan` WRITE;
/*!40000 ALTER TABLE `laporan` DISABLE KEYS */;
INSERT INTO `laporan` VALUES (18,'contohkasusjalan1@gmail.com','Jalan Berlubang','Terdapat lubang besar sedalam 1 meter di tengah jalan akibat jalan amblas. Sangat berbahaya bagi pengendara motor, sudah ada korban jatuh tadi pagi. Mohon segera ditangani.\r\nAlamat/Lokasi: Jl. Dr. Djunjunan No. 154 (Pasteur), Kec. Cicendo, Kota Bandung.\r\nKondisi: Membahayakan Jiwa.','1769567464_8c00661795605e1cc5c4.jpeg',-6.89228000,107.58409000,'Diproses','tinggi',1,'2026-01-28 02:36:10',NULL,NULL,'2026-01-28 02:31:04','2026-01-29 10:34:00','Laporan diterima. Berdasarkan foto dan tingkat keparahan (lubang >50cm), status ditetapkan sebagai Prioritas Tinggi. Tim URC (Unit Reaksi Cepat) akan segera meluncur ke lokasi pagi ini untuk pemasangan rambu pengaman sementara.'),(19,'contohkasusumum1@gmail.com','Fasilitas Publik Rusak','Lampu lalu lintas di perempatan mati total sejak hujan deras semalam. Arus lalu lintas kacau dan rawan tabrakan beruntun.\r\nAlamat: Simpang Lima Asia Afrika, Jl. Gatot Subroto, Kec. Lengkong, Kota Bandung.\r\nKondisi: Kekacauan Lalu Lintas Vital.','1769567751_6e5acbdba2dd0f56544c.jpg',-6.89174700,107.61316500,'Baru','sedang',1,'2026-01-28 02:36:37',NULL,NULL,'2026-01-28 02:35:51',NULL,NULL),(20,'nizarbeet88@gmail.com','Jalan Berlubang','ohiuhoihiuh;ogo9u','1784993019_7a91ecc9954b097912b4.jpg',-6.89747800,107.63133800,'Dijadwalkan','tinggi',1,'2026-07-25 15:24:54',NULL,NULL,'2026-07-25 15:23:39','2026-07-22 14:29:00','otw'),(21,'OPANGANJING@GMAIL.COM','Fasilitas Publik Rusak','GEURA BERESAN KDM','1784993454_edeeceb5a5f10d94e688.jpg',-6.91750000,107.60620000,'Baru','sedang',0,NULL,NULL,NULL,'2026-07-25 15:30:54',NULL,NULL);
/*!40000 ALTER TABLE `laporan` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-03 15:07:52
