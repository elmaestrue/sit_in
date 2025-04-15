-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 15, 2025 at 03:34 AM
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
-- Database: `test`
--

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_posted` timestamp NOT NULL DEFAULT current_timestamp(),
  `language` varchar(50) DEFAULT 'English',
  `message` text NOT NULL,
  `posted_at` datetime DEFAULT current_timestamp(),
  `posted_by` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `title`, `content`, `created_at`, `date_posted`, `language`, `message`, `posted_at`, `posted_by`) VALUES
(1, '', '', '2025-03-25 23:49:07', '2025-03-25 23:49:07', 'English', 'UC did it again.', '2025-04-14 03:11:59', NULL),
(3, 'Announcement', '', '2025-04-13 19:15:31', '2025-04-13 19:15:31', 'English', 'uc!', '2025-04-14 03:15:31', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `laboratory` varchar(10) NOT NULL,
  `date` datetime DEFAULT current_timestamp(),
  `message` text NOT NULL,
  `sit_in_id` int(11) DEFAULT NULL,
  `submitted_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `student_id`, `laboratory`, `date`, `message`, `sit_in_id`, `submitted_at`) VALUES
(1, '19835644', '524', '2024-05-21 00:00:00', 'dawdawd', NULL, '2025-04-15 09:14:39'),
(2, '19835644', '524', '2024-05-21 00:00:00', 'Feedback message 1', NULL, '2025-04-15 09:14:39'),
(3, '19835644', '524', '2024-05-21 00:00:00', 'Feedback message 2', NULL, '2025-04-15 09:14:39'),
(4, '1', '', '0000-00-00 00:00:00', 'Good morning!', 10, '2025-04-15 09:14:39'),
(5, '2', '', '0000-00-00 00:00:00', 'I love uc', 11, '2025-04-15 09:14:39'),
(6, '3', '', '0000-00-00 00:00:00', 'hi', 12, '2025-04-15 09:14:39'),
(7, '22616791', '', '0000-00-00 00:00:00', 'ok', 6, '2025-04-15 09:14:39'),
(8, '22616791', '', '2025-04-15 00:00:00', 'yes', 13, '2025-04-15 09:14:39'),
(9, '1', '', '2025-04-15 00:00:00', 'Again', 10, '2025-04-15 09:14:39'),
(10, '1', '', '2025-04-15 00:00:00', 'Thank you', 10, '2025-04-15 09:14:39'),
(11, '1', '', '2025-04-15 00:00:00', 'btw', 10, '2025-04-15 09:14:39'),
(12, '2', '', '2025-04-15 00:00:00', 'stupid', 11, '2025-04-15 09:14:39');

-- --------------------------------------------------------

--
-- Table structure for table `foul_words`
--

CREATE TABLE `foul_words` (
  `id` int(11) NOT NULL,
  `word` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `foul_words`
--

INSERT INTO `foul_words` (`id`, `word`) VALUES
(1, 'badword1'),
(2, 'badword2'),
(3, 'badword3');

-- --------------------------------------------------------

--
-- Table structure for table `registration`
--

CREATE TABLE `registration` (
  `idno` varchar(20) NOT NULL,
  `firstname` varchar(50) DEFAULT NULL,
  `lastname` varchar(50) DEFAULT NULL,
  `lab` varchar(50) DEFAULT NULL,
  `remaining_sessions` int(11) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sit_in_records`
--

CREATE TABLE `sit_in_records` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `sit_in_time` datetime DEFAULT current_timestamp(),
  `log_out_time` datetime DEFAULT NULL,
  `laboratory` varchar(100) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `language` varchar(50) NOT NULL,
  `remaining_sessions` int(11) DEFAULT 30,
  `subject` varchar(100) DEFAULT NULL,
  `lab` varchar(100) NOT NULL,
  `purpose` varchar(255) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `time_in` time NOT NULL DEFAULT curtime(),
  `time_out` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sit_in_records`
--

INSERT INTO `sit_in_records` (`id`, `student_id`, `sit_in_time`, `log_out_time`, `laboratory`, `name`, `language`, `remaining_sessions`, `subject`, `lab`, `purpose`, `date`, `time_in`, `time_out`) VALUES
(6, 22616791, '2025-03-26 08:00:53', '2025-03-26 11:39:25', NULL, 'Annelob Munoz', 'C#', 30, NULL, '', '', '2025-04-14 06:02:32', '06:02:54', NULL),
(10, 1, '2025-03-26 08:04:43', '2025-04-15 05:38:50', 'Lab 1', 'Alice Johnson', 'Python', 30, NULL, '', '', '2025-04-14 06:02:32', '06:02:54', NULL),
(11, 2, '2025-03-26 08:04:43', '2025-04-15 05:38:57', 'Lab 2', 'Bob Smith', 'Java', 30, NULL, '', '', '2025-04-14 06:02:32', '06:02:54', NULL),
(12, 3, '2025-03-26 08:04:43', '2025-03-26 11:40:45', 'Lab 3', 'Charlie Brown', 'C++', 30, NULL, '', '', '2025-04-14 06:02:32', '06:02:54', NULL),
(13, 22616791, '2025-03-26 11:36:07', '2025-03-26 11:39:25', '526', 'Annelob Munoz', 'C', 30, NULL, '', '', '2025-04-14 06:02:32', '06:02:54', NULL),
(25, 22616791, '2025-04-15 08:00:45', NULL, NULL, 'Annelob Munoz', '', 29, NULL, 'Not Assigned', 'C', '2025-04-15 08:00:45', '08:00:45', NULL),
(26, 22616791, '2025-04-15 08:22:46', NULL, NULL, 'Annelob Munoz', '', 28, NULL, 'Not Assigned', 'C', '2025-04-15 08:22:46', '08:22:46', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `studentinfo`
--

CREATE TABLE `studentinfo` (
  `student_id` varchar(50) NOT NULL,
  `lastname` varchar(100) DEFAULT NULL,
  `firstname` varchar(100) DEFAULT NULL,
  `middlename` varchar(100) DEFAULT NULL,
  `course` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `yearlevel` varchar(50) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `remaining_sessions` int(11) DEFAULT 30
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `studentinfo`
--

INSERT INTO `studentinfo` (`student_id`, `lastname`, `firstname`, `middlename`, `course`, `email`, `yearlevel`, `username`, `password`, `address`, `remaining_sessions`) VALUES
('22616791', 'munoz', 'annelob', 'n/a', 'BSIT', NULL, '3', 'annelob', '$2y$10$sQrskgX1RiN24dEw6MIXs.HDCWRk5lpKk8KLFpHEORYF/N4jQHlzG', 'annelob@gmail.com', 11);

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `course` varchar(100) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `remaining_sessions` int(11) NOT NULL DEFAULT 30,
  `laboratory` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `name`, `course`, `year`, `remaining_sessions`, `laboratory`) VALUES
(1, 'Alice Johnson', '', 3, 30, NULL),
(2, 'Bob Smith', NULL, NULL, 30, NULL),
(3, 'Charlie Brown', NULL, NULL, 30, NULL),
(123, 'Ranidel Padoga', 'BSIT', 3, 30, '524'),
(321, 'Chrisnino Pagente', 'BSIT', 3, 30, NULL),
(22616791, 'Annelob Munoz', '', 3, 30, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `course` varchar(255) NOT NULL,
  `year` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `session` int(11) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `year_level` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sit_in_id` (`sit_in_id`);

--
-- Indexes for table `foul_words`
--
ALTER TABLE `foul_words`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `registration`
--
ALTER TABLE `registration`
  ADD PRIMARY KEY (`idno`);

--
-- Indexes for table `sit_in_records`
--
ALTER TABLE `sit_in_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `studentinfo`
--
ALTER TABLE `studentinfo`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `foul_words`
--
ALTER TABLE `foul_words`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sit_in_records`
--
ALTER TABLE `sit_in_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`sit_in_id`) REFERENCES `sit_in_records` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sit_in_records`
--
ALTER TABLE `sit_in_records`
  ADD CONSTRAINT `sit_in_records_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
