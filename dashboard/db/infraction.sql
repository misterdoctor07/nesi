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
-- Table structure for table `infraction`
--

CREATE TABLE `infraction` (
  `id` int(100) NOT NULL,
  `idno` varchar(100) DEFAULT NULL,
  `dateissued` date DEFAULT NULL,
  `dateserved` date DEFAULT NULL,
  `typeofmemo` varchar(100) DEFAULT NULL,
  `typeofoffense` varchar(100) DEFAULT NULL,
  `points` double DEFAULT NULL,
  `memonumber` varchar(100) DEFAULT NULL,
  `dateofsuspension` varchar(100) DEFAULT NULL,
  `status` varchar(100) DEFAULT NULL,
  `addedby` varchar(100) DEFAULT NULL,
  `addeddatetime` datetime DEFAULT NULL,
  `updatedby` varchar(100) DEFAULT NULL,
  `updateddatetime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `infraction`
--

INSERT INTO `infraction` (`id`, `idno`, `dateissued`, `dateserved`, `typeofmemo`, `typeofoffense`, `points`, `memonumber`, `dateofsuspension`, `status`, `addedby`, `addeddatetime`, `updatedby`, `updateddatetime`) VALUES
(3, '102503', '2022-01-07', '2022-01-07', 'Verbal Warning', 'Category A', 1, '22-0001', '-', 'Served', 'Quijano, Jose Andres', '2022-03-04 08:33:59', 'Kalaw, Aladin', '2024-09-13 09:02:00'),
(4, '102110', '2024-10-05', '2024-10-05', 'Verbal Warning', 'Category A', 1, '22-2', 'attendance', 'Served', 'Lupon, Khalid', '2024-10-05 11:10:15', 'Lupon, Khalid', '2024-10-05 11:12:32');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `infraction`
--
ALTER TABLE `infraction`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `infraction`
--
ALTER TABLE `infraction`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
