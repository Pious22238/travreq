-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 14, 2025 at 01:01 PM
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
-- Database: `travel_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `destinations`
--

CREATE TABLE `destinations` (
  `id` int(11) NOT NULL,
  `destination_name` varchar(100) NOT NULL,
  `country` varchar(100) NOT NULL,
  `purpose` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `destinations`
--

INSERT INTO `destinations` (`id`, `destination_name`, `country`, `purpose`, `created_at`) VALUES
(1, 'oaksdpoasjk', 'okasdpodjaspo', NULL, '2025-11-02 16:01:40'),
(2, 'Manila', 'Philippines', 'Business', '2025-11-02 16:03:18'),
(3, 'Cebu City', 'Philippines', 'Training', '2025-11-02 16:03:18'),
(4, 'aposkd', 'asjkpodjaspo', NULL, '2025-11-02 16:23:51'),
(5, 'asdasdasdasd', 'Philippines', NULL, '2025-11-02 16:36:06'),
(6, 'oaksdpoasjk', 'Philippines', NULL, '2025-11-02 16:37:43'),
(7, 'zamboanga del sur', 'Philippines', NULL, '2025-11-03 03:03:19'),
(8, 'asdkoadsk', 'askdpoasj', NULL, '2025-11-04 12:56:18'),
(9, 'bataan', 'philippines', NULL, '2025-11-08 11:02:33'),
(10, 'asldkas;lk', 'as;ldklas;k', NULL, '2025-11-08 18:38:22'),
(11, 'philippines', 'bonton', NULL, '2025-11-16 21:55:06'),
(12, 'sulu', 'Philippines', NULL, '2025-12-08 12:15:13'),
(13, 'alsdklkasldk', 'kasldkalsdk', NULL, '2025-12-08 20:03:23'),
(14, 'dkaoskd', 'asodkasdko', NULL, '2025-12-13 15:33:02'),
(15, 'jasiodjasj', 'ijasiodjasiodj', NULL, '2025-12-14 06:06:58'),
(16, 'poposadpo', 'aoskdpoj', NULL, '2025-12-14 06:30:10'),
(17, 'asodkpoasjdpoj', 'jdpoasjdpojas', NULL, '2025-12-14 09:06:32'),
(18, 'ASODJASDJKJAS', 'OASJPDJASPODJ', NULL, '2025-12-14 09:23:55');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `department` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `full_name`, `department`, `email`, `created_at`, `user_id`) VALUES
(1, 'ASDNASJDN', 'ODASODKAS', 'IAJSSDQWIEJ@GMIAL.COM', '2025-11-02 16:02:24', NULL),
(4, 'as;lmdkasdj', 'podjaspjdpo', 'podkaspojdpowj22@gmail.com', '2025-11-02 16:34:50', NULL),
(14, '', '', '', '2025-12-14 06:02:17', NULL),
(16, 'asdkasjd', 'jioasjdiojiaso', 'jioasjdiojasio@gmail.com', '2025-12-14 06:06:31', NULL),
(17, 'askmdlkas', 'aspodjaspo', 'pojposdjaposd@gmai.com', '2025-12-14 06:17:19', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `enrollment_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `course_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `liquidations`
--

CREATE TABLE `liquidations` (
  `id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `total_amount` decimal(12,2) DEFAULT 0.00,
  `balance` decimal(12,2) DEFAULT 0.00,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `liquidations`
--

INSERT INTO `liquidations` (`id`, `request_id`, `total_amount`, `balance`, `created_at`, `updated_at`) VALUES
(1, 16, 120462.00, 2661.00, '2025-11-13 07:10:28', '2025-11-14 07:18:06');

-- --------------------------------------------------------

--
-- Table structure for table `liquidation_history`
--

CREATE TABLE `liquidation_history` (
  `id` int(11) NOT NULL,
  `report_id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `balance` decimal(12,2) DEFAULT 0.00,
  `saved_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_amount` decimal(12,2) DEFAULT NULL,
  `total_expense` decimal(12,2) DEFAULT NULL,
  `remaining_balance` decimal(12,2) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Pending',
  `pdf_path` varchar(255) DEFAULT NULL,
  `action` varchar(100) DEFAULT NULL,
  `employee_name` varchar(255) DEFAULT NULL,
  `purpose` varchar(255) DEFAULT NULL,
  `admin_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `liquidation_history`
--

INSERT INTO `liquidation_history` (`id`, `report_id`, `request_id`, `balance`, `saved_by`, `created_at`, `total_amount`, `total_expense`, `remaining_balance`, `status`, `pdf_path`, `action`, `employee_name`, `purpose`, `admin_name`) VALUES
(1, 1, 17, 2535.01, 1, '2025-11-23 10:13:41', 30000.00, 27464.99, 2535.01, 'Pending', NULL, 'Created', NULL, 'wondering around the world', NULL),
(2, 2, 17, 2535.01, 4, '2025-12-14 07:10:27', 30000.00, 27464.99, 2535.01, 'Pending', NULL, 'Created', NULL, 'wondering around the world', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `liquidation_items`
--

CREATE TABLE `liquidation_items` (
  `id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `expense_date` date DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `liquidation_items`
--

INSERT INTO `liquidation_items` (`id`, `request_id`, `description`, `expense_date`, `amount`, `remarks`, `created_at`) VALUES
(1, 17, 'Transportation', NULL, 119.99, NULL, '2025-11-23 10:09:48'),
(2, 17, 'Accommodation', NULL, 123.00, NULL, '2025-11-23 10:09:48'),
(3, 17, 'Meals', NULL, 22222.00, NULL, '2025-11-23 10:09:48'),
(5, 19, 'Transportation', NULL, 0.00, NULL, '2025-12-10 14:54:04'),
(6, 19, 'Accommodation', NULL, 0.00, NULL, '2025-12-10 14:54:04'),
(7, 19, 'Meals', NULL, 0.00, NULL, '2025-12-10 14:54:04'),
(8, 19, 'Miscellaneous', NULL, 0.00, NULL, '2025-12-10 14:54:04'),
(9, 18, 'Transportation', NULL, 0.00, NULL, '2025-12-13 15:14:20'),
(10, 18, 'Accommodation', NULL, 0.00, NULL, '2025-12-13 15:14:20'),
(11, 18, 'Meals', NULL, 0.00, NULL, '2025-12-13 15:14:20'),
(12, 18, 'Miscellaneous', NULL, 0.00, NULL, '2025-12-13 15:14:20'),
(13, 20, 'Transportation', NULL, 0.00, NULL, '2025-12-13 15:33:06'),
(14, 20, 'Accommodation', NULL, 0.00, NULL, '2025-12-13 15:33:06'),
(15, 20, 'Meals', NULL, 0.00, NULL, '2025-12-13 15:33:06'),
(16, 20, 'Miscellaneous', NULL, 0.00, NULL, '2025-12-13 15:33:06'),
(17, 23, 'Transportation', NULL, 0.00, NULL, '2025-12-14 06:50:22'),
(18, 23, 'Accommodation', NULL, 0.00, NULL, '2025-12-14 06:50:22'),
(19, 23, 'Meals', NULL, 0.00, NULL, '2025-12-14 06:50:22'),
(20, 23, 'Miscellaneous', NULL, 0.00, NULL, '2025-12-14 06:50:22'),
(21, 13, 'Transportation', NULL, 0.00, NULL, '2025-12-14 07:11:59'),
(22, 13, 'Accommodation', NULL, 0.00, NULL, '2025-12-14 07:11:59'),
(23, 13, 'Meals', NULL, 0.00, NULL, '2025-12-14 07:11:59'),
(24, 13, 'Miscellaneous', NULL, 0.00, NULL, '2025-12-14 07:11:59'),
(25, 13, 'New Expense', NULL, 0.00, NULL, '2025-12-14 07:12:13'),
(26, 13, 'New Expense', NULL, 0.00, NULL, '2025-12-14 07:12:14'),
(27, 13, 'New Expense', NULL, 0.00, NULL, '2025-12-14 07:12:14'),
(28, 13, 'New Expense', NULL, 0.00, NULL, '2025-12-14 07:12:16'),
(29, 13, 'New Expense', NULL, 0.00, NULL, '2025-12-14 07:12:17'),
(30, 13, 'New Expense', NULL, 0.00, NULL, '2025-12-14 07:12:18'),
(31, 13, 'New Expense', NULL, 0.00, NULL, '2025-12-14 07:12:20'),
(32, 13, 'New Expense', NULL, 0.00, NULL, '2025-12-14 07:12:20'),
(33, 13, 'New Expense', NULL, 0.00, NULL, '2025-12-14 07:13:17'),
(34, 13, 'New Expense', NULL, 0.00, NULL, '2025-12-14 07:13:17'),
(35, 13, 'New Expense', NULL, 0.00, NULL, '2025-12-14 07:13:17'),
(44, 13, 'New Expense', NULL, 0.00, NULL, '2025-12-14 07:32:54'),
(45, 23, 'New Expense', NULL, 0.00, NULL, '2025-12-14 07:36:40'),
(46, 23, 'New Expense', NULL, 0.00, NULL, '2025-12-14 07:36:41'),
(47, 23, 'New Expense', NULL, 0.00, NULL, '2025-12-14 07:36:43'),
(48, 23, 'New Expense', NULL, 0.00, NULL, '2025-12-14 07:36:45'),
(49, 23, 'New Expense', NULL, 0.00, NULL, '2025-12-14 07:36:45'),
(50, 23, 'New Expense', NULL, 0.00, NULL, '2025-12-14 07:37:17'),
(51, 23, 'New Expense', NULL, 0.00, NULL, '2025-12-14 07:37:17'),
(52, 23, 'New Expense', NULL, 0.00, NULL, '2025-12-14 07:37:19'),
(53, 17, 'New Expense', NULL, 0.00, NULL, '2025-12-14 07:37:57'),
(54, 17, 'New Expense', NULL, 0.00, NULL, '2025-12-14 07:38:23'),
(55, 17, 'New Expense', NULL, 0.00, NULL, '2025-12-14 07:38:34'),
(56, 17, 'New Expense', NULL, 0.00, NULL, '2025-12-14 07:38:36');

-- --------------------------------------------------------

--
-- Table structure for table `liquidation_reports`
--

CREATE TABLE `liquidation_reports` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `total_amount` decimal(12,2) DEFAULT NULL,
  `total_expense` decimal(12,2) DEFAULT NULL,
  `remaining_balance` decimal(12,2) DEFAULT NULL,
  `pdf_path` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `saved_by` int(11) NOT NULL,
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `liquidation_reports`
--

INSERT INTO `liquidation_reports` (`id`, `employee_id`, `request_id`, `total_amount`, `total_expense`, `remaining_balance`, `pdf_path`, `status`, `created_at`, `saved_by`, `remarks`) VALUES
(1, 4, 17, 30000.00, 27464.99, 2535.01, NULL, 'Pending', '2025-11-23 10:13:41', 1, 'Saved via UI'),
(2, 4, 17, 30000.00, 27464.99, 2535.01, NULL, 'Pending', '2025-12-14 07:10:27', 4, 'Saved via UI');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `travel_requests`
--

CREATE TABLE `travel_requests` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `destination_id` int(11) NOT NULL,
  `reason` text NOT NULL,
  `travel_date` date NOT NULL,
  `departure_date` date NOT NULL,
  `return_date` date NOT NULL,
  `total_cost` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('Pending','Approved','Rejected','Withdrawn') NOT NULL DEFAULT 'Pending',
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `liquidation_total` decimal(10,2) DEFAULT 0.00,
  `liquidation_balance` decimal(10,2) DEFAULT 0.00,
  `last_modified_by` varchar(100) DEFAULT NULL,
  `last_modified_at` datetime DEFAULT NULL,
  `withdrawal_reason` text DEFAULT NULL,
  `withdrawal_status` enum('None','Pending','Approved','Rejected') DEFAULT 'None',
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `travel_requests`
--

INSERT INTO `travel_requests` (`id`, `employee_id`, `destination_id`, `reason`, `travel_date`, `departure_date`, `return_date`, `total_cost`, `status`, `user_id`, `created_at`, `liquidation_total`, `liquidation_balance`, `last_modified_by`, `last_modified_at`, `withdrawal_reason`, `withdrawal_status`, `is_deleted`) VALUES
(7, 1, 1, 'Business Meeting', '2025-11-03', '2025-11-03', '2025-11-06', 2500.00, 'Approved', 1, '2025-11-02 16:28:10', 0.00, 0.00, NULL, NULL, NULL, 'None', 0),
(11, 4, 5, 'asdkasp', '2121-12-31', '0000-00-00', '2312-12-31', 1.00, 'Approved', 4, '2025-11-02 16:36:06', 0.00, 0.00, '4', '2025-11-23 17:39:10', NULL, 'None', 0),
(12, 1, 6, 'asp[dkaoskd', '3121-12-31', '2123-12-31', '1212-12-12', 1.00, 'Approved', 1, '2025-11-02 16:37:43', 0.00, 0.00, NULL, NULL, NULL, 'None', 0),
(13, 4, 7, 'vacation', '2025-12-11', '2025-12-11', '2026-01-12', 10000.00, 'Approved', 4, '2025-11-03 03:03:19', 9910.00, 0.00, '4', '2025-11-23 17:32:41', NULL, 'None', 0),
(14, 4, 8, 'djaspkjdiowebg', '0323-12-31', '0232-12-31', '0002-12-31', 12312039.00, 'Approved', 4, '2025-11-04 12:56:18', 0.00, 0.00, NULL, NULL, NULL, 'None', 0),
(16, 4, 10, 'laskd;aslkd', '2312-12-31', '2321-12-31', '2312-12-31', 123123.00, '', 4, '2025-11-08 18:38:22', 120462.00, 0.00, '4', '2025-11-23 16:56:43', NULL, 'None', 0),
(17, 4, 11, 'wondering around the world', '2025-12-12', '2025-12-29', '2025-12-30', 30000.00, 'Approved', 4, '2025-11-16 21:55:06', 30000.00, 2535.01, '4', '2025-12-14 15:10:27', NULL, 'None', 0),
(18, 1, 12, 'travel', '2025-12-12', '2025-12-12', '2024-01-12', 6000.00, 'Withdrawn', 1, '2025-12-08 12:15:13', 0.00, 0.00, '1', '2025-12-14 17:21:54', NULL, 'None', 0),
(19, 4, 13, 'p[alsd[lak[sldkas[dk', '1231-02-13', '2321-12-31', '2321-12-31', 1231231.00, 'Withdrawn', 4, '2025-12-08 20:03:23', 0.00, 0.00, '4', '2025-12-13 17:15:58', NULL, 'None', 0),
(20, 4, 14, 'dokasdkoaskd', '2312-12-31', '0000-00-00', '0000-00-00', 11.00, 'Withdrawn', 4, '2025-12-13 15:33:03', 0.00, 0.00, '4', '2025-12-13 23:34:45', NULL, 'None', 0),
(21, 16, 15, 'iojasiodjioasjiodj', '2321-12-31', '1232-02-13', '0213-02-13', 123123.00, 'Withdrawn', 16, '2025-12-14 06:06:58', 0.00, 0.00, '16', '2025-12-14 14:07:07', NULL, 'None', 0),
(22, 17, 12, 'bussiness proposal', '2025-12-12', '2025-12-12', '2024-01-12', 6000.00, 'Withdrawn', 17, '2025-12-14 06:18:13', 0.00, 0.00, '17', '2025-12-14 14:19:02', NULL, 'None', 0),
(23, 17, 16, 'oppoaspodpo', '0000-00-00', '0000-00-00', '0000-00-00', 12312.00, 'Approved', 17, '2025-12-14 06:30:10', 0.00, 0.00, '17', '2025-12-14 14:30:16', NULL, 'None', 0),
(24, 4, 17, 'oakspodjaspdjopj', '1321-12-31', '1232-12-21', '2312-12-31', 2323.00, 'Withdrawn', 4, '2025-12-14 09:06:32', 0.00, 0.00, '4', '2025-12-14 17:06:43', NULL, 'None', 0),
(25, 1, 18, 'DPOASJDPOASJDPO', '2331-12-31', '1231-02-13', '1231-02-13', 123123.00, 'Withdrawn', 1, '2025-12-14 09:23:55', 0.00, 0.00, '1', '2025-12-14 17:24:01', NULL, 'None', 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `employee_id` int(11) DEFAULT NULL,
  `remaining_balance` decimal(12,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `created_at`, `employee_id`, `remaining_balance`) VALUES
(1, 'admin', NULL, '$2y$10$mrZlf0GnZ98Uq.pssUqDPumWQCaZUsEerXpaEmjvPbpkLNCag53Ga', 'admin', '2025-11-02 16:01:13', NULL, 0.00),
(4, 'jd', NULL, '$2y$10$LGXnqmw4mEmGAnUyaCxLJexoDuUjBG/EjBr.MYOZfxarhOlhDKbDy', 'user', '2025-11-02 16:23:21', NULL, 0.00),
(6, 'ken', NULL, '$2y$10$gPXh8AOy9MwYZcuL0gPDO.0FxGFtke0IHW2GOrOBTEpx6a.gf3bVS', 'user', '2025-11-02 17:57:35', NULL, 0.00),
(7, 'clint', NULL, '$2y$10$Yl8hGAJ2s9kcKNhlALM7RO.6yAHrGKa0xbJnFb2bpIymfUeYT.2Zm', 'user', '2025-12-14 04:32:11', NULL, 0.00),
(8, 'clint12', NULL, '$2y$10$vGFOi8jSasVX07wOXTfy0ecuUshgVFuiAf4cXAvrctY1Pmxwv1mIO', 'user', '2025-12-14 04:38:38', NULL, 0.00),
(9, 'clin1233', NULL, '$2y$10$LXHOpyBNpi52VYULEJjVV.fotbB.L0TbU0DO.pTl3T31to4bAyNFW', 'user', '2025-12-14 04:46:04', NULL, 0.00),
(10, 'wynry', NULL, '$2y$10$r8gPuHYSj8dGtZSsV0VC7eevClMLBh.uDo4KUGmqb0qscmVXO40R2', 'user', '2025-12-14 04:46:44', NULL, 0.00),
(11, 'wynry12', NULL, '$2y$10$fOhKGSrXZAHEYsndqyffXe5iw3xxSyOsepxlD3Um5ddROnqRj4rZS', 'user', '2025-12-14 04:47:58', NULL, 0.00),
(12, 'ronel', NULL, '$2y$10$jealBBt0npSCtpPZCGHhZuIb.Gxp7EfnQk6EuOn5o3har6i2H/fh6', 'user', '2025-12-14 04:50:23', NULL, 0.00),
(13, 'ken123', NULL, '$2y$10$slvtvh1WOkloHnAWUX82ce28Yt5P0HQHJhxEOcpttuGz2PT.zkO/q', 'user', '2025-12-14 04:52:15', NULL, 0.00),
(14, '', NULL, '$2y$10$e3XUkls8xKOhyt/lAx0WEe/XzdMzK1BBiY85qaFV0cXbClE4ZQtNK', 'user', '2025-12-14 06:02:17', NULL, 0.00),
(16, 'anthony222', NULL, '$2y$10$4Wu1nDwdfaMrQusMYOxjv.NAv/Y4ZIHR6nBsIGiZ2Zq/No5/O6qSu', 'user', '2025-12-14 06:06:31', NULL, 0.00),
(17, 'mary', NULL, '$2y$10$1Iy.AFW3id3FEUg9QrPhm.F45Ejx6XNTDcnYfyUW8ualN6F05PLVO', 'user', '2025-12-14 06:17:19', NULL, 0.00);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_liquidation_summaries`
-- (See below for the actual view)
--
CREATE TABLE `vw_liquidation_summaries` (
`id` int(11)
,`request_id` int(11)
,`saved_by` int(11)
,`total_amount` decimal(12,2)
,`total_expense` decimal(12,2)
,`remaining_balance` decimal(12,2)
,`status` varchar(50)
,`pdf_path` varchar(255)
,`created_at` timestamp
);

-- --------------------------------------------------------

--
-- Table structure for table `withdrawal_audit_logs`
--

CREATE TABLE `withdrawal_audit_logs` (
  `id` int(11) NOT NULL,
  `withdrawal_id` int(11) NOT NULL,
  `action` enum('Submitted','Approved','Rejected') NOT NULL,
  `actor_id` int(11) NOT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `withdrawal_history`
--

CREATE TABLE `withdrawal_history` (
  `id` int(11) NOT NULL,
  `withdrawal_id` int(11) NOT NULL,
  `travel_request_id` int(11) NOT NULL,
  `action` varchar(50) NOT NULL,
  `actor_id` int(11) NOT NULL,
  `actor_role` varchar(30) NOT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `withdrawal_requests`
--

CREATE TABLE `withdrawal_requests` (
  `id` int(11) NOT NULL,
  `travel_request_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `reason` text NOT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `admin_remarks` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `withdrawal_requests`
--

INSERT INTO `withdrawal_requests` (`id`, `travel_request_id`, `user_id`, `reason`, `status`, `admin_remarks`, `created_at`, `updated_at`) VALUES
(1, 19, 4, 'ASDASDASDASD', 'Pending', NULL, '2025-12-09 04:19:33', '2025-12-09 04:19:33');

-- --------------------------------------------------------

--
-- Structure for view `vw_liquidation_summaries`
--
DROP TABLE IF EXISTS `vw_liquidation_summaries`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_liquidation_summaries`  AS SELECT `liquidation_history`.`id` AS `id`, `liquidation_history`.`request_id` AS `request_id`, `liquidation_history`.`saved_by` AS `saved_by`, `liquidation_history`.`total_amount` AS `total_amount`, `liquidation_history`.`total_expense` AS `total_expense`, `liquidation_history`.`remaining_balance` AS `remaining_balance`, `liquidation_history`.`status` AS `status`, `liquidation_history`.`pdf_path` AS `pdf_path`, `liquidation_history`.`created_at` AS `created_at` FROM `liquidation_history` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `destinations`
--
ALTER TABLE `destinations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_destination` (`destination_name`,`country`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`enrollment_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `liquidations`
--
ALTER TABLE `liquidations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `request_id` (`request_id`);

--
-- Indexes for table `liquidation_history`
--
ALTER TABLE `liquidation_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `liquidation_items`
--
ALTER TABLE `liquidation_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `request_id` (`request_id`);

--
-- Indexes for table `liquidation_reports`
--
ALTER TABLE `liquidation_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `request_id` (`request_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `travel_requests`
--
ALTER TABLE `travel_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `destination_id` (`destination_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `employee_id` (`employee_id`);

--
-- Indexes for table `withdrawal_audit_logs`
--
ALTER TABLE `withdrawal_audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `withdrawal_id` (`withdrawal_id`);

--
-- Indexes for table `withdrawal_history`
--
ALTER TABLE `withdrawal_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `withdrawal_id` (`withdrawal_id`),
  ADD KEY `travel_request_id` (`travel_request_id`);

--
-- Indexes for table `withdrawal_requests`
--
ALTER TABLE `withdrawal_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `travel_request_id` (`travel_request_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `destinations`
--
ALTER TABLE `destinations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `enrollment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `liquidations`
--
ALTER TABLE `liquidations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `liquidation_history`
--
ALTER TABLE `liquidation_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `liquidation_items`
--
ALTER TABLE `liquidation_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `liquidation_reports`
--
ALTER TABLE `liquidation_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `travel_requests`
--
ALTER TABLE `travel_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `withdrawal_audit_logs`
--
ALTER TABLE `withdrawal_audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `withdrawal_history`
--
ALTER TABLE `withdrawal_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `withdrawal_requests`
--
ALTER TABLE `withdrawal_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`);

--
-- Constraints for table `liquidations`
--
ALTER TABLE `liquidations`
  ADD CONSTRAINT `liquidations_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `travel_requests` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `liquidation_items`
--
ALTER TABLE `liquidation_items`
  ADD CONSTRAINT `liquidation_items_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `travel_requests` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `liquidation_reports`
--
ALTER TABLE `liquidation_reports`
  ADD CONSTRAINT `liquidation_reports_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `travel_requests` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `travel_requests`
--
ALTER TABLE `travel_requests`
  ADD CONSTRAINT `travel_requests_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `travel_requests_ibfk_2` FOREIGN KEY (`destination_id`) REFERENCES `destinations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `travel_requests_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `withdrawal_audit_logs`
--
ALTER TABLE `withdrawal_audit_logs`
  ADD CONSTRAINT `withdrawal_audit_logs_ibfk_1` FOREIGN KEY (`withdrawal_id`) REFERENCES `withdrawal_requests` (`id`);

--
-- Constraints for table `withdrawal_history`
--
ALTER TABLE `withdrawal_history`
  ADD CONSTRAINT `fk_history_withdraw` FOREIGN KEY (`withdrawal_id`) REFERENCES `withdrawal_requests` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `withdrawal_requests`
--
ALTER TABLE `withdrawal_requests`
  ADD CONSTRAINT `fk_withdraw_tr` FOREIGN KEY (`travel_request_id`) REFERENCES `travel_requests` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
