-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 24, 2025 at 09:49 AM
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
-- Database: `multiuser`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user`
--

CREATE TABLE `tbl_user` (
  `user_id` int(10) NOT NULL,
  `user_name` varchar(50) NOT NULL,
  `user_email` varchar(50) NOT NULL,
  `user_phone` varchar(15) NOT NULL,
  `user_gender` int(1) NOT NULL,
  `user_type` int(1) NOT NULL,
  `user_password` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_user`
--

INSERT INTO `tbl_user` (`user_id`, `user_name`, `user_email`, `user_phone`, `user_gender`, `user_type`, `user_password`) VALUES
(1, 'manav', 'manav@gmail.com', '8140801537', 0, 0, '123456'),
(2, 'John Doe', 'john.doe@example.com', '9876543210', 0, 0, '123456'),
(3, 'Jane Smith', 'jane.smith@example.com', '9876543211', 1, 1, '123456'),
(4, 'Alice Johnson', 'alice.johnson@example.com', '9876543212', 1, 2, '123456'),
(5, 'Bob Brown', 'bob.brown@example.com', '9876543213', 0, 1, '123456'),
(6, 'Charlie Wilson', 'charlie.wilson@example.com', '9876543214', 0, 0, '123456'),
(7, 'Emily Davis', 'emily.davis@example.com', '9876543215', 1, 2, '123456'),
(8, 'Frank Thomas', 'frank.thomas@example.com', '9876543216', 0, 1, '123456'),
(9, 'Grace Lee', 'grace.lee@example.com', '9876543217', 1, 0, '123456'),
(10, 'Hannah Taylor', 'hannah.taylor@example.com', '9876543218', 1, 1, '123456'),
(11, 'Ivy Clark', 'ivy.clark@example.com', '9876543219', 1, 2, '123456'),
(12, 'Jack Turner', 'jack.turner@example.com', '9876543220', 0, 1, '123456'),
(13, 'Kara Moore', 'kara.moore@example.com', '9876543221', 1, 0, '123456'),
(14, 'Leo Hall', 'leo.hall@example.com', '9876543222', 0, 1, '123456'),
(15, 'Mia Allen', 'mia.allen@example.com', '9876543223', 1, 2, '123456'),
(16, 'Nathan Young', 'nathan.young@example.com', '9876543224', 0, 0, '123456'),
(17, 'Olivia King', 'olivia.king@example.com', '9876543225', 1, 1, '123456'),
(18, 'Paul Wright', 'paul.wright@example.com', '9876543226', 0, 2, '123456'),
(19, 'Quinn Scott', 'quinn.scott@example.com', '9876543227', 0, 1, '123456'),
(20, 'Rachel Adams', 'rachel.adams@example.com', '9876543228', 1, 0, '123456'),
(21, 'Samuel Evans', 'samuel.evans@example.com', '9876543229', 0, 2, '123456'),
(22, 'Tina Nelson', 'tina.nelson@example.com', '9876543230', 1, 1, '123456'),
(23, 'Uma Harris', 'uma.harris@example.com', '9876543231', 1, 2, '123456'),
(24, 'Victor Edwards', 'victor.edwards@example.com', '9876543232', 0, 1, '123456'),
(25, 'Wendy Hughes', 'wendy.hughes@example.com', '9876543233', 1, 0, '123456'),
(26, 'Xander Green', 'xander.green@example.com', '9876543234', 0, 2, '123456'),
(27, 'Yara Baker', 'yara.baker@example.com', '9876543235', 1, 1, '123456'),
(28, 'Zane Perez', 'zane.perez@example.com', '9876543236', 0, 0, '123456'),
(29, 'Ashley Carter', 'ashley.carter@example.com', '9876543237', 1, 1, '123456'),
(30, 'Brian Martinez', 'brian.martinez@example.com', '9876543238', 0, 2, '123456'),
(31, 'Clara Ramirez', 'clara.ramirez@example.com', '9876543239', 1, 1, '123456'),
(32, 'Derek Hernandez', 'derek.hernandez@example.com', '9876543240', 0, 2, '123456'),
(33, 'Evelyn Lopez', 'evelyn.lopez@example.com', '9876543241', 1, 0, '123456'),
(34, 'George Gonzalez', 'george.gonzalez@example.com', '9876543242', 0, 1, '123456'),
(35, 'Holly Wilson', 'holly.wilson@example.com', '9876543243', 1, 1, '123456'),
(36, 'Isaac Clark', 'isaac.clark@example.com', '9876543244', 0, 2, '123456'),
(37, 'Jessica Lewis', 'jessica.lewis@example.com', '9876543245', 1, 0, '123456'),
(38, 'Kyle Robinson', 'kyle.robinson@example.com', '9876543246', 0, 1, '123456'),
(39, 'Lara Walker', 'lara.walker@example.com', '9876543247', 1, 2, '123456'),
(40, 'Michael Hall', 'michael.hall@example.com', '9876543248', 0, 0, '123456'),
(57, 'manava', 'manav1@gmail.com', '1234567890', 0, 2, '111111');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_user`
--
ALTER TABLE `tbl_user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `user_email` (`user_email`),
  ADD UNIQUE KEY `user_name` (`user_name`),
  ADD UNIQUE KEY `user_phone` (`user_phone`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_user`
--
ALTER TABLE `tbl_user`
  MODIFY `user_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
