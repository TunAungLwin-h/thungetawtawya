-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Mar 03, 2026 at 10:12 AM
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
-- Database: `thungwetaw_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `role` enum('admin','superadmin') DEFAULT 'admin',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password_hash`, `name`, `role`, `created_at`) VALUES
(1, 'admin', '$2y$10$xDFfFIbnfewIZ1lmuymPiOPmuZqHs90dFn0so6Hd/vW2kTUlejLJe', 'TunAungLwin', 'admin', '2026-01-17 13:40:09'),
(2, 'superadmin', '$2y$10$hDcFhAXbsi2I9I7obMVrM.rGv2a2tZv.4v8qgOo4aLR0EvmEOpwDG', 'System Owner', 'superadmin', '2026-02-07 16:06:27'),
(3, 'aung', '$2y$10$oFJQ0wgFgSF1MTlsohBvcOkEuBaonYbmmELq7dFFGpdluM1.6EunW', 'Aung Gyi', 'admin', '2026-02-26 13:28:43');

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(150) NOT NULL,
  `content` text NOT NULL,
  `author_id` int(10) UNSIGNED NOT NULL,
  `author_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `publish_date` date DEFAULT NULL,
  `status` enum('draft','published','archived') DEFAULT 'draft',
  `importance_level` enum('normal','high') DEFAULT 'normal',
  `category` varchar(50) NOT NULL DEFAULT 'general',
  `title_en` varchar(255) DEFAULT NULL,
  `title_mm` varchar(255) DEFAULT NULL,
  `content_en` text DEFAULT NULL,
  `content_mm` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `title`, `content`, `author_id`, `author_name`, `created_at`, `publish_date`, `status`, `importance_level`, `category`, `title_en`, `title_mm`, `content_en`, `content_mm`) VALUES
(2, 'Special Event Day', 'Tomorrow will be special even day!', 1, 'TunAungLwin', '2026-01-21 09:40:40', '2026-01-21', 'published', 'normal', 'general', 'Special Event Day', NULL, 'Tomorrow will be special even day!', NULL),
(3, '', '', 2, 'System Owner', '2026-02-07 10:11:02', '2026-02-07', 'published', 'normal', 'exam_results', 'Exam Announcement Date', 'စာမေးပွဲရက် ကြော်ငြာချက်', 'Tomorrow Exam date', 'မနက်ဖြန်စာမေးပွဲရှိပါတယ်'),
(4, '', '', 2, 'System Owner', '2026-02-26 04:14:12', '2026-02-26', 'published', 'normal', 'general', 'If AI learns buddha lecture could they be professional?', 'AI ကို စာပေသင်ကြားပေးခြင်းသည်ကောင်းသည့်အရာလား', 'Yes,they could be professional', ''),
(5, '', '', 2, 'System Owner', '2026-02-26 04:14:45', '2026-02-26', 'published', 'normal', 'general', 'If AI learns buddha lecture could they be professional?', 'AI ကို စာပေသင်ကြားပေးခြင်းသည်ကောင်းသည့်အရာလား', 'Yes,they could be professional', '');

-- --------------------------------------------------------

--
-- Table structure for table `candidates`
--

CREATE TABLE `candidates` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `dob` date DEFAULT NULL,
  `address_region` varchar(100) DEFAULT NULL,
  `address_township` varchar(100) DEFAULT NULL,
  `address_village` varchar(255) DEFAULT NULL,
  `father_name` varchar(255) DEFAULT NULL,
  `mother_name` varchar(255) DEFAULT NULL,
  `parent_address_region` varchar(255) DEFAULT NULL,
  `parent_address_village` varchar(255) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `roll_number` varchar(50) DEFAULT NULL,
  `status` enum('pending','approved','rejected','passed','failed') DEFAULT 'pending',
  `email` varchar(250) DEFAULT NULL,
  `monastery_name` varchar(200) DEFAULT NULL,
  `abbot_name` varchar(200) DEFAULT NULL,
  `parent_address_township` varchar(120) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `candidates`
--

INSERT INTO `candidates` (`id`, `name`, `dob`, `address_region`, `address_township`, `address_village`, `father_name`, `mother_name`, `parent_address_region`, `parent_address_village`, `country`, `created_at`, `updated_at`, `roll_number`, `status`, `email`, `monastery_name`, `abbot_name`, `parent_address_township`) VALUES
(9, 'TunAungLwin', '2026-01-20', 'mandalay', 'Chanayethazan', 'Mandalay', 'UWin', 'DawHla', 'Mandalay', 'NayPyitaw', 'Japan', '2026-01-20 19:52:36', '2026-01-20 23:01:49', '2026-1-00013', 'approved', NULL, NULL, NULL, NULL),
(13, 'TunTun', '2026-02-03', 'mandalay', 'Amarapura', 'တအုံ', 'U Aye', 'Daw Mya', 'mandalay', 'Singu', '-', '2026-01-21 15:11:30', '2026-01-21 15:44:06', '2026-1-00017', 'passed', NULL, NULL, NULL, NULL),
(15, 'Kyaw Kyaw', '2026-01-23', 'mandalay', 'Amarapura', 'mandalay', 'UL', 'Daw Mya', 'mandalay', 'mandaly', '-', '2026-01-23 22:45:42', '2026-01-28 21:14:08', '2026-1-00018', 'approved', 'kyaw@gmail.com', 'monastery', 'U Thuta', NULL),
(16, 'Aung Aung', '2004-06-27', 'mandalay', 'Aungmyaythazan', 'mandalay', 'UMaung', 'Daw Mya', 'mandalay', 'mandalay', NULL, '2026-01-27 20:15:00', '2026-01-28 20:54:02', '2026-1-00019', 'approved', 'aung@gmail.com', 'မစိုးရိမ်ကျောင်းတိုက်', 'ဦးသုခ', NULL),
(17, 'Oo lay', '2026-01-27', 'mandalay', 'Chanayethazan', 'naypyitaw', 'UHla', 'Daw Mya', 'Taohn', 'Taohn', NULL, '2026-01-27 20:20:09', '2026-01-27 21:49:02', '{27}-1-00020', 'approved', 'oolay@gmail.com', 'masoerun', 'UThulay', NULL),
(18, 'Kyaw gyi', '2004-02-27', 'sagaing', 'Shwebo', 'saging', 'UMaung', 'DawMu', 'HlaGyi', 'maymoy', 'japan', '2026-01-27 20:23:24', '2026-01-28 20:39:47', '1-00021', 'approved', 'kyawgyi@gmail.com', 'masoerue', 'ULaung', NULL),
(19, 'Gyi Kyaw', '2026-01-20', 'yangon', 'Hlaing', 'mandalay', 'UJu', 'Daw Hmu', 'mandalay', 'mandalay', NULL, '2026-01-28 20:59:03', '2026-02-07 16:39:00', '2026-1-00022', 'passed', 'gyi@gmail.com', 'masoe', 'ULay', NULL),
(20, 'KyawAungThu', '2026-01-30', 'mandalay', 'Amarapura', 'bota', 'U Maung', 'dawmya', 'mandalay', 'mandalay', '-', '2026-01-30 19:11:11', '2026-01-30 19:42:05', '2026-1-00023', 'passed', 'kyaw@gmail.com', 'saging', 'Uthukha', NULL),
(21, 'Thihazaw', '2013-02-28', 'mandalay', 'Amarapura', 'mandalay', 'UWin', 'DawHlaing', 'mandalay', 'mandalay', '-', '2026-01-30 20:02:50', '2026-01-30 20:06:07', '2026-1-00026', 'approved', 'thihazaw@gmail.com', 'မစိုးရိမ်ကျောင်းတိုက်', 'ရှင်သုခ', NULL),
(22, 'zar ni moe', '2026-02-07', 'mandalay', 'Amarapura', 'Mandalay', 'UNyi', 'Dawlay', 'Mandalay', 'Mandalay', 'Korea', '2026-02-07 16:16:00', '2026-02-26 13:27:36', '2026-1-00002', 'approved', 'zarni@gmail.com', 'Monastery', 'UThukha', NULL),
(23, 'TunTun', '2026-02-08', 'mandalay', 'Pyigyidagun', 'Manalay', 'UAye', 'DawMya', 'Mandalay', 'Mandalay', '-', '2026-02-08 14:01:12', '2026-02-26 11:02:32', '2026-1-00028', '', 'tun@gmail.com', 'MAsoeRein', 'UThukha', NULL),
(29, 'tal', '2004-02-03', 'mandalay', 'ပုသိမ်ကြီး', 'y', 's', 'd', 'mandalay', 'd', NULL, '2026-02-25 22:06:56', '2026-02-26 13:19:32', '2026-1-00005', 'failed', 'tal@gmail.com', 'f', 'd', 'စဉ့်ကူ'),
(30, 'မောင်သာသန', '2004-05-28', 'sagaing', 'ကလေး', 'စစ်ကိုင်း', 'ဉီးမောင်မောင်', 'ဒေါ်အေး', 'mandalay', 'မန်းလေး', '-', '2026-02-26 13:10:40', '2026-02-26 13:22:05', '2026-1-00004', 'passed', 'thartana@gmail.com', 'မစိုးရိမ်ကျောင်းတိုက်', 'ဉီးသုခ', 'မတ္တရာ');

-- --------------------------------------------------------

--
-- Table structure for table `exam_results`
--

CREATE TABLE `exam_results` (
  `id` int(11) NOT NULL,
  `registration_id` char(36) NOT NULL,
  `marks` decimal(5,2) DEFAULT NULL,
  `total_marks` decimal(5,2) DEFAULT 100.00,
  `percentage` decimal(5,2) DEFAULT NULL,
  `result` enum('passed','failed') NOT NULL,
  `position` int(11) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `published_at` datetime DEFAULT NULL,
  `published_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feedback_submissions`
--

CREATE TABLE `feedback_submissions` (
  `id` int(11) NOT NULL,
  `sender_name` varchar(100) NOT NULL,
  `sender_email` varchar(100) NOT NULL,
  `category` enum('Inquiry','Complaint','Donation','Visit') DEFAULT 'Inquiry',
  `message_body` text NOT NULL,
  `status` enum('New','Read','Resolved') DEFAULT 'New',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `feedback_submissions`
--

INSERT INTO `feedback_submissions` (`id`, `sender_name`, `sender_email`, `category`, `message_body`, `status`, `created_at`) VALUES
(1, 'TunAungLwin', 'tun@gmail.com', 'Donation', 'I would like to donate 100k', 'New', '2026-02-13 07:21:29'),
(2, 'TheinThanZaw', 'then@gmail.com', 'Visit', 'I wanna visit', 'New', '2026-02-26 06:53:35');

-- --------------------------------------------------------

--
-- Table structure for table `registrations`
--

CREATE TABLE `registrations` (
  `id` char(36) NOT NULL,
  `candidate_id` int(11) NOT NULL,
  `level` tinyint(4) NOT NULL COMMENT '1,2,3',
  `registration_year` year(4) NOT NULL,
  `roll_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','approved','rejected','passed','failed') DEFAULT 'pending',
  `is_published` tinyint(1) DEFAULT 0,
  `passed_year` year(4) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `result_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `previous_registration_id` char(36) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `registrations`
--

INSERT INTO `registrations` (`id`, `candidate_id`, `level`, `registration_year`, `roll_number`, `status`, `is_published`, `passed_year`, `remarks`, `result_id`, `created_at`, `updated_at`, `previous_registration_id`) VALUES
('1d29a433dd254f043694747c6957e821', 17, 1, '2026', '{27}-1-00020', 'passed', 0, '2026', '', NULL, '2026-01-27 21:43:15', '2026-01-27 21:43:15', NULL),
('2152df7f589a0ccdaae5d17a65361581', 13, 1, '2026', '2026-1-00017', 'passed', 0, '2026', '', NULL, '2026-01-21 15:23:08', '2026-01-21 15:23:08', NULL),
('2eae0ac13a3f7da0c9eab80bf7d32e56', 16, 1, '2026', '2026-1-00019', 'passed', 0, '2026', '', NULL, '2026-01-27 20:27:30', '2026-01-27 20:27:30', NULL),
('2fe33abe704e62ae201dac7928ba28c8', 20, 1, '2026', '2026-1-00024', 'failed', 0, '2026', '', NULL, '2026-01-30 19:33:37', '2026-01-30 19:34:21', 'c44698e477a3fdda6a1d523a84de87f1'),
('35454f007ce1100b94261bd70bcc26fb', 20, 3, '2026', '2026-3-00009', 'failed', 0, '2026', '', NULL, '2026-01-30 19:41:35', '2026-01-30 19:41:39', '5de75555e00f6fab6d134828152b94c4'),
('3feed896cfd5d544963318a430472e4b', 9, 3, '2026', '2026-3-00005', 'approved', 0, NULL, NULL, NULL, '2026-01-20 19:56:44', '2026-01-20 23:01:49', 'aa07bc62-809b-466d-b22d-63e720427101'),
('413dbae9b2844b3ab1e976610bb71a25', 18, 2, '2026', '2026-2-00001', 'pending', 0, NULL, NULL, NULL, '2026-02-09 19:16:49', '2026-02-09 19:16:49', 'b73e3cd421c8caae99ffd9c5ce6e36b7'),
('431ef680529e28fa68105b5f62ab6c12', 19, 1, '2026', '2026-1-00027', 'passed', 0, '2026', '', NULL, '2026-02-07 16:34:59', '2026-02-07 16:39:00', '5b495860c57cc616a1ed4c9d486896ee'),
('4af98b81-6c8f-4f5a-8b86-a60ce9e1cd0f', 23, 2, '2026', '2026-2-00018', 'failed', 0, '2026', '', NULL, '2026-02-08 14:05:27', '2026-02-08 14:06:02', '8423aa3ef98ed70891b69801631f611d'),
('5b495860c57cc616a1ed4c9d486896ee', 19, 1, '2026', '2026-1-00022', 'failed', 0, '2026', '', NULL, '2026-01-30 19:34:28', '2026-01-30 19:34:28', NULL),
('5de75555e00f6fab6d134828152b94c4', 20, 2, '2026', '2026-2-00015', 'passed', 0, '2026', '', NULL, '2026-01-30 19:41:22', '2026-01-30 19:41:34', '93572d77c17a6c7bf736e733e0b9c5af'),
('5e15bd8cda2d3ea09836ed016035a029', 15, 2, '2026', '2026-2-00010', 'passed', 0, '2026', '', NULL, '2026-01-23 22:50:11', '2026-01-27 21:43:41', '6670998eef35be1ff5bef5e9d5c0bad7'),
('6053ff5f8ff8fea7ae4d8a5d6fb2d6a6', 17, 2, '2026', '2026-2-00012', 'failed', 0, NULL, NULL, NULL, '2026-01-27 21:49:00', '2026-01-27 21:55:29', '1d29a433dd254f043694747c6957e821'),
('639ad1fe2b57fc3a1c26b92f87162ffd', 21, 2, '2026', '2026-2-00016', 'approved', 0, NULL, NULL, NULL, '2026-01-30 20:06:05', '2026-01-30 20:06:07', 'e97a3a963faef30d669198975f4b6218'),
('6670998eef35be1ff5bef5e9d5c0bad7', 15, 1, '2026', '2026-1-00018', 'passed', 0, '2026', '', NULL, '2026-01-23 22:49:56', '2026-01-23 22:49:56', NULL),
('7a72ef6d-51bc-45d9-87a7-d3e1a5f2b46e', 13, 3, '2026', '2026-3-00007', 'passed', 0, '2026', '', NULL, '2026-01-21 15:43:23', '2026-01-21 15:44:06', 'e1b4c159-a638-4674-a24a-f45acda031a8'),
('7bd69466-f1be-4028-ba4f-cb5d77419c85', 19, 2, '2026', '2026-2-00017', 'pending', 0, NULL, NULL, NULL, '2026-02-07 16:39:51', '2026-02-07 16:39:51', '431ef680529e28fa68105b5f62ab6c12'),
('81c961d193f55d0cab11f0a1961a1338', 23, 2, '2026', '2026-2-00005', 'passed', 0, '2026', '', NULL, '2026-02-25 22:01:03', '2026-02-25 22:18:46', '4af98b81-6c8f-4f5a-8b86-a60ce9e1cd0f'),
('8423aa3ef98ed70891b69801631f611d', 23, 1, '2026', '2026-1-00028', 'passed', 0, '2026', '', NULL, '2026-02-08 14:04:31', '2026-02-08 14:04:31', NULL),
('93572d77c17a6c7bf736e733e0b9c5af', 20, 2, '2026', '2026-2-00014', 'failed', 0, '2026', '', NULL, '2026-01-30 19:40:29', '2026-01-30 19:41:05', 'b31048aeec894f93d2023aede191dda3'),
('9d8b84465b3679667db2f5fc895c1c1a', 22, 1, '2026', '2026-1-00002', 'passed', 0, '2026', '', NULL, '2026-02-13 12:10:29', '2026-02-13 12:10:29', NULL),
('a361a0c9-ab2c-4e05-b35b-b86b086780f2', 30, 2, '2026', '2026-2-00020', 'passed', 0, '2026', '', NULL, '2026-02-26 13:18:31', '2026-02-26 13:21:14', 'a3beccf65f6242ed1a5c913f2a955911'),
('a3715511bcadbf30595981452afe55bf', 15, 3, '2026', '2026-3-00006', 'failed', 0, NULL, '', NULL, '2026-01-28 21:14:05', '2026-01-28 21:14:11', '5e15bd8cda2d3ea09836ed016035a029'),
('a3beccf65f6242ed1a5c913f2a955911', 30, 1, '2026', '2026-1-00004', 'passed', 0, '2026', '', NULL, '2026-02-26 13:16:32', '2026-02-26 13:16:32', NULL),
('a57cca9c-64c5-4b84-955d-a5c2d206eaa9', 22, 2, '2026', '2026-2-00019', 'approved', 0, NULL, NULL, NULL, '2026-02-25 22:00:18', '2026-02-26 13:27:36', '9d8b84465b3679667db2f5fc895c1c1a'),
('a8e25a83a103a73c4b413ffc36236680', 18, 1, '2026', '1-00021', 'passed', 0, '2026', '', NULL, '2026-01-27 21:43:05', '2026-01-27 21:43:05', NULL),
('aa07bc62-809b-466d-b22d-63e720427101', 9, 2, '2026', NULL, 'passed', 0, '2026', '', NULL, '2026-01-20 19:55:47', '2026-01-20 19:56:40', 'ad42a2b17aab79038ab438be2a555112'),
('ad42a2b17aab79038ab438be2a555112', 9, 1, '2026', '2026-1-00013', 'passed', 0, '2026', '', NULL, '2026-01-20 19:53:42', '2026-01-20 19:53:42', NULL),
('b31048aeec894f93d2023aede191dda3', 20, 1, '2026', '2026-1-00025', 'passed', 0, '2026', '', NULL, '2026-01-30 19:39:45', '2026-01-30 19:40:06', '2fe33abe704e62ae201dac7928ba28c8'),
('b73e3cd421c8caae99ffd9c5ce6e36b7', 18, 2, '2026', '2026-2-00013', 'failed', 0, NULL, NULL, NULL, '2026-01-28 20:39:43', '2026-01-28 20:39:49', 'a8e25a83a103a73c4b413ffc36236680'),
('beea67cb4c64b21067f2902674a3fbe6', 20, 3, '2026', '2026-3-00010', 'passed', 0, '2026', '', NULL, '2026-01-30 19:41:53', '2026-01-30 19:42:05', '35454f007ce1100b94261bd70bcc26fb'),
('c44698e477a3fdda6a1d523a84de87f1', 20, 1, '2026', '2026-1-00023', 'failed', 0, '2026', '', NULL, '2026-01-30 19:27:08', '2026-01-30 19:27:08', NULL),
('d568f61a596d5f57b2d76dbfee1ae725', 29, 1, '2026', '2026-1-00005', 'failed', 0, '2026', '', NULL, '2026-02-26 13:19:32', '2026-02-26 13:19:32', NULL),
('d5c0f279e14e9a008e4d605055321376', 16, 2, '2026', '2026-2-00011', 'failed', 0, NULL, NULL, NULL, '2026-01-27 20:27:42', '2026-01-28 20:54:05', '2eae0ac13a3f7da0c9eab80bf7d32e56'),
('e1b4c159-a638-4674-a24a-f45acda031a8', 13, 2, '2026', '2026-2-00009', 'passed', 0, '2026', '', NULL, '2026-01-21 15:29:21', '2026-01-21 15:30:50', '2152df7f589a0ccdaae5d17a65361581'),
('e64bcd6f45f7b1e3fbeab8ec3aaff901', 29, 1, '2026', '2026-1-00006', 'pending', 0, NULL, NULL, NULL, '2026-02-26 13:20:51', '2026-02-26 13:20:51', 'd568f61a596d5f57b2d76dbfee1ae725'),
('e97a3a963faef30d669198975f4b6218', 21, 1, '2026', '2026-1-00026', 'passed', 0, '2026', '', NULL, '2026-01-30 20:03:48', '2026-01-30 20:03:48', NULL),
('fd9516a5-703b-4bb4-9b30-312c91790918', 30, 3, '2026', '2026-3-00011', 'passed', 0, '2026', '', NULL, '2026-02-26 13:21:50', '2026-02-26 13:22:05', 'a361a0c9-ab2c-4e05-b35b-b86b086780f2');

-- --------------------------------------------------------

--
-- Table structure for table `registration_publish`
--

CREATE TABLE `registration_publish` (
  `id` int(11) NOT NULL,
  `level1` tinyint(1) NOT NULL DEFAULT 0,
  `level2` tinyint(1) NOT NULL DEFAULT 0,
  `level3` tinyint(1) NOT NULL DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `result_level1` tinyint(1) DEFAULT 0,
  `result_level2` tinyint(1) DEFAULT 0,
  `result_level3` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Dumping data for table `registration_publish`
--

INSERT INTO `registration_publish` (`id`, `level1`, `level2`, `level3`, `updated_at`, `result_level1`, `result_level2`, `result_level3`) VALUES
(1, 0, 0, 0, '2026-02-26 06:55:48', 0, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `registration_sequence`
--

CREATE TABLE `registration_sequence` (
  `id` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `results`
--

CREATE TABLE `results` (
  `id` int(11) NOT NULL,
  `roll_number` varchar(255) DEFAULT NULL,
  `status` enum('Passed','Failed') DEFAULT 'Passed',
  `published_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `published_by` int(11) DEFAULT NULL,
  `is_published` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `results`
--

INSERT INTO `results` (`id`, `roll_number`, `status`, `published_at`, `published_by`, `is_published`) VALUES
(1, '2026-1-00016', 'Passed', '2026-01-21 14:08:08', 1, 0),
(2, '2026-1-00015', 'Passed', '2026-01-21 14:08:34', 1, 1),
(3, '2026-1-00013', 'Passed', '2026-01-21 14:08:51', 1, 1),
(4, '2026-1-00014', 'Passed', '2026-01-21 15:56:52', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `roll_formats`
--

CREATE TABLE `roll_formats` (
  `id` int(11) NOT NULL,
  `format_string` varchar(255) NOT NULL,
  `year_format` enum('YYYY','YY','NONE') DEFAULT 'YYYY',
  `seq_length` tinyint(4) DEFAULT 5,
  `is_active` tinyint(4) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roll_formats`
--

INSERT INTO `roll_formats` (`id`, `format_string`, `year_format`, `seq_length`, `is_active`, `created_at`) VALUES
(1, '{YEAR}-{LEVEL}-{SEQ}', 'YYYY', 5, 0, '2026-01-27 14:09:19'),
(2, '{27}-{LEVEL}-{SEQ}', 'YY', 5, 0, '2026-01-27 14:17:52'),
(3, '{YEAR}-{LEVEL}-{SEQ}', 'NONE', 5, 0, '2026-01-27 14:19:25'),
(4, '{YEAR}-{LEVEL}-{SEQ}', 'YY', 5, 0, '2026-01-27 14:19:33'),
(5, '{LEVEL}-{SEQ}', 'YY', 5, 0, '2026-01-27 14:20:15'),
(6, '{YEAR}-{LEVEL}-{SEQ}', 'YYYY', 0, 0, '2026-01-27 14:21:03'),
(7, '{YEAR}-{LEVEL}-{SEQ', 'YYYY', 5, 0, '2026-01-27 15:45:57'),
(8, '{YEAR}-{LEVEL}-{SEQ}', 'YYYY', 5, 0, '2026-01-27 15:46:03'),
(9, '{LEVEL}-{SEQ}', 'YYYY', 5, 0, '2026-02-11 13:03:19'),
(10, '{YEAR}-{LEVEL}-{SEQ}', 'YYYY', 5, 0, '2026-02-11 13:03:29'),
(11, '{LEVEL}-{SEQ}', 'YYYY', 5, 1, '2026-02-26 06:57:27');

-- --------------------------------------------------------

--
-- Table structure for table `roll_publishings`
--

CREATE TABLE `roll_publishings` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `level` tinyint(4) NOT NULL,
  `year` year(4) NOT NULL,
  `publish_start` datetime DEFAULT NULL,
  `publish_end` datetime DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roll_publishings`
--

INSERT INTO `roll_publishings` (`id`, `name`, `description`, `level`, `year`, `publish_start`, `publish_end`, `is_active`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Level1', 'must be', 1, '2026', '2026-01-23 16:17:00', '2026-01-23 17:17:00', 1, 1, '2026-01-23 22:48:48', '2026-02-26 13:15:35'),
(2, NULL, NULL, 2, '2026', '2026-01-26 20:40:00', '2026-01-28 20:40:00', 1, 1, '2026-01-26 20:42:58', '2026-02-25 21:59:21'),
(3, NULL, NULL, 3, '2026', '2026-01-26 20:51:00', '2026-01-27 20:51:00', 0, 1, '2026-01-26 20:52:00', '2026-01-30 20:00:39');

-- --------------------------------------------------------

--
-- Table structure for table `roll_sequences`
--

CREATE TABLE `roll_sequences` (
  `year` year(4) NOT NULL,
  `level` tinyint(4) NOT NULL,
  `last_seq` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roll_sequences`
--

INSERT INTO `roll_sequences` (`year`, `level`, `last_seq`) VALUES
('2026', 1, 6),
('2026', 2, 5);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `candidates`
--
ALTER TABLE `candidates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `exam_results`
--
ALTER TABLE `exam_results`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `registration_id` (`registration_id`),
  ADD KEY `fk_results_admin` (`published_by`);

--
-- Indexes for table `feedback_submissions`
--
ALTER TABLE `feedback_submissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `registrations`
--
ALTER TABLE `registrations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roll_number` (`roll_number`),
  ADD KEY `fk_reg_previous` (`previous_registration_id`),
  ADD KEY `idx_reg_candidate_level` (`candidate_id`,`level`),
  ADD KEY `fk_reg_result` (`result_id`);

--
-- Indexes for table `registration_publish`
--
ALTER TABLE `registration_publish`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `registration_sequence`
--
ALTER TABLE `registration_sequence`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `results`
--
ALTER TABLE `results`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roll_formats`
--
ALTER TABLE `roll_formats`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roll_publishings`
--
ALTER TABLE `roll_publishings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `level` (`level`),
  ADD KEY `publish_start` (`publish_start`),
  ADD KEY `publish_end` (`publish_end`),
  ADD KEY `idx_roll_publishings_year_level` (`year`,`level`,`is_active`);

--
-- Indexes for table `roll_sequences`
--
ALTER TABLE `roll_sequences`
  ADD PRIMARY KEY (`year`,`level`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `candidates`
--
ALTER TABLE `candidates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `exam_results`
--
ALTER TABLE `exam_results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feedback_submissions`
--
ALTER TABLE `feedback_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `registration_sequence`
--
ALTER TABLE `registration_sequence`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `results`
--
ALTER TABLE `results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `roll_formats`
--
ALTER TABLE `roll_formats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `roll_publishings`
--
ALTER TABLE `roll_publishings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `exam_results`
--
ALTER TABLE `exam_results`
  ADD CONSTRAINT `fk_results_admin` FOREIGN KEY (`published_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_results_registration` FOREIGN KEY (`registration_id`) REFERENCES `registrations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `registrations`
--
ALTER TABLE `registrations`
  ADD CONSTRAINT `fk_reg_previous` FOREIGN KEY (`previous_registration_id`) REFERENCES `registrations` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_reg_result` FOREIGN KEY (`result_id`) REFERENCES `exam_results` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `registrations_ibfk_1` FOREIGN KEY (`candidate_id`) REFERENCES `candidates` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
