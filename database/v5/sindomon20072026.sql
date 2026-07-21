-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               12.3.2-MariaDB-log - Homebrew
-- Server OS:                    Linux
-- HeidiSQL Version:             12.20.1.1
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for sindomondb
CREATE DATABASE IF NOT EXISTS `sindomondb` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */;
USE `sindomondb`;

-- Dumping structure for table sindomondb.tbl_amunisi_batch
CREATE TABLE IF NOT EXISTS `tbl_amunisi_batch` (
  `batch_id` int(11) NOT NULL AUTO_INCREMENT,
  `polda_id` int(11) NOT NULL,
  `kode_batch` varchar(50) NOT NULL,
  `kategori_id` int(11) NOT NULL,
  `jumlah_butir` int(11) NOT NULL,
  `tanggal_masuk` date NOT NULL,
  `tanggal_kedaluwarsa` date NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`batch_id`),
  UNIQUE KEY `kode_batch` (`kode_batch`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table sindomondb.tbl_amunisi_batch: ~1 rows (approximately)
INSERT INTO `tbl_amunisi_batch` (`batch_id`, `polda_id`, `kode_batch`, `kategori_id`, `jumlah_butir`, `tanggal_masuk`, `tanggal_kedaluwarsa`, `created_at`, `updated_at`) VALUES
	(1, 1, 'BATCH-H90-TRIGGER', 1, 5000, '2026-04-11', '2026-09-03', '2026-07-20 06:14:45', '2026-07-20 06:14:45');

-- Dumping structure for table sindomondb.tbl_dms_surat
CREATE TABLE IF NOT EXISTS `tbl_dms_surat` (
  `surat_id` varchar(36) NOT NULL,
  `pengirim_polda_id` int(11) DEFAULT NULL,
  `penerima_polda_id` int(11) DEFAULT NULL,
  `judul_surat` varchar(255) NOT NULL,
  `nomor_surat` varchar(100) NOT NULL,
  `file_pdf_url` varchar(500) NOT NULL,
  `status_tracking` enum('Terkirim','Dibaca','Diproses','Selesai') NOT NULL DEFAULT 'Terkirim',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`surat_id`),
  KEY `idx_pengirim` (`pengirim_polda_id`),
  KEY `idx_penerima` (`penerima_polda_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Dumping data for table sindomondb.tbl_dms_surat: ~7 rows (approximately)
INSERT INTO `tbl_dms_surat` (`surat_id`, `pengirim_polda_id`, `penerima_polda_id`, `judul_surat`, `nomor_surat`, `file_pdf_url`, `status_tracking`, `created_at`) VALUES
	('001d2a40-4775-40fe-8c7a-f49e615b04d5', 12, NULL, 'Laporan Mingguan Keamanan Wilayah', 'S-002/VI/2026', 'uploads/dms/472f06e5fd671e604f572243cb20215e.docx', 'Terkirim', '2026-07-14 11:54:13'),
	('048c01bd-8115-4c2f-819d-0c8950cad7ae', NULL, 12, 'Instruksi Rahasia Pengamanan', 'RHS/999/MABES/2026', 'uploads/dms/7583b334eefe45a0d465687d6e8d244b.pdf', 'Terkirim', '2026-07-15 09:55:27'),
	('0e2d771f-5306-4e24-9914-3bc7293a718b', 12, NULL, 'Laporan Persiapan Pilkada', 'B/123/OPS/VII/2026', 'uploads/dms/4bf2d4b68c59364bea96678311a3b6e3.pdf', 'Terkirim', '2026-07-14 13:31:53'),
	('2a9b5c69-f60b-4604-ad6c-a77ecacac244', 12, NULL, 'Undangan Rapat Koordinasi Kamtibmas', 'S-001/VI/2026', 'uploads/dms/b8f72b9f229aaaa03befc1187fbd7792.pdf', 'Terkirim', '2026-07-14 11:54:13'),
	('2b6c940b-b7e2-4a18-9a6f-3b4635709a64', NULL, 12, 'Instruksi Rahasia Pengamanan', 'RHS/999/MABES/2026', 'uploads/dms/04da6f6d4cd415076f8d0dbd32982c7f.pdf', 'Terkirim', '2026-07-15 10:06:35'),
	('88294894-74d4-4205-9ce4-f60ec9b7db96', 12, 15, 'Laporan Persiapan Pilkada', 'B/123/OPS/VII/2026', 'uploads/dms/38befd285788fa38eba23b57a9beedfd.pdf', 'Terkirim', '2026-07-14 13:31:22'),
	('e47b4247-e47d-4ae0-a1f6-f39375731824', 12, NULL, 'Surat ke Mabes Polri', 'S-005/VI/2026', 'uploads/dms/28c90bf7153f7c7000f9e4be5e55f23c.pdf', 'Terkirim', '2026-07-14 11:54:13');

-- Dumping structure for table sindomondb.tbl_dokumen_hukum
CREATE TABLE IF NOT EXISTS `tbl_dokumen_hukum` (
  `dokumen_id` int(11) NOT NULL AUTO_INCREMENT,
  `kategori` enum('Perkap','Perpol','SOP','Juknis') NOT NULL,
  `judul_dokumen` varchar(255) NOT NULL,
  `file_url` varchar(500) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`dokumen_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Dumping data for table sindomondb.tbl_dokumen_hukum: ~5 rows (approximately)
INSERT INTO `tbl_dokumen_hukum` (`dokumen_id`, `kategori`, `judul_dokumen`, `file_url`, `created_at`) VALUES
	(1, 'Perkap', 'Peraturan Kapolri No 1 Tahun 2024 tentang Pelayanan Publik', '/uploads/dokumen/perkap_1_2024.pdf', '2024-10-15 08:00:00'),
	(2, 'Perpol', 'Peraturan Polisi No 3 Tahun 2024 tentang Patroli', '/uploads/dokumen/perpol_3_2024.pdf', '2024-09-20 10:30:00'),
	(3, 'SOP', 'SOP Penanganan Pengaduan Masyarakat', '/uploads/dokumen/sop_pengaduan.pdf', '2024-08-01 14:00:00'),
	(4, 'Juknis', 'Juknis Aplikasi e-Complaint 2024', '/uploads/dokumen/juknis_ecomplaint.pdf', '2024-07-10 09:15:00'),
	(5, 'Perkap', 'Peraturan Kapolri No 5 Tahun 2023 tentang IT', '/uploads/dokumen/perkap_5_2023.pdf', '2023-12-01 11:00:00');

-- Dumping structure for table sindomondb.tbl_hub_pengaduan
CREATE TABLE IF NOT EXISTS `tbl_hub_pengaduan` (
  `pengaduan_id` int(11) NOT NULL AUTO_INCREMENT,
  `polda_id` int(11) DEFAULT NULL,
  `sumber` enum('Email','Hotline') NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `status` enum('Open','In Progress','Resolved','Closed') DEFAULT 'Open',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`pengaduan_id`),
  KEY `idx_polda_id` (`polda_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Dumping data for table sindomondb.tbl_hub_pengaduan: ~3 rows (approximately)
INSERT INTO `tbl_hub_pengaduan` (`pengaduan_id`, `polda_id`, `sumber`, `deskripsi`, `status`, `created_at`) VALUES
	(1, 12, 'Hotline', 'Tes tiket masih Open (Polda ID 12)', 'In Progress', '2026-07-14 04:53:38'),
	(2, 12, 'Email', 'Tes tiket sudah Closed (Polda ID 12)', 'Closed', '2026-07-14 04:53:38'),
	(3, 15, 'Hotline', 'Tes tiket milik wilayah lain (Polda ID 15)', 'Open', '2026-07-14 04:53:38');

-- Dumping structure for table sindomondb.tbl_jabatan
CREATE TABLE IF NOT EXISTS `tbl_jabatan` (
  `jabatan_id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_jabatan` varchar(200) NOT NULL,
  `formasi_ideal` int(11) NOT NULL DEFAULT 0,
  `parent_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`jabatan_id`),
  KEY `parent_id` (`parent_id`),
  CONSTRAINT `1` FOREIGN KEY (`parent_id`) REFERENCES `tbl_jabatan` (`jabatan_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table sindomondb.tbl_jabatan: ~5 rows (approximately)
INSERT INTO `tbl_jabatan` (`jabatan_id`, `nama_jabatan`, `formasi_ideal`, `parent_id`) VALUES
	(1, 'Dirsamapta', 1, NULL),
	(2, 'Wadirsamapta', 1, NULL),
	(3, 'Kasat Sabhara', 1, NULL),
	(4, 'Komandan Peleton', 4, NULL),
	(5, 'Anggota Dalmas', 50, NULL);

-- Dumping structure for table sindomondb.tbl_kategori_senjata
CREATE TABLE IF NOT EXISTS `tbl_kategori_senjata` (
  `kategori_id` int(11) NOT NULL AUTO_INCREMENT,
  `tipe_laras` enum('Panjang','Pendek') NOT NULL,
  `kaliber` varchar(50) NOT NULL,
  PRIMARY KEY (`kategori_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Dumping data for table sindomondb.tbl_kategori_senjata: ~2 rows (approximately)
INSERT INTO `tbl_kategori_senjata` (`kategori_id`, `tipe_laras`, `kaliber`) VALUES
	(1, 'Pendek', '9mm'),
	(2, 'Panjang', '5.56mm');

-- Dumping structure for table sindomondb.tbl_pangkat
CREATE TABLE IF NOT EXISTS `tbl_pangkat` (
  `pangkat_id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_pangkat` varchar(100) NOT NULL,
  PRIMARY KEY (`pangkat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table sindomondb.tbl_pangkat: ~13 rows (approximately)
INSERT INTO `tbl_pangkat` (`pangkat_id`, `nama_pangkat`) VALUES
	(1, 'Bripda'),
	(2, 'Briptu'),
	(3, 'Brigpol'),
	(4, 'Bripka'),
	(5, 'Aipda'),
	(6, 'Aiptu'),
	(7, 'Ipda'),
	(8, 'Iptu'),
	(9, 'AKP'),
	(10, 'Kompol'),
	(11, 'AKBP'),
	(12, 'Kombes Pol'),
	(13, 'Irjen Pol');

-- Dumping structure for table sindomondb.tbl_personil
CREATE TABLE IF NOT EXISTS `tbl_personil` (
  `personil_id` varchar(36) NOT NULL,
  `nrp` varchar(20) NOT NULL,
  `nama_lengkap` varchar(200) NOT NULL,
  `polres_id` int(11) DEFAULT NULL,
  `status_aktif` enum('Aktif','Mutasi','Pensiun') DEFAULT 'Aktif',
  `pangkat_id` int(11) NOT NULL,
  `jabatan_id` int(11) NOT NULL,
  `polda_id` int(11) NOT NULL,
  PRIMARY KEY (`personil_id`),
  UNIQUE KEY `nrp` (`nrp`),
  KEY `pangkat_id` (`pangkat_id`),
  KEY `jabatan_id` (`jabatan_id`),
  KEY `polda_id` (`polda_id`),
  KEY `polres_id` (`polres_id`),
  CONSTRAINT `1` FOREIGN KEY (`pangkat_id`) REFERENCES `tbl_pangkat` (`pangkat_id`),
  CONSTRAINT `2` FOREIGN KEY (`jabatan_id`) REFERENCES `tbl_jabatan` (`jabatan_id`),
  CONSTRAINT `3` FOREIGN KEY (`polda_id`) REFERENCES `tbl_polda` (`id`),
  CONSTRAINT `4` FOREIGN KEY (`polres_id`) REFERENCES `tbl_polres` (`polres_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table sindomondb.tbl_personil: ~25 rows (approximately)
INSERT INTO `tbl_personil` (`personil_id`, `nrp`, `nama_lengkap`, `polres_id`, `status_aktif`, `pangkat_id`, `jabatan_id`, `polda_id`) VALUES
	('12e8acaf-6291-492e-b06e-d9a615e55a0d', 'NRP2024024', 'Personel 24', 47, 'Aktif', 5, 4, 24),
	('174fd8cb-3ce9-4749-8be1-e724a3547a82', 'NRP2024009', 'Personel 9', 11, 'Aktif', 11, 5, 6),
	('18be78a1-62a9-4dd6-ba83-47617b53e9e3', 'NRP2024011', 'Personel 11', 18, 'Aktif', 4, 5, 9),
	('19b48579-c25f-4381-9713-919a6c9529cb', 'NRP2024025', 'Personel 25', 10, 'Aktif', 1, 4, 5),
	('23ea878b-f0a1-4ee6-b091-c82bb55b86c5', 'NRP2024019', 'Personel 19', 55, 'Aktif', 8, 5, 28),
	('2cd9a99d-99df-47fe-b293-8e2c4bfef9bd', 'NRP2024020', 'Personel 20', 68, 'Aktif', 10, 5, 34),
	('3c66b55d-1c47-4878-b04b-6c3f2af597e7', 'NRP2024014', 'Personel 14', 36, 'Aktif', 13, 5, 18),
	('5eef6ecb-daf3-4b67-9366-9054d317ec55', 'NRP2024023', 'Personel 23', 68, 'Aktif', 11, 4, 34),
	('66041406-ba59-4b7f-8a67-5254177bc87f', 'NRP2024002', 'Personel 2', 18, 'Aktif', 13, 5, 9),
	('665bde5c-e4f6-4bc2-bdf6-0f4c0606e679', 'NRP2024015', 'Personel 15', 72, 'Aktif', 3, 5, 36),
	('6f0897bb-b241-4248-a226-edc9f271c1ef', 'NRP2024008', 'Personel 8', 20, 'Aktif', 3, 5, 10),
	('83b5f217-e5e1-4b29-a914-f41af1205c48', 'NRP2024010', 'Personel 10', 19, 'Aktif', 11, 5, 10),
	('83d701e0-0231-4e45-aca1-aa2cd6ebd379', 'NRP2024005', 'Personel 5', 27, 'Aktif', 13, 5, 14),
	('8e6a79e0-a164-43e9-ac7a-8c8c61bdfe99', 'NRP2024012', 'Personel 12', 19, 'Aktif', 2, 5, 10),
	('9c4215a2-d837-4dc6-ac1c-acca77d6ca3d', 'NRP2024022', 'Personel 22', 26, 'Aktif', 10, 4, 13),
	('ab27c575-b7ff-4008-b598-fff101f4a5dc', 'NRP2024007', 'Personel 7', 13, 'Aktif', 9, 5, 7),
	('b5c0db05-670d-46c0-a5ed-2dfef72f8529', 'NRP2024004', 'Personel 4', 67, 'Aktif', 13, 5, 34),
	('be285f30-9628-4ce1-b20e-82ca34775edb', 'NRP2024003', 'Personel 3', 16, 'Aktif', 12, 5, 8),
	('bfee08c0-8e69-4241-a711-31e293987093', 'NRP2024017', 'Personel 17', 2, 'Aktif', 11, 5, 1),
	('d6e6c150-4cf1-40c9-bf0e-7d1dbc1dd839', 'NRP2024013', 'Personel 13', 2, 'Aktif', 1, 5, 1),
	('d812e3db-a341-4b21-8180-b40152bd8245', 'NRP2024001', 'Personel 1', 67, 'Aktif', 1, 5, 34),
	('e2ed5abb-8d4d-4623-8eea-be6d924c4ad3', 'NRP2024016', 'Personel 16', 3, 'Aktif', 5, 5, 2),
	('e83d795e-be3a-40d4-a201-9155904db199', 'NRP2024006', 'Personel 6', 20, 'Aktif', 1, 5, 10),
	('ef6943b2-bf55-41f2-b66d-c54196e4f683', 'NRP2024021', 'Personel 21', 10, 'Aktif', 12, 4, 5),
	('f18a1ae3-d892-417a-977a-9770978fa9df', 'NRP2024018', 'Personel 18', 25, 'Aktif', 2, 5, 13);

-- Dumping structure for table sindomondb.tbl_polda
CREATE TABLE IF NOT EXISTS `tbl_polda` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_polda` varchar(100) NOT NULL,
  `latitude` varchar(100) DEFAULT NULL,
  `longitude` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Dumping data for table sindomondb.tbl_polda: ~38 rows (approximately)
INSERT INTO `tbl_polda` (`id`, `nama_polda`, `latitude`, `longitude`, `created_at`) VALUES
	(1, 'Polda Aceh', '5.550000', '95.316666', '2026-07-20 06:14:45'),
	(2, 'Polda Sumatera Utara', '3.583333', '98.666667', '2026-07-20 06:14:45'),
	(3, 'Polda Sumatera Barat', '-0.916667', '100.366667', '2026-07-20 06:14:45'),
	(4, 'Polda Riau', '0.533333', '101.450000', '2026-07-20 06:14:45'),
	(5, 'Polda Kepulauan Riau', '0.916667', '104.450000', '2026-07-20 06:14:45'),
	(6, 'Polda Jambi', '-1.583333', '103.616667', '2026-07-20 06:14:45'),
	(7, 'Polda Sumatera Selatan', '-2.983333', '104.750000', '2026-07-20 06:14:45'),
	(8, 'Polda Bangka Belitung', '-2.133333', '106.116667', '2026-07-20 06:14:45'),
	(9, 'Polda Bengkulu', '-3.800000', '102.266667', '2026-07-20 06:14:45'),
	(10, 'Polda Lampung', '-5.416667', '105.250000', '2026-07-20 06:14:45'),
	(11, 'Polda Metro Jaya', '-6.200000', '106.816666', '2026-07-20 06:14:45'),
	(12, 'Polda Banten', '-6.116667', '106.150000', '2026-07-20 06:14:45'),
	(13, 'Polda Jawa Barat', '-6.914744', '107.609810', '2026-07-20 06:14:45'),
	(14, 'Polda Jawa Tengah', '-6.983333', '110.366667', '2026-07-20 06:14:45'),
	(15, 'Polda D.I. Yogyakarta', '-7.800000', '110.366667', '2026-07-20 06:14:45'),
	(16, 'Polda Jawa Timur', '-7.250000', '112.750000', '2026-07-20 06:14:45'),
	(17, 'Polda Kalimantan Barat', '-0.016667', '109.350000', '2026-07-20 06:14:45'),
	(18, 'Polda Kalimantan Tengah', '-2.216667', '113.916667', '2026-07-20 06:14:45'),
	(19, 'Polda Kalimantan Selatan', '-3.316667', '114.583333', '2026-07-20 06:14:45'),
	(20, 'Polda Kalimantan Timur', '-0.500000', '117.150000', '2026-07-20 06:14:45'),
	(21, 'Polda Kalimantan Utara', '3.000000', '116.533333', '2026-07-20 06:14:45'),
	(22, 'Polda Bali', '-8.550000', '115.266667', '2026-07-20 06:14:45'),
	(23, 'Polda Nusa Tenggara Barat', '-8.583333', '116.116667', '2026-07-20 06:14:45'),
	(24, 'Polda Nusa Tenggara Timur', '-10.166667', '123.583333', '2026-07-20 06:14:45'),
	(25, 'Polda Sulawesi Utara', '1.483333', '124.850000', '2026-07-20 06:14:45'),
	(26, 'Polda Gorontalo', '0.533333', '123.066667', '2026-07-20 06:14:45'),
	(27, 'Polda Sulawesi Tengah', '-0.900000', '119.850000', '2026-07-20 06:14:45'),
	(28, 'Polda Sulawesi Selatan', '-5.133333', '119.416667', '2026-07-20 06:14:45'),
	(29, 'Polda Sulawesi Tenggara', '-3.966667', '122.516667', '2026-07-20 06:14:45'),
	(30, 'Polda Sulawesi Barat', '-2.683333', '118.900000', '2026-07-20 06:14:45'),
	(31, 'Polda Maluku', '-3.700000', '128.166667', '2026-07-20 06:14:45'),
	(32, 'Polda Maluku Utara', '0.783333', '127.366667', '2026-07-20 06:14:45'),
	(33, 'Polda Papua', '-2.533333', '140.716667', '2026-07-20 06:14:45'),
	(34, 'Polda Papua Barat', '-0.866667', '134.083333', '2026-07-20 06:14:45'),
	(35, 'Polda Papua Selatan', '-8.500000', '140.400000', '2026-07-20 06:14:45'),
	(36, 'Polda Papua Tengah', '-3.350000', '135.500000', '2026-07-20 06:14:45'),
	(37, 'Polda Papua Pegunungan', '-4.100000', '138.950000', '2026-07-20 06:14:45'),
	(38, 'Polda Papua Barat Daya', '-0.866667', '131.250000', '2026-07-20 06:14:45');

-- Dumping structure for table sindomondb.tbl_polres
CREATE TABLE IF NOT EXISTS `tbl_polres` (
  `polres_id` int(11) NOT NULL AUTO_INCREMENT,
  `polda_id` int(11) NOT NULL,
  `nama_polres` varchar(200) NOT NULL,
  PRIMARY KEY (`polres_id`),
  KEY `polda_id` (`polda_id`),
  CONSTRAINT `1` FOREIGN KEY (`polda_id`) REFERENCES `tbl_polda` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=77 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Dumping data for table sindomondb.tbl_polres: ~76 rows (approximately)
INSERT INTO `tbl_polres` (`polres_id`, `polda_id`, `nama_polres`) VALUES
	(1, 1, 'Polrestabes 1.1'),
	(2, 1, 'Polres 1.2'),
	(3, 2, 'Polrestabes 2.1'),
	(4, 2, 'Polres 2.2'),
	(5, 3, 'Polrestabes 3.1'),
	(6, 3, 'Polres 3.2'),
	(7, 4, 'Polrestabes 4.1'),
	(8, 4, 'Polres 4.2'),
	(9, 5, 'Polrestabes 5.1'),
	(10, 5, 'Polres 5.2'),
	(11, 6, 'Polrestabes 6.1'),
	(12, 6, 'Polres 6.2'),
	(13, 7, 'Polrestabes 7.1'),
	(14, 7, 'Polres 7.2'),
	(15, 8, 'Polrestabes 8.1'),
	(16, 8, 'Polres 8.2'),
	(17, 9, 'Polrestabes 9.1'),
	(18, 9, 'Polres 9.2'),
	(19, 10, 'Polrestabes 10.1'),
	(20, 10, 'Polres 10.2'),
	(21, 11, 'Polrestabes 11.1'),
	(22, 11, 'Polres 11.2'),
	(23, 12, 'Polrestabes 12.1'),
	(24, 12, 'Polres 12.2'),
	(25, 13, 'Polrestabes 13.1'),
	(26, 13, 'Polres 13.2'),
	(27, 14, 'Polrestabes 14.1'),
	(28, 14, 'Polres 14.2'),
	(29, 15, 'Polrestabes 15.1'),
	(30, 15, 'Polres 15.2'),
	(31, 16, 'Polrestabes 16.1'),
	(32, 16, 'Polres 16.2'),
	(33, 17, 'Polrestabes 17.1'),
	(34, 17, 'Polres 17.2'),
	(35, 18, 'Polrestabes 18.1'),
	(36, 18, 'Polres 18.2'),
	(37, 19, 'Polrestabes 19.1'),
	(38, 19, 'Polres 19.2'),
	(39, 20, 'Polrestabes 20.1'),
	(40, 20, 'Polres 20.2'),
	(41, 21, 'Polrestabes 21.1'),
	(42, 21, 'Polres 21.2'),
	(43, 22, 'Polrestabes 22.1'),
	(44, 22, 'Polres 22.2'),
	(45, 23, 'Polrestabes 23.1'),
	(46, 23, 'Polres 23.2'),
	(47, 24, 'Polrestabes 24.1'),
	(48, 24, 'Polres 24.2'),
	(49, 25, 'Polrestabes 25.1'),
	(50, 25, 'Polres 25.2'),
	(51, 26, 'Polrestabes 26.1'),
	(52, 26, 'Polres 26.2'),
	(53, 27, 'Polrestabes 27.1'),
	(54, 27, 'Polres 27.2'),
	(55, 28, 'Polrestabes 28.1'),
	(56, 28, 'Polres 28.2'),
	(57, 29, 'Polrestabes 29.1'),
	(58, 29, 'Polres 29.2'),
	(59, 30, 'Polrestabes 30.1'),
	(60, 30, 'Polres 30.2'),
	(61, 31, 'Polrestabes 31.1'),
	(62, 31, 'Polres 31.2'),
	(63, 32, 'Polrestabes 32.1'),
	(64, 32, 'Polres 32.2'),
	(65, 33, 'Polrestabes 33.1'),
	(66, 33, 'Polres 33.2'),
	(67, 34, 'Polrestabes 34.1'),
	(68, 34, 'Polres 34.2'),
	(69, 35, 'Polrestabes 35.1'),
	(70, 35, 'Polres 35.2'),
	(71, 36, 'Polrestabes 36.1'),
	(72, 36, 'Polres 36.2'),
	(73, 37, 'Polrestabes 37.1'),
	(74, 37, 'Polres 37.2'),
	(75, 38, 'Polrestabes 38.1'),
	(76, 38, 'Polres 38.2');

-- Dumping structure for table sindomondb.tbl_proses_hukum
CREATE TABLE IF NOT EXISTS `tbl_proses_hukum` (
  `hukum_id` int(11) NOT NULL AUTO_INCREMENT,
  `personil_id` varchar(36) NOT NULL,
  `klasifikasi` enum('Pemeriksaan Propam','Sidang Kode Etik','Sidang Disiplin','Pidana Umum') NOT NULL,
  `status_hukum` varchar(100) NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `deskripsi_kasus` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`hukum_id`),
  KEY `idx_personil_id` (`personil_id`),
  CONSTRAINT `fk_proses_hukum_personil` FOREIGN KEY (`personil_id`) REFERENCES `tbl_personil` (`personil_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table sindomondb.tbl_proses_hukum: ~2 rows (approximately)
INSERT INTO `tbl_proses_hukum` (`hukum_id`, `personil_id`, `klasifikasi`, `status_hukum`, `tanggal_mulai`, `deskripsi_kasus`, `created_at`) VALUES
	(1, 'd812e3db-a341-4b21-8180-b40152bd8245', 'Pemeriksaan Propam', 'Dalam Penyelidikan', '2026-07-19', 'Kasus disiplin simulasi seeder — 1', '2026-07-20 13:14:45'),
	(2, '66041406-ba59-4b7f-8a67-5254177bc87f', 'Sidang Kode Etik', 'Dalam Penyelidikan', '2026-07-19', 'Kasus disiplin simulasi seeder — 2', '2026-07-20 13:14:45');

-- Dumping structure for table sindomondb.tbl_role
CREATE TABLE IF NOT EXISTS `tbl_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `roles` varchar(100) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Dumping data for table sindomondb.tbl_role: ~2 rows (approximately)
INSERT INTO `tbl_role` (`id`, `roles`, `created_at`) VALUES
	(1, 'Super Admin', '2026-07-11 00:00:00'),
	(2, 'Operator Polda', '2026-07-12 00:00:00'),
	(3, 'Eksekutif', '2026-07-14 11:25:28');

-- Dumping structure for table sindomondb.tbl_satwa
CREATE TABLE IF NOT EXISTS `tbl_satwa` (
  `satwa_id` int(11) NOT NULL AUTO_INCREMENT,
  `polda_id` int(11) NOT NULL,
  `nomor_registrasi` varchar(100) NOT NULL,
  `jenis_satwa` varchar(100) NOT NULL,
  `nama_satwa` varchar(200) NOT NULL,
  `nama_handler` varchar(200) NOT NULL,
  `kualifikasi` varchar(200) NOT NULL,
  `jadwal_vaksin` date DEFAULT NULL,
  `foto_url` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`satwa_id`),
  UNIQUE KEY `nomor_registrasi` (`nomor_registrasi`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Dumping data for table sindomondb.tbl_satwa: ~0 rows (approximately)

-- Dumping structure for table sindomondb.tbl_senjata
CREATE TABLE IF NOT EXISTS `tbl_senjata` (
  `senjata_id` varchar(36) NOT NULL,
  `nomor_seri` varchar(100) NOT NULL,
  `kategori_id` int(11) NOT NULL,
  `polda_id` int(11) NOT NULL,
  `tahun_pengadaan` year(4) NOT NULL,
  `status_kelayakan` enum('Layak','Tidak Layak','Dalam Perbaikan') NOT NULL DEFAULT 'Layak',
  `foto_url` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`senjata_id`),
  UNIQUE KEY `nomor_seri` (`nomor_seri`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Dumping data for table sindomondb.tbl_senjata: ~2 rows (approximately)
INSERT INTO `tbl_senjata` (`senjata_id`, `nomor_seri`, `kategori_id`, `polda_id`, `tahun_pengadaan`, `status_kelayakan`, `foto_url`, `created_at`, `updated_at`) VALUES
	('05efad39-dd82-404e-b16a-b29efa4f120a', 'SNJ-00-2024-002', 2, 1, '2024', '', 'https://placehold.co/400x300?text=Senjata+2', '2026-07-20 13:14:45', NULL),
	('d2f78084-a820-4b26-b289-dcb4a9113416', 'SNJ-00-2024-001', 1, 1, '2024', '', 'https://placehold.co/400x300?text=Senjata+1', '2026-07-20 13:14:45', NULL);

-- Dumping structure for table sindomondb.tbl_sitkamtibmas
CREATE TABLE IF NOT EXISTS `tbl_sitkamtibmas` (
  `sitkamtibmas_id` varchar(36) NOT NULL,
  `polda_id` int(11) NOT NULL,
  `deskripsi_kejadian` text DEFAULT NULL,
  `level_kritis` enum('Aman','Waspada','Darurat') NOT NULL,
  `foto_tkp_url` varchar(500) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`sitkamtibmas_id`),
  KEY `idx_polda_id` (`polda_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Dumping data for table sindomondb.tbl_sitkamtibmas: ~2 rows (approximately)
INSERT INTO `tbl_sitkamtibmas` (`sitkamtibmas_id`, `polda_id`, `deskripsi_kejadian`, `level_kritis`, `foto_tkp_url`, `created_at`) VALUES
	('2030d5af-e982-4e54-804c-fe07fd2d6ce2', 1, 'Situasi kondusif — laporan rutin.', 'Aman', 'https://placehold.co/400x300?text=Aman', '2026-07-20 06:14:45'),
	('38c13949-5de0-42ae-bd6e-1202bf9f0480', 1, 'Laporan Darurat — Tes Trigger Command Center.', 'Darurat', 'https://placehold.co/400x300?text=Darurat', '2026-07-20 06:14:45');

-- Dumping structure for table sindomondb.tbl_users
CREATE TABLE IF NOT EXISTS `tbl_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `roles_id` int(11) DEFAULT NULL,
  `polda_id` int(11) DEFAULT NULL,
  `uuid` varchar(100) DEFAULT NULL,
  `token` varchar(100) DEFAULT NULL,
  `expired` varchar(50) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_polda_id` (`polda_id`)
) ENGINE=InnoDB AUTO_INCREMENT=76 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- Dumping data for table sindomondb.tbl_users: ~6 rows (approximately)
INSERT INTO `tbl_users` (`id`, `username`, `password`, `roles_id`, `polda_id`, `uuid`, `token`, `expired`, `created_at`) VALUES
	(4, 'admin', '$2y$10$IDgbgGycDFkbdsJ/SZYLge.jo/0lWOHR0RINxo5.tptPAKzRuAWwW', 1, NULL, '7997324a-bce7-48b1-bfad-3008068d7ebe', '3b9ecb26465fada071efba2eab80da00', '30', '2026-07-11 16:07:10'),
	(17, 'admin_pusat', '$2y$12$kqPJBnIaT.OCbkaNGhwqjuH0GW7fBa0FISVC6SGk3yqbkIb4qiJ8u', 1, NULL, 'c8181188-85f2-402b-a442-5dd1a39c1356', '1206e43ddd889395526536d3befde35b', '365', '2026-07-15 16:29:16'),
	(18, 'operator_jabar', '$2y$12$jbR5L/pfJI6yl/LN6pJ1guSnryAIZgsU8flzyTSHDYkDEde6nC0SS', 2, 12, 'f4be3489-5957-7cf1-8a15-888aef151919', 'cb9aae83fb420faa4b31130a376d3f7c', '365', '2026-07-15 16:29:16'),
	(19, 'pimpinan_mabes', '$2y$12$k4duXcHWGXbihsq/MqxxyeraTUNz7hQ71ck2u2TxbZy585SxnHoWu', 3, NULL, '8db34e62-8949-5e47-a3de-77272a315f31', '458582a81b5f7c8730ac0e5d1683d82c', '365', '2026-07-15 16:29:16'),
	(20, 'op_test', '$2y$10$r/ZdR8xjW5zQWi6qj3pioumqknv.Q3z5VjcMYWgx1XbcK61NbwMPK', 2, 12, '1db309ee-819e-11f1-bd0c-60f81db0df76', 'e4173d827dfddf2b4ef4dbe1b02416e5', '30', '2026-07-17 12:12:32'),
	(75, 'operator_test', '$2y$10$DyMewajc80kiy4AqWicp3.2dbBwkvxPdb/0LR1pkyfvlSiibFhXXO', 2, 12, '86c1a5e5-81ab-11f1-bd0c-60f81db0df76', 'testtoken', '30', '2026-07-17 13:48:32');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
