-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 18 Jun 2025 pada 11.06
-- Versi server: 11.4.7-MariaDB-cll-lve
-- Versi PHP: 8.3.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+07:00"; -- Jakarta timezone


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kodr8873_perumahan`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `aktivitas`
--

CREATE TABLE `aktivitas` (
  `aktivitas_id` varchar(12) NOT NULL,
  `users_id` varchar(6) DEFAULT NULL,
  `tamu_id` varchar(7) DEFAULT NULL,
  `judul` varchar(255) NOT NULL,
  `sub_judul` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `aktivitas`
--

INSERT INTO `aktivitas` (`aktivitas_id`, `users_id`, `tamu_id`, `judul`, `sub_judul`, `created_at`, `updated_at`) VALUES
('AKT-YR3ZMN87', NULL, 'LXGHHNR', 'Kunjungan Baru', 'Tamu Richard Lionheart mengajukan kunjungan ke RT 1 No.001 Samping Bakso Pak Sutejo 2 dengan tujuan: jenguk sakit', '2025-06-15 21:03:48', '2025-06-15 21:03:48');

-- --------------------------------------------------------

--
-- Struktur dari tabel `chats`
--

CREATE TABLE `chats` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `chat_id` varchar(255) NOT NULL,
  `user1_id` varchar(6) NOT NULL,
  `user2_id` varchar(6) NOT NULL,
  `last_activity` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `chats`
--

INSERT INTO `chats` (`id`, `chat_id`, `user1_id`, `user2_id`, `last_activity`, `is_active`, `created_at`, `updated_at`) VALUES
(30, 'a227a305-20d0-4c20-9c60-8b3bba67ab6a', '202278', '230909', '2025-06-16 04:02:02', 1, '2025-06-16 04:00:46', '2025-06-16 04:02:02'),
(32, '9cd78461-89dd-4f14-bfbc-9e28d4b52e4b', '993286', '230909', '2025-06-16 04:18:27', 1, '2025-06-16 04:17:55', '2025-06-16 04:18:27'),
(34, 'c3973ac2-4896-4cfb-94b7-6a34dd4fd79c', '182199', '230909', '2025-06-16 06:42:58', 1, '2025-06-16 04:55:18', '2025-06-16 06:42:58'),
(35, '3beec082-fe33-4fcc-8ea8-f7dd4eca4767', '202278', '993286', '2025-06-16 21:14:47', 1, '2025-06-16 04:56:23', '2025-06-16 21:14:47'),
(36, 'f2d42354-e941-4b49-9666-94da3c93a6a3', '182199', '202278', '2025-06-16 21:11:35', 1, '2025-06-16 04:57:09', '2025-06-16 21:11:35'),
(37, '27b73d7b-c949-44d7-a92f-6866d5c5d0d9', '182199', '993287', '2025-06-16 07:33:43', 1, '2025-06-16 06:49:35', '2025-06-16 07:33:43');

-- --------------------------------------------------------

--
-- Struktur dari tabel `datarumah`
--

CREATE TABLE `datarumah` (
  `rumah_id` varchar(255) NOT NULL,
  `users_id` varchar(6) NOT NULL,
  `warga_id1` varchar(255) DEFAULT NULL,
  `warga_id2` varchar(255) DEFAULT NULL,
  `warga_id3` varchar(255) DEFAULT NULL,
  `warga_id4` varchar(255) DEFAULT NULL,
  `warga_id5` varchar(255) DEFAULT NULL,
  `blok_rt` varchar(255) NOT NULL,
  `status_kepemilikan` varchar(255) NOT NULL,
  `alamat_cluster` varchar(255) NOT NULL,
  `no_kk` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `datarumah`
--

INSERT INTO `datarumah` (`rumah_id`, `users_id`, `warga_id1`, `warga_id2`, `warga_id3`, `warga_id4`, `warga_id5`, `blok_rt`, `status_kepemilikan`, `alamat_cluster`, `no_kk`, `created_at`, `updated_at`) VALUES
('RMH001', '993286', '93U2IWA', NULL, NULL, NULL, NULL, '2', 'Milik Pribadi', 'RT 2 No.001 Samping Bakso Pak Sutejo', '3201052501210005', '2025-06-15 07:58:30', '2025-06-15 21:22:46'),
('RMH004', '182199', 'BG3GKJK', NULL, NULL, NULL, NULL, '1', 'Milik Pribadi', 'No.1 RT 1 blok C01', '3215336172174001', '2025-06-16 01:02:54', '2025-06-16 01:02:54');

-- --------------------------------------------------------

--
-- Struktur dari tabel `datasatpam`
--

CREATE TABLE `datasatpam` (
  `users_id` varchar(255) NOT NULL,
  `nik` varchar(255) NOT NULL,
  `tanggal_lahir` varchar(255) NOT NULL,
  `no_kep` varchar(255) NOT NULL,
  `seksi_unit_gerbang` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `datasatpam`
--

INSERT INTO `datasatpam` (`users_id`, `nik`, `tanggal_lahir`, `no_kep`, `seksi_unit_gerbang`, `created_at`, `updated_at`) VALUES
('993287', '3218036122174003', '1991-07-16', '001/BAC/2025', 'Gerbang Utama', '2025-06-15 20:56:03', '2025-06-15 20:56:03');

-- --------------------------------------------------------

--
-- Struktur dari tabel `datawarga`
--

CREATE TABLE `datawarga` (
  `warga_id` varchar(255) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `nik` varchar(20) NOT NULL,
  `no_kk` varchar(20) DEFAULT NULL,
  `domisili_ktp` varchar(255) NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `gender` enum('Laki-laki','Perempuan') NOT NULL,
  `agama` varchar(255) NOT NULL,
  `status_pernikahan` varchar(255) NOT NULL,
  `pekerjaan` varchar(255) NOT NULL,
  `pendidikan_terakhir` varchar(255) NOT NULL,
  `foto_ktp` varchar(255) DEFAULT NULL,
  `foto_kk` varchar(255) DEFAULT NULL,
  `blok_rt` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `datawarga`
--

INSERT INTO `datawarga` (`warga_id`, `nama`, `nik`, `no_kk`, `domisili_ktp`, `tanggal_lahir`, `gender`, `agama`, `status_pernikahan`, `pekerjaan`, `pendidikan_terakhir`, `foto_ktp`, `foto_kk`, `blok_rt`, `created_at`, `updated_at`) VALUES
('93U2IWA', 'Fauzan Nur Hafidz', '3215032112040004', '3201052501210005', 'Karawang', '2004-12-21', 'Laki-laki', 'Islam', 'Menikah', 'Pelajar/Mahasiswa', 'SMK/SMA SEDERAJAT', 'ktp_684edf4b2237f.png', 'kk_684edf4b22707.png', '2', '2025-06-15 07:57:15', '2025-06-15 07:57:15'),
('BG3GKJK', 'Ahmad Sofyan', '3218336172174002', '3215336172174001', 'Karawang', '1993-10-16', 'Laki-laki', 'Islam', 'Menikah', 'PNS', 'S1 Sederajat', 'ktp_684fcf59d6225.jpg', 'kk_684fcf59d6a35.jpg', '1', '2025-06-16 01:01:29', '2025-06-16 01:01:29');

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_tamu`
--

CREATE TABLE `detail_tamu` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tamu_id` varchar(7) NOT NULL,
  `nik` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `tempat_lahir` varchar(50) NOT NULL,
  `tgl_lahir` date NOT NULL,
  `kewarganegaraan` varchar(30) NOT NULL,
  `alamat` varchar(255) NOT NULL,
  `rt` varchar(5) NOT NULL,
  `rw` varchar(5) NOT NULL,
  `kel_desa` varchar(50) NOT NULL,
  `kecamatan` varchar(50) NOT NULL,
  `kabupaten` varchar(50) NOT NULL,
  `agama` varchar(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `detail_tamu`
--

INSERT INTO `detail_tamu` (`id`, `tamu_id`, `nik`, `nama`, `tempat_lahir`, `tgl_lahir`, `kewarganegaraan`, `alamat`, `rt`, `rw`, `kel_desa`, `kecamatan`, `kabupaten`, `agama`, `created_at`, `updated_at`) VALUES
(1, 'LXGHHNR', '32183502112050005', 'Richard Lionheart', 'Bekasi', '2005-02-11', 'WNI', 'TanjungDuren', '2', '10', 'Soedarjo', 'Bekasi Barat', 'bekasi', 'Islam', '2025-06-15 21:02:51', '2025-06-15 21:02:51');

-- --------------------------------------------------------

--
-- Struktur dari tabel `jadwal_kerja_satpam`
--

CREATE TABLE `jadwal_kerja_satpam` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `users_id` varchar(255) NOT NULL,
  `tanggal` date NOT NULL,
  `shift` enum('pagi','siang','malam','libur') NOT NULL,
  `jam_mulai` time DEFAULT NULL,
  `jam_selesai` time DEFAULT NULL,
  `lokasi` varchar(255) NOT NULL DEFAULT 'Pos Utama',
  `lokasi_detail` varchar(255) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `jadwal_kerja_satpam`
--

INSERT INTO `jadwal_kerja_satpam` (`id`, `users_id`, `tanggal`, `shift`, `jam_mulai`, `jam_selesai`, `lokasi`, `lokasi_detail`, `catatan`, `is_active`, `created_at`, `updated_at`) VALUES
(31, '993287', '2025-06-01', 'pagi', '06:00:00', '14:00:00', 'Gerbang Utama', 'Gerbang Utama Cluster Bukit Asri', NULL, 1, '2025-06-16 05:17:03', '2025-06-16 05:17:03'),
(32, '993287', '2025-06-02', 'pagi', '06:00:00', '14:00:00', 'Gerbang Utama', 'Gerbang Utama Cluster Bukit Asri', NULL, 1, '2025-06-16 05:17:03', '2025-06-16 05:17:03'),
(33, '993287', '2025-06-03', 'pagi', '06:00:00', '14:00:00', 'Gerbang Utama', 'Gerbang Utama Cluster Bukit Asri', NULL, 1, '2025-06-16 05:17:03', '2025-06-16 05:17:03'),
(34, '993287', '2025-06-04', 'siang', '14:00:00', '22:00:00', 'Gerbang Utama', 'Gerbang Utama Cluster Bukit Asri', NULL, 1, '2025-06-16 05:17:03', '2025-06-16 05:17:03'),
(35, '993287', '2025-06-05', 'siang', '14:00:00', '22:00:00', 'Gerbang Utama', 'Gerbang Utama Cluster Bukit Asri', NULL, 1, '2025-06-16 05:17:03', '2025-06-16 05:17:03'),
(36, '993287', '2025-06-06', 'siang', '14:00:00', '22:00:00', 'Gerbang Utama', 'Gerbang Utama Cluster Bukit Asri', NULL, 1, '2025-06-16 05:17:03', '2025-06-16 05:17:03'),
(37, '993287', '2025-06-07', 'malam', '22:00:00', '06:00:00', 'Gerbang Utama', 'Gerbang Utama Cluster Bukit Asri', 'Lakukan patroli setiap 2 jam sekali', 1, '2025-06-16 05:17:03', '2025-06-16 05:17:03'),
(38, '993287', '2025-06-08', 'malam', '22:00:00', '06:00:00', 'Gerbang Utama', 'Gerbang Utama Cluster Bukit Asri', 'Lakukan patroli setiap 2 jam sekali', 1, '2025-06-16 05:17:03', '2025-06-16 05:17:03'),
(39, '993287', '2025-06-09', 'malam', '22:00:00', '06:00:00', 'Gerbang Utama', 'Gerbang Utama Cluster Bukit Asri', 'Lakukan patroli setiap 2 jam sekali', 1, '2025-06-16 05:17:03', '2025-06-16 05:17:03'),
(40, '993287', '2025-06-10', 'pagi', '06:00:00', '14:00:00', 'Gerbang Utama', 'Gerbang Utama Cluster Bukit Asri', NULL, 1, '2025-06-16 05:17:03', '2025-06-16 05:17:03'),
(41, '993287', '2025-06-11', 'pagi', '06:00:00', '14:00:00', 'Gerbang Utama', 'Gerbang Utama Cluster Bukit Asri', NULL, 1, '2025-06-16 05:17:03', '2025-06-16 05:17:03'),
(42, '993287', '2025-06-12', 'pagi', '06:00:00', '14:00:00', 'Gerbang Utama', 'Gerbang Utama Cluster Bukit Asri', NULL, 1, '2025-06-16 05:17:03', '2025-06-16 05:17:03'),
(43, '993287', '2025-06-13', 'siang', '14:00:00', '22:00:00', 'Gerbang Utama', 'Gerbang Utama Cluster Bukit Asri', NULL, 1, '2025-06-16 05:17:03', '2025-06-16 05:17:03'),
(44, '993287', '2025-06-14', 'siang', '14:00:00', '22:00:00', 'Gerbang Utama', 'Gerbang Utama Cluster Bukit Asri', NULL, 1, '2025-06-16 05:17:03', '2025-06-16 05:17:03'),
(45, '993287', '2025-06-15', 'siang', '14:00:00', '22:00:00', 'Gerbang Utama', 'Gerbang Utama Cluster Bukit Asri', NULL, 1, '2025-06-16 05:17:03', '2025-06-16 05:17:03'),
(46, '993287', '2025-06-16', 'malam', '22:00:00', '06:00:00', 'Gerbang Utama', 'Gerbang Utama Cluster Bukit Asri', 'Lakukan patroli setiap 2 jam sekali', 1, '2025-06-16 05:17:03', '2025-06-16 05:17:03'),
(47, '993287', '2025-06-17', 'malam', '22:00:00', '06:00:00', 'Gerbang Utama', 'Gerbang Utama Cluster Bukit Asri', 'Lakukan patroli setiap 2 jam sekali', 1, '2025-06-16 05:17:03', '2025-06-16 05:17:03'),
(48, '993287', '2025-06-18', 'malam', '22:00:00', '06:00:00', 'Gerbang Utama', 'Gerbang Utama Cluster Bukit Asri', 'Lakukan patroli setiap 2 jam sekali', 1, '2025-06-16 05:17:03', '2025-06-16 05:17:03'),
(49, '993287', '2025-06-19', 'pagi', '06:00:00', '14:00:00', 'Gerbang Utama', 'Gerbang Utama Cluster Bukit Asri', NULL, 1, '2025-06-16 05:17:03', '2025-06-16 05:17:03'),
(50, '993287', '2025-06-20', 'pagi', '06:00:00', '14:00:00', 'Gerbang Utama', 'Gerbang Utama Cluster Bukit Asri', NULL, 1, '2025-06-16 05:17:03', '2025-06-16 05:17:03'),
(51, '993287', '2025-06-21', 'pagi', '06:00:00', '14:00:00', 'Gerbang Utama', 'Gerbang Utama Cluster Bukit Asri', NULL, 1, '2025-06-16 05:17:03', '2025-06-16 05:17:03'),
(52, '993287', '2025-06-22', 'siang', '14:00:00', '22:00:00', 'Gerbang Utama', 'Gerbang Utama Cluster Bukit Asri', NULL, 1, '2025-06-16 05:17:03', '2025-06-16 05:17:03'),
(53, '993287', '2025-06-23', 'siang', '14:00:00', '22:00:00', 'Gerbang Utama', 'Gerbang Utama Cluster Bukit Asri', NULL, 1, '2025-06-16 05:17:03', '2025-06-16 05:17:03'),
(54, '993287', '2025-06-24', 'siang', '14:00:00', '22:00:00', 'Gerbang Utama', 'Gerbang Utama Cluster Bukit Asri', NULL, 1, '2025-06-16 05:17:03', '2025-06-16 05:17:03'),
(55, '993287', '2025-06-25', 'malam', '22:00:00', '06:00:00', 'Gerbang Utama', 'Gerbang Utama Cluster Bukit Asri', 'Lakukan patroli setiap 2 jam sekali', 1, '2025-06-16 05:17:03', '2025-06-16 05:17:03'),
(56, '993287', '2025-06-26', 'malam', '22:00:00', '06:00:00', 'Gerbang Utama', 'Gerbang Utama Cluster Bukit Asri', 'Lakukan patroli setiap 2 jam sekali', 1, '2025-06-16 05:17:03', '2025-06-16 05:17:03'),
(57, '993287', '2025-06-27', 'malam', '22:00:00', '06:00:00', 'Gerbang Utama', 'Gerbang Utama Cluster Bukit Asri', 'Lakukan patroli setiap 2 jam sekali', 1, '2025-06-16 05:17:03', '2025-06-16 05:17:03'),
(58, '993287', '2025-06-28', 'pagi', '06:00:00', '14:00:00', 'Gerbang Utama', 'Gerbang Utama Cluster Bukit Asri', NULL, 1, '2025-06-16 05:17:03', '2025-06-16 05:17:03'),
(59, '993287', '2025-06-29', 'pagi', '06:00:00', '14:00:00', 'Gerbang Utama', 'Gerbang Utama Cluster Bukit Asri', NULL, 1, '2025-06-16 05:17:03', '2025-06-16 05:17:03'),
(60, '993287', '2025-06-30', 'pagi', '06:00:00', '14:00:00', 'Gerbang Utama', 'Gerbang Utama Cluster Bukit Asri', NULL, 1, '2025-06-16 05:17:03', '2025-06-16 05:17:03');

-- --------------------------------------------------------

--
-- Struktur dari tabel `keamanan`
--

CREATE TABLE `keamanan` (
  `users_id` varchar(6) NOT NULL,
  `pin` varchar(255) DEFAULT NULL,
  `hint` varchar(255) DEFAULT NULL,
  `pin_active` enum('aktif','nonaktif') NOT NULL DEFAULT 'nonaktif',
  `login_pin_active` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `keamanan`
--

INSERT INTO `keamanan` (`users_id`, `pin`, `hint`, `pin_active`, `login_pin_active`, `created_at`, `updated_at`) VALUES
('182199', '$2y$12$X0j36BiG/TXFjx76lxuK/e5eVNSJRsSibP/pZeBc8XhknXDlfTRoG', 'Karawang', 'aktif', 'nonaktif', '2025-06-16 04:51:35', '2025-06-16 06:41:10');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kunjungan`
--

CREATE TABLE `kunjungan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kunjungan_id` varchar(12) NOT NULL,
  `tamu_id` varchar(7) NOT NULL,
  `rumah_id` varchar(255) NOT NULL,
  `tujuan_kunjungan` varchar(255) NOT NULL,
  `status_kunjungan` enum('Menunggu Menuju Cluster','Sedang Berlangsung','Meninggalkan Cluster') NOT NULL DEFAULT 'Menunggu Menuju Cluster',
  `waktu_masuk` timestamp NULL DEFAULT NULL,
  `waktu_keluar` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `kunjungan`
--

INSERT INTO `kunjungan` (`id`, `kunjungan_id`, `tamu_id`, `rumah_id`, `tujuan_kunjungan`, `status_kunjungan`, `waktu_masuk`, `waktu_keluar`, `created_at`, `updated_at`) VALUES
(1, 'KJG-FO6BPN8P', 'LXGHHNR', 'RMH001', 'jenguk sakit', 'Sedang Berlangsung', '2025-06-16 05:19:35', NULL, '2025-06-15 21:03:48', '2025-06-16 05:19:35');

-- --------------------------------------------------------

--
-- Struktur dari tabel `messages`
--

CREATE TABLE `messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `message_id` varchar(255) NOT NULL,
  `chat_id` bigint(20) UNSIGNED NOT NULL,
  `sender_id` varchar(6) NOT NULL,
  `message` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `document_path` varchar(255) DEFAULT NULL,
  `document_name` varchar(255) DEFAULT NULL,
  `document_type` varchar(255) DEFAULT NULL,
  `document_size` varchar(255) DEFAULT NULL,
  `reply_to` bigint(20) UNSIGNED DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `messages`
--

INSERT INTO `messages` (`id`, `message_id`, `chat_id`, `sender_id`, `message`, `image_path`, `document_path`, `document_name`, `document_type`, `document_size`, `reply_to`, `is_read`, `created_at`, `updated_at`, `deleted_at`) VALUES
(149, 'f2a3d685-7f70-4ead-a27d-506aae018156', 30, '202278', 'pak', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-16 04:01:00', '2025-06-16 04:01:11', NULL),
(150, '2e6fecfa-0853-481b-aabd-cfe6046bbd20', 30, '230909', 'p', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-16 04:02:02', '2025-06-16 04:02:02', NULL),
(161, 'a526a716-9f84-49a7-b931-79278ef57222', 32, '993286', 'pak', NULL, NULL, NULL, NULL, NULL, NULL, 0, '2025-06-16 04:18:02', '2025-06-16 04:18:02', NULL),
(162, 'd7b3470a-e3d2-4c1d-88b9-5ec3b93de178', 32, '993286', 'pak abi bade ka kantor bada magriban atau bada isya', NULL, NULL, NULL, NULL, NULL, NULL, 0, '2025-06-16 04:18:27', '2025-06-16 04:18:27', NULL),
(165, 'fd24dc72-2a62-443e-b47f-5c946eda944f', 34, '182199', 'atest', NULL, NULL, NULL, NULL, NULL, NULL, 0, '2025-06-16 04:55:24', '2025-06-16 04:55:24', NULL),
(166, 'eaf65996-8b1e-4ea0-9475-bb8fe75611d6', 34, '182199', 'test', NULL, NULL, NULL, NULL, NULL, NULL, 0, '2025-06-16 04:55:28', '2025-06-16 04:55:28', NULL),
(167, '060af99e-dbb9-4ba4-8147-41f965752af8', 35, '202278', 'test', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-16 04:56:33', '2025-06-16 10:20:02', '2025-06-16 10:20:02'),
(168, '13adcbb4-4202-4875-8ff2-c121f9b87e40', 36, '182199', 'pak', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-16 04:57:13', '2025-06-16 04:57:22', NULL),
(169, 'a52497be-3b1b-4a0a-a33b-1c9488f9868e', 36, '202278', 'yaa test balik', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-16 04:58:13', '2025-06-16 04:58:28', NULL),
(170, '33e878da-64ce-4bcf-88a5-78ea199cd4d9', 36, '182199', 'ok siap', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-16 04:58:32', '2025-06-16 04:58:37', NULL),
(171, '0f6d4665-f84b-444f-a2b0-2fc7cd9f4a6e', 36, '182199', 'pp', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-16 06:41:53', '2025-06-16 21:11:32', NULL),
(172, '12014f6e-a5b8-4789-9dc5-0054808d7f1e', 34, '182199', NULL, NULL, 'chat/documents/Screenshot_2025-06-16-18-12-40-290_com.whatsapp.jpg', 'Screenshot_2025-06-16-18-12-40-290_com.whatsapp.jpg', 'jpg', '19 Bytes', NULL, 0, '2025-06-16 06:42:33', '2025-06-16 06:42:33', NULL),
(173, '13273152-ac63-40bf-aa8a-7ff5a23713ea', 34, '182199', 'ni pak buktinya', NULL, NULL, NULL, NULL, NULL, 172, 0, '2025-06-16 06:42:58', '2025-06-16 06:42:58', NULL),
(174, '0382ab1f-1034-43fc-8f9e-401384e65eef', 37, '182199', 'p', NULL, NULL, NULL, NULL, NULL, NULL, 0, '2025-06-16 07:33:43', '2025-06-16 07:33:43', NULL),
(175, 'abaacad9-2cf6-4d93-a9b0-064685b652c6', 35, '993286', 'test', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-16 10:20:17', '2025-06-16 21:11:21', NULL),
(176, '742f88ec-f652-4f9a-9bab-27d55c9a7133', 35, '202278', 'ya', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-16 21:11:26', '2025-06-16 21:11:45', NULL),
(177, '6b2080f8-bf2a-4f1f-9bee-f2145e016f6b', 36, '202278', 'uyy', NULL, NULL, NULL, NULL, NULL, NULL, 0, '2025-06-16 21:11:35', '2025-06-16 21:11:35', NULL),
(178, 'da188025-2ca3-4099-8392-df4de0989199', 35, '993286', 'oke', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-16 21:12:08', '2025-06-16 21:12:19', NULL),
(179, '37c7054d-d2bd-4908-b1a3-d9290db86029', 35, '993286', '☝️', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-16 21:12:37', '2025-06-16 21:12:44', NULL),
(180, 'fdffa1a0-81aa-4109-b414-471fdcf6d07c', 35, '202278', 'dwdw', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-16 21:12:51', '2025-06-16 21:13:08', NULL),
(181, 'f497a83e-b7f0-4199-b036-163dea333cee', 35, '993286', 'はい、お元気ですか', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-16 21:13:33', '2025-06-16 21:13:35', NULL),
(182, '46c25ff7-5a97-47e9-bb7e-6a4f9f628101', 35, '202278', 'あなたなら大丈夫', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-16 21:14:11', '2025-06-16 21:14:11', NULL),
(183, '20462328-e66a-42eb-9819-f3797afb8f71', 35, '993286', 'あなたなら大丈夫', NULL, NULL, NULL, NULL, NULL, 182, 1, '2025-06-16 21:14:47', '2025-06-16 21:14:50', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2025_04_28_102315_create_users', 1),
(2, '2025_04_28_103111_create_sessions_table', 1),
(3, '2025_04_28_160756_make_rt_blok_nullable_in_users_table', 1),
(4, '2025_04_29_100638_create_tamu', 1),
(5, '2025_05_07_142344_create_datasatpam', 1),
(6, '2025_05_08_143553_create_datawarga', 1),
(7, '2025_05_09_070735_create_datarumah', 1),
(8, '2025_05_11_023629_create_personal_access_tokens_table', 1),
(9, '2025_05_13_162235_create_suratpengajuan', 1),
(10, '2025_05_24_170035_create_otp', 1),
(11, '2025_05_24_170036_create_otp_email', 1),
(12, '2025_05_25_151702_create_cache_table', 1),
(13, '2025_05_27_152856_create_pengaduan', 1),
(14, '2025_05_29_094942_create_keamanan', 1),
(15, '2025_06_02_095350_create_notifikasi', 1),
(16, '2025_06_05_130323_create_jadwal-kerja-satpam', 1),
(17, '2025_06_05_155000_modify_jadwal_kerja_satpam_nullable_columns', 1),
(18, '2025_06_09_061559_create_detail_tamu', 1),
(19, '2025_06_10_100637_create_kunjungan', 1),
(20, '2025_06_12_124812_create_aktivitas', 1),
(23, '2025_06_15_084216_create_chats_table', 2),
(24, '2025_06_15_084242_create_messages_table', 2),
(25, '2025_06_15_152228_add_online_status_columns_to_users_table', 3);

-- --------------------------------------------------------

--
-- Struktur dari tabel `notifikasi`
--

CREATE TABLE `notifikasi` (
  `id_notifikasi` bigint(20) UNSIGNED NOT NULL,
  `users_id` varchar(6) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `sub_judul` varchar(255) NOT NULL,
  `halaman` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `otp`
--

CREATE TABLE `otp` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `users_id` varchar(255) NOT NULL,
  `no_hp` varchar(255) NOT NULL,
  `otp_code` varchar(10) NOT NULL,
  `is_used` tinyint(1) NOT NULL DEFAULT 0,
  `expired_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `otp`
--

INSERT INTO `otp` (`id`, `users_id`, `no_hp`, `otp_code`, `is_used`, `expired_at`, `created_at`, `updated_at`) VALUES
(1, '993286', '083879258721', '834096', 0, '2025-06-16 04:48:11', '2025-06-16 04:43:11', '2025-06-16 04:43:11');

-- --------------------------------------------------------

--
-- Struktur dari tabel `otp_email`
--

CREATE TABLE `otp_email` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `users_id` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `otp_code` varchar(10) NOT NULL,
  `is_used` tinyint(1) NOT NULL DEFAULT 0,
  `expired_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `otp_email`
--

INSERT INTO `otp_email` (`id`, `users_id`, `email`, `otp_code`, `is_used`, `expired_at`, `created_at`, `updated_at`) VALUES
(1, '993286', 'komputerhobi@gmail.com', '775142', 0, '2025-06-16 04:47:58', '2025-06-16 04:42:58', '2025-06-16 04:42:58'),
(2, '182199', 'komputerhobi@gmail.com', '117975', 1, '2025-06-16 11:51:09', '2025-06-16 04:50:48', '2025-06-16 04:51:09');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengaduan`
--

CREATE TABLE `pengaduan` (
  `pengaduan_id` varchar(10) NOT NULL,
  `users_id` bigint(20) UNSIGNED NOT NULL,
  `jenis_pengaduan` varchar(32) NOT NULL,
  `detail_pengaduan` text NOT NULL,
  `lokasi` varchar(255) DEFAULT NULL,
  `status_pengaduan` enum('Tersampaikan','Dibaca RT') NOT NULL DEFAULT 'Tersampaikan',
  `dokumen1` varchar(255) DEFAULT NULL,
  `dokumen2` varchar(255) DEFAULT NULL,
  `remark` text DEFAULT NULL,
  `blok_rt` varchar(32) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\User', 993286, 'auth_token', '6d091eb94df9ff2ae8d1de1d8a6778813ed114eb79da571fbbb9091b4b939be6', '[\"*\"]', NULL, NULL, '2025-06-15 07:59:05', '2025-06-15 07:59:05'),
(2, 'App\\Models\\User', 993286, 'auth_token', 'f1f9cf0b03e57373dc79fd32557cefd0d7cd9574f4e10343f7f66783909c82ad', '[\"*\"]', NULL, NULL, '2025-06-15 20:58:42', '2025-06-15 20:58:42'),
(3, 'App\\Models\\User', 993287, 'auth_token', 'c53c5817bec593d50c7a670175a98af1130c0c44eaa57aae2ed25ede935430ce', '[\"*\"]', NULL, NULL, '2025-06-15 20:58:57', '2025-06-15 20:58:57'),
(4, 'App\\Models\\User', 993286, 'auth_token', '5147baeb5667c4ee36b0b938c64692d1c824493efe0c53890265a16cbdaa021f', '[\"*\"]', NULL, NULL, '2025-06-16 00:26:05', '2025-06-16 00:26:05'),
(5, 'App\\Models\\User', 993286, 'auth_token', 'd570e42d8b0b1131fc285779048a8549d6b47f40fc8df474887ea497e6fab827', '[\"*\"]', NULL, NULL, '2025-06-16 00:26:16', '2025-06-16 00:26:16'),
(6, 'App\\Models\\User', 993286, 'auth_token', '580d13c70e88fdcc1e269b460df653ba0760564d0e52e62ea194be095c41d255', '[\"*\"]', '2025-06-16 04:02:43', NULL, '2025-06-16 00:29:16', '2025-06-16 04:02:43'),
(7, 'App\\Models\\User', 182199, 'auth_token', '4c3d61079a3fa2056c685c4457fe76f72db5ccaa9ab5946da9aa47f209ae7de8', '[\"*\"]', '2025-06-16 04:02:43', NULL, '2025-06-16 01:03:42', '2025-06-16 04:02:43'),
(8, 'App\\Models\\User', 993286, 'auth_token', '4fa59826e7b1a890ed3989e9006053497f61ff0d97c6a844dff28353a348b368', '[\"*\"]', NULL, NULL, '2025-06-16 04:08:38', '2025-06-16 04:08:38'),
(9, 'App\\Models\\User', 993286, 'auth_token', '3d46b68fcebc68f6194edb8c5824a1020fece6046a4898dff165a4b21c425eec', '[\"*\"]', '2025-06-16 04:47:05', NULL, '2025-06-16 04:10:57', '2025-06-16 04:47:05'),
(10, 'App\\Models\\User', 182199, 'auth_token', 'b87c22187aa4f0035237afc9fac3e66a6210ea475e37af5a3de6630330949dc0', '[\"*\"]', '2025-06-16 07:01:59', NULL, '2025-06-16 04:13:05', '2025-06-16 07:01:59'),
(11, 'App\\Models\\User', 182199, 'auth_token', 'f2000a6729314d9897b8cf037104f8c128b51b7c77ad1fe5ac3a048f6f399c89', '[\"*\"]', NULL, NULL, '2025-06-16 04:47:23', '2025-06-16 04:47:23'),
(12, 'App\\Models\\User', 182199, 'auth_token', '3aaeaca3825de6a29dac4eeaec410c2e12d7261acd2d1329bf58c5c692806e08', '[\"*\"]', '2025-06-16 04:51:39', NULL, '2025-06-16 04:47:38', '2025-06-16 04:51:39'),
(13, 'App\\Models\\User', 182199, 'auth_token', '183bdceeccea1592e1e03486d998cb6694b69b4e9b56c904ee540676abf783b0', '[\"*\"]', '2025-06-16 05:14:16', NULL, '2025-06-16 04:53:06', '2025-06-16 05:14:16'),
(14, 'App\\Models\\User', 993287, 'auth_token', '670d82e141a823fce5819512353ec9505a2409206b6d0936661f1c993b4cb8a9', '[\"*\"]', '2025-06-16 05:19:46', NULL, '2025-06-16 05:15:22', '2025-06-16 05:19:46'),
(15, 'App\\Models\\User', 182199, 'auth_token', 'dc73b3d438eb562ceff1edbb28cd7de1d181f345bcc8c2b9d5527dd02c8ebdea', '[\"*\"]', '2025-06-16 07:33:47', NULL, '2025-06-16 05:19:57', '2025-06-16 07:33:47'),
(16, 'App\\Models\\User', 993287, 'auth_token', 'efabeabbbb9ec63334a8fba6d6b56614467fc2433c2fd89f4456e8b4be5e7d6b', '[\"*\"]', '2025-06-16 07:34:35', NULL, '2025-06-16 07:34:06', '2025-06-16 07:34:35'),
(17, 'App\\Models\\User', 993286, 'auth_token', 'fada93135b09fa9a168a25f3d6cbca61e21adbe84f15242045a15859dd32fb75', '[\"*\"]', '2025-06-17 00:35:54', NULL, '2025-06-16 10:18:34', '2025-06-17 00:35:54');

-- --------------------------------------------------------

--
-- Struktur dari tabel `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('1jgCkVEndaW1ROyiHBDyOgojq1GKu3awj4zYIOvs', NULL, '2001:470:1:332::109', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoidDdzZXBvUTRXYVRSR2dkYkUzazVXRURuRnA0eFdKanFWT0REOUthciI7czoxMToibG9naW5fdG9rZW4iO3M6MzI6IjYwYzFhN2JjMGQ4NzRlYjQ5MmQ0MzJiMDM4NjhmOTMxIjtzOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czozMToiaHR0cDovL1syMDAxOmRmMDoyN2I6Mzo6Mzo2MmFhXSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1750164004),
('3pGmxik3tvv4wrL0PZPKrUlxC1NNr0l7YzA9hW08', NULL, '110.138.81.173', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Mobile Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicWxqVkpVQjVYUURDaXFDOTJwSDNkQ2JjMkR3eFpKT2hXNm04S0pZeCI7czoxMToibG9naW5fdG9rZW4iO3M6MzI6IjdkNzBiNGZjMWZjMzJlODg1ZDUyNWMzY2Q3MDAzZmJhIjtzOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1750218986),
('4cEFpAhqwn9VFOwwru8HELVjkDB9frosZbJGwtgr', NULL, '147.185.132.129', 'Expanse, a Palo Alto Networks company, searches across the global IPv4 space multiple times per day to identify customers&#39; presences on the Internet. If you would like to be excluded from our scans, please send IP addresses/domains to: scaninfo@paloaltonetworks.com', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiOUZpb056TTlPR1VkdnNJOWRIblJ5UkN2YURXbGlpN2V0Q0hWUWxPMSI7czoxMToibG9naW5fdG9rZW4iO3M6MzI6IjAwNjYxODYxOTZlMjZhMzNlNTIyYmJlYTVhMGRlMmEwIjtzOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czoyMDoiaHR0cDovL2tvZGViaXQubXkuaWQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1750187510),
('5jP1MAKjeGjccTPDeVJhBk60kDfppambuJPWVIyY', NULL, '2602:80d:1000::17', '', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiQzFsSm5kSHBjZEpYVGg2RzVmSHRnb3prQU9zV3B1bkdqNTNHSW9iOCI7czoxMToibG9naW5fdG9rZW4iO3M6MzI6IjFjNWQ0MjVlMTFkNDMyYmQ5MTZiOWZlNTg1MjM5MzkzIjtzOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czoyMDoiaHR0cDovL2tvZGViaXQubXkuaWQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1750171590),
('5vz9SSqpNuuqGntrp9o95FWYKjLKMmchcTv21J9B', NULL, '2620:96:e000::108', 'Mozilla/5.0 (compatible; CensysInspect/1.1; +https://about.censys.io/)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiNEl4VVdsMHBlUGozcmo5Z0p1cUZFMUdQZmhISVk5TEUySEVQT2pUWCI7czoxMToibG9naW5fdG9rZW4iO3M6MzI6ImE0YmY4MzEwNTcwY2I0MzAyOWZiZDQ5ZjkzZWVjOGNhIjtzOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9bMjAwMTpkZjA6MjdiOjM6OjM6NjJhYV0iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1750171677),
('5YI8rIoq62PgS6gKzsAGRAosx40cgGLJY2MDGFlz', NULL, '110.138.81.173', 'Mozilla/5.0 (Linux; Android 15; 23122PCD1G Build/AQ3A.240912.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/137.0.7151.89 Mobile Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoib3BvY0xNTXNCQmZROGhsVVJJSFhzSXZjVE8ya0hsRk02aGVXaTZtbiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDk6Imh0dHBzOi8va29kZWJpdC5teS5pZC9hcGkvcnVtYWgvUk1IMDAxL3RhbXUvdG9kYXkiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1750145598),
('8SX61yUR4mxLVRYRv1fFJmxVpFiGdOY3iqKJWlJt', NULL, '2602:80d:1003::1c', '', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiM3hCeW1TSFpCRUc0ejBVN21FcGx1VHhlSWZLaEh6RFNsMzQwNjhRbSI7czoxMToibG9naW5fdG9rZW4iO3M6MzI6IjE5YTNhMzY2MzRkMDY3ZjAxM2IwMTU0ZDhkYjRhNTBmIjtzOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czoyMDoiaHR0cDovL2tvZGViaXQubXkuaWQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1750176919),
('9e1amZr5gOPG8NqBh1WKyS7PjmV37kpXS2IHpjnE', NULL, '110.138.81.173', 'Mozilla/5.0 (Linux; Android 15; 23122PCD1G Build/AQ3A.240912.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/137.0.7151.89 Mobile Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoia0ZXb2RRREdoVWpqUk42SzdOcTViR3RZYmlJcFg3VUE0SkVvU2d0OCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDY6Imh0dHBzOi8va29kZWJpdC5teS5pZC9hcGkvcnVtYWgvUk1IMDAxL2FuZ2dvdGEiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1750145598),
('ahxqUBvsQu5SYxZtcwS7mQyADrKSgHYGEcTZRESz', NULL, '2602:80d:1003::1c', 'Mozilla/5.0 (compatible; CensysInspect/1.1; +https://about.censys.io/)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiVFhReEJZMHlTQUJFQkZ2MTB5eVJDNm16R3ZvT0FFbG05VmM3bWluaCI7czoxMToibG9naW5fdG9rZW4iO3M6MzI6IjdjODlmOTZmM2M2MjhkMDY2MmE4NjcxY2M1MWVlZjZhIjtzOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czozMToiaHR0cDovL1syMDAxOmRmMDoyN2I6Mzo6Mzo2MmFhXSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1750176909),
('BOMkcjrU8AXiHqqGjlxhHHZnCO5wLeThAJHHsvyE', NULL, '110.138.81.173', 'Mozilla/5.0 (Linux; Android 15; 23122PCD1G Build/AQ3A.240912.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/137.0.7151.89 Mobile Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTDNvSzBjYmNmMlFqRmpUZVFLc3V6ZER6cG5nOW40VkFOWnJzRWdvaCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Njg6Imh0dHBzOi8va29kZWJpdC5teS5pZC9hcGkvd2FyZ2Evc3VyYXQtc3RhdHMtYnktcnVtYWg/cnVtYWhfaWQ9Uk1IMDAxIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1750145598),
('CUX4fMrndHzZQcneAKUU8yuhYpjHwShJmFE7sr9F', NULL, '2602:80d:1000::17', 'Mozilla/5.0 (compatible; CensysInspect/1.1; +https://about.censys.io/)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiRHZOd09yYk9ZaEFRQ2xXYWdDeVVqTG5qSjMwQkdlbUY5WFRBbUFZNCI7czoxMToibG9naW5fdG9rZW4iO3M6MzI6IjQ5YTA1ZTMzYWRkYjE5Y2FhZmVlNjJlZDczN2UzZTU0IjtzOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czozMToiaHR0cDovL1syMDAxOmRmMDoyN2I6Mzo6Mzo2MmFhXSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1750171558),
('DgmyYju6B9a81v4H4T8Mnp8k6QV8AOmfUEP7HGv9', NULL, '2602:80d:1004::19', 'Mozilla/5.0 (compatible; CensysInspect/1.1; +https://about.censys.io/)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiaE5DSU44b0VnNkhsRlBKVEhDTTY4Z3ptMkxhaWtyRnpoVG8wcGJtUSI7czoxMToibG9naW5fdG9rZW4iO3M6MzI6IjZmNzA5MzUwZGNjMTJjM2E2YjE2ZTNkZGRmM2QwMDFkIjtzOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czozMjoiaHR0cHM6Ly9bMjAwMTpkZjA6MjdiOjM6OjM6NjJhYV0iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1750170822),
('E7lH7xcCOmhgTgzlm0e6dzZ4MBTVg60pjey2KJDk', NULL, '110.138.81.173', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiQzdxQ3dXU21QWUtKZWpNZ24xRUxVN1BiZEJVTmRkYlBVNDg0eXpHTSI7czoxMToibG9naW5fdG9rZW4iO3M6MzI6ImJmZjRkYTYwZTE5OTdlZjZlOTBkNGUwNDZkODI1MTgyIjtzOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czoyMToiaHR0cHM6Ly9rb2RlYml0Lm15LmlkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1750147253),
('FFa4qeEkofrMfIRaOl4WFNzyxSYoX7a1UEzkCLC2', NULL, '110.138.81.173', 'Mozilla/5.0 (Linux; Android 15; 23122PCD1G Build/AQ3A.240912.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/137.0.7151.89 Mobile Safari/537.36', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiTk1hWktzRnZ1d2llMDk4SjVXZWlEU0tvSjlLbTBhWlU2TUpOVDVWcSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1750145598),
('hVbxgQ7EKUrZPlTZXDJVfFld7yW0tloiKD4742Lk', NULL, '110.138.81.173', 'Mozilla/5.0 (Linux; Android 15; 23122PCD1G Build/AQ3A.240912.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/137.0.7151.89 Mobile Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiYzJzVUJ1SmdobGhvak8yWDFOcXV6aGptSEdEOUc0ejRJYWRFZ080ZiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDQ6Imh0dHBzOi8va29kZWJpdC5teS5pZC9hcGkvd2FyZ2EvOTkzMjg2L3J1bWFoIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1750145598),
('kZjdVybpRcMfjJQC84C7roJkR7aaRvlOCNj0wNgx', NULL, '2602:80d:1000::17', '', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiRVNha2FiQkw5czg4VnFIamlrbWVZdEhrRXUzZGpnM2laRTlvUnpXSyI7czoxMToibG9naW5fdG9rZW4iO3M6MzI6IjYyMjc1NzA3NzIzMjIzZmI1NGI4MjJhNmE2YzUzOTc1IjtzOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czoyMDoiaHR0cDovL2tvZGViaXQubXkuaWQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1750171577),
('NfQc4p4wIOL8yjpImjyDN657M9HrUZM7sDYGq9AK', NULL, '2602:80d:1000::17', 'Mozilla/5.0 (compatible; CensysInspect/1.1; +https://about.censys.io/)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiczZabFhRS2t4TmtWN1pDU3hWeHhMaWNsQUdsNTRJaFhsb3dvMUluMyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly9bMjAwMTpkZjA6MjdiOjM6OjM6NjJhYV0vbG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1750171596),
('nTExMeSoB5CVFDoD6Z5QIpnLMuwZCUgdT4GilId1', NULL, '110.138.81.173', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Mobile Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoieUNINUVrWnJLT1l1YnFpdnYyVDFobHFKZm16a1g5dWdHS05vWTlKQSI7czoxMToibG9naW5fdG9rZW4iO3M6MzI6ImU5MzhiZWY2OTliOWI5MThkYzczOTQ3MTMzYWIxZjI0IjtzOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1750156717),
('OUCmGbpVJNXvr50AQ0H9KdqBWt3JTg1M3TMKGS8p', NULL, '2a06:4882:3000::35', 'Mozilla/5.0 (compatible; InternetMeasurement/1.0; +https://internet-measurement.com/)', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoia2dEUW5idEwzemhwbkVDUlYzSUFDdXBxd3NFajBWeHVjT1BQOWFsZiI7czoxMToibG9naW5fdG9rZW4iO3M6MzI6IjYwNzJkY2M1MTgzZjNhNWY0YTQzZjQ1MjA3NjNmYzkzIjtzOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czozMToiaHR0cDovL1syMDAxOmRmMDoyN2I6Mzo6Mzo2MmFhXSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1750146980),
('RX8OULstQ9oin5OhuIyhVVjlm6HSW1fzQF4gLD08', NULL, '110.138.81.173', 'Mozilla/5.0 (Linux; Android 15; 23122PCD1G Build/AQ3A.240912.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/137.0.7151.89 Mobile Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNUloUE03RWFVSDZCTEZveEFuYWp0N3hSS3p4TVlGWUVDSWZTU05ObiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzA6Imh0dHBzOi8va29kZWJpdC5teS5pZC9hcGkvcGluZyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1750145596),
('UEkzblX7U9eLUNkCjy7p7HHMHSopqbx5S3ZyMSmD', NULL, '110.138.81.173', 'Mozilla/5.0 (Linux; Android 15; 23122PCD1G Build/AQ3A.240912.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/137.0.7151.89 Mobile Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVmxiOHNWeWtlUWVlMDZRN1JwbTl2U05LZ2RVR1h4TmRSdnV5aUlxNiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzA6Imh0dHBzOi8va29kZWJpdC5teS5pZC9hcGkvcGluZyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1750140501),
('uxGPbSTC1n1Nj2oDAjy2fqvvvX6vQACSWw6Sh7rL', NULL, '2602:80d:1003::1c', '', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoieTJ5WDF6cVpLYzJpazdtM0Z2TGIwOXFYT1ZIWWRsODIxWUNKUmdZNiI7czoxMToibG9naW5fdG9rZW4iO3M6MzI6ImY2NjVmMjI1ZTJmNjBjOGI5ZTc3ZmMxN2M5MDk5NjAyIjtzOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czoyMDoiaHR0cDovL2tvZGViaXQubXkuaWQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1750176915),
('vEYR4fa4xKE2l1K9vugcHZ6pQRiOB5tTap37jOqC', NULL, '110.138.81.173', 'Mozilla/5.0 (Linux; Android 15; 23122PCD1G Build/AQ3A.240912.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/137.0.7151.89 Mobile Safari/537.36', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiaEV0SXRJN2lpbGwyTkZXUTg3RkhsWHNtZUg1NEFDZmV2M0JtTWh4QyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1750140501),
('vWs8S4Sx1LdAZ716JB2Uq4Z5F8fH8EOpVWgCOyiO', NULL, '110.138.81.173', 'Mozilla/5.0 (Linux; Android 15; 23122PCD1G Build/AQ3A.240912.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/137.0.7151.89 Mobile Safari/537.36', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiMFFMWEFSY2xUYzFMcmtZck90SzljUVo5OWtZOW13cnVWV1ZIcW03aCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1750145597),
('VyAZ41EIUbjbxFZrH2vmWWzhAR1RfmKyPifEFIrL', NULL, '103.247.9.9', '', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiSTFzMGhsQzhxd1NiWktkcG5JUWFSWTdPZUVOSDV6cm5JdWk5ZWJmQiI7czoxMToibG9naW5fdG9rZW4iO3M6MzI6IjEwNDI4YjEyYWMzOWRmYWE3OTI0OTg0MjNjZmQwYTQxIjtzOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czoyMDoiaHR0cDovL2tvZGViaXQubXkuaWQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1750208198),
('VYk41pzKTjWgv5sM4xaOlMQtb83XDQyVguMWXm7s', NULL, '198.235.24.66', 'Expanse, a Palo Alto Networks company, searches across the global IPv4 space multiple times per day to identify customers&#39; presences on the Internet. If you would like to be excluded from our scans, please send IP addresses/domains to: scaninfo@paloaltonetworks.com', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZGMxV2lzcWEzdDBXOEdmY0tuRXp3MGdjZXJBY25MVzNZdXhrWVp1UiI7czoxMToibG9naW5fdG9rZW4iO3M6MzI6IjQ4MjliYWU1MjUyNDA4Yzc0YjNhZmRkMTFlYjhlYTBlIjtzOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czoyMToiaHR0cHM6Ly9rb2RlYml0Lm15LmlkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1750196712),
('wO53HyuePLwMThyTVXvitsjyOH6m7PjynIJ56JIE', NULL, '110.138.81.173', 'Mozilla/5.0 (Linux; Android 15; 23122PCD1G Build/AQ3A.240912.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/137.0.7151.89 Mobile Safari/537.36', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiUDF5T2NwdVFqUWlJR3NZOHFoNHdYeWd4bzJUcXp3M3JROFZUdFV2WCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1750140502),
('XfE8T8DUP0PauGtAimrZyAZsfUuqEyfGQfTn3w0F', NULL, '110.138.81.173', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiYUppM2JPMXY0d0NPNDkzeWhXYndETDJBWG9wdTdJdHkwQUhldVExUiI7czoxMToibG9naW5fdG9rZW4iO3M6MzI6IjcwZjUxZDc0NGQ5ZDljY2MyMDIxYTk1Yzk2M2Q0NDM0IjtzOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czoyMToiaHR0cHM6Ly9rb2RlYml0Lm15LmlkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1750218986);

-- --------------------------------------------------------

--
-- Struktur dari tabel `suratpengajuan`
--

CREATE TABLE `suratpengajuan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `surat_id` varchar(255) NOT NULL,
  `warga_id` varchar(255) NOT NULL,
  `rumah_id` varchar(255) NOT NULL,
  `jenis_surat` varchar(255) NOT NULL,
  `status_penegerjaan` enum('menunggu verifikasi','sedang di validasi','disetujui','ditolak') NOT NULL DEFAULT 'menunggu verifikasi',
  `foto_ktp` varchar(255) DEFAULT NULL,
  `kartu_keluarga` varchar(255) DEFAULT NULL,
  `dokumen_lainnya1` varchar(255) DEFAULT NULL,
  `dokumen_lainnya2` varchar(255) DEFAULT NULL,
  `keperluan_keramaian` varchar(255) DEFAULT NULL,
  `tempat_keramaian` varchar(255) DEFAULT NULL,
  `tanggal_keramaian` varchar(255) DEFAULT NULL,
  `jam_keramaian` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tamu`
--

CREATE TABLE `tamu` (
  `tamu_id` varchar(7) NOT NULL,
  `no_hp` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `tamu`
--

INSERT INTO `tamu` (`tamu_id`, `no_hp`, `email`, `created_at`, `updated_at`) VALUES
('LXGHHNR', '087879566521', 'richard@mail.com', '2025-06-15 21:02:51', '2025-06-15 21:02:51');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `users_id` varchar(6) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `no_hp` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_online` tinyint(1) NOT NULL DEFAULT 0,
  `last_active` timestamp NULL DEFAULT NULL,
  `gender` enum('laki-laki','perempuan') NOT NULL,
  `role` enum('RT','Satpam','Warga','Developer') NOT NULL DEFAULT 'RT',
  `alamat` varchar(255) NOT NULL,
  `rt_blok` varchar(255) DEFAULT NULL,
  `fcm_token` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`users_id`, `nama`, `email`, `no_hp`, `password`, `is_online`, `last_active`, `gender`, `role`, `alamat`, `rt_blok`, `fcm_token`, `created_at`, `updated_at`) VALUES
('182199', 'Ahmad Sofyan', 'komputerhobi@gmail.com', '092836172821', '$2y$12$zAQlr0kUVMhXXKsrrJ5sYODsGz4OS5biDsUfTFh4kQG8NTopCV5UO', 0, NULL, 'laki-laki', 'Warga', 'Karawang', NULL, NULL, '2025-06-15 21:35:33', '2025-06-16 04:51:09'),
('202278', 'RT 1 Pak herman', 'rt1@mail.com', '12345567890', '$2y$12$zq76UPnkzUnCacIWEBnUpuEGSJz49pwOskP5rWzjn1l4BlE1bkg6K', 0, '2025-06-16 21:24:58', 'laki-laki', 'RT', 'Adiarsa barat', '1', NULL, '2025-06-15 08:10:50', '2025-06-16 21:24:58'),
('230909', 'Pak Budi RT2', 'rt2@mail.com', '085829258722', '$2y$12$t76WjlBbHiy.DP4IppeZKO5qtZijXzVvlxM/1B8AKPkNMC9iHJ/Wu', 0, '2025-06-16 04:03:25', 'laki-laki', 'RT', 'Adiarsa barat', '2', NULL, '2025-06-15 02:53:30', '2025-06-16 04:03:25'),
('993286', 'Fauzan Nur Hafidz', 'fauzan@mail.com', '083879258721', '$2y$12$qkxze.jQ8qrDaHEMg2gbfu0q7W4hcaUoIaGTBogI42GwerWq/oxo6', 0, NULL, 'laki-laki', 'Warga', 'Adiarsa barat', NULL, NULL, '2025-06-15 07:55:20', '2025-06-15 07:59:00'),
('993287', 'Pratama Sumoto', 'pratama@mail.com', '082391638101', '$2y$12$eN0UaEi/7SksyPFojAiPAe9KcRpvXoOeXcJ5gbcO57nKZIGSVU5HG', 0, NULL, 'laki-laki', 'Satpam', 'Cikarang Barat', NULL, NULL, '2025-06-15 20:55:14', '2025-06-15 20:55:14');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `aktivitas`
--
ALTER TABLE `aktivitas`
  ADD PRIMARY KEY (`aktivitas_id`),
  ADD INDEX `aktivitas_users_id_index` (`users_id`),
  ADD INDEX `aktivitas_tamu_id_index` (`tamu_id`),
  ADD INDEX `aktivitas_created_at_index` (`created_at`);

--
-- Indeks untuk tabel `chats`
--
ALTER TABLE `chats`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `chats_chat_id_unique` (`chat_id`),
  ADD KEY `chats_user1_id_foreign` (`user1_id`),
  ADD KEY `chats_user2_id_foreign` (`user2_id`),
  ADD INDEX `chats_last_activity_index` (`last_activity`),
  ADD INDEX `chats_is_active_index` (`is_active`);

--
-- Indeks untuk tabel `datarumah`
--
ALTER TABLE `datarumah`
  ADD PRIMARY KEY (`rumah_id`),
  ADD KEY `datarumah_users_id_foreign` (`users_id`),
  ADD INDEX `datarumah_blok_rt_index` (`blok_rt`);

--
-- Indeks untuk tabel `datasatpam`
--
ALTER TABLE `datasatpam`
  ADD KEY `datasatpam_users_id_foreign` (`users_id`);

--
-- Indeks untuk tabel `datawarga`
--
ALTER TABLE `datawarga`
  ADD UNIQUE KEY `datawarga_warga_id_unique` (`warga_id`),
  ADD UNIQUE KEY `datawarga_nik_unique` (`nik`);

--
-- Indeks untuk tabel `detail_tamu`
--
ALTER TABLE `detail_tamu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `detail_tamu_tamu_id_foreign` (`tamu_id`);

--
-- Indeks untuk tabel `jadwal_kerja_satpam`
--
ALTER TABLE `jadwal_kerja_satpam`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `jadwal_kerja_satpam_users_id_tanggal_unique` (`users_id`,`tanggal`),
  ADD INDEX `jadwal_kerja_satpam_tanggal_index` (`tanggal`),
  ADD INDEX `jadwal_kerja_satpam_shift_index` (`shift`),
  ADD INDEX `jadwal_kerja_satpam_is_active_index` (`is_active`);

--
-- Indeks untuk tabel `keamanan`
--
ALTER TABLE `keamanan`
  ADD KEY `keamanan_users_id_foreign` (`users_id`);

--
-- Indeks untuk tabel `kunjungan`
--
ALTER TABLE `kunjungan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kunjungan_kunjungan_id_unique` (`kunjungan_id`),
  ADD KEY `kunjungan_tamu_id_foreign` (`tamu_id`),
  ADD KEY `kunjungan_rumah_id_foreign` (`rumah_id`),
  ADD INDEX `kunjungan_status_index` (`status_kunjungan`),
  ADD INDEX `kunjungan_waktu_masuk_index` (`waktu_masuk`),
  ADD INDEX `kunjungan_waktu_keluar_index` (`waktu_keluar`),
  ADD INDEX `kunjungan_created_at_index` (`created_at`);

--
-- Indeks untuk tabel `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `messages_message_id_unique` (`message_id`),
  ADD KEY `messages_chat_id_foreign` (`chat_id`),
  ADD KEY `messages_sender_id_foreign` (`sender_id`),
  ADD KEY `messages_reply_to_foreign` (`reply_to`),
  ADD INDEX `messages_is_read_index` (`is_read`),
  ADD INDEX `messages_created_at_index` (`created_at`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD PRIMARY KEY (`id_notifikasi`);

--
-- Indeks untuk tabel `otp`
--
ALTER TABLE `otp`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `otp_email`
--
ALTER TABLE `otp_email`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pengaduan`
--
ALTER TABLE `pengaduan`
  ADD PRIMARY KEY (`pengaduan_id`),
  ADD INDEX `pengaduan_users_id_index` (`users_id`),
  ADD INDEX `pengaduan_jenis_pengaduan_index` (`jenis_pengaduan`),
  ADD INDEX `pengaduan_status_pengaduan_index` (`status_pengaduan`),
  ADD INDEX `pengaduan_blok_rt_index` (`blok_rt`),
  ADD INDEX `pengaduan_created_at_index` (`created_at`);

--
-- Indeks untuk tabel `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indeks untuk tabel `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indeks untuk tabel `suratpengajuan`
--
ALTER TABLE `suratpengajuan`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `tamu`
--
ALTER TABLE `tamu`
  ADD PRIMARY KEY (`tamu_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`users_id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD INDEX `users_role_index` (`role`),
  ADD INDEX `users_rt_blok_index` (`rt_blok`),
  ADD INDEX `users_is_online_index` (`is_online`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `chats`
--
ALTER TABLE `chats`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT untuk tabel `detail_tamu`
--
ALTER TABLE `detail_tamu`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `jadwal_kerja_satpam`
--
ALTER TABLE `jadwal_kerja_satpam`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT untuk tabel `kunjungan`
--
ALTER TABLE `kunjungan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `messages`
--
ALTER TABLE `messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=184;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT untuk tabel `notifikasi`
--
ALTER TABLE `notifikasi`
  MODIFY `id_notifikasi` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `otp`
--
ALTER TABLE `otp`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `otp_email`
--
ALTER TABLE `otp_email`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT untuk tabel `suratpengajuan`
--
ALTER TABLE `suratpengajuan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `chats`
--
ALTER TABLE `chats`
  ADD CONSTRAINT `chats_user1_id_foreign` FOREIGN KEY (`user1_id`) REFERENCES `users` (`users_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chats_user2_id_foreign` FOREIGN KEY (`user2_id`) REFERENCES `users` (`users_id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `datarumah`
--
ALTER TABLE `datarumah`
  ADD CONSTRAINT `datarumah_users_id_foreign` FOREIGN KEY (`users_id`) REFERENCES `users` (`users_id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `datasatpam`
--
ALTER TABLE `datasatpam`
  ADD CONSTRAINT `datasatpam_users_id_foreign` FOREIGN KEY (`users_id`) REFERENCES `users` (`users_id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `detail_tamu`
--
ALTER TABLE `detail_tamu`
  ADD CONSTRAINT `detail_tamu_tamu_id_foreign` FOREIGN KEY (`tamu_id`) REFERENCES `tamu` (`tamu_id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `jadwal_kerja_satpam`
--
ALTER TABLE `jadwal_kerja_satpam`
  ADD CONSTRAINT `jadwal_kerja_satpam_users_id_foreign` FOREIGN KEY (`users_id`) REFERENCES `users` (`users_id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `keamanan`
--
ALTER TABLE `keamanan`
  ADD CONSTRAINT `keamanan_users_id_foreign` FOREIGN KEY (`users_id`) REFERENCES `users` (`users_id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `kunjungan`
--
ALTER TABLE `kunjungan`
  ADD CONSTRAINT `kunjungan_rumah_id_foreign` FOREIGN KEY (`rumah_id`) REFERENCES `datarumah` (`rumah_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kunjungan_tamu_id_foreign` FOREIGN KEY (`tamu_id`) REFERENCES `tamu` (`tamu_id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_chat_id_foreign` FOREIGN KEY (`chat_id`) REFERENCES `chats` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_reply_to_foreign` FOREIGN KEY (`reply_to`) REFERENCES `messages` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`users_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
