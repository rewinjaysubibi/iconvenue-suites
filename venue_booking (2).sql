-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 23, 2026 at 05:05 AM
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
-- Database: `venue_booking`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `booking_reference` varchar(20) NOT NULL,
  `venue_id` bigint(20) UNSIGNED NOT NULL,
  `package_id` bigint(20) UNSIGNED DEFAULT NULL,
  `staff_id` bigint(20) UNSIGNED DEFAULT NULL,
  `client_name` varchar(255) NOT NULL,
  `client_email` varchar(255) NOT NULL,
  `client_phone` varchar(255) NOT NULL,
  `booking_date` date NOT NULL,
  `end_date` date NOT NULL,
  `time_slot` varchar(255) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','cancelled','completed') NOT NULL DEFAULT 'pending',
  `payment_status` enum('unpaid','partial','paid') NOT NULL DEFAULT 'unpaid',
  `notes` text DEFAULT NULL,
  `reminder_sent_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `booking_reference`, `venue_id`, `package_id`, `staff_id`, `client_name`, `client_email`, `client_phone`, `booking_date`, `end_date`, `time_slot`, `total_amount`, `status`, `payment_status`, `notes`, `reminder_sent_at`, `created_at`, `updated_at`, `deleted_at`) VALUES
(11, 'IVS-2026-Q2CZ', 4, 4, 1, 'rewin jay', 'subibirewinjay@gmail.com', '09123456798', '2026-01-20', '2026-01-20', 'morning', 30000.00, 'confirmed', 'paid', NULL, NULL, '2026-01-18 07:30:18', '2026-01-18 07:44:13', NULL),
(12, 'IVS-2026-6F5O', 4, NULL, 1, 'Demo Client', 'demo@example.com', '+63 123 456 7890', '2026-01-19', '2026-01-19', 'afternoon', 15000.00, 'confirmed', 'partial', 'Demo booking for testing reminder system', '2026-01-18 07:59:03', '2026-01-18 07:58:58', '2026-01-18 07:59:03', NULL),
(13, 'IVS-2026-0VO3', 5, NULL, 1, 'rewin jay', 'subibirewinjay@gmail.com', '09123456789', '2026-01-20', '2026-01-20', NULL, 1800.00, 'completed', 'paid', NULL, NULL, '2026-01-19 08:30:28', '2026-01-19 08:32:36', NULL),
(14, 'IVS-2026-A0U2', 4, 4, 2, 'jay', 'subibirewinjay@gmail.com', '09123456789', '2026-01-23', '2026-01-23', 'morning', 35000.00, 'confirmed', 'paid', 'please', NULL, '2026-01-22 07:36:46', '2026-01-22 07:37:20', NULL),
(15, 'IVS-2026-72MP', 4, 4, 2, 'rewin jay', 'subibirewinjay@gmail.com', '09123456789', '2026-01-24', '2026-01-24', 'evening', 33000.00, 'confirmed', 'unpaid', NULL, NULL, '2026-01-22 07:45:22', '2026-01-22 07:45:22', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `booking_addons`
--

CREATE TABLE `booking_addons` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `booking_id` bigint(20) UNSIGNED NOT NULL,
  `venue_addon_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price_at_booking` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `booking_addons`
--

INSERT INTO `booking_addons` (`id`, `booking_id`, `venue_addon_id`, `quantity`, `price_at_booking`, `created_at`, `updated_at`) VALUES
(1, 14, 6, 1, 5000.00, '2026-01-22 07:36:46', '2026-01-22 07:36:46'),
(2, 15, 8, 1, 3000.00, '2026-01-22 07:45:22', '2026-01-22 07:45:22');

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
-- Table structure for table `carousel_images`
--

CREATE TABLE `carousel_images` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `carousel_images`
--

INSERT INTO `carousel_images` (`id`, `image_path`, `title`, `order`, `is_active`, `created_at`, `updated_at`) VALUES
(9, 'carousel/BJvm1TKX6jdmYp7rxqI8fc6g4plzl6WFpTYiwmL9.jpg', NULL, 0, 1, '2026-01-21 07:32:36', '2026-01-21 07:32:36');

-- --------------------------------------------------------

--
-- Table structure for table `contact_settings`
--

CREATE TABLE `contact_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `facebook` varchar(255) DEFAULT NULL,
  `messenger` varchar(255) DEFAULT NULL,
  `google_form_url` varchar(255) DEFAULT NULL,
  `whatsapp` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `business_hours` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contact_settings`
--

INSERT INTO `contact_settings` (`id`, `phone`, `email`, `facebook`, `messenger`, `google_form_url`, `whatsapp`, `address`, `business_hours`, `created_at`, `updated_at`) VALUES
(1, '+1234567890', 'subibirewinjay@gmail.com', 'https://www.facebook.com/ICONVenueandSuites', 'https://www.messenger.com/e2ee/t/7480004012115734/', 'https://forms.gle/9DorYJttnj3eYgUW9', '+1234567890', '123 Venue Street, City, Country', 'Monday - Sunday: 9:00 AM - 6:00 PM', '2026-01-15 19:03:34', '2026-01-15 20:52:53');

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
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2024_01_01_000001_create_roles_table', 1),
(5, '2024_01_01_000002_add_role_to_users_table', 1),
(6, '2024_01_01_000003_create_venues_table', 1),
(7, '2024_01_01_000004_create_bookings_table', 1),
(8, '2024_01_01_000005_create_payments_table', 1),
(9, '2024_01_01_000006_create_contact_settings_table', 1),
(10, '2024_01_16_000001_add_time_based_pricing_to_venues', 2),
(11, '2024_01_16_000002_add_time_slot_to_bookings', 3),
(12, '2026_01_16_035335_create_carousel_images_table', 4),
(13, '2026_01_16_041358_create_venue_packages_table', 5),
(14, '2026_01_16_041433_add_package_to_bookings_table', 5),
(15, '2026_01_16_044325_add_google_form_to_contact_settings_table', 6),
(16, '2026_01_16_052517_add_profile_image_to_users_table', 7),
(17, '2026_01_16_105857_add_soft_deletes_to_bookings_and_payments_tables', 8),
(18, '2026_01_18_143836_add_booking_reference_to_bookings_table', 9),
(19, '2026_01_18_155418_add_reminder_sent_at_to_bookings_table', 10),
(20, '2026_01_19_142852_create_venue_addons_table', 11),
(21, '2026_01_19_143024_create_booking_addons_table', 11),
(22, '2026_01_19_145931_add_stock_fields_to_venue_addons_table', 12);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `booking_id` bigint(20) UNSIGNED NOT NULL,
  `verified_by` bigint(20) UNSIGNED DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(255) DEFAULT NULL,
  `reference_number` varchar(255) DEFAULT NULL,
  `proof_image` text DEFAULT NULL,
  `status` enum('pending','verified','rejected') NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `booking_id`, `verified_by`, `amount`, `payment_method`, `reference_number`, `proof_image`, `status`, `notes`, `verified_at`, `created_at`, `updated_at`, `deleted_at`) VALUES
(12, 11, 1, 15000.00, 'Cash', 'cash', NULL, 'verified', NULL, '2026-01-18 07:38:45', '2026-01-18 07:38:11', '2026-01-18 07:38:45', NULL),
(13, 11, 1, 15000.00, 'GCash', '1234567', NULL, 'verified', NULL, '2026-01-18 07:44:13', '2026-01-18 07:43:48', '2026-01-18 07:44:13', NULL),
(14, 13, 1, 1800.00, 'Cash', 'cash', NULL, 'verified', NULL, '2026-01-19 08:32:24', '2026-01-19 08:32:17', '2026-01-19 08:32:24', NULL),
(15, 14, 2, 35000.00, 'Cash', 'cash', NULL, 'verified', NULL, '2026-01-22 07:37:20', '2026-01-22 07:37:17', '2026-01-22 07:37:20', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'Administrator with full system access', '2026-01-15 19:03:34', '2026-01-15 19:03:34'),
(2, 'staff', 'Staff member who manages bookings', '2026-01-15 19:03:34', '2026-01-15 19:03:34');

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
('FT78nIegKZ78g4tCNBGh3YE4AooteQOipVDpeKlx', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiZzMzZEs3S3RpN29EUVN0ZjR2UVY5bGs1dUsyWmJMTVV2ZXZXdXRtQyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1769140281),
('xrkhSXJ61gFHvsX44wxzmtgM7CYKhei1sAE3wVbQ', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoidHp0SXMwWFl4RU1lR3hKQlNqOGhKUHhUelEwUXFncTh1VnJvODZCZiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7fQ==', 1769140105);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `role_id` bigint(20) UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `profile_image`, `role_id`, `is_active`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin@iconvenue.com', NULL, 1, 1, NULL, '$2y$12$zNJF5FzVRwN4nSpDLrySqelYid71yWX9vlyU57WIGEpYArr7nAx62', NULL, '2026-01-15 19:03:34', '2026-01-18 09:20:48'),
(2, 'pogi', 'staff@iconvenue.com', NULL, 2, 1, NULL, '$2y$12$TljFIRfoFAUp/osHI/a05uEDRx96GbQcBq0Vrw2ia3AjG0zQ4qpZa', NULL, '2026-01-15 19:03:34', '2026-01-18 09:45:51');

-- --------------------------------------------------------

--
-- Table structure for table `venues`
--

CREATE TABLE `venues` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'venue',
  `description` text NOT NULL,
  `capacity` int(11) NOT NULL,
  `price_per_day` decimal(10,2) NOT NULL,
  `price_morning` decimal(10,2) DEFAULT NULL,
  `price_afternoon` decimal(10,2) DEFAULT NULL,
  `price_evening` decimal(10,2) DEFAULT NULL,
  `amenities` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`amenities`)),
  `images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`images`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `venues`
--

INSERT INTO `venues` (`id`, `name`, `type`, `description`, `capacity`, `price_per_day`, `price_morning`, `price_afternoon`, `price_evening`, `amenities`, `images`, `is_active`, `created_at`, `updated_at`) VALUES
(4, 'diamond', 'venue', 'vasaasa', 100, 75000.00, 25000.00, 25000.00, 25000.00, '[null]', '[\"venues\\/lOiqhpghM7CDKBbl9LBtJLxp52PxYfAxsDqruL01.jpg\",\"venues\\/9uLfcFZaz3GONUSP7Fm5tL3HxZeocu0mr1FDYFhb.jpg\",\"venues\\/2n7zsJ0TDQiiwUOWBUzjOU59b41JZEIvWJwqAOki.jpg\"]', 1, '2026-01-16 06:42:49', '2026-01-16 06:49:57'),
(5, 'jay', 'suite', 'dsadadas', 2, 1800.00, NULL, NULL, NULL, '[null]', '[\"venues\\/BssbZp2PamKjihdyDB2UQfYvegWyFCTv5PwjyoDe.png\"]', 1, '2026-01-19 08:29:18', '2026-01-19 08:29:18'),
(6, 'sadasdas', 'venue', 'dasda', 122, 7500.00, 2500.00, 2500.00, 2500.00, '[\"dasdasd\"]', '[\"venues\\/1769008239_6970ec6fa8166.jpg\"]', 1, '2026-01-21 07:10:39', '2026-01-21 07:10:39');

-- --------------------------------------------------------

--
-- Table structure for table `venue_addons`
--

CREATE TABLE `venue_addons` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `category` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `stock_quantity` int(11) DEFAULT NULL,
  `track_stock` tinyint(1) NOT NULL DEFAULT 0,
  `low_stock_threshold` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `venue_addons`
--

INSERT INTO `venue_addons` (`id`, `name`, `description`, `price`, `category`, `is_active`, `sort_order`, `stock_quantity`, `track_stock`, `low_stock_threshold`, `notes`, `created_at`, `updated_at`) VALUES
(6, 'Photo Booth Setup', 'Professional photo booth with props and backdrop', 5000.00, 'decoration', 1, 6, 0, 1, 0, NULL, '2026-01-19 06:33:21', '2026-01-22 07:36:46'),
(7, 'Sound System Upgrade', 'Professional sound system with microphones', 2500.00, 'equipment', 1, 7, NULL, 0, NULL, NULL, '2026-01-19 06:33:21', '2026-01-19 06:33:21'),
(8, 'Projector & Screen', 'HD projector with large screen for presentations', 3000.00, 'equipment', 1, 8, NULL, 0, NULL, NULL, '2026-01-19 06:33:21', '2026-01-19 06:33:21'),
(9, 'Additional Tables & Chairs', 'Extra seating arrangement (10 chairs + 2 tables)', 1500.00, 'equipment', 1, 9, NULL, 0, NULL, NULL, '2026-01-19 06:33:21', '2026-01-19 06:33:21'),
(10, 'Event Coordinator', 'Professional event coordinator for the entire event', 4000.00, 'service', 1, 10, NULL, 0, NULL, NULL, '2026-01-19 06:33:21', '2026-01-19 06:33:21'),
(11, 'Photography Service', 'Professional photographer for 4 hours', 8000.00, 'service', 1, 11, NULL, 0, NULL, NULL, '2026-01-19 06:33:21', '2026-01-19 06:33:21'),
(12, 'Cleanup Service', 'Post-event cleanup and venue restoration', 2000.00, 'service', 1, 12, NULL, 0, NULL, NULL, '2026-01-19 06:33:21', '2026-01-19 06:33:21');

-- --------------------------------------------------------

--
-- Table structure for table `venue_packages`
--

CREATE TABLE `venue_packages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `venue_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `inclusions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`inclusions`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `venue_packages`
--

INSERT INTO `venue_packages` (`id`, `venue_id`, `name`, `description`, `price`, `inclusions`, `is_active`, `created_at`, `updated_at`) VALUES
(4, 4, 'birthday package', 'sadasd', 30000.00, '[\"table\"]', 1, '2026-01-16 06:43:19', '2026-01-18 09:42:20');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bookings_booking_reference_unique` (`booking_reference`),
  ADD KEY `bookings_venue_id_foreign` (`venue_id`),
  ADD KEY `bookings_staff_id_foreign` (`staff_id`),
  ADD KEY `bookings_package_id_foreign` (`package_id`),
  ADD KEY `bookings_booking_reference_index` (`booking_reference`);

--
-- Indexes for table `booking_addons`
--
ALTER TABLE `booking_addons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `booking_addons_booking_id_venue_addon_id_unique` (`booking_id`,`venue_addon_id`),
  ADD KEY `booking_addons_venue_addon_id_foreign` (`venue_addon_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `carousel_images`
--
ALTER TABLE `carousel_images`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_settings`
--
ALTER TABLE `contact_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

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
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payments_booking_id_foreign` (`booking_id`),
  ADD KEY `payments_verified_by_foreign` (`verified_by`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_unique` (`name`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_role_id_foreign` (`role_id`);

--
-- Indexes for table `venues`
--
ALTER TABLE `venues`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `venue_addons`
--
ALTER TABLE `venue_addons`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `venue_packages`
--
ALTER TABLE `venue_packages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `venue_packages_venue_id_foreign` (`venue_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `booking_addons`
--
ALTER TABLE `booking_addons`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `carousel_images`
--
ALTER TABLE `carousel_images`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `contact_settings`
--
ALTER TABLE `contact_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `venues`
--
ALTER TABLE `venues`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `venue_addons`
--
ALTER TABLE `venue_addons`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `venue_packages`
--
ALTER TABLE `venue_packages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_package_id_foreign` FOREIGN KEY (`package_id`) REFERENCES `venue_packages` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `bookings_staff_id_foreign` FOREIGN KEY (`staff_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `bookings_venue_id_foreign` FOREIGN KEY (`venue_id`) REFERENCES `venues` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `booking_addons`
--
ALTER TABLE `booking_addons`
  ADD CONSTRAINT `booking_addons_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `booking_addons_venue_addon_id_foreign` FOREIGN KEY (`venue_addon_id`) REFERENCES `venue_addons` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `venue_packages`
--
ALTER TABLE `venue_packages`
  ADD CONSTRAINT `venue_packages_venue_id_foreign` FOREIGN KEY (`venue_id`) REFERENCES `venues` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
