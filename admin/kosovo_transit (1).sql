-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 23, 2025 at 12:30 AM
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
-- Database: `kosovo_transit`
--

-- --------------------------------------------------------

--
-- Table structure for table `buses`
--

CREATE TABLE `buses` (
  `id` int(11) NOT NULL,
  `line_number` varchar(10) NOT NULL,
  `route_name` varchar(100) NOT NULL,
  `frequency` varchar(50) DEFAULT NULL,
  `type` enum('city','intercity') NOT NULL,
  `status` enum('active','inactive','maintenance') DEFAULT 'active',
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `buses`
--

INSERT INTO `buses` (`id`, `line_number`, `route_name`, `frequency`, `type`, `status`, `last_updated`) VALUES
(1, '1A', 'Pristina Center - Airport', 'Every 30 minutes', 'city', 'active', '2025-05-22 21:06:49'),
(2, '2B', 'Pristina - Prizren', 'Every hour', 'intercity', 'active', '2025-05-22 21:06:49'),
(3, '3C', 'Pristina - Peja', 'Every 2 hours', 'intercity', 'active', '2025-05-22 21:06:49'),
(4, '4D', 'Pristina - Gjakova', 'Every 2 hours', 'intercity', 'active', '2025-05-22 21:06:49'),
(5, '5E', 'Pristina - Gjilan', 'Every hour', 'intercity', 'active', '2025-05-22 21:06:49'),
(6, '6F', 'Pristina - Ferizaj', 'Every 45 minutes', 'intercity', 'active', '2025-05-22 21:06:49'),
(7, '7G', 'Pristina - Mitrovica', 'Every hour', 'intercity', 'active', '2025-05-22 21:06:49'),
(8, '8H', 'Prizren - Peja', 'Every 3 hours', 'intercity', 'active', '2025-05-22 21:06:49'),
(9, '9I', 'Gjakova - Prizren', 'Every 2 hours', 'intercity', 'active', '2025-05-22 21:06:49'),
(10, '10J', 'Ferizaj - Gjilan', 'Every hour', 'intercity', 'active', '2025-05-22 21:06:49');

-- --------------------------------------------------------

--
-- Table structure for table `bus_stations`
--

CREATE TABLE `bus_stations` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `status` enum('active','inactive','maintenance') DEFAULT 'active',
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bus_stations`
--

INSERT INTO `bus_stations` (`id`, `name`, `location`, `city`, `status`, `last_updated`) VALUES
(1, 'Pristina Central Station', 'Rr. Ilir Konushevci, Prishtinë', 'Pristina', 'active', '2025-05-22 21:06:49'),
(2, 'Prizren Bus Terminal', 'Rr. Adem Jashari, Prizren', 'Prizren', 'active', '2025-05-22 21:06:49'),
(3, 'Peja Bus Station', 'Rr. UÇK, Pejë', 'Peja', 'active', '2025-05-22 21:06:49'),
(4, 'Gjakova Bus Terminal', 'Rr. Ismail Qemali, Gjakovë', 'Gjakova', 'active', '2025-05-22 21:06:49'),
(5, 'Gjilan Bus Station', 'Rr. Rexhep Luci, Gjilan', 'Gjilan', 'active', '2025-05-22 21:06:49'),
(6, 'Ferizaj Bus Terminal', 'Rr. Skenderbeu, Ferizaj', 'Ferizaj', 'active', '2025-05-22 21:06:49'),
(7, 'Mitrovica Bus Station', 'Rr. Isa Boletini, Mitrovicë', 'Mitrovica', 'active', '2025-05-22 21:06:49'),
(8, 'Pristina Airport Station', 'Pristina International Airport', 'Pristina', 'active', '2025-05-22 21:06:49');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `profile_picture` varchar(255) DEFAULT 'img/default-avatar.png',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `home_destination` varchar(255) DEFAULT NULL,
  `work_destination` varchar(255) DEFAULT NULL,
  `course_destination` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `profile_picture`, `created_at`, `updated_at`, `home_destination`, `work_destination`, `course_destination`) VALUES
(1, 'admin', 'admin@a.com', '$2y$10$rJvOPpEIdREiVUTGtRHCZOIkIE/4Uix92QLodv4dhrN4aMWaZiW7a', 'img/default-avatar.png', '2025-05-22 23:03:57', '2025-05-22 21:03:57', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_destinations`
--

CREATE TABLE `user_destinations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` enum('home','work','course','other') NOT NULL,
  `name` varchar(100) NOT NULL,
  `address` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_destinations`
--

INSERT INTO `user_destinations` (`id`, `user_id`, `type`, `name`, `address`) VALUES
(1, 1, 'home', 'Home', 'Rr. Nëna Terezë, Nr. 15, Prishtinë'),
(2, 1, 'work', 'Office', 'Rr. Zenel Salihu, Nr. 31, Prishtinë'),
(3, 1, 'course', 'University', 'Rr. Ilir Konushevci, Nr. 1, Prishtinë'),
(4, 1, 'other', 'Gym', 'Rr. Rexhep Luci, Nr. 45, Prishtinë'),
(5, 1, 'other', 'Shopping Mall', 'Rr. Adem Jashari, Nr. 100, Prishtinë');

-- --------------------------------------------------------

--
-- Table structure for table `user_preferences`
--

CREATE TABLE `user_preferences` (
  `user_id` int(11) NOT NULL,
  `notifications_enabled` tinyint(1) DEFAULT 1,
  `dark_mode` tinyint(1) DEFAULT 0,
  `language` varchar(10) DEFAULT 'en'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_preferences`
--

INSERT INTO `user_preferences` (`user_id`, `notifications_enabled`, `dark_mode`, `language`) VALUES
(1, 1, 0, 'en');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `buses`
--
ALTER TABLE `buses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bus_stations`
--
ALTER TABLE `bus_stations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email_unique` (`email`);

--
-- Indexes for table `user_destinations`
--
ALTER TABLE `user_destinations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user_preferences`
--
ALTER TABLE `user_preferences`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `buses`
--
ALTER TABLE `buses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `bus_stations`
--
ALTER TABLE `bus_stations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user_destinations`
--
ALTER TABLE `user_destinations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `user_destinations`
--
ALTER TABLE `user_destinations`
  ADD CONSTRAINT `user_destinations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `user_preferences`
--
ALTER TABLE `user_preferences`
  ADD CONSTRAINT `user_preferences_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
