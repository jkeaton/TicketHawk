-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 03, 2015 at 12:07 AM
-- Server version: 5.6.21
-- PHP Version: 5.6.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `TicketHawk`
--
CREATE DATABASE IF NOT EXISTS `TicketHawk` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `TicketHawk`;

-- --------------------------------------------------------

--
-- Table structure for table `EVENT`
--

CREATE TABLE IF NOT EXISTS `EVENT` (
`event_id` int(10) unsigned NOT NULL,
  `eventname` varchar(30) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `location` varchar(60) NOT NULL,
  `venue` varchar(30) NOT NULL,
  `price` double NOT NULL,
  `ticket_qty` int(10) unsigned NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `EVENT`
--

INSERT INTO `EVENT` (`event_id`, `eventname`, `date`, `time`, `location`, `venue`, `price`, `ticket_qty`) VALUES
(1, 'Lion King', '2015-01-27', '12:30:00', '660 Peachtree Street Northeast, Atlanta, GA 30308', 'Fox Theatre', 25, 100);

-- --------------------------------------------------------

--
-- Table structure for table `USER`
--

CREATE TABLE IF NOT EXISTS `USER` (
`user_id` int(10) unsigned NOT NULL,
  `username` varchar(20) NOT NULL,
  `fname` varchar(20) NOT NULL,
  `lname` varchar(20) NOT NULL,
  `street_address` varchar(40) NOT NULL,
  `city` varchar(30) NOT NULL,
  `state` varchar(2) NOT NULL,
  `zipcode` varchar(5) NOT NULL,
  `email` varchar(40) NOT NULL,
  `hashed_pass` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `USER`
--

INSERT INTO `USER` (`user_id`, `username`, `fname`, `lname`, `street_address`, `city`, `state`, `zipcode`, `email`, `hashed_pass`) VALUES
(1, 'jkeaton', 'Jestin', 'Keaton', '3430 Millenium View Dr', 'Snellville', 'GA', '30039', 'jkeaton@spsu.edu', 'balloons'),
(2, 'jacknick1', 'jack', 'nick', '456 Elm Street', 'Atlanta', 'DC', '34728', 'jacknick@gmail.com', '$2y$10$joYjfG5o//qc32zYXk3G4uNOcf9jDrYttnaDPcGu7wmD5bdnnbrDS'),
(3, 'smick', 'sam', 'm', '567 Oak St', 'Atlanta', 'RI', '34550', 'jrak@gmail.com', '$2y$10$0Is06MV3tbE5H5ooA/cJX.9ao4T6UnFo4OWxiweuEYi4Rb3p/lvrm'),
(4, 'smick', 'sam', 'mickey', '567 Oak St', 'Atlanta', 'TX', '45673', 'asdkfja@gmail.com', '$2y$10$SApqMCjuSvRdo6CUHVacQuy/Rv8bxbixev8QDE80rjEAHuCbqEhHa'),
(5, 'hellogoodbye', 'jestin', 'kingston', 'aldksjflakj', 'lakjdflask', 'AL', '39281', 'jack@email.com', '$2y$10$TcUGyXS.R43DQRQ6LjdTL.JJERVz9QMx5UeGCUksaTbM..oeP/TDa'),
(6, 'admin', 'Jestin', 'Keaton', 'lkadjf', 'Snellville', 'GA', '47563', 'jestinlk@gmail.com', '$2y$10$TsTvaJ5mKKB/B0OJXuBrue3GwPQDxOnyAk/1MDQ3lva9KsV.RqAVi'),
(7, 'bozo123', 'Bozo', 'Clown', '123 Elm St', 'Atlanta', 'GA', '30060', 'bozo123@email.com', '$2y$10$sIWLiTc9EhemdunEd3o/V.djxzWlQSSl4bDnsLmRF6s00COWgwjV.'),
(8, 'admin', 'Bill', 'Jones', '123 Elm St', 'Atlanta', 'GA', '30060', 'bill_jones@email.com', '$2y$10$/OuCGj/i/82d/r3DxONKjuFeywHLutTHj648MqRQSx7ybC.9ocdQC'),
(9, 'jkeaton', 'jestin', 'keaton', '3430 Millenium View Dr', 'bananas', 'GA', '39283', 'fake@email.com', '$2y$10$imMf9xnwZQfvWbcyMD74vODR4qGpxLhzzOF0jnR1j5TcOb1Vzb8dy'),
(10, 'jkeaton', 'jestin', 'keaton', '3430 Millenium View Dr', 'bananas', 'GA', '39283', 'fake@email.com', '$2y$10$coxh4r7F97JlBSUVI8raV.QTc5MISLSXaGgMHyb/B9cOhKt6fEpqC'),
(11, 'bojangle', 'jack', 'nick', '564', 'aldkfj', 'UT', '48738', 'email@email.com', '$2y$10$vnt2eNn9KSlKgS6FVzUe9u01OAMPZMzfU2V4USSMRdXjNM/NgRzZy');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `EVENT`
--
ALTER TABLE `EVENT`
 ADD PRIMARY KEY (`event_id`);

--
-- Indexes for table `USER`
--
ALTER TABLE `USER`
 ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `EVENT`
--
ALTER TABLE `EVENT`
MODIFY `event_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `USER`
--
ALTER TABLE `USER`
MODIFY `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=12;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
