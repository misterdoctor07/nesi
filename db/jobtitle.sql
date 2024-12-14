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
-- Table structure for table `jobtitle`
--

CREATE TABLE `jobtitle` (
  `id` int(45) NOT NULL,
  `jobtitle` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `jobtitle`
--

INSERT INTO `jobtitle` (`id`, `jobtitle`) VALUES
(1, 'Quality Assurance Specialist\r'),
(2, 'Accounting Officer\r'),
(3, 'Accounting Specialist\r'),
(5, 'Admin Auditor\r'),
(6, 'Admin Officer-In-Charge\r'),
(8, 'Assessor\r'),
(10, 'Assistant Assessor\r'),
(11, 'Assistant Team Leader\r'),
(12, 'Auditor \r'),
(16, 'Care Coordinator\r'),
(17, 'Chief Executive Officer\r'),
(18, 'Chief Financial Officer\r'),
(19, 'Claims Specialist\r'),
(20, 'Client Support Associate\r'),
(22, 'Clinical Coordinator\r'),
(23, 'Clinical Quality Assurance Document Specialist\r'),
(26, 'Coder\r'),
(27, 'Custodian OIC\r'),
(28, 'Data Processing Specialist\r'),
(29, 'Data Processor\r'),
(30, 'Data Review Specialist\r'),
(31, 'HR Officer-In-Charge\r'),
(33, 'Human Resource Associate\r'),
(34, 'Human Resource Coordinator\r'),
(35, 'Human Resource Generalist\r'),
(36, 'Intake Coordinator\r'),
(37, 'IT Admin Assistant\r'),
(38, 'IT Support Assistant\r'),
(46, 'Liaison Officer\r'),
(49, 'Office Support Specialist\r'),
(50, 'Operations Supervisor\r'),
(51, 'Payment Poster\r'),
(52, 'POC Writer\r'),
(54, 'Quality Assurance Coordinator\r'),
(56, 'Quality Control Coordinator\r'),
(57, 'Quality Control Specialist\r'),
(59, 'Team Leader  \r'),
(65, 'Team Manager '),
(67, 'Technical QA Specialist\r'),
(68, 'Technical Support Specialist\r'),
(69, 'Trainer \r'),
(71, 'Utility\r'),
(72, 'Visit Poster\r'),
(73, 'Recruitment Officer'),
(74, 'Data Entry'),
(75, 'Client Relation Officer'),
(76, 'Team Coordinator'),
(77, 'Admin Clerk'),
(78, 'Company Nurse'),
(79, 'Admin'),
(80, 'Human Resource Officer'),
(81, 'Validator'),
(82, 'Recruitment Associate'),
(83, 'Human Resource Support'),
(84, 'Invoicing'),
(85, 'Quality Assurance/Control Coordinator (APB - HH MC)'),
(86, 'Purchasing Specialist'),
(87, 'Credit & Collection'),
(88, 'Accounting Associate'),
(89, 'Operations Manager'),
(90, 'Admin Officer'),
(91, 'Patient Relation Representative'),
(92, 'Senior Admin Auditor'),
(93, 'IT Officer'),
(94, 'Officer-In-Charge'),
(95, 'Virtual Assistant'),
(96, 'A/R Survey Associate');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `jobtitle`
--
ALTER TABLE `jobtitle`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `jobtitle`
--
ALTER TABLE `jobtitle`
  MODIFY `id` int(45) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
