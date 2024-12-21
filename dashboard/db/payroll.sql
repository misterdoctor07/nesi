-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 10, 2024 at 01:53 AM
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
-- Table structure for table `payroll`
--

CREATE TABLE `payroll` (
  `id` int(45) NOT NULL,
  `periodfrom` date DEFAULT NULL,
  `periodto` date DEFAULT NULL,
  `period` varchar(100) DEFAULT NULL,
  `addedby` varchar(100) DEFAULT NULL,
  `addeddatetime` datetime DEFAULT NULL,
  `updatedby` varchar(100) DEFAULT NULL,
  `updateddatetime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `payroll`
--

INSERT INTO `payroll` (`id`, `periodfrom`, `periodto`, `period`, `addedby`, `addeddatetime`, `updatedby`, `updateddatetime`) VALUES
(2, '2021-12-28', '2022-01-12', 'mid', 'Administrator', '2022-01-25 00:37:45', NULL, NULL),
(3, '2022-01-13', '2022-01-27', 'end', 'Cantomayor, Jayrald', '2022-01-27 00:00:08', NULL, NULL),
(4, '2022-02-15', '2022-02-26', 'end', 'Hassan, Lovely Jane', '2022-02-16 00:05:03', NULL, NULL),
(5, '2022-02-15', '2022-02-28', 'end', 'Salvador, Jacqueline', '2022-02-16 00:30:03', NULL, NULL),
(6, '2022-02-17', '2022-02-26', 'end', 'Hassan, Lovely Jane', '2022-02-17 00:50:51', NULL, NULL),
(7, '2022-02-12', '2022-02-28', 'end', 'Hassan, Lovely Jane', '2022-02-18 02:54:35', NULL, NULL),
(8, '2022-02-13', '2022-02-26', 'end', 'Hassan, Lovely Jane', '2022-02-19 04:43:36', NULL, NULL),
(9, '2022-02-01', '2022-03-12', 'mid', 'Administrator', '2022-02-28 23:02:16', NULL, NULL),
(10, '2022-03-01', '2022-03-12', 'mid', 'Administrator', '2022-02-28 23:40:12', NULL, NULL),
(11, '2022-02-01', '2022-02-12', 'mid', 'Administrator', '2022-02-28 23:54:26', NULL, NULL),
(12, '2022-03-13', '2022-03-27', 'end', 'Salvador, Jacqueline', '2022-03-15 03:59:06', NULL, NULL),
(13, '2022-02-28', '2022-03-12', 'mid', 'Hassan, Lovely Jane', '2022-03-18 02:29:15', NULL, NULL),
(14, '2022-03-28', '2022-04-12', 'mid', 'Hassan, Lovely Jane', '2022-03-31 04:35:45', NULL, NULL),
(15, '2022-03-29', '2022-04-02', 'mid', 'Hassan, Lovely Jane', '2022-04-01 08:02:22', NULL, NULL),
(16, '2022-04-13', '2022-04-27', 'end', 'Salvador, Jacqueline', '2022-04-15 06:04:41', NULL, NULL),
(17, '2022-04-27', '2022-04-28', 'mid', 'Salvador, Jacqueline', '2022-04-28 07:39:03', NULL, NULL),
(18, '2022-05-13', '2022-05-27', 'end', 'Salvador, Jacqueline', '2022-06-02 09:07:39', NULL, NULL),
(19, '2022-05-28', '2022-06-12', 'mid', 'Salvador, Jacqueline', '2022-06-03 04:19:19', NULL, NULL),
(20, '2022-07-28', '2022-08-12', 'mid', 'Salvador, Jacqueline', '2022-07-29 01:26:58', NULL, NULL),
(21, '2022-07-13', '2022-07-27', 'end', 'Hassan, Lovely Jane', '2022-07-30 08:38:51', NULL, NULL),
(22, '2022-07-27', '2022-08-12', 'mid', 'Salvador, Jacqueline', '2022-08-06 00:25:05', NULL, NULL),
(23, '2022-08-12', '2022-08-18', 'end', 'Salvador, Jacqueline', '2022-08-18 01:03:50', NULL, NULL),
(24, '2022-08-13', '2022-08-20', 'end', 'Hassan, Lovely Jane', '2022-08-19 07:21:39', NULL, NULL),
(25, '2022-08-13', '2022-08-27', 'end', 'Hassan, Lovely Jane', '2022-08-23 07:16:47', NULL, NULL),
(26, '2022-11-13', '2022-11-27', 'end', 'Hassan, Lovely Jane', '2022-11-19 02:41:38', NULL, NULL),
(27, '2022-10-28', '2022-11-12', 'mid', 'Hassan, Lovely Jane', '2022-11-19 03:34:31', NULL, NULL),
(28, '2022-11-28', '2022-12-03', 'mid', 'Salvador, Jacqueline', '2022-12-02 23:39:46', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `payroll`
--
ALTER TABLE `payroll`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `payroll`
--
ALTER TABLE `payroll`
  MODIFY `id` int(45) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
