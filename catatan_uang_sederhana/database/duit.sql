<br />
<b>Warning</b>:  Undefined variable $F in <b>C:\laragon\www\adminer.php</b> on line <b>1147</b><br />
-- Adminer 4.8.1 MySQL 8.0.30 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `pemasukan`;
CREATE TABLE `pemasukan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `deskripsi` varchar(255) NOT NULL,
  `jumlah` int NOT NULL,
  `tanggal` date NOT NULL,
  `bukti` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `pemasukan` (`id`, `deskripsi`, `jumlah`, `tanggal`, `bukti`, `created_at`, `updated_at`) VALUES
(1,	'Gaji',	5000000,	'2025-06-01',	'684a0fab1d137.jpg',	'2025-06-11 23:02:21',	'2025-06-12 14:15:55'),
(5,	'transfer bulanan',	5000000,	'2025-06-01',	'684ae70d7c77a.jpg',	'2025-06-12 14:41:17',	'2025-06-12 14:41:17');

DROP TABLE IF EXISTS `pengeluaran`;
CREATE TABLE `pengeluaran` (
  `id` int NOT NULL AUTO_INCREMENT,
  `deskripsi` varchar(255) NOT NULL,
  `jumlah` int NOT NULL,
  `tanggal` date NOT NULL,
  `bukti` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `pengeluaran` (`id`, `deskripsi`, `jumlah`, `tanggal`, `bukti`, `created_at`, `updated_at`) VALUES
(2,	'Belanja Mingguan',	500000,	'2025-06-11',	'684a0fa2a868c.jpg',	'2025-06-11 23:02:21',	'2025-06-11 23:22:10'),
(4,	'top up',	1000000,	'2025-06-12',	'684ae16620d00.jpg',	'2025-06-11 23:23:20',	'2025-06-12 14:17:10'),
(5,	'listrik mingguan',	500000,	'2025-06-12',	'684ae6e863e43.jpg',	'2025-06-12 14:16:55',	'2025-06-12 14:40:40');

DROP TABLE IF EXISTS `transaksi`;
CREATE TABLE `transaksi` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tipe_transaksi` enum('pemasukan','pengeluaran') NOT NULL,
  `deskripsi` varchar(255) NOT NULL,
  `jumlah` int NOT NULL,
  `tanggal` date NOT NULL,
  `bukti` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `transaksi` (`id`, `tipe_transaksi`, `deskripsi`, `jumlah`, `tanggal`, `bukti`, `created_at`, `updated_at`) VALUES
(2,	'pemasukan',	'transfer bulanan',	3000001,	'2025-06-05',	'bukti.jpg',	'2025-06-11 12:45:10',	'2025-06-11 14:26:56'),
(3,	'pemasukan',	'Bonus Proyek',	1000000,	'2025-06-10',	'bukti.jpg',	'2025-06-11 12:45:10',	'2025-06-11 12:53:16'),
(7,	'pengeluaran',	'top up',	500000,	'2025-06-11',	'bub.jpg',	'2025-06-11 14:27:27',	'2025-06-11 14:27:27');

-- 2025-06-12 14:46:28
