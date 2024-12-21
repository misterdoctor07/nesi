-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 10, 2024 at 03:09 AM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 8.1.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hris`
--

-- --------------------------------------------------------

--
-- Table structure for table `offense`
--

CREATE TABLE `offense` (
  `id` int(45) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `points` double DEFAULT NULL,
  `frequency` int(45) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `fpoints` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `offense`
--

INSERT INTO `offense` (`id`, `title`, `description`, `points`, `frequency`, `category`, `fpoints`) VALUES
(12, 'Attendance Infraction Code A', 'Absent with proper called-in', 0.2, 3, 'Absence', 0.5),
(13, 'Attendance Infraction Code B', 'Absent with proper called-in, Invalid reason', 1, 3, 'Absence', 0.5),
(15, 'Attendance Infraction Code D', 'Late within 15 minutes ', 0.2, 5, 'Late', 0.5),
(16, 'Attendance Infraction Code E', 'Late 16 minutes and up with called-in', 0.3, 5, 'Late', 0.5),
(17, 'Attendance Infraction Code F', 'Late 16 minutes and up without called-in', 0.5, 5, 'Late', 0.5),
(19, 'Attendance Infraction Code L', 'Over - Break (2 minutes and up)', 0.2, 0, 'OB', 0),
(20, 'Attendance Infraction Code  L-', 'Lunch Break Missed-In/Missed-Out', 0.2, 0, 'OB', 0),
(22, 'Attendance Infraction Code I-', 'Morning Missed IN ', 0.2, 0, 'Missed IN ', 0),
(24, 'Consistent Underperformance', '-', 0, 0, 'Grave', 0),
(25, 'Category A', '-', 0, 0, '-', 0),
(26, 'AWOL', '-', 0, 0, '-', 0),
(27, '-', '-', 0, 0, '-', 0),
(28, 'HIPAA', '-', 0, 0, 'Minor', 0),
(29, 'Personal Appearance/Dress Code ', '-', 0, 0, 'Minor', 0),
(30, 'Company Premises ID ', '-', 0, 0, 'Minor', 0),
(31, 'Cleanliness', '-', 0, 0, 'Minor', 0),
(32, 'Noise Level ', '-', 0, 0, 'Minor', 0),
(33, 'Sleeping', '-', 0, 0, 'Serious', 0),
(34, 'Absence on a day of disapproved leave', '-', 0, 0, 'Minor', 0),
(35, 'Smoking', '-', 0, 0, 'Serious', 0),
(36, 'Loitering and Over Break ', '-', 0, 0, 'Serious', 0),
(37, 'Visitor without permission ', '-', 0, 0, 'Serious', 0),
(38, 'Telephones and correspondence', '-', 0, 0, 'Serious', 0),
(39, 'Earphone usage/Cell phone usage ', '-', 0, 0, 'Serious', 0),
(40, 'Written Correspondence', '-', 0, 0, 'Serious', 0),
(41, 'Client’s Complaint ', '-', 0, 0, 'Grave', 0),
(42, 'Buddy Punching ', '-', 0, 0, 'Grave', 0),
(43, 'Buddy Punching ', '-', 0, 0, 'Grave', 0),
(44, 'Changes in Personal Information for Employment Purposes', '-', 0, 0, 'Grave', 0),
(45, 'Confidentiality', '-', 0, 0, 'Grave', 0),
(46, 'Conflict of Interest ', '-', 0, 0, 'Grave', 0),
(47, 'Receipt of Gifts ', '-', 0, 0, 'Grave', 0),
(48, 'Theft ', '-', 0, 0, 'Grave', 0),
(49, 'Possession of deadly items ', '-', 0, 0, 'Grave', 0),
(50, 'Possession of deadly items ', '-', 0, 0, 'Grave', 0),
(51, 'Harassment ', '-', 0, 0, 'Grave', 0),
(52, 'Harassment ', '-', 0, 0, 'Grave', 0),
(53, 'Scandalous Actions ', '-', 0, 0, 'Grave', 0),
(54, 'Under the influence of alcohol and other substance', '-', 0, 0, 'Grave', 0),
(55, 'Computer and Internet Usage ', '-', 0, 0, 'Grave', 0),
(56, 'Data Protection and Access to Information ', '-', 0, 0, 'Grave', 0),
(57, 'Data Protection and Access to Information ', '-', 0, 0, 'Grave', 0),
(59, 'Idleness ', '-', 0, 0, 'Grave', 0),
(60, 'Insubordination ', '-', 0, 0, 'Grave', 0),
(61, 'HIPAA', '-', 0, 0, 'Major', 0),
(62, 'Attendance Infraction Code W', 'Absence Without Leave', 0, 0, 'Absence', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `offense`
--
ALTER TABLE `offense`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `offense`
--
ALTER TABLE `offense`
  MODIFY `id` int(45) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
