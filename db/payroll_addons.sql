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
-- Table structure for table `payroll_addons`
--

CREATE TABLE `payroll_addons` (
  `id` int(45) NOT NULL,
  `idno` varchar(100) DEFAULT NULL,
  `payrollperiod` int(45) DEFAULT NULL,
  `description` varchar(100) DEFAULT NULL,
  `amount` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `payroll_addons`
--

INSERT INTO `payroll_addons` (`id`, `idno`, `payrollperiod`, `description`, `amount`) VALUES
(1, '102110', 7, 'allowance', 1000),
(2, '102110', 11, 'allowance', 1000),
(3, '101841', 3, 'Reimbursements: Elec', 250),
(5, '102145', 12, 'TL Allowance for the month', 1000),
(6, '101841', 12, 'TL Allowance for the month', 1000),
(7, '101850', 13, 'Allowance for Mid Month', 1000),
(8, '101841', 13, 'Allowance for Mid Month', 1000),
(9, '102010', 15, 'Medicine reimbursement', 200),
(10, '102010', 15, 'Load reimbursement', 150),
(11, '101740', 24, 'Basic Salary', 10000),
(12, '101740', 24, 'Allowance for End Month', 10000);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `payroll_addons`
--
ALTER TABLE `payroll_addons`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `payroll_addons`
--
ALTER TABLE `payroll_addons`
  MODIFY `id` int(45) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
