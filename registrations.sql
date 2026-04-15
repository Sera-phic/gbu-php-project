-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 03, 2025 at 02:21 AM
-- Server version: 10.6.20-MariaDB-cll-lve
-- PHP Version: 8.1.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `krishabhi_2003`
--

-- --------------------------------------------------------

--
-- Table structure for table `registrations`
--

CREATE TABLE `registrations` (
  `id` int(11) NOT NULL,
  `rollNumber` varchar(20) NOT NULL,
  `fullName` varchar(255) NOT NULL,
  `fathersName` varchar(255) NOT NULL,
  `nameOfProgramme` varchar(255) NOT NULL,
  `branchSpecialization` varchar(255) NOT NULL,
  `year` int(11) NOT NULL,
  `semester` int(11) NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `stateDomicile` varchar(100) DEFAULT NULL,
  `aadharCard` varchar(12) DEFAULT NULL,
  `permanentAddress` text DEFAULT NULL,
  `hostelAddress` text DEFAULT NULL,
  `studentContact` varchar(15) DEFAULT NULL,
  `studentEmail` varchar(100) DEFAULT NULL,
  `fatherContact` varchar(15) DEFAULT NULL,
  `fatherOccupation` varchar(100) NOT NULL,
  `fatherEmail` varchar(100) DEFAULT NULL,
  `oddSemesterAmount` decimal(10,2) DEFAULT NULL,
  `oddSemesterRemaining` decimal(10,2) DEFAULT NULL,
  `oddSemesterDetails` text DEFAULT NULL,
  `oddSemesterTxnDetails` text DEFAULT NULL,
  `oddSemesterPlatform` varchar(100) DEFAULT NULL,
  `oddSemesterDate` date DEFAULT NULL,
  `evenSemesterAmount` decimal(10,2) DEFAULT NULL,
  `evenSemesterRemaining` decimal(10,2) DEFAULT NULL,
  `evenSemesterDetails` text DEFAULT NULL,
  `evenSemesterTxnDetails` text DEFAULT NULL,
  `evenSemesterPlatform` varchar(100) DEFAULT NULL,
  `evenSemesterDate` date DEFAULT NULL,
  `hostelAmount` decimal(10,2) DEFAULT NULL,
  `hostelRemaining` decimal(10,2) DEFAULT NULL,
  `hostelPaymentMode` varchar(50) DEFAULT NULL,
  `hostelTxnDetails` text DEFAULT NULL,
  `hostelPlatform` varchar(100) DEFAULT NULL,
  `hostelDate` date DEFAULT NULL,
  `messAmount` decimal(10,2) DEFAULT NULL,
  `messRemaining` decimal(10,2) DEFAULT NULL,
  `messPaymentMode` varchar(50) DEFAULT NULL,
  `messTxnDetails` text DEFAULT NULL,
  `messPlatform` varchar(100) DEFAULT NULL,
  `messDate` date DEFAULT NULL,
  `motherContact` varchar(15) DEFAULT NULL,
  `motherEmail` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `registrations`
--
ALTER TABLE `registrations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `rollNumber` (`rollNumber`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `registrations`
--
ALTER TABLE `registrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
