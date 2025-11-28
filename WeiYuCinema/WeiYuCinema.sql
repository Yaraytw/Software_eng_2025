-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 24, 2025 at 12:08 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `WeiYuCinema`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookingRecord`
--

CREATE TABLE `bookingRecord` (
  `orderNumber` varchar(20) NOT NULL,
  `memberId` varchar(10) DEFAULT NULL,
  `showingId` int(11) DEFAULT NULL,
  `time` datetime DEFAULT NULL,
  `seat` varchar(100) DEFAULT NULL,
  `chooseMeal` varchar(100) DEFAULT NULL,
  `ticketTypeId` int(11) DEFAULT NULL,
  `ticketNums` int(11) DEFAULT NULL,
  `totalPrice` int(11) DEFAULT NULL,
  `orderStatusId` int(11) DEFAULT NULL,
  `getTicketNum` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cinema`
--

CREATE TABLE `cinema` (
  `cinemaId` varchar(2) NOT NULL,
  `cinemaName` varchar(20) DEFAULT NULL,
  `cinemaAddress` varchar(80) DEFAULT NULL,
  `cinemaTele` varchar(15) DEFAULT NULL,
  `cinemaImg` varchar(50) DEFAULT NULL,
  `cinemaInfo` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `meals`
--

CREATE TABLE `meals` (
  `mealsId` int(11) NOT NULL,
  `mealsName` varchar(20) DEFAULT NULL,
  `mealsPrice` int(11) DEFAULT NULL,
  `mealsPhoto` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `memberCashCard`
--

CREATE TABLE `memberCashCard` (
  `memberId` varchar(10) NOT NULL,
  `balance` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `memberProfile`
--

CREATE TABLE `memberProfile` (
  `memberId` varchar(10) NOT NULL,
  `memberName` varchar(20) DEFAULT NULL,
  `memberEmail` varchar(50) DEFAULT NULL,
  `memberPwd` varchar(255) DEFAULT NULL,
  `memberPhone` varchar(10) DEFAULT NULL,
  `memberBirth` varchar(10) DEFAULT NULL,
  `memberPwdHintId` int(11) DEFAULT NULL,
  `memberPwdHintAns` varchar(50) DEFAULT NULL,
  `memberPayAccount` varchar(20) DEFAULT NULL,
  `memberConfirm` varchar(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `memberPwdQuestion`
--

CREATE TABLE `memberPwdQuestion` (
  `memberPwdHintId` int(11) NOT NULL,
  `memberPwdHintContent` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `movie`
--

CREATE TABLE `movie` (
  `movieId` int(11) NOT NULL,
  `movieName` varchar(35) DEFAULT NULL,
  `movieTime` int(11) DEFAULT NULL,
  `movieStart` varchar(10) DEFAULT NULL,
  `movieImg` varchar(50) DEFAULT NULL,
  `gradeId` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `seatCondition`
--

CREATE TABLE `seatCondition` (
  `showingId` int(11) NOT NULL,
  `seatNumber` varchar(10) NOT NULL,
  `seatEmpty` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `showing`
--

CREATE TABLE `showing` (
  `showingId` int(11) NOT NULL,
  `movieId` int(11) DEFAULT NULL,
  `cinemaId` varchar(2) DEFAULT NULL,
  `theaterId` varchar(6) DEFAULT NULL,
  `showingDate` varchar(10) DEFAULT NULL,
  `startTime` varchar(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ticketClass`
--

CREATE TABLE `ticketClass` (
  `ticketClassId` int(11) NOT NULL,
  `ticketClassName` varchar(20) DEFAULT NULL,
  `ticketClassPrice` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookingRecord`
--
ALTER TABLE `bookingRecord`
  ADD PRIMARY KEY (`orderNumber`),
  ADD KEY `memberId` (`memberId`),
  ADD KEY `showingId` (`showingId`);

--
-- Indexes for table `cinema`
--
ALTER TABLE `cinema`
  ADD PRIMARY KEY (`cinemaId`);

--
-- Indexes for table `meals`
--
ALTER TABLE `meals`
  ADD PRIMARY KEY (`mealsId`);

--
-- Indexes for table `memberCashCard`
--
ALTER TABLE `memberCashCard`
  ADD PRIMARY KEY (`memberId`);

--
-- Indexes for table `memberProfile`
--
ALTER TABLE `memberProfile`
  ADD PRIMARY KEY (`memberId`),
  ADD KEY `memberPwdHintId` (`memberPwdHintId`);

--
-- Indexes for table `memberPwdQuestion`
--
ALTER TABLE `memberPwdQuestion`
  ADD PRIMARY KEY (`memberPwdHintId`);

--
-- Indexes for table `movie`
--
ALTER TABLE `movie`
  ADD PRIMARY KEY (`movieId`);

--
-- Indexes for table `seatCondition`
--
ALTER TABLE `seatCondition`
  ADD PRIMARY KEY (`showingId`,`seatNumber`);

--
-- Indexes for table `showing`
--
ALTER TABLE `showing`
  ADD PRIMARY KEY (`showingId`),
  ADD KEY `movieId` (`movieId`),
  ADD KEY `cinemaId` (`cinemaId`);

--
-- Indexes for table `ticketClass`
--
ALTER TABLE `ticketClass`
  ADD PRIMARY KEY (`ticketClassId`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookingRecord`
--
ALTER TABLE `bookingRecord`
  ADD CONSTRAINT `bookingrecord_ibfk_1` FOREIGN KEY (`memberId`) REFERENCES `memberProfile` (`memberId`),
  ADD CONSTRAINT `bookingrecord_ibfk_2` FOREIGN KEY (`showingId`) REFERENCES `showing` (`showingId`);

--
-- Constraints for table `memberCashCard`
--
ALTER TABLE `memberCashCard`
  ADD CONSTRAINT `membercashcard_ibfk_1` FOREIGN KEY (`memberId`) REFERENCES `memberProfile` (`memberId`);

--
-- Constraints for table `memberProfile`
--
ALTER TABLE `memberProfile`
  ADD CONSTRAINT `memberprofile_ibfk_1` FOREIGN KEY (`memberPwdHintId`) REFERENCES `memberPwdQuestion` (`memberPwdHintId`);

--
-- Constraints for table `seatCondition`
--
ALTER TABLE `seatCondition`
  ADD CONSTRAINT `seatcondition_ibfk_1` FOREIGN KEY (`showingId`) REFERENCES `showing` (`showingId`);

--
-- Constraints for table `showing`
--
ALTER TABLE `showing`
  ADD CONSTRAINT `showing_ibfk_1` FOREIGN KEY (`movieId`) REFERENCES `movie` (`movieId`),
  ADD CONSTRAINT `showing_ibfk_2` FOREIGN KEY (`cinemaId`) REFERENCES `cinema` (`cinemaId`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
