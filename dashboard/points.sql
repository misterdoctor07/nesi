-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 10, 2024 at 12:19 AM
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
-- Table structure for table `points`
--

CREATE TABLE `points` (
  `id` int(45) NOT NULL,
  `idno` varchar(100) DEFAULT NULL,
  `logindate` date DEFAULT NULL,
  `points` double DEFAULT NULL,
  `offense` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `points`
--

INSERT INTO `points` (`id`, `idno`, `logindate`, `points`, `offense`) VALUES
(1, '102183', '2022-03-01', 0.2, '15'),
(7, '102006', '2022-03-01', 0.5, '17'),
(8, '102459', '2022-03-03', 0.2, '22'),
(10, '102006', '2022-03-29', 0.2, '22'),
(11, '102110', '2022-03-04', 0.2, '20'),
(12, '102615', '2022-03-01', 0.2, '15'),
(13, '102663', '2022-04-05', 0.2, '15'),
(14, '102875', '2022-04-06', 0.2, '15'),
(15, '102136', '2022-04-06', 0.2, '15'),
(16, '101955', '2022-04-06', 0.3, '16'),
(17, '102387', '2022-04-06', 0.2, '15'),
(18, '102005', '2022-04-06', 0.3, '16'),
(19, '102259', '2022-04-06', 0.2, '15'),
(20, '102814', '2022-04-27', 0.2, '15'),
(21, '102603', '2022-05-04', 0.2, '12'),
(22, '102073', '2022-05-04', 0.2, '12'),
(23, '102248', '2022-07-14', 0.3, '16'),
(25, '102778', '2022-09-30', 0.2, '15'),
(26, '102958', '2022-09-30', 0.2, '15'),
(28, '102285', '2022-12-01', 0.2, '12'),
(29, '102300', '2022-12-07', 0.3, '16');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `points`
--
ALTER TABLE `points`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `points`
--
ALTER TABLE `points`
  MODIFY `id` int(45) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
