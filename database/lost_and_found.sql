-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 23, 2025 at 07:47 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lost_and_found`
--

-- --------------------------------------------------------

--
-- Table structure for table `handover`
--

CREATE TABLE `handover` (
  `handoverID` int(11) NOT NULL,
  `itemID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `handoverDate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `handover`
--

INSERT INTO `handover` (`handoverID`, `itemID`, `userID`, `handoverDate`) VALUES
(1, 2, 1, '2025-01-15'),
(2, 3, 1, '2025-01-16');

-- --------------------------------------------------------

--
-- Table structure for table `item`
--

CREATE TABLE `item` (
  `itemID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `item`
--

INSERT INTO `item` (`itemID`, `userID`, `name`, `category`, `status`) VALUES
(1, 1, 'iPhone 13', 'Electronics', 'Lost'),
(2, 2, 'Black Wallet', 'Accessories', 'Found'),
(3, 1, 'Backpack', 'Bags', 'Found');

-- --------------------------------------------------------

--
-- Table structure for table `report`
--

CREATE TABLE `report` (
  `reportID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `itemID` int(11) NOT NULL,
  `reportType` varchar(255) NOT NULL,
  `reportDate` date NOT NULL,
  `location` varchar(255) NOT NULL,
  `picture` blob NOT NULL,
  `detail` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `report`
--

INSERT INTO `report` (`reportID`, `userID`, `itemID`, `reportType`, `reportDate`, `location`, `picture`, `detail`) VALUES
(1, 1, 1, 'Lost', '2025-01-10', 'Shibuya', 0x6970686f6e652e6a7067, 'Blue case'),
(2, 2, 2, 'Found', '2025-01-11', 'Toyosu', 0x77616c6c65742e6a7067, 'Contains ID'),
(3, 1, 3, 'Found', '2025-01-12', 'Toyosu', 0x6261672e6a7067, 'Red backpack');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `userID` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(25) NOT NULL,
  `tel` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`userID`, `username`, `email`, `tel`) VALUES
(1, 'alice', 'alice@mail.com', '123456789'),
(2, 'bob', 'bob@mail.com', '987654321'),
(3, 'carol', 'carol@mail.com', '555666777');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `handover`
--
ALTER TABLE `handover`
  ADD PRIMARY KEY (`handoverID`),
  ADD KEY `itemID` (`itemID`),
  ADD KEY `userID` (`userID`);

--
-- Indexes for table `item`
--
ALTER TABLE `item`
  ADD PRIMARY KEY (`itemID`),
  ADD KEY `userID` (`userID`);

--
-- Indexes for table `report`
--
ALTER TABLE `report`
  ADD PRIMARY KEY (`reportID`),
  ADD KEY `itemID` (`itemID`),
  ADD KEY `userID` (`userID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`userID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `handover`
--
ALTER TABLE `handover`
  MODIFY `handoverID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `item`
--
ALTER TABLE `item`
  MODIFY `itemID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `report`
--
ALTER TABLE `report`
  MODIFY `reportID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `handover`
--
ALTER TABLE `handover`
  ADD CONSTRAINT `handover_ibfk_1` FOREIGN KEY (`itemID`) REFERENCES `item` (`itemID`),
  ADD CONSTRAINT `handover_ibfk_2` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`);

--
-- Constraints for table `item`
--
ALTER TABLE `item`
  ADD CONSTRAINT `item_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`);

--
-- Constraints for table `report`
--
ALTER TABLE `report`
  ADD CONSTRAINT `report_ibfk_1` FOREIGN KEY (`itemID`) REFERENCES `item` (`itemID`),
  ADD CONSTRAINT `report_ibfk_2` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
