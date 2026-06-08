-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 04, 2026 at 01:46 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cleango`
--

-- --------------------------------------------------------

--
-- Table structure for table `app_settings`
--

CREATE TABLE `app_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(100) NOT NULL,
  `value` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `app_settings`
--

INSERT INTO `app_settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES
(1, 'antar_jemput_foto', 'settings/dmJ7icYa3hodogBYKkkUfSR7YCEcjlfKjCGV6inT.png', '2026-06-03 05:04:16', '2026-06-03 19:48:05'),
(2, 'antar_jemput_judul', 'Antar Jemput', '2026-06-03 05:04:16', '2026-06-03 19:48:05'),
(3, 'antar_jemput_desc', 'Kami siap menjemput & mengantar pakaian Anda kapanpun. Gratis Antar Jemput untuk radius maksimal 4 Km dari outlet terdekat, dengan minimal transaksi Rp 75.000', '2026-06-03 05:04:16', '2026-06-03 19:48:05');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoice`
--

CREATE TABLE `invoice` (
  `id_invoice` bigint(20) UNSIGNED NOT NULL,
  `id_bayar` bigint(20) UNSIGNED NOT NULL,
  `no_invoice` varchar(50) NOT NULL,
  `tgl_invoice` timestamp NOT NULL DEFAULT current_timestamp(),
  `nomor_wa` varchar(20) NOT NULL DEFAULT '',
  `status_kirim` enum('Belum Dikirim','Terkirim','Gagal Kirim') NOT NULL DEFAULT 'Belum Dikirim',
  `waktu_kirim` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invoice`
--

INSERT INTO `invoice` (`id_invoice`, `id_bayar`, `no_invoice`, `tgl_invoice`, `nomor_wa`, `status_kirim`, `waktu_kirim`, `created_at`, `updated_at`) VALUES
(1, 1, 'INV-20260604-001', '2026-06-04 04:02:20', '6284444444444', 'Belum Dikirim', NULL, '2026-06-04 04:02:20', NULL),
(2, 2, 'INV-20260604-002', '2026-06-04 04:38:39', '6284444444444', 'Belum Dikirim', NULL, '2026-06-04 04:38:39', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `katalog`
--

CREATE TABLE `katalog` (
  `id_katalog` bigint(20) UNSIGNED NOT NULL,
  `id_layanan` bigint(20) UNSIGNED NOT NULL,
  `jenis_layanan` varchar(100) NOT NULL DEFAULT '',
  `varian` enum('Regular','Express','Hemat') NOT NULL DEFAULT 'Regular',
  `harga` decimal(10,2) NOT NULL,
  `satuan` enum('kg','pcs') NOT NULL DEFAULT 'kg',
  `deskripsi` text DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `status` enum('Aktif','Nonaktif') NOT NULL DEFAULT 'Aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `katalog`
--

INSERT INTO `katalog` (`id_katalog`, `id_layanan`, `jenis_layanan`, `varian`, `harga`, `satuan`, `deskripsi`, `foto`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, '', 'Regular', 7000.00, 'kg', NULL, 'katalog/2h1yjswojHSDbYftecHWYk2X4BqOB8glm5UVtV1q.png', 'Aktif', '2026-06-03 05:04:16', '2026-06-04 03:26:34'),
(2, 1, '', 'Express', 12000.00, 'kg', NULL, 'katalog/HG95eW0KIpxTCyBKrrxGgKvRugfesyOb9ezcw6XP.png', 'Aktif', '2026-06-03 05:04:16', '2026-06-04 03:26:40'),
(3, 2, '', 'Regular', 10000.00, 'kg', NULL, 'katalog/z7YC4YROJzxweQeSGQNQBWSeP9eQWYiDORdwTDKM.webp', 'Aktif', '2026-06-03 05:04:16', '2026-06-04 03:26:47'),
(4, 2, '', 'Express', 15000.00, 'kg', NULL, 'katalog/ImgWTZjIEveHaX6vO38J4gCaMrAj1iztdNBys9bW.webp', 'Aktif', '2026-06-03 05:04:16', '2026-06-04 03:26:55'),
(5, 3, '', 'Regular', 6000.00, 'kg', NULL, 'katalog/wNymqmoNzT3FDzuWWNx8OZUKh1qyb6ocKOe0taXC.jpg', 'Aktif', '2026-06-03 05:04:16', '2026-06-04 03:27:04');

-- --------------------------------------------------------

--
-- Table structure for table `layanan`
--

CREATE TABLE `layanan` (
  `id_layanan` bigint(20) UNSIGNED NOT NULL,
  `nama_layanan` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `layanan`
--

INSERT INTO `layanan` (`id_layanan`, `nama_layanan`, `deskripsi`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Cuci Kering', 'Layanan cuci dan pengeringan standar', 1, '2026-06-03 05:04:16', NULL),
(2, 'Cuci Setrika', 'Layanan cuci lengkap dengan setrika', 1, '2026-06-03 05:04:16', NULL),
(3, 'Setrika Saja', 'Khusus setrika pakaian', 1, '2026-06-03 05:04:16', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `login_logs`
--

CREATE TABLE `login_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role` enum('owner','staff','customer') NOT NULL,
  `actor_id` bigint(20) UNSIGNED NOT NULL,
  `login_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `login_logs`
--

INSERT INTO `login_logs` (`id`, `role`, `actor_id`, `login_time`, `ip_address`, `user_agent`) VALUES
(1, 'customer', 1, '2026-06-03 05:05:12', '127.0.0.1', NULL),
(2, 'owner', 1, '2026-06-03 05:29:37', '127.0.0.1', NULL),
(3, 'staff', 1, '2026-06-03 05:32:56', '127.0.0.1', NULL),
(4, 'customer', 1, '2026-06-03 19:43:37', '127.0.0.1', NULL),
(5, 'owner', 1, '2026-06-03 19:46:55', '127.0.0.1', NULL),
(6, 'owner', 1, '2026-06-04 03:18:45', '127.0.0.1', NULL),
(7, 'owner', 1, '2026-06-04 03:36:35', '127.0.0.1', NULL),
(8, 'owner', 1, '2026-06-04 03:41:06', '127.0.0.1', NULL),
(9, 'staff', 2, '2026-06-04 03:42:39', '127.0.0.1', NULL),
(10, 'owner', 1, '2026-06-04 03:43:49', '127.0.0.1', NULL),
(11, 'owner', 1, '2026-06-04 03:45:49', '127.0.0.1', NULL),
(12, 'owner', 1, '2026-06-04 03:46:48', '127.0.0.1', NULL),
(13, 'customer', 1, '2026-06-04 03:49:26', '127.0.0.1', NULL),
(14, 'owner', 1, '2026-06-04 03:52:26', '127.0.0.1', NULL),
(15, 'owner', 1, '2026-06-04 03:55:05', '127.0.0.1', NULL),
(16, 'staff', 2, '2026-06-04 03:58:20', '127.0.0.1', NULL),
(17, 'customer', 1, '2026-06-04 03:58:39', '127.0.0.1', NULL),
(18, 'staff', 2, '2026-06-04 03:58:46', '127.0.0.1', NULL),
(19, 'customer', 1, '2026-06-04 04:00:40', '127.0.0.1', NULL),
(20, 'customer', 1, '2026-06-04 04:29:34', '127.0.0.1', NULL),
(21, 'customer', 1, '2026-06-04 04:32:51', '127.0.0.1', NULL),
(22, 'customer', 1, '2026-06-04 04:36:55', '127.0.0.1', NULL),
(23, 'staff', 2, '2026-06-04 04:37:48', '127.0.0.1', NULL),
(24, 'owner', 1, '2026-06-04 04:39:15', '127.0.0.1', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000001_create_cache_table', 1),
(2, '0001_01_01_000002_create_jobs_table', 1),
(3, '2024_01_01_000001_create_owner_table', 1),
(4, '2024_01_01_000002_create_staff_table', 1),
(5, '2024_01_01_000003_create_users_table', 1),
(6, '2024_01_01_000004_create_layanan_table', 1),
(7, '2024_01_01_000005_create_katalog_table', 1),
(8, '2024_01_01_000006_create_orders_table', 1),
(9, '2024_01_01_000007_create_order_detail_table', 1),
(10, '2024_01_01_000008_create_pembayaran_table', 1),
(11, '2024_01_01_000009_create_tracking_table', 1),
(12, '2024_01_01_000010_create_invoice_table', 1),
(13, '2024_01_01_000011_create_notifications_table', 1),
(14, '2024_01_01_000012_create_login_logs_table', 1),
(15, '2024_01_01_000013_add_foto_katalog_and_settings', 1),
(16, '2024_01_01_000014_hash_existing_passwords', 1),
(17, '2026_06_04_103621_create_sessions_table', 2);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role` enum('customer','staff','owner') NOT NULL,
  `actor_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `role`, `actor_id`, `title`, `message`, `link`, `is_read`, `created_at`) VALUES
(1, 'staff', 2, '📦 Order Baru Masuk!', 'Order ORD-20260604-001 dari Dhira Cust menunggu konfirmasi. Segera proses!', 'http://127.0.0.1:8000/staff/order-masuk', 0, '2026-06-04 04:00:58'),
(2, 'owner', 1, '📦 Order Baru: ORD-20260604-001', 'Customer Dhira Cust membuat order baru (ORD-20260604-001).', 'http://127.0.0.1:8000/owner/orders', 0, '2026-06-04 04:00:58'),
(3, 'customer', 1, '🚗 Laundry Sedang Dijemput!', 'Order ORD-20260604-001 sedang dijemput oleh staff kami.', 'http://127.0.0.1:8000/customer/tracking', 0, '2026-06-04 04:01:15'),
(4, 'owner', 1, '🚗 Order Dijemput: ORD-20260604-001', 'Staff Karimah menjemput laundry ORD-20260604-001 dari Dhira Cust.', 'http://127.0.0.1:8000/owner/orders', 0, '2026-06-04 04:01:15'),
(5, 'customer', 1, '💳 Tagihan Laundry Kamu Sudah Siap!', 'Order ORD-20260604-001 — Tagihan sebesar Rp 60.000 sudah dimasukkan.', 'http://127.0.0.1:8000/customer/pembayaran', 0, '2026-06-04 04:01:26'),
(6, 'owner', 1, '📊 Tagihan Dibuat: ORD-20260604-001', 'Staff Karimah memasukkan tagihan Rp 60.000 untuk Dhira Cust.', 'http://127.0.0.1:8000/owner/orders', 0, '2026-06-04 04:01:26'),
(7, 'staff', 2, '💳 Pembayaran Masuk!', 'Customer Dhira Cust sudah upload bukti bayar untuk order ORD-20260604-001.', 'http://127.0.0.1:8000/staff/konfirmasi-bayar', 0, '2026-06-04 04:01:47'),
(8, 'owner', 1, '💳 Bukti Bayar Diterima', 'Order ORD-20260604-001 — Dhira Cust mengirimkan bukti pembayaran.', 'http://127.0.0.1:8000/owner/orders', 0, '2026-06-04 04:01:47'),
(9, 'customer', 1, '✅ Pembayaran Dikonfirmasi!', 'Pembayaran untuk order ORD-20260604-001 sudah dikonfirmasi.', 'http://127.0.0.1:8000/customer/tracking', 0, '2026-06-04 04:01:56'),
(10, 'owner', 1, '✅ Bayar Lunas: ORD-20260604-001', 'Staff Karimah mengkonfirmasi pembayaran order ORD-20260604-001.', 'http://127.0.0.1:8000/owner/orders', 0, '2026-06-04 04:01:56'),
(11, 'customer', 1, '📦 Update Order: Dicuci', 'Order ORD-20260604-001 sedang dicuci. Proses laundry sedang berjalan!', 'http://127.0.0.1:8000/customer/tracking', 0, '2026-06-04 04:02:09'),
(12, 'owner', 1, '📦 Order ORD-20260604-001: Dicuci', 'Staff Karimah mengupdate status ke Dicuci.', 'http://127.0.0.1:8000/owner/orders', 0, '2026-06-04 04:02:09'),
(13, 'customer', 1, '📦 Update Order: Disetrika', 'Order ORD-20260604-001 sedang disetrika. Hampir selesai!', 'http://127.0.0.1:8000/customer/tracking', 0, '2026-06-04 04:02:13'),
(14, 'owner', 1, '📦 Order ORD-20260604-001: Disetrika', 'Staff Karimah mengupdate status ke Disetrika.', 'http://127.0.0.1:8000/owner/orders', 0, '2026-06-04 04:02:13'),
(15, 'customer', 1, '📦 Update Order: Dikirim', 'Order ORD-20260604-001 sedang dalam perjalanan ke alamatmu.', 'http://127.0.0.1:8000/customer/tracking', 0, '2026-06-04 04:02:17'),
(16, 'owner', 1, '📦 Order ORD-20260604-001: Dikirim', 'Staff Karimah mengupdate status ke Dikirim.', 'http://127.0.0.1:8000/owner/orders', 0, '2026-06-04 04:02:17'),
(17, 'customer', 1, '📦 Update Order: Selesai', 'Order ORD-20260604-001 sudah selesai! Terima kasih sudah menggunakan CleanGo.', 'http://127.0.0.1:8000/customer/tracking', 0, '2026-06-04 04:02:20'),
(18, 'owner', 1, '📦 Order ORD-20260604-001: Selesai', 'Staff Karimah mengupdate status ke Selesai.', 'http://127.0.0.1:8000/owner/orders', 0, '2026-06-04 04:02:20'),
(19, 'staff', 2, '📦 Order Baru Masuk!', 'Order ORD-20260604-002 dari Dhira Cust menunggu konfirmasi. Segera proses!', 'http://127.0.0.1:8000/staff/order-masuk', 0, '2026-06-04 04:29:55'),
(20, 'owner', 1, '📦 Order Baru: ORD-20260604-002', 'Customer Dhira Cust membuat order baru (ORD-20260604-002).', 'http://127.0.0.1:8000/owner/orders', 0, '2026-06-04 04:29:55'),
(21, 'owner', 1, '🗑️ Booking Dibatalkan: ORD-20260604-002', 'Customer Dhira Cust membatalkan booking ORD-20260604-002.', 'http://127.0.0.1:8000/owner/orders', 0, '2026-06-04 04:33:13'),
(22, 'staff', 2, '📦 Order Baru Masuk!', 'Order ORD-20260604-002 dari Dhira Cust menunggu konfirmasi. Segera proses!', 'http://127.0.0.1:8000/staff/order-masuk', 0, '2026-06-04 04:33:26'),
(23, 'owner', 1, '📦 Order Baru: ORD-20260604-002', 'Customer Dhira Cust membuat order baru (ORD-20260604-002).', 'http://127.0.0.1:8000/owner/orders', 0, '2026-06-04 04:33:26'),
(24, 'owner', 1, '🗑️ Booking Dibatalkan: ORD-20260604-002', 'Customer Dhira Cust membatalkan booking ORD-20260604-002.', 'http://127.0.0.1:8000/owner/orders', 0, '2026-06-04 04:33:40'),
(25, 'staff', 2, '📦 Order Baru Masuk!', 'Order ORD-20260604-002 dari Dhira Cust menunggu konfirmasi. Segera proses!', 'http://127.0.0.1:8000/staff/order-masuk', 0, '2026-06-04 04:34:00'),
(26, 'owner', 1, '📦 Order Baru: ORD-20260604-002', 'Customer Dhira Cust membuat order baru (ORD-20260604-002).', 'http://127.0.0.1:8000/owner/orders', 0, '2026-06-04 04:34:00'),
(27, 'owner', 1, '🗑️ Booking Dibatalkan: ORD-20260604-002', 'Customer Dhira Cust membatalkan booking ORD-20260604-002.', 'http://127.0.0.1:8000/owner/orders', 0, '2026-06-04 04:34:28'),
(28, 'staff', 2, '📦 Order Baru Masuk!', 'Order ORD-20260604-002 dari Dhira Cust menunggu konfirmasi. Segera proses!', 'http://127.0.0.1:8000/staff/order-masuk', 0, '2026-06-04 04:35:12'),
(29, 'owner', 1, '📦 Order Baru: ORD-20260604-002', 'Customer Dhira Cust membuat order baru (ORD-20260604-002).', 'http://127.0.0.1:8000/owner/orders', 0, '2026-06-04 04:35:12'),
(30, 'owner', 1, '🗑️ Booking Dibatalkan: ORD-20260604-002', 'Customer Dhira Cust membatalkan booking ORD-20260604-002.', 'http://127.0.0.1:8000/owner/orders', 0, '2026-06-04 04:35:25'),
(31, 'staff', 2, '📦 Order Baru Masuk!', 'Order ORD-20260604-002 dari Dhira Cust menunggu konfirmasi. Segera proses!', 'http://127.0.0.1:8000/staff/order-masuk', 0, '2026-06-04 04:37:23'),
(32, 'owner', 1, '📦 Order Baru: ORD-20260604-002', 'Customer Dhira Cust membuat order baru (ORD-20260604-002).', 'http://127.0.0.1:8000/owner/orders', 0, '2026-06-04 04:37:23'),
(33, 'customer', 1, '🚗 Laundry Sedang Dijemput!', 'Order ORD-20260604-002 sedang dijemput oleh staff kami.', 'http://127.0.0.1:8000/customer/tracking', 0, '2026-06-04 04:37:52'),
(34, 'owner', 1, '🚗 Order Dijemput: ORD-20260604-002', 'Staff Karimah menjemput laundry ORD-20260604-002 dari Dhira Cust.', 'http://127.0.0.1:8000/owner/orders', 0, '2026-06-04 04:37:52'),
(35, 'customer', 1, '💳 Tagihan Laundry Kamu Sudah Siap!', 'Order ORD-20260604-002 — Tagihan sebesar Rp 35.000 sudah dimasukkan.', 'http://127.0.0.1:8000/customer/pembayaran', 0, '2026-06-04 04:37:58'),
(36, 'owner', 1, '📊 Tagihan Dibuat: ORD-20260604-002', 'Staff Karimah memasukkan tagihan Rp 35.000 untuk Dhira Cust.', 'http://127.0.0.1:8000/owner/orders', 0, '2026-06-04 04:37:58'),
(37, 'staff', 2, '💳 Pembayaran Masuk!', 'Customer Dhira Cust sudah upload bukti bayar untuk order ORD-20260604-002.', 'http://127.0.0.1:8000/staff/konfirmasi-bayar', 0, '2026-06-04 04:38:12'),
(38, 'owner', 1, '💳 Bukti Bayar Diterima', 'Order ORD-20260604-002 — Dhira Cust mengirimkan bukti pembayaran.', 'http://127.0.0.1:8000/owner/orders', 0, '2026-06-04 04:38:12'),
(39, 'customer', 1, '✅ Pembayaran Dikonfirmasi!', 'Pembayaran untuk order ORD-20260604-002 sudah dikonfirmasi.', 'http://127.0.0.1:8000/customer/tracking', 0, '2026-06-04 04:38:20'),
(40, 'owner', 1, '✅ Bayar Lunas: ORD-20260604-002', 'Staff Karimah mengkonfirmasi pembayaran order ORD-20260604-002.', 'http://127.0.0.1:8000/owner/orders', 0, '2026-06-04 04:38:20'),
(41, 'customer', 1, '📦 Update Order: Dicuci', 'Order ORD-20260604-002 sedang dicuci. Proses laundry sedang berjalan!', 'http://127.0.0.1:8000/customer/tracking', 0, '2026-06-04 04:38:27'),
(42, 'owner', 1, '📦 Order ORD-20260604-002: Dicuci', 'Staff Karimah mengupdate status ke Dicuci.', 'http://127.0.0.1:8000/owner/orders', 0, '2026-06-04 04:38:27'),
(43, 'customer', 1, '📦 Update Order: Disetrika', 'Order ORD-20260604-002 sedang disetrika. Hampir selesai!', 'http://127.0.0.1:8000/customer/tracking', 0, '2026-06-04 04:38:30'),
(44, 'owner', 1, '📦 Order ORD-20260604-002: Disetrika', 'Staff Karimah mengupdate status ke Disetrika.', 'http://127.0.0.1:8000/owner/orders', 0, '2026-06-04 04:38:30'),
(45, 'customer', 1, '📦 Update Order: Dikirim', 'Order ORD-20260604-002 sedang dalam perjalanan ke alamatmu.', 'http://127.0.0.1:8000/customer/tracking', 0, '2026-06-04 04:38:34'),
(46, 'owner', 1, '📦 Order ORD-20260604-002: Dikirim', 'Staff Karimah mengupdate status ke Dikirim.', 'http://127.0.0.1:8000/owner/orders', 0, '2026-06-04 04:38:34'),
(47, 'customer', 1, '📦 Update Order: Selesai', 'Order ORD-20260604-002 sudah selesai! Terima kasih sudah menggunakan CleanGo.', 'http://127.0.0.1:8000/customer/tracking', 0, '2026-06-04 04:38:39'),
(48, 'owner', 1, '📦 Order ORD-20260604-002: Selesai', 'Staff Karimah mengupdate status ke Selesai.', 'http://127.0.0.1:8000/owner/orders', 0, '2026-06-04 04:38:39');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id_order` bigint(20) UNSIGNED NOT NULL,
  `kode_order` varchar(20) NOT NULL,
  `id_cust` bigint(20) UNSIGNED NOT NULL,
  `id_layanan` bigint(20) UNSIGNED NOT NULL,
  `id_staff` bigint(20) UNSIGNED DEFAULT NULL,
  `tanggal_pesan` datetime NOT NULL DEFAULT current_timestamp(),
  `alamat_penjemputan` text NOT NULL,
  `jadwal_jemput` datetime DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `total_harga` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status_order` enum('Menunggu Konfirmasi','Dijemput','Dicuci','Disetrika','Dikirim','Selesai','Dibatalkan') NOT NULL DEFAULT 'Menunggu Konfirmasi',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id_order`, `kode_order`, `id_cust`, `id_layanan`, `id_staff`, `tanggal_pesan`, `alamat_penjemputan`, `jadwal_jemput`, `catatan`, `total_harga`, `status_order`, `created_at`, `updated_at`) VALUES
(1, 'ORD-20260604-001', 1, 1, 2, '2026-06-04 11:00:58', 'Jl. Mawar No. 10', '2026-06-04 15:00:00', NULL, 60000.00, 'Selesai', '2026-06-04 04:00:58', '2026-06-04 04:02:20'),
(6, 'ORD-20260604-002', 1, 1, 2, '2026-06-04 11:37:23', 'Jl. Mawar No. 10', '2026-06-05 08:00:00', NULL, 35000.00, 'Selesai', '2026-06-04 04:37:23', '2026-06-04 04:38:39');

-- --------------------------------------------------------

--
-- Table structure for table `order_detail`
--

CREATE TABLE `order_detail` (
  `id_detail` bigint(20) UNSIGNED NOT NULL,
  `id_order` bigint(20) UNSIGNED NOT NULL,
  `id_katalog` bigint(20) UNSIGNED NOT NULL,
  `berat` decimal(8,2) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `harga_satuan` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_detail`
--

INSERT INTO `order_detail` (`id_detail`, `id_order`, `id_katalog`, `berat`, `qty`, `harga_satuan`, `subtotal`) VALUES
(1, 1, 2, 5.00, NULL, 12000.00, 60000.00),
(6, 6, 1, 5.00, NULL, 7000.00, 35000.00);

-- --------------------------------------------------------

--
-- Table structure for table `owner`
--

CREATE TABLE `owner` (
  `id_owner` bigint(20) UNSIGNED NOT NULL,
  `nama_owner` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `notelp_owner` varchar(20) NOT NULL,
  `sandi_owner` varchar(255) NOT NULL,
  `alamat_owner` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `owner`
--

INSERT INTO `owner` (`id_owner`, `nama_owner`, `username`, `notelp_owner`, `sandi_owner`, `alamat_owner`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Asa Owner', 'owner', '081234567890', '$2y$12$wKd.m3/lgmEw9FJCMelKTeIPKBvZalTUWi7nY5QQ9hfF/H9eG68DW', NULL, 1, '2026-06-03 05:04:16', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id_bayar` bigint(20) UNSIGNED NOT NULL,
  `id_order` bigint(20) UNSIGNED NOT NULL,
  `metode` enum('QRIS') NOT NULL DEFAULT 'QRIS',
  `jumlah` decimal(10,2) NOT NULL,
  `status_bayar` enum('Pending','Menunggu Konfirmasi','Lunas','Gagal') NOT NULL DEFAULT 'Pending',
  `bukti_transfer` varchar(255) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `waktu_bayar` timestamp NULL DEFAULT NULL,
  `dikonfirmasi_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pembayaran`
--

INSERT INTO `pembayaran` (`id_bayar`, `id_order`, `metode`, `jumlah`, `status_bayar`, `bukti_transfer`, `catatan`, `waktu_bayar`, `dikonfirmasi_oleh`, `created_at`, `updated_at`) VALUES
(1, 1, 'QRIS', 60000.00, 'Lunas', NULL, NULL, '2026-06-04 04:01:56', 2, '2026-06-04 04:01:26', '2026-06-04 04:01:56'),
(2, 6, 'QRIS', 35000.00, 'Lunas', NULL, NULL, '2026-06-04 04:38:20', 2, '2026-06-04 04:37:58', '2026-06-04 04:38:20');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
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
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('0Dhgr4WdTeOw0p5AuGaBvI8XSq05OcfytvfYR0dp', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.122.1 Chrome/142.0.7444.265 Electron/39.8.8 Safari/537.36', 'YTo3OntzOjY6Il90b2tlbiI7czo0MDoiNEl2UHZWNkQwdjFyblVobnN4RWtXOVVKU1hnMVRHb3BrNDk4SWxjOCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDI6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9zdGFmZi9zdGF0dXMtbGF1bmRyeSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NzoidXNlcl9pZCI7aToyO3M6NDoidXNlciI7czo1OiJzdGFmZiI7czo0OiJuYW1hIjtzOjc6IkthcmltYWgiO3M6NDoicm9sZSI7czo1OiJzdGFmZiI7fQ==', 1780573119),
('tvaKN5OFh4cFvwqoHLFN76tbbUJBXNGIPUJVt5iy', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'YTozOntzOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjY6Il90b2tlbiI7czo0MDoiMnMyZm54UFhEYWZzU3BLaGJkTmxuTGVZeTZKd0ptVUo2TzM3bFVuVSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7fX0=', 1780573181);

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `id_staff` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `notelp` varchar(20) NOT NULL,
  `sandi` varchar(255) NOT NULL,
  `alamat` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`id_staff`, `nama`, `username`, `notelp`, `sandi`, `alamat`, `is_active`, `created_at`, `updated_at`) VALUES
(2, 'Karimah', 'staff', '089528031112', '$2y$12$kLA1M9gK53qQm9L/IlL6q.7ppfhxDdNE9fYvxczaJO2Vn1sYRGGkq', 'sdsa', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tracking`
--

CREATE TABLE `tracking` (
  `id_tracking` bigint(20) UNSIGNED NOT NULL,
  `id_order` bigint(20) UNSIGNED NOT NULL,
  `status` enum('Menunggu Konfirmasi','Dijemput','Dicuci','Disetrika','Dikirim','Selesai','Dibatalkan') NOT NULL,
  `keterangan` text DEFAULT NULL,
  `waktu_update` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tracking`
--

INSERT INTO `tracking` (`id_tracking`, `id_order`, `status`, `keterangan`, `waktu_update`, `updated_by`) VALUES
(1, 1, 'Menunggu Konfirmasi', 'Order masuk dari customer', '2026-06-04 04:00:58', NULL),
(2, 1, 'Dijemput', 'Staff menjemput laundry', '2026-06-04 04:01:15', 2),
(3, 1, 'Dijemput', 'Berat diverifikasi, tagihan dikirim ke customer', '2026-06-04 04:01:26', 2),
(4, 1, 'Dicuci', 'Status diperbarui oleh staff', '2026-06-04 04:02:09', 2),
(5, 1, 'Disetrika', 'Status diperbarui oleh staff', '2026-06-04 04:02:13', 2),
(6, 1, 'Dikirim', 'Status diperbarui oleh staff', '2026-06-04 04:02:17', 2),
(7, 1, 'Selesai', 'Status diperbarui oleh staff', '2026-06-04 04:02:20', 2),
(12, 6, 'Menunggu Konfirmasi', 'Order masuk dari customer', '2026-06-04 04:37:23', NULL),
(13, 6, 'Dijemput', 'Staff menjemput laundry', '2026-06-04 04:37:52', 2),
(14, 6, 'Dijemput', 'Berat diverifikasi, tagihan dikirim ke customer', '2026-06-04 04:37:58', 2),
(15, 6, 'Dicuci', 'Status diperbarui oleh staff', '2026-06-04 04:38:27', 2),
(16, 6, 'Disetrika', 'Status diperbarui oleh staff', '2026-06-04 04:38:30', 2),
(17, 6, 'Dikirim', 'Status diperbarui oleh staff', '2026-06-04 04:38:34', 2),
(18, 6, 'Selesai', 'Status diperbarui oleh staff', '2026-06-04 04:38:39', 2);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_cust` bigint(20) UNSIGNED NOT NULL,
  `nama_cust` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `notelp_cust` varchar(20) NOT NULL,
  `sandi_cust` varchar(255) NOT NULL,
  `alamat_cust` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_cust`, `nama_cust`, `username`, `notelp_cust`, `sandi_cust`, `alamat_cust`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Dhira Cust', 'dhira', '084444444444', '$2y$12$r5.KwUdbXI4TKuFzTZlnJ.2dfPuoHq5uGEQ5DzgFZsX0OZPBGcgjK', 'Jl. Mawar No. 10', 1, '2026-06-03 05:04:16', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `app_settings`
--
ALTER TABLE `app_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `app_settings_key_unique` (`key`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `invoice`
--
ALTER TABLE `invoice`
  ADD PRIMARY KEY (`id_invoice`),
  ADD UNIQUE KEY `invoice_id_bayar_unique` (`id_bayar`),
  ADD UNIQUE KEY `invoice_no_invoice_unique` (`no_invoice`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `katalog`
--
ALTER TABLE `katalog`
  ADD PRIMARY KEY (`id_katalog`),
  ADD KEY `katalog_id_layanan_foreign` (`id_layanan`);

--
-- Indexes for table `layanan`
--
ALTER TABLE `layanan`
  ADD PRIMARY KEY (`id_layanan`);

--
-- Indexes for table `login_logs`
--
ALTER TABLE `login_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_role_actor_id_is_read_created_at_index` (`role`,`actor_id`,`is_read`,`created_at`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id_order`),
  ADD UNIQUE KEY `orders_kode_order_unique` (`kode_order`),
  ADD KEY `orders_id_cust_foreign` (`id_cust`),
  ADD KEY `orders_id_layanan_foreign` (`id_layanan`),
  ADD KEY `orders_id_staff_foreign` (`id_staff`);

--
-- Indexes for table `order_detail`
--
ALTER TABLE `order_detail`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `order_detail_id_order_foreign` (`id_order`),
  ADD KEY `order_detail_id_katalog_foreign` (`id_katalog`);

--
-- Indexes for table `owner`
--
ALTER TABLE `owner`
  ADD PRIMARY KEY (`id_owner`),
  ADD UNIQUE KEY `owner_username_unique` (`username`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id_bayar`),
  ADD UNIQUE KEY `pembayaran_id_order_unique` (`id_order`),
  ADD KEY `pembayaran_dikonfirmasi_oleh_foreign` (`dikonfirmasi_oleh`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id_staff`),
  ADD UNIQUE KEY `staff_username_unique` (`username`);

--
-- Indexes for table `tracking`
--
ALTER TABLE `tracking`
  ADD PRIMARY KEY (`id_tracking`),
  ADD KEY `tracking_id_order_foreign` (`id_order`),
  ADD KEY `tracking_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_cust`),
  ADD UNIQUE KEY `users_username_unique` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `app_settings`
--
ALTER TABLE `app_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoice`
--
ALTER TABLE `invoice`
  MODIFY `id_invoice` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `katalog`
--
ALTER TABLE `katalog`
  MODIFY `id_katalog` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `layanan`
--
ALTER TABLE `layanan`
  MODIFY `id_layanan` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `login_logs`
--
ALTER TABLE `login_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id_order` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `order_detail`
--
ALTER TABLE `order_detail`
  MODIFY `id_detail` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `owner`
--
ALTER TABLE `owner`
  MODIFY `id_owner` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id_bayar` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id_staff` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tracking`
--
ALTER TABLE `tracking`
  MODIFY `id_tracking` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_cust` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `invoice`
--
ALTER TABLE `invoice`
  ADD CONSTRAINT `invoice_id_bayar_foreign` FOREIGN KEY (`id_bayar`) REFERENCES `pembayaran` (`id_bayar`);

--
-- Constraints for table `katalog`
--
ALTER TABLE `katalog`
  ADD CONSTRAINT `katalog_id_layanan_foreign` FOREIGN KEY (`id_layanan`) REFERENCES `layanan` (`id_layanan`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_id_cust_foreign` FOREIGN KEY (`id_cust`) REFERENCES `users` (`id_cust`),
  ADD CONSTRAINT `orders_id_layanan_foreign` FOREIGN KEY (`id_layanan`) REFERENCES `layanan` (`id_layanan`),
  ADD CONSTRAINT `orders_id_staff_foreign` FOREIGN KEY (`id_staff`) REFERENCES `staff` (`id_staff`) ON DELETE SET NULL;

--
-- Constraints for table `order_detail`
--
ALTER TABLE `order_detail`
  ADD CONSTRAINT `order_detail_id_katalog_foreign` FOREIGN KEY (`id_katalog`) REFERENCES `katalog` (`id_katalog`),
  ADD CONSTRAINT `order_detail_id_order_foreign` FOREIGN KEY (`id_order`) REFERENCES `orders` (`id_order`) ON DELETE CASCADE;

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_dikonfirmasi_oleh_foreign` FOREIGN KEY (`dikonfirmasi_oleh`) REFERENCES `staff` (`id_staff`) ON DELETE SET NULL,
  ADD CONSTRAINT `pembayaran_id_order_foreign` FOREIGN KEY (`id_order`) REFERENCES `orders` (`id_order`);

--
-- Constraints for table `tracking`
--
ALTER TABLE `tracking`
  ADD CONSTRAINT `tracking_id_order_foreign` FOREIGN KEY (`id_order`) REFERENCES `orders` (`id_order`) ON DELETE CASCADE,
  ADD CONSTRAINT `tracking_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `staff` (`id_staff`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
