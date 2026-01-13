-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 13, 2026 at 07:58 PM
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
-- Database: `jeevchi`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action_type` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `reference_table` varchar(100) DEFAULT NULL,
  `reference_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `advance_salary`
--

CREATE TABLE `advance_salary` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `payment_type` varchar(20) NOT NULL,
  `upi_id` varchar(100) DEFAULT NULL,
  `account_number` varchar(30) DEFAULT NULL,
  `ifsc_code` varchar(20) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `request_date` date NOT NULL,
  `reason` text NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `advance_salary`
--

INSERT INTO `advance_salary` (`id`, `user_id`, `payment_type`, `upi_id`, `account_number`, `ifsc_code`, `amount`, `request_date`, `reason`, `status`, `created_at`) VALUES
(1, 5, 'Cash', NULL, NULL, NULL, 100.00, '2001-01-01', 'uiu', 'pending', '2026-01-03 15:58:10'),
(2, 5, 'Bank Transfer', NULL, '105156116166166', 'HDFCSGCDYDVUW', 50000.00, '1111-11-11', 'GSUSGquq', 'pending', '2026-01-03 15:59:27'),
(3, 5, 'Cash', NULL, NULL, NULL, 100.00, '2026-01-01', 'GG', 'pending', '2026-01-03 19:03:34'),
(4, 5, 'Cash', NULL, NULL, NULL, 5000.00, '2006-01-01', 'GGG', 'pending', '2026-01-03 19:22:27'),
(5, 5, 'Cash', NULL, NULL, NULL, 50.00, '2006-01-01', 'FF', 'pending', '2026-01-03 19:23:24'),
(6, 5, 'Cash', NULL, NULL, NULL, 5000.00, '2006-01-01', 'fefewf', 'pending', '2026-01-03 19:56:15'),
(7, 5, 'Cash', NULL, NULL, NULL, 5555.00, '5555-01-11', 'eefef', 'pending', '2026-01-03 19:58:47'),
(8, 5, 'Cash', NULL, NULL, NULL, 88989.00, '0101-01-08', 'dcfwfwfcwebug\r\n', 'pending', '2026-01-03 20:04:23'),
(9, 5, 'Cash', NULL, NULL, NULL, 50000.00, '1111-01-01', 'NOE', 'pending', '2026-01-03 20:05:59'),
(10, 5, 'Cash', NULL, NULL, NULL, 500.00, '2000-01-01', 'wssww', 'pending', '2026-01-03 20:11:12'),
(11, 5, 'Cash', NULL, NULL, NULL, 500.00, '2026-01-01', 'GT', 'pending', '2026-01-12 19:43:02'),
(12, 5, 'Cash', NULL, NULL, NULL, 45000.00, '2027-01-01', 'NOW', 'pending', '2026-01-12 20:03:16'),
(13, 5, 'Cash', NULL, NULL, NULL, 1000000.00, '2026-01-13', 'Money Back', 'pending', '2026-01-13 17:36:03'),
(14, 5, 'Bank Transfer', NULL, '', '', 10000000.00, '2025-01-10', 'Money', 'pending', '2026-01-13 18:10:07'),
(15, 5, 'Cash', NULL, NULL, NULL, 500000.00, '2025-01-01', 'GTTTTTT', 'pending', '2026-01-13 18:24:00');

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `punch_in` datetime DEFAULT NULL,
  `punch_in_location` varchar(255) DEFAULT NULL,
  `punch_out` datetime DEFAULT NULL,
  `punch_out_location` varchar(255) DEFAULT NULL,
  `hours_worked` decimal(5,2) DEFAULT NULL,
  `created_at` date NOT NULL,
  `attempt_count` int(11) DEFAULT 0,
  `attendance_date` date GENERATED ALWAYS AS (cast(`punch_in` as date)) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `user_id`, `punch_in`, `punch_in_location`, `punch_out`, `punch_out_location`, `hours_worked`, `created_at`, `attempt_count`) VALUES
(1, 5, '2026-01-08 01:23:06', 'Lat:19.0971904, Lng:72.8891392', '2026-01-08 01:23:38', 'Lat:19.0971904, Lng:72.8891392', -4.49, '0000-00-00', 0),
(5, 9, '2026-01-09 01:08:34', 'Society Road, Asalpha, L Ward, Zone 5, Mumbai, Mumbai Suburban, Maharashtra, 400072, India', NULL, NULL, NULL, '0000-00-00', 0),
(10, 9, '2026-01-11 01:36:19', 'Society Road, Asalpha, Mumbai, Maharashtra, 400072, India', NULL, NULL, NULL, '0000-00-00', 0),
(11, 5, '2026-01-11 15:35:49', 'Vidya Vihar West, Mumbai, Maharashtra, 400070, India', NULL, NULL, NULL, '0000-00-00', 0),
(12, 10, '2026-01-11 16:50:37', 'Kurla East, Mumbai, Maharashtra, 400070, India', NULL, NULL, NULL, '0000-00-00', 0),
(13, 11, '2026-01-11 17:13:09', 'Kurla East, Mumbai, Maharashtra, 400070, India', NULL, NULL, NULL, '0000-00-00', 0),
(14, 12, '2026-01-11 17:38:57', 'Kurla East, Mumbai, Maharashtra, 400024, India', '2026-01-12 02:38:57', 'Kurla East, Mumbai, Maharashtra, 400024, India', NULL, '0000-00-00', 0);

-- --------------------------------------------------------

--
-- Table structure for table `leaves`
--

CREATE TABLE `leaves` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `reason` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `admin_remark` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `leave_type` enum('Casual','Sick','Earned','') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leaves`
--

INSERT INTO `leaves` (`id`, `user_id`, `start_date`, `end_date`, `reason`, `status`, `admin_remark`, `created_at`, `leave_type`) VALUES
(1, 5, '2026-01-01', '2026-01-02', 'Tst', 'pending', NULL, '2026-01-03 14:49:56', ''),
(2, 5, '2026-01-01', '2026-01-30', 'leaeveeeeeeeeee', 'pending', NULL, '2026-01-03 14:53:07', ''),
(3, 5, '2991-01-01', '2991-01-01', 'ksfk', 'pending', NULL, '2026-01-03 15:51:17', ''),
(4, 5, '2201-01-01', '2201-01-01', 'kadhkahdkad', 'pending', NULL, '2026-01-03 15:55:30', 'Earned'),
(5, 5, '2026-01-12', '2026-01-13', 'SICK', 'pending', NULL, '2026-01-03 20:27:24', 'Sick'),
(6, 5, '0065-01-08', '0616-12-16', 'sdfefef', 'pending', NULL, '2026-01-11 12:37:47', 'Casual');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` enum('cash','upi','bank transfer') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `reason` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `admin_remark` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(200) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `emp_id` varchar(20) DEFAULT NULL,
  `first_login` tinyint(4) DEFAULT 1,
  `join_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `status`, `created_at`, `emp_id`, `first_login`, `join_date`) VALUES
(1, 'admin', 'admin@jeevchi.com', '$2y$10$oyr/0U3PgFSzRpV4jYnhlePa0S1TbD/mGLZMnhOuyKIGh9VRPtFmu', 'admin', 'active', '2025-12-16 19:29:51', NULL, 1, NULL),
(2, 'user', 'user@jeevchi.com', '$2y$10$r7mQZt8OeKq7X9Yp6n0Z2uE9QZq8kJ0Z4n8oPZcA0lU0zM6Qm2ZJ6', 'user', 'active', '2025-12-16 19:29:51', NULL, 1, NULL),
(3, 'Test User', 'test@jeevchi.com', 'PASTE_HASH_OF_user123', 'user', 'active', '2025-12-23 19:57:30', 'EMP-TEST-001', 0, NULL),
(4, 'anjali.jha', 'anjali.jha@test.com', '$2y$10$wH5M6q7mZ3d9fJm1kQ2e5uG8dZ3lQy6sXKx9R0QkN0bR0uW7mQyK2\n', 'user', 'active', '2025-12-27 10:21:37', NULL, 1, NULL),
(5, 'john.doe', 'john.doe@test.com', '$2y$10$Rt3U0TzctSCBxOzrWq.ht.rk2wvdhK8h/bAsGm9MGu/zqeNKd3Hxe', 'user', 'active', '2025-12-27 19:20:06', 'EMP003', 1, NULL),
(7, 'admin.jeevchi', 'admin@jeevchi.com', '$2y$10$zqkG6Z8h3p6Zz6FJw5Wm5eYv8nM3k6dYpF7h3H3GZk1fGx5xZb1yC', 'admin', 'active', '2025-12-27 11:33:25', 'EMP001', 0, NULL),
(8, 'user.jeevchi', 'user@jeevchi.com', '$2y$10$K9l8kZrG3y6P6Ww4JzJHFe4W9GZ8pY9M0nZQ6bQ2Z2JcE3pCq5r2e', 'user', 'active', '2025-12-27 11:33:25', 'EMP002', 1, NULL),
(9, 'john.smith', NULL, '$2y$10$Rt3U0TzctSCBxOzrWq.ht.rk2wvdhK8h/bAsGm9MGu/zqeNKd3Hxe', 'user', 'active', '2026-01-03 11:23:07', 'JEE-0001', 1, NULL),
(10, 'Sam', NULL, '$2y$10$Rt3U0TzctSCBxOzrWq.ht.rk2wvdhK8h/bAsGm9MGu/zqeNKd3Hxe', 'user', 'active', '2026-01-11 11:15:57', NULL, 1, NULL),
(11, 'Rose', NULL, '$2y$10$Rt3U0TzctSCBxOzrWq.ht.rk2wvdhK8h/bAsGm9MGu/zqeNKd3Hxe', 'user', 'active', '2026-01-11 11:42:26', NULL, 1, NULL),
(12, 'Hash', NULL, '$2y$10$Rt3U0TzctSCBxOzrWq.ht.rk2wvdhK8h/bAsGm9MGu/zqeNKd3Hxe', 'user', 'active', '2026-01-11 12:08:19', NULL, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_profiles`
--

CREATE TABLE `user_profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(10) DEFAULT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `guardian_name` varchar(100) DEFAULT NULL,
  `aadhaar` varchar(12) DEFAULT NULL,
  `pan` varchar(10) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `permanent_address` text DEFAULT NULL,
  `correspondence_address` text DEFAULT NULL,
  `job_role` varchar(50) DEFAULT NULL,
  `date_of_joining` date DEFAULT NULL,
  `bank_name` varchar(100) DEFAULT NULL,
  `account_number` varchar(30) DEFAULT NULL,
  `ifsc_code` varchar(20) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_profiles`
--

INSERT INTO `user_profiles` (`id`, `user_id`, `title`, `full_name`, `guardian_name`, `aadhaar`, `pan`, `email`, `phone`, `permanent_address`, `correspondence_address`, `job_role`, `date_of_joining`, `bank_name`, `account_number`, `ifsc_code`, `updated_at`) VALUES
(1, 5, 'Mr', 'Gaurav Chapela', 'Gaurav Chapela', '798589444609', 'CKBPC8820R', 'gauravchapela77-2@okaxis', '8850487816', 'R no 18B M S Bldg', 'Gautam Nagar', 'HouseKeeping', '2201-01-01', 'Saraswat Bank', '1021151511616', 'HDFCXXXX', '2026-01-06 16:22:19');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `advance_salary`
--
ALTER TABLE `advance_salary`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_day` (`user_id`,`attendance_date`);

--
-- Indexes for table `leaves`
--
ALTER TABLE `leaves`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `emp_id` (`emp_id`);

--
-- Indexes for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `advance_salary`
--
ALTER TABLE `advance_salary`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `leaves`
--
ALTER TABLE `leaves`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `user_profiles`
--
ALTER TABLE `user_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `leaves`
--
ALTER TABLE `leaves`
  ADD CONSTRAINT `leaves_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
