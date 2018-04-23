-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 23 Apr 2018 la 08:07
-- Versiune server: 10.1.25-MariaDB
-- PHP Version: 7.1.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `steemcasino`
--

-- --------------------------------------------------------

--
-- Structura de tabel pentru tabelul `coinflip`
--

CREATE TABLE `coinflip` (
  `ID` int(11) NOT NULL,
  `player1` varchar(255) DEFAULT NULL,
  `player2` varchar(255) DEFAULT NULL,
  `win` int(11) DEFAULT NULL,
  `bet` float DEFAULT NULL,
  `reward` float DEFAULT NULL,
  `secret` varchar(128) DEFAULT NULL,
  `hash` varchar(300) DEFAULT NULL,
  `timestamp` int(144) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structura de tabel pentru tabelul `history`
--

CREATE TABLE `history` (
  `ID` int(11) NOT NULL,
  `transType` int(11) DEFAULT NULL,
  `amount` float DEFAULT NULL,
  `gameid` int(11) DEFAULT NULL,
  `user1` varchar(255) DEFAULT NULL,
  `user2` varchar(255) DEFAULT NULL,
  `win` int(11) DEFAULT NULL,
  `reward` float DEFAULT NULL,
  `timestamp` int(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structura de tabel pentru tabelul `info`
--

CREATE TABLE `info` (
  `ID` int(11) NOT NULL,
  `name` varchar(256) NOT NULL,
  `value` int(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Salvarea datelor din tabel `info`
--

INSERT INTO `info` (`ID`, `name`, `value`) VALUES
(1, 'lastTrans', 0),
(2, 'isMaintenance', 0),
(3, 'roulettetimestamp', 0),
(4, 'roulettestate', 0),
(5, 'rouletteid', 0),
(6, 'jackpotstate', 0),
(7, 'jackpotgame', 0);

-- --------------------------------------------------------

--
-- Structura de tabel pentru tabelul `jackpot`
--

CREATE TABLE `jackpot` (
  `ID` int(11) NOT NULL,
  `player` varchar(255) NOT NULL,
  `bet` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structura de tabel pentru tabelul `mines`
--

CREATE TABLE `mines` (
  `id` int(11) NOT NULL,
  `player` varchar(255) NOT NULL,
  `mode` int(1) NOT NULL,
  `secret` varchar(256) NOT NULL,
  `hash` varchar(256) NOT NULL,
  `bet` float NOT NULL,
  `win` int(1) NOT NULL,
  `reward` float NOT NULL,
  `blocks` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structura de tabel pentru tabelul `promocodes`
--

CREATE TABLE `promocodes` (
  `ID` int(10) NOT NULL,
  `type` int(1) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `amount` float DEFAULT NULL,
  `usedCode` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structura de tabel pentru tabelul `roulette`
--

CREATE TABLE `roulette` (
  `ID` int(11) NOT NULL,
  `player` varchar(255) NOT NULL,
  `bet` float NOT NULL,
  `beton` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structura de tabel pentru tabelul `rps`
--

CREATE TABLE `rps` (
  `ID` int(11) NOT NULL,
  `player1` varchar(255) DEFAULT NULL,
  `player2` varchar(255) DEFAULT NULL,
  `win` int(11) DEFAULT NULL,
  `bet` float DEFAULT NULL,
  `reward` float DEFAULT NULL,
  `player1pick` int(11) DEFAULT NULL,
  `player2pick` int(11) DEFAULT NULL,
  `timestamp` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structura de tabel pentru tabelul `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `balance` float NOT NULL DEFAULT '0',
  `won` float NOT NULL DEFAULT '0',
  `losted` float NOT NULL DEFAULT '0',
  `reffered` varchar(255) DEFAULT NULL,
  `privacy` int(1) DEFAULT '0',
  `promob` float NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `coinflip`
--
ALTER TABLE `coinflip`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `history`
--
ALTER TABLE `history`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `info`
--
ALTER TABLE `info`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `jackpot`
--
ALTER TABLE `jackpot`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `mines`
--
ALTER TABLE `mines`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `promocodes`
--
ALTER TABLE `promocodes`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `roulette`
--
ALTER TABLE `roulette`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `rps`
--
ALTER TABLE `rps`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `coinflip`
--
ALTER TABLE `coinflip`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `history`
--
ALTER TABLE `history`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `info`
--
ALTER TABLE `info`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `jackpot`
--
ALTER TABLE `jackpot`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `mines`
--
ALTER TABLE `mines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `promocodes`
--
ALTER TABLE `promocodes`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `roulette`
--
ALTER TABLE `roulette`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `rps`
--
ALTER TABLE `rps`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
