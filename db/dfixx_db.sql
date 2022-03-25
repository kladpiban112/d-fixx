-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 25, 2022 at 04:50 AM
-- Server version: 10.1.37-MariaDB
-- PHP Version: 7.3.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dfixx_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `repair_jobout`
--

CREATE TABLE `repair_jobout` (
  `oid` int(11) NOT NULL,
  `repair_id` int(11) NOT NULL,
  `jobticket_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `job_date` datetime NOT NULL,
  `job_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `job_desc` text COLLATE utf8_unicode_ci NOT NULL,
  `flag` char(1) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `repair_jobout`
--

INSERT INTO `repair_jobout` (`oid`, `repair_id`, `jobticket_id`, `job_date`, `job_name`, `job_desc`, `flag`) VALUES
(1, 124, 'S7451345684', '2022-03-25 00:00:00', 'Acer ', '', '0'),
(2, 124, 'E12354846', '2022-03-25 00:00:00', 'jib ', '', '0'),
(3, 124, 'e234234', '2022-03-25 00:00:00', 'df', '', '0'),
(4, 124, 'dd323423432423432432', '2022-03-25 00:00:00', 'dddd', '', '0'),
(5, 124, '2555d2222', '2022-03-25 00:00:00', 'fffff', '', '0'),
(6, 124, 'E123548S8215d', '2022-03-25 00:00:00', 'Acer', 'wssssssssssssssssss1', '1');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `repair_jobout`
--
ALTER TABLE `repair_jobout`
  ADD PRIMARY KEY (`oid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `repair_jobout`
--
ALTER TABLE `repair_jobout`
  MODIFY `oid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
