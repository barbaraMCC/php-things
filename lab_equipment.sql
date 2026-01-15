-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 15, 2026 at 12:43 PM
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
-- Database: `lab_equipment`
--

-- --------------------------------------------------------

--
-- Table structure for table `equipment`
--

CREATE TABLE `equipment` (
  `equipment_id` int(11) NOT NULL,
  `equipment_number` varchar(10) NOT NULL,
  `equipment_name` varchar(100) NOT NULL,
  `status` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `equipment`
--

INSERT INTO `equipment` (`equipment_id`, `equipment_number`, `equipment_name`, `status`) VALUES
(1, '123456', 'Microscope A', 'available'),
(2, '7891011', 'Microscope B', 'available'),
(3, '121314', 'Oscilloscope', 'available'),
(4, '151617', '3D Printer', 'available'),
(5, '181920', 'Spectrometer', 'available'),
(6, '212223', 'Centrifuge', 'available'),
(7, '242526', 'Microscope C', 'available');

-- --------------------------------------------------------

--
-- Table structure for table `problem_report`
--

CREATE TABLE `problem_report` (
  `report_id` int(11) NOT NULL,
  `reservation_id` int(11) NOT NULL,
  `description` varchar(250) NOT NULL,
  `reported_at` datetime NOT NULL,
  `status` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `problem_report`
--

INSERT INTO `problem_report` (`report_id`, `reservation_id`, `description`, `reported_at`, `status`) VALUES
(1, 24, 'bzzdjbznzdznznd', '2026-01-15 04:33:28', 'pending'),
(2, 20, 'ahhh too spectral ghosts', '2026-01-15 04:34:22', 'pending'),
(3, 12, 'too good', '2026-01-15 05:06:52', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `reservation`
--

CREATE TABLE `reservation` (
  `reservation_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `equipment_id` int(11) NOT NULL,
  `reservation_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `created_at` datetime NOT NULL,
  `status` varchar(20) NOT NULL,
  `purpose` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservation`
--

INSERT INTO `reservation` (`reservation_id`, `user_id`, `equipment_id`, `reservation_date`, `start_time`, `end_time`, `created_at`, `status`, `purpose`) VALUES
(2, 1, 5, '2026-01-12', '14:00:00', '15:00:00', '2026-01-11 13:29:23', 'canceled', 'Lab work'),
(4, 1, 7, '2026-01-12', '10:00:00', '11:00:00', '2026-01-11 13:29:48', 'canceled', 'Lab work'),
(5, 1, 3, '2026-01-12', '12:00:00', '13:00:00', '2026-01-11 13:30:10', 'canceled', 'Lab work'),
(6, 1, 6, '2026-01-12', '09:00:00', '10:00:00', '2026-01-11 13:30:22', 'canceled', 'Lab work'),
(7, 1, 4, '2026-01-13', '14:00:00', '15:00:00', '2026-01-11 13:33:27', 'canceled', 'Lab work'),
(8, 2, 4, '2026-01-13', '16:00:00', '17:00:00', '2026-01-11 13:35:57', 'canceled', 'Lab work'),
(9, 2, 4, '2026-01-13', '13:00:00', '14:00:00', '2026-01-11 13:36:08', 'canceled', 'Lab work'),
(10, 2, 4, '2026-01-13', '16:00:00', '17:00:00', '2026-01-11 13:36:33', 'canceled', 'Lab work'),
(11, 2, 1, '2026-01-13', '18:00:00', '19:00:00', '2026-01-11 13:36:51', 'canceled', 'Lab work'),
(12, 4, 3, '2026-01-13', '14:00:00', '15:00:00', '2026-01-11 13:41:55', 'In progress', 'Lab work'),
(13, 4, 5, '2026-01-13', '09:00:00', '10:00:00', '2026-01-11 13:42:11', 'canceled', 'Lab work'),
(14, 2, 2, '2026-01-22', '14:00:00', '15:00:00', '2026-01-11 13:42:24', 'canceled', 'Lab work'),
(15, 2, 3, '2026-01-16', '19:00:00', '20:00:00', '2026-01-14 20:21:09', 'In progress', 'Lab work'),
(16, 2, 1, '2026-01-14', '09:00:00', '10:00:00', '2026-01-14 20:21:15', 'In progress', 'Lab work'),
(17, 2, 6, '2026-01-31', '11:00:00', '12:00:00', '2026-01-14 20:21:30', 'canceled', 'Lab work'),
(18, 2, 1, '2026-01-13', '12:00:00', '13:00:00', '2026-01-14 20:21:50', 'canceled', 'Lab work'),
(19, 2, 5, '2026-01-13', '12:00:00', '13:00:00', '2026-01-14 20:22:01', 'canceled', 'Lab work'),
(20, 2, 5, '2026-01-13', '13:00:00', '14:00:00', '2026-01-14 20:22:12', 'In progress', 'Lab work'),
(21, 2, 4, '2026-01-31', '16:00:00', '17:00:00', '2026-01-14 20:23:15', 'In progress', 'Lab work'),
(22, 2, 2, '2026-01-15', '09:00:00', '10:00:00', '2026-01-15 11:38:57', 'canceled', 'Lab work'),
(23, 2, 2, '2026-01-15', '14:00:00', '15:00:00', '2026-01-15 11:39:24', 'In progress', 'Lab work'),
(24, 2, 4, '2026-02-14', '09:00:00', '10:00:00', '2026-01-15 11:42:40', 'In progress', 'Lab work'),
(25, 2, 2, '2026-02-15', '19:00:00', '20:00:00', '2026-01-15 11:43:19', 'In progress', 'Lab work'),
(26, 4, 2, '2026-01-15', '15:00:00', '16:00:00', '2026-01-15 12:46:45', 'canceled', 'Lab work'),
(27, 4, 2, '2026-01-15', '16:00:00', '17:00:00', '2026-01-15 12:46:51', 'canceled', 'Lab work'),
(28, 4, 3, '2026-01-15', '13:00:00', '14:00:00', '2026-01-15 12:52:18', 'canceled', 'Lab work'),
(29, 4, 3, '2026-01-15', '16:00:00', '17:00:00', '2026-01-15 13:06:57', 'In progress', 'Lab work'),
(30, 8, 5, '2026-01-15', '11:00:00', '12:00:00', '2026-01-15 19:53:17', 'In progress', 'Lab work'),
(31, 8, 1, '2026-01-15', '16:00:00', '17:00:00', '2026-01-15 19:53:29', 'In progress', 'Lab work');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `user_name`, `email`, `password`) VALUES
(1, 'm', 'm@gmail.com', '$2a$12$F564CjlJCI9vzf9LIjn2Me3ClNfiUjJmRfZCNuXJ6LkXucEzwSu5m'),
(2, 'jelly', 'jelly@mail.com', '$2a$12$VVFDy4sSuviQfBjo.FJxbuiQVIaaEknG2jtXsySTRpZY/4JcK9JSC'),
(4, 'jello', 'jello@mail.com', '$2a$12$uPNPEZJH9rVZDs3827SsU.HurxGJPRgNgNYAyToeuNsbqtMSlU1cG'),
(8, 'dylan', 'dylan@mail.com', '$2y$10$Gq7Nh2LbDS9IwX4Aw.vRmu9oQDDREeslnu3d7Lkys3oV0KU8Mhxla');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `equipment`
--
ALTER TABLE `equipment`
  ADD PRIMARY KEY (`equipment_id`);

--
-- Indexes for table `problem_report`
--
ALTER TABLE `problem_report`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `reservation_id` (`reservation_id`);

--
-- Indexes for table `reservation`
--
ALTER TABLE `reservation`
  ADD PRIMARY KEY (`reservation_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `equipment_id` (`equipment_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `equipment`
--
ALTER TABLE `equipment`
  MODIFY `equipment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `problem_report`
--
ALTER TABLE `problem_report`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `reservation`
--
ALTER TABLE `reservation`
  MODIFY `reservation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `problem_report`
--
ALTER TABLE `problem_report`
  ADD CONSTRAINT `problem_report_ibfk_1` FOREIGN KEY (`reservation_id`) REFERENCES `reservation` (`reservation_id`);

--
-- Constraints for table `reservation`
--
ALTER TABLE `reservation`
  ADD CONSTRAINT `reservation_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `reservation_ibfk_2` FOREIGN KEY (`equipment_id`) REFERENCES `equipment` (`equipment_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
