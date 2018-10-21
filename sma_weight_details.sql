-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 21, 2018 at 02:28 PM
-- Server version: 10.1.25-MariaDB
-- PHP Version: 5.6.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `admin_app`
--

-- --------------------------------------------------------

--
-- Table structure for table `sma_weight_details`
--

CREATE TABLE `sma_weight_details` (
  `id` int(11) UNSIGNED NOT NULL,
  `employee_id` varchar(100) NOT NULL,
  `created_by` int(11) UNSIGNED DEFAULT NULL,
  `year` varchar(10) NOT NULL,
  `month` varchar(20) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `created_date` datetime DEFAULT NULL,
  `standard_weight` varchar(100) DEFAULT NULL,
  `current_weight` varchar(25) DEFAULT NULL,
  `reference_no` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sma_weight_details`
--

INSERT INTO `sma_weight_details` (`id`, `employee_id`, `created_by`, `year`, `month`, `start_date`, `end_date`, `created_date`, `standard_weight`, `current_weight`, `reference_no`) VALUES
(23, 'PG000004', NULL, '2016', 'January', '2018-01-10', '1969-12-31', NULL, '77', '78', '2016_January');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `sma_weight_details`
--
ALTER TABLE `sma_weight_details`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_id` (`employee_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `sma_weight_details`
--
ALTER TABLE `sma_weight_details`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
