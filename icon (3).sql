-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 17, 2026 at 04:37 PM
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
-- Database: `icon`
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
  `number_of_days` int(11) NOT NULL DEFAULT 1,
  `end_date` date NOT NULL,
  `time_slots` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`time_slots`)),
  `time_slot_times` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`time_slot_times`)),
  `total_amount` decimal(10,2) NOT NULL,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount_percentage` decimal(5,2) DEFAULT NULL,
  `discount_reason` varchar(255) DEFAULT NULL,
  `original_amount` decimal(10,2) DEFAULT NULL,
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

INSERT INTO `bookings` (`id`, `booking_reference`, `venue_id`, `package_id`, `staff_id`, `client_name`, `client_email`, `client_phone`, `booking_date`, `number_of_days`, `end_date`, `time_slots`, `time_slot_times`, `total_amount`, `discount_amount`, `discount_percentage`, `discount_reason`, `original_amount`, `status`, `payment_status`, `notes`, `reminder_sent_at`, `created_at`, `updated_at`, `deleted_at`) VALUES
(27, 'IVS-2026-IAWP', 15, NULL, 1, 'rewin jay', 'subibirewinjay@gmail.com', '09123456798', '2026-06-01', 1, '2026-06-01', NULL, NULL, 50000.01, 0.00, NULL, NULL, 50000.01, 'completed', 'paid', NULL, NULL, '2026-05-30 23:56:46', '2026-06-09 13:19:15', NULL),
(28, 'IVS-2026-RTK1', 16, NULL, 1, 'rewin jay', 'subibirewinjay@gmail.com', '09123456798', '2026-06-10', 1, '2026-06-10', '[\"morning\"]', NULL, 7000.00, 0.00, NULL, NULL, 7000.00, 'cancelled', 'unpaid', NULL, NULL, '2026-06-10 00:07:30', '2026-06-10 00:11:25', '2026-06-10 00:11:25'),
(29, 'IVS-2026-EYJN', 16, NULL, 1, 'rewin jay', 'subibirewinjay@gmail.com', '09123456798', '2026-06-10', 1, '2026-06-10', '[\"afternoon\"]', NULL, 7000.00, 0.00, NULL, NULL, 7000.00, 'pending', 'unpaid', NULL, NULL, '2026-06-10 00:46:01', '2026-06-10 00:46:01', NULL),
(30, 'IVS-2026-3ZW8', 16, NULL, 1, 'rewin jay', 'subibirewinjay@gmail.com', '09123456798', '2026-06-10', 1, '2026-06-10', '[\"evening\"]', NULL, 7000.00, 0.00, NULL, NULL, 7000.00, 'pending', 'unpaid', NULL, NULL, '2026-06-10 00:57:29', '2026-06-10 00:57:29', NULL),
(31, 'IVS-2026-JHUM', 15, NULL, 1, 'jay', 'subibirewinjay@gmail.com', '09537967052', '2026-06-10', 1, '2026-06-10', '[\"afternoon\"]', NULL, 16666.67, 0.00, NULL, NULL, 16666.67, 'pending', 'unpaid', NULL, NULL, '2026-06-10 01:11:53', '2026-06-10 01:11:53', NULL),
(32, 'IVS-2026-Z95F', 15, NULL, 1, 'jereniah', 'subibirewinjay@gmail.com', '09537967052', '2026-06-10', 1, '2026-06-10', '[\"evening\"]', NULL, 16666.67, 0.00, NULL, NULL, 16666.67, 'confirmed', 'paid', NULL, NULL, '2026-06-10 01:15:25', '2026-06-10 01:24:59', NULL);

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

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('icon-venue-suites-cache-bookings_auto_complete_last_run', 'b:1;', 1781055774),
('venue-and-suites-booking-cache-bookings_auto_complete_last_run', 'b:1;', 1781011905);

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
(1, '0933 866 7716', 'iconvenueandsuites@gmail.com', 'https://www.facebook.com/ICONVenueandSuites', 'https://www.messenger.com/e2ee/t/7480004012115734/', 'https://forms.gle/9DorYJttnj3eYgUW9', '+1234567890', '123 Venue Street, City, Country', 'Monday - Sunday: 9:00 AM - 6:00 PM', '2026-01-15 19:03:34', '2026-04-19 16:02:19');

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
(22, '2026_01_19_145931_add_stock_fields_to_venue_addons_table', 12),
(23, '2026_01_26_233610_rename_venues_table_to_venues_and_suites', 13),
(24, '2026_01_26_233719_update_foreign_keys_for_venues_and_suites_table', 13),
(25, '2026_01_26_235327_add_discount_fields_to_bookings_table', 13),
(26, '2026_01_27_001923_add_time_based_pricing_to_venue_packages_table', 13),
(27, '2026_01_27_010840_update_time_slot_to_support_multiple_selections', 13),
(28, '2026_02_11_005918_add_allow_same_day_booking_to_venues_and_suites_table', 13),
(29, '2026_03_12_165636_add_number_of_days_to_bookings_table', 13),
(30, '2026_06_10_081412_add_room_number_to_venues_and_suites_table', 14),
(31, '2026_06_10_093113_add_time_slot_times_to_bookings_table', 15);

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
(24, 27, 1, 50000.10, 'Cash', 'cash', NULL, 'verified', 'sdadsa', '2026-05-30 23:57:15', '2026-05-30 23:57:11', '2026-05-30 23:57:15', NULL),
(25, 32, 1, 16666.67, 'Cash', 'cash', NULL, 'verified', 'cash', '2026-06-10 01:24:59', '2026-06-10 01:24:59', '2026-06-10 01:24:59', NULL);

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
(1, 'Admin', 'admin@iconvenue.com', 'profiles/uSrzXHoEryqeh0rojgD0oMXk7APiWhSEWSWikZNU.jpg', 1, 1, NULL, '$2y$12$zNJF5FzVRwN4nSpDLrySqelYid71yWX9vlyU57WIGEpYArr7nAx62', NULL, '2026-01-15 19:03:34', '2026-06-10 01:16:04'),
(2, 'pogi', 'staff@iconvenue.com', NULL, 2, 1, NULL, '$2y$12$TljFIRfoFAUp/osHI/a05uEDRx96GbQcBq0Vrw2ia3AjG0zQ4qpZa', NULL, '2026-01-15 19:03:34', '2026-01-18 09:45:51');

-- --------------------------------------------------------

--
-- Table structure for table `venues_and_suites`
--

CREATE TABLE `venues_and_suites` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `room_number` varchar(50) DEFAULT NULL,
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
-- Dumping data for table `venues_and_suites`
--

INSERT INTO `venues_and_suites` (`id`, `name`, `room_number`, `type`, `description`, `capacity`, `price_per_day`, `price_morning`, `price_afternoon`, `price_evening`, `amenities`, `images`, `is_active`, `created_at`, `updated_at`) VALUES
(10, 'Standard Single', '5', 'suite', 'Cancellation policy\r\nCancel for free before May 20, 2026\r\n\r\nStay flexible! Cancel for free 2 days before the booking. ​Any cancellation received within 7 days prior to the arrival date will be charged for the entire stay. Failure to arrive at your hotel or property will be treated as a No-Show and will incur a charge of 100% of the booking value (Hotel policy).', 1, 1126.00, NULL, NULL, NULL, '[\"Featured amenities\",\"17 m\\u00b2\\/183 ft\\u00b2\",\"1 single bed\",\"Comforts\",\"Air conditioning\",\"One Single Bed\",\"WiFi\",\"Breakfast Included\"]', '[\"suites\\/1779067735_6a0a6b57eba75.jpg\"]', 1, '2026-05-18 00:44:19', '2026-06-10 00:23:24'),
(11, 'Standard Room', '4', 'suite', 'Cancellation policy\r\nNon-refundable (Low price!)\r\n\r\nThis special offer includes an extra-low price, but cannot be amended or cancelled. In case of a no-show, the property will not refund the booking. If you\'re sure of your travel dates, you can take advantage of this special offer!\r\n\r\nCancellation policy\r\nNon-refundable (Low price!)', 2, 1613.00, NULL, NULL, NULL, '[\"Wifi\",\"Breakfast Included\",\"No Windows view\",\"Private bathroom\",\"Walk-in shower\",\"Towels\",\"Toiletries\",\"TV\",\"Free instant coffee\",\"Laptop workspace\",\"Top floor available\",\"Non-smoking\"]', '[\"suites\\/1779065233_6a0a6191a4e10.webp\"]', 1, '2026-05-18 00:47:13', '2026-06-10 00:23:18'),
(12, 'Deluxe Room', '3', 'suite', 'Cancellation policy\r\nCancel for free 2 days before the Booking\r\n\r\nStay flexible! Cancel for free before May 20, 2026 ​Any cancellation received within 7 days prior to the arrival date will be charged for the entire stay. Failure to arrive at your hotel or property will be treated as a No-Show and will incur a charge of 100% of the booking value (Hotel policy).', 2, 2169.00, NULL, NULL, NULL, '[\"Wifi\",\"Breakfast Included\",\"Private bathroom\",\"Towels\",\"TV\",\"Air conditioning\",\"Desk\",\"Non-smoking\",\"Safety\\/security feature\"]', '[\"suites\\/1779066188_6a0a654c20c90.webp\"]', 1, '2026-05-18 01:03:08', '2026-06-10 00:23:12'),
(13, 'Triple Room', '2', 'suite', 'Cancellation policy\r\nNon-refundable (Low price!)\r\n\r\nThis special offer includes an extra-low price, but cannot be amended or cancelled. In case of a no-show, the property will not refund the booking. If you\'re sure of your travel dates, you can take advantage of this special offer!', 3, 2150.00, NULL, NULL, NULL, '[\"WiFi\",\"Breakfast Included\",\"Private bathroom\",\"Towels\",\"TV\",\"Air conditioning\",\"Desk\",\"Non-smoking\",\"Safety\\/security feature\"]', '[\"suites\\/1779066340_6a0a65e4404a0.webp\"]', 1, '2026-05-18 01:05:40', '2026-06-10 00:23:07'),
(14, 'Family Room', '1', 'suite', 'Cancellation policy\r\nCancel for free 2 days before the booking\r\n\r\nStay flexible! Cancel for free before May 20, 2026 ​Any cancellation received within 7 days prior to the arrival date will be charged for the entire stay. Failure to arrive at your hotel or property will be treated as a No-Show and will incur a charge of 100% of the booking value (Hotel policy)', 4, 3298.00, NULL, NULL, NULL, '[\"WiFi\",\"Breakfast Included\",\"Private bathroom\",\"Towels\",\"TV\",\"Air conditioning\",\"Desk\",\"Non-smoking\",\"Safety\\/security feature\"]', '[\"suites\\/1779066466_6a0a66621e6e2.webp\",\"suites\\/1779066466_6a0a666220b02.webp\",\"suites\\/1779066466_6a0a6662210b1.webp\",\"suites\\/1779066466_6a0a6662216ad.jpg\"]', 1, '2026-05-18 01:07:46', '2026-06-10 00:21:34'),
(15, 'Platinum', NULL, 'venue', 'Platinum Venue', 50, 50000.00, 16666.67, 16666.67, 16666.67, '[\"Food\",\"Catering\",\"For 4 hours using of function hall\"]', '[\"venues\\/1779066873_6a0a67f949891.jpg\"]', 1, '2026-05-18 01:14:33', '2026-05-18 01:26:41'),
(16, 'VIP Room', NULL, 'venue', 'vip room', 15, 21000.00, 7000.00, 7000.00, 7000.00, '[\"Food\",\"Catering\",\"Projector\"]', '[\"venues\\/1779067504_6a0a6a70d5ff9.jpg\"]', 1, '2026-05-18 01:21:39', '2026-05-18 01:25:04'),
(17, 'Diamond', NULL, 'venue', 'Diamond', 50, 54000.00, 18000.00, 18000.00, 18000.00, '[]', '[\"venues\\/1779067363_6a0a69e33d38f.jpg\"]', 1, '2026-05-18 01:22:43', '2026-05-18 01:22:43');

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
(6, 'Photo Booth Setup', 'Professional photo booth with props and backdrop', 5000.00, 'decoration', 1, 6, 9, 1, 0, NULL, '2026-01-19 06:33:21', '2026-03-20 08:06:23'),
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
  `price_morning` decimal(10,2) DEFAULT NULL,
  `price_afternoon` decimal(10,2) DEFAULT NULL,
  `price_evening` decimal(10,2) DEFAULT NULL,
  `has_time_based_pricing` tinyint(1) NOT NULL DEFAULT 0,
  `inclusions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`inclusions`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `venue_packages`
--

INSERT INTO `venue_packages` (`id`, `venue_id`, `name`, `description`, `price`, `price_morning`, `price_afternoon`, `price_evening`, `has_time_based_pricing`, `inclusions`, `is_active`, `created_at`, `updated_at`) VALUES
(5, 15, 'All in wedding package', 'Wedding', 50999.00, NULL, NULL, NULL, 0, '[\"Food\",\"Catering\",\"4 hours use of function hall\"]', 1, '2026-05-18 01:15:51', '2026-05-18 01:16:27'),
(6, 17, 'Birthday Package', 'Birthday', 54000.00, 16000.00, 16000.00, 16000.00, 1, '[\"Food\",\"Catering\"]', 1, '2026-05-18 01:24:11', '2026-05-18 01:24:11');

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
-- Indexes for table `venues_and_suites`
--
ALTER TABLE `venues_and_suites`
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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `booking_addons`
--
ALTER TABLE `booking_addons`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

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
-- AUTO_INCREMENT for table `venues_and_suites`
--
ALTER TABLE `venues_and_suites`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `venue_addons`
--
ALTER TABLE `venue_addons`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `venue_packages`
--
ALTER TABLE `venue_packages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_package_id_foreign` FOREIGN KEY (`package_id`) REFERENCES `venue_packages` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `bookings_staff_id_foreign` FOREIGN KEY (`staff_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `bookings_venue_id_foreign` FOREIGN KEY (`venue_id`) REFERENCES `venues_and_suites` (`id`) ON DELETE CASCADE;

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
  ADD CONSTRAINT `venue_packages_venue_id_foreign` FOREIGN KEY (`venue_id`) REFERENCES `venues_and_suites` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
