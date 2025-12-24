-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 24, 2025 at 03:47 AM
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
-- Database: `club_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_name` varchar(100) DEFAULT NULL,
  `action` varchar(50) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`id`, `user_id`, `user_name`, `action`, `details`, `created_at`) VALUES
(1, 2, 'Manager', 'Update Attendance', 'Updated attendance for Meeting ID: 1', '2025-12-18 00:35:37'),
(2, 2, 'Manager', 'Login', 'User logged into the system.', '2025-12-18 00:42:38'),
(3, 3, 'lilian', 'Login', 'User logged into the system.', '2025-12-18 00:43:05'),
(4, 1, 'SuperAdmin', 'Login', 'User logged into the system.', '2025-12-18 00:43:16'),
(5, 1, 'SuperAdmin', 'Login', 'User logged into the system.', '2025-12-18 01:10:25'),
(6, 1, 'SuperAdmin', 'Login', 'User logged into the system.', '2025-12-18 01:51:38'),
(7, 1, 'SuperAdmin', 'Create Meeting', 'Created: Baking Club', '2025-12-18 01:57:17'),
(8, 1, 'SuperAdmin', 'Update Attendance', 'Updated attendance for Meeting ID: 6', '2025-12-18 01:57:49'),
(9, 1, 'SuperAdmin', 'Update Attendance', 'Updated attendance for Meeting ID: 4', '2025-12-18 02:10:59'),
(10, 1, 'SuperAdmin', 'Export', 'Downloaded full database.', '2025-12-18 02:20:51'),
(11, 1, 'Stella', 'Login', 'User logged in.', '2025-12-18 03:09:17'),
(12, 6, 'raf', 'Register', 'User registered new account.', '2025-12-18 03:13:17'),
(13, 6, 'raf', 'Login', 'User logged in.', '2025-12-18 03:14:00'),
(14, 1, 'Stella', 'Login', 'User logged in.', '2025-12-18 03:14:23'),
(15, 1, 'Stella', 'Export', 'Downloaded full database.', '2025-12-18 03:16:04'),
(16, 2, 'Manager', 'Login', 'User logged in.', '2025-12-18 03:16:36'),
(17, 7, 'jj', 'Register', 'User registered new account.', '2025-12-18 03:17:21'),
(18, 8, 'kenny', 'Register', 'User registered new account.', '2025-12-18 03:17:57'),
(19, 1, 'Stella', 'Login', 'User logged in.', '2025-12-18 03:18:06'),
(20, 1, 'Stella', 'Update Attendance', 'Updated attendance for Meeting ID: 3', '2025-12-18 03:19:37'),
(21, 1, 'Stella', 'Export', 'Downloaded full database.', '2025-12-18 03:20:32'),
(22, 1, 'Stella', 'Export', 'Downloaded full database.', '2025-12-18 03:20:49'),
(23, 2, 'Manager', 'Login', 'User logged in.', '2025-12-18 03:21:28'),
(24, 2, 'Manager', 'Update Attendance', 'Updated attendance for Meeting ID: 2', '2025-12-18 03:25:36'),
(25, 2, 'Manager', 'Update Attendance', 'Updated attendance for Meeting ID: 2', '2025-12-18 03:26:18'),
(26, 1, 'Stella', 'Login', 'User logged in.', '2025-12-18 09:04:49'),
(27, 2, 'Manager', 'Login', 'User logged in.', '2025-12-18 21:54:20'),
(28, 1, 'Stella', 'Login', 'User logged in.', '2025-12-18 21:55:26'),
(29, 1, 'Stella', 'Export', 'Downloaded full database.', '2025-12-18 21:56:09');

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(20) UNSIGNED NOT NULL,
  `user_id` int(20) UNSIGNED NOT NULL,
  `status` enum('Present','Absent','Pending') DEFAULT 'Pending',
  `schedule_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `user_id`, `status`, `schedule_id`) VALUES
(1, 4, 'Present', 3),
(2, 3, 'Present', 3),
(3, 2, 'Present', 3),
(4, 1, 'Absent', 3),
(5, 4, 'Present', 2),
(6, 3, 'Present', 2),
(7, 2, 'Present', 2),
(8, 1, 'Present', 2),
(9, 4, 'Present', 1),
(10, 3, 'Absent', 1),
(11, 2, 'Absent', 1),
(12, 1, 'Pending', 1),
(13, 4, 'Pending', 6),
(14, 3, 'Pending', 6),
(15, 2, 'Pending', 6),
(16, 1, 'Pending', 6),
(17, 4, 'Absent', 4),
(18, 3, 'Absent', 4),
(19, 2, 'Present', 4),
(20, 1, 'Present', 4),
(23, 6, 'Present', 3),
(25, 7, 'Absent', 2),
(26, 8, 'Present', 2),
(27, 6, 'Pending', 2);

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `id` int(20) NOT NULL,
  `role` enum('Member','Admin','Manager') DEFAULT 'Member',
  `user_id` int(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`id`, `role`, `user_id`) VALUES
(1, 'Admin', 1),
(2, 'Manager', 2),
(3, 'Manager', 3),
(4, 'Member', 4),
(5, 'Member', 5),
(6, 'Member', 6),
(7, 'Member', 7),
(8, 'Member', 8);

-- --------------------------------------------------------

--
-- Table structure for table `schedule`
--

CREATE TABLE `schedule` (
  `id` int(20) UNSIGNED NOT NULL,
  `meeting_name` varchar(100) NOT NULL,
  `meeting_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedule`
--

INSERT INTO `schedule` (`id`, `meeting_name`, `meeting_time`) VALUES
(1, 'Media Club', '2025-12-20 14:00:00'),
(2, 'Art Club', '2025-12-22 09:30:00'),
(3, 'Design Club', '2025-12-15 10:00:00'),
(4, 'IT Club', '2025-12-10 13:00:00'),
(5, 'Dance Club', '2026-01-05 16:00:00'),
(6, 'Baking Club', '2026-01-12 11:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(20) UNSIGNED NOT NULL,
  `name` varchar(250) NOT NULL,
  `major` varchar(150) NOT NULL,
  `email` varchar(250) NOT NULL,
  `password` varchar(300) NOT NULL,
  `profile_pic` varchar(255) NOT NULL DEFAULT 'avatar1.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `name`, `major`, `email`, `password`, `profile_pic`) VALUES
(1, 'Stella', 'software engineering', 'admin1@mail.com', '$2y$10$AlxrLTKeAfGoPqG69otN8.RM3CrpmmdfZYjJs9QviDQ1qmLEmGClS', 'avatar4.jpg'),
(2, 'Manager', 'data science', 'manager@gmail.com', '$2y$10$GjngPUS44OP8NzA55M955uHRU1PGZDhL3chsBc31WNMj/AtOF4Wni', 'avatar1.jpg'),
(3, 'lilian', 'software engineering', 'lilian123@gmail.com', '$2y$10$9wv/x937AraDlVK7riorK.NBvUW0w4ymdvMvtdvPw.3LLuCjsAZs2', 'avatar2.jpg'),
(4, 'Alvino', 'information technology', 'vino@gmail.com', '$2y$10$l6cX2mhWHN52oJMgvPb.iOz22nhifK6qv3bn1A89grZNjWnqeXpWe', 'avatar3.jpg'),
(5, 'shandy', 'information technology', 'shandy@gmail.com', '$2y$10$kuQbRnM/Z5eqirDEhmyO5.BruLmwGgGROt53L/hXDlX4vqrnDrsk.', 'avatar4.jpg'),
(6, 'raf', 'accounting', 'raf@gmail.com', '$2y$10$AK9lpd2EpUkxFjhMrmgRl.ME6/pCw4kI1RS05P8DWXcDky7YdYD3q', 'avatar3.jpg'),
(7, 'jj', 'data science', 'jj@gmail.com', '$2y$10$M8NCrWyxC9DNbPVzs/ujweNE8LBtMMw3/4TJjs8y1oJDsnNYtVL9y', 'avatar3.jpg'),
(8, 'kenny', 'information technology', 'kenny@gmail.com', '$2y$10$5wOezxdUD6YBGcYcbDCzJuN3knFEEQq7YY33eWaCToAMMvTWzpbwG', 'avatar2.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `venue`
--

CREATE TABLE `venue` (
  `id` int(100) NOT NULL,
  `schedule_id` int(20) UNSIGNED NOT NULL,
  `room_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `venue`
--

INSERT INTO `venue` (`id`, `schedule_id`, `room_name`) VALUES
(1, 1, '103'),
(2, 2, '506'),
(3, 3, '505'),
(4, 4, '606'),
(5, 5, '702'),
(6, 6, '502');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FOREIGN KEY` (`schedule_id`),
  ADD KEY `attendance_user` (`user_id`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FOREIGN KEY` (`user_id`);

--
-- Indexes for table `schedule`
--
ALTER TABLE `schedule`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQUE` (`email`);

--
-- Indexes for table `venue`
--
ALTER TABLE `venue`
  ADD PRIMARY KEY (`id`),
  ADD KEY `schedule_id` (`schedule_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `schedule`
--
ALTER TABLE `schedule`
  MODIFY `id` int(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `venue`
--
ALTER TABLE `venue`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `schedule_attendance` FOREIGN KEY (`schedule_id`) REFERENCES `schedule` (`id`);

--
-- Constraints for table `role`
--
ALTER TABLE `role`
  ADD CONSTRAINT `user_role` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `venue`
--
ALTER TABLE `venue`
  ADD CONSTRAINT `venue_ibfk_1` FOREIGN KEY (`schedule_id`) REFERENCES `schedule` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
