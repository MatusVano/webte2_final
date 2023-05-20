-- phpMyAdmin SQL Dump
-- version 5.2.1deb1+jammy2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 18, 2023 at 10:28 PM
-- Server version: 8.0.33-0ubuntu0.22.04.2
-- PHP Version: 8.2.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `final`
--

-- --------------------------------------------------------

--
-- Table structure for table `account`
--

CREATE TABLE `account` (
  `id` int UNSIGNED NOT NULL,
  `username` varchar(32) NOT NULL,
  `password` varchar(512) NOT NULL,
  `type` enum('Teacher','Student') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `first_name` varchar(32) NOT NULL,
  `surname` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `account`
--

INSERT INTO `account` (`id`, `username`, `password`, `type`, `first_name`, `surname`) VALUES
(6, 'student1', '$argon2id$v=19$m=65536,t=4,p=1$Ri9kcXF4eUFJRmNSVHl6cw$rg3rPWF0xDRaZZ57VwsIVtIeMXJMqj8K2agnt8qywAU', 'Student', 'Student1', 'Student'),
(7, 'student2', '$argon2id$v=19$m=65536,t=4,p=1$OFUvLlhGTGg3bUZSbnN6Mg$EvqJEP+x/jwHijGI2wMOq949/wKPXXgbFM3DyyRf+oU', 'Student', 'Student2', 'Student'),
(8, 'teacher1', '$argon2id$v=19$m=65536,t=4,p=1$Q1dBM0d1MExVbVFPTzlUcA$RUTGJJofvJI6Ar7RZ67ntwc1ZCJjvw5B76jSwSM5aNo', 'Teacher', 'Teacher', 'Teacher');

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE `files` (
  `id` int UNSIGNED NOT NULL,
  `active` tinyint(1) NOT NULL,
  `active_end` date NOT NULL,
  `source` varchar(64) NOT NULL,
  `points` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `files`
--

INSERT INTO `files` (`id`, `active`, `active_end`, `source`, `points`) VALUES
(1, 1, '2023-05-20', 'uploads/blokovka01pr.tex', 7),
(2, 1, '2023-05-14', 'uploads/blokovka02pr.tex', 5),
(3, 1, '2023-05-21', 'uploads/odozva01pr.tex', 15),
(4, 1, '2023-05-21', 'uploads/odozva02pr.tex', 20);

-- --------------------------------------------------------

--
-- Table structure for table `test`
--

CREATE TABLE `test` (
  `id` int UNSIGNED NOT NULL,
  `points_gained` int DEFAULT NULL,
  `student_id` int UNSIGNED NOT NULL,
  `question` varchar(512) NOT NULL,
  `image` varchar(256) DEFAULT NULL,
  `solution` varchar(512) NOT NULL,
  `answer` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `file_id` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `test`
--

INSERT INTO `test` (`id`, `points_gained`, `student_id`, `question`, `image`, `solution`, `answer`, `file_id`) VALUES
(43, 0, 6, 'Nájdite prenosovú funkciu $F(s)=\\dfrac{Y(s)}{W(s)}$ pre systém opísaný blokovou schémou:', 'zadanie99/images/blokovka01_00002.jpg', '\\begin{equation*}\r\n        \\dfrac{2s^2+13s+10}{s^3+7s^2+18s+15}\r\n    \\end{equation*}', 'test', 1),
(44, 5, 6, 'Nájdite prenosovú funkciu $F(s)=\\dfrac{Y(s)}{W(s)}$ pre systém opísaný blokovou schémou: \\\\', 'zadanie99/images/blokovka02_00004.jpg', '\\begin{equation*}\n        \\dfrac{5s+32}{10s^2+45s+32}\n    \\end{equation*}', '\\begin{equation*}\\dfrac{5s+32}{10s^2+45s+32}\\end{equation*}', 2),
(45, NULL, 6, 'Vypočítajte prechodovú funkciu pre systém opísaný prenosovou funkciou\r\n    \\begin{equation*}\r\n        F(s)=\\dfrac{35}{(2s+5)^2}e^{-6s}\r\n    \\end{equation*}', NULL, '\\begin{equation*}\r\n        y(t)=\\left[ \\dfrac{7}{5}-\\dfrac{7}{5}e^{-\\frac{5}{2}(t-6)}-\\dfrac{7}{2}(t-6)e^{-\\frac{5}{2}(t-6)} \\right] \\eta(t-6)\r\n    \\end{equation*}', NULL, 3),
(46, NULL, 6, 'Vypočítajte odozvu systému popísaného diferenciálnou rovnicou \r\n    \\begin{equation*}\r\n        y^{\'\'\'}(t)+8y^{\'\'}(t)+19y^{\'}(t)+12y(t)=u(t)\r\n    \\end{equation*}\r\n    na jednotkový skok, ak počiatočné podmienky sú: \\\\\r\n    $y(0)=-1$, $y^{\'}(0)=1$ a $y^{\'\'}(0)=-1$.', NULL, '\\begin{equation*}\r\n        y(t)=\\dfrac{1}{12} - \\dfrac{7}{6}e^{-t} + \\dfrac{1}{6}e^{-3t} - \\dfrac{1}{12}e^{-4t} = 0.0833 -1.166 e^{-t} + 0.1666 e^{-3t} - 0.0833 e^{-4t}\r\n    \\end{equation*}', NULL, 4);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `account`
--
ALTER TABLE `account`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `test`
--
ALTER TABLE `test`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_test` (`student_id`),
  ADD KEY `file_test` (`file_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `account`
--
ALTER TABLE `account`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `files`
--
ALTER TABLE `files`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `test`
--
ALTER TABLE `test`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `test`
--
ALTER TABLE `test`
  ADD CONSTRAINT `file_test` FOREIGN KEY (`file_id`) REFERENCES `files` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `student_test` FOREIGN KEY (`student_id`) REFERENCES `account` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
