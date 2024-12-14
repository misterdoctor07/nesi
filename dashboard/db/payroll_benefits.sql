-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 10, 2024 at 01:54 AM
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
-- Table structure for table `payroll_benefits`
--

CREATE TABLE `payroll_benefits` (
  `id` int(45) NOT NULL,
  `idno` varchar(100) DEFAULT NULL,
  `payrollperiod` int(45) DEFAULT NULL,
  `description` varchar(100) DEFAULT NULL,
  `amount` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `payroll_benefits`
--

INSERT INTO `payroll_benefits` (`id`, `idno`, `payrollperiod`, `description`, `amount`) VALUES
(4, '102467', 6, 'Philcare', 200),
(5, '102467', 6, 'generali', 100),
(6, '102010', 15, 'Philcare', 696.36),
(7, '102010', 26, 'Generali Insurance', 71),
(8, '102010', 26, 'Philcare', 100);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `payroll_benefits`
--
ALTER TABLE `payroll_benefits`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `payroll_benefits`
--
ALTER TABLE `payroll_benefits`
  MODIFY `id` int(45) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
