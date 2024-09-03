-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 10, 2024 at 04:53 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `attendancedb`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `attendance_id` int(11) NOT NULL,
  `teacher_id` varchar(10) NOT NULL,
  `usn` varchar(15) NOT NULL,
  `course_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `status` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`attendance_id`, `teacher_id`, `usn`, `course_id`, `date`, `status`) VALUES
(148, '88', 'usn69', 1, '2024-03-01', 'Present'),
(149, '88', 'usn69', 1, '2024-03-02', 'Absent'),
(150, '88', 'usn69', 1, '2024-03-03', 'Present'),
(151, '88', 'usn69', 1, '2024-03-04', 'Present'),
(152, '88', 'usn69', 1, '2024-03-05', 'Present'),
(153, '88', 'usn69', 1, '2024-03-09', 'Present'),
(154, '88', 'usn69', 1, '2024-03-16', 'Present'),
(155, '88', 'usn69', 1, '2024-03-14', 'Present'),
(156, '88', 'usn69', 1, '2024-03-13', 'Present'),
(161, '4', '99', 2154, '2024-03-01', 'Present'),
(162, '4', '99', 2154, '2024-03-02', 'Absent'),
(163, '4', '99', 2154, '2024-03-03', 'Present'),
(164, '4', '99', 2154, '2024-03-04', 'Present'),
(165, '4', '99', 2154, '2024-03-05', 'Present'),
(166, '4', '99', 2154, '2024-03-06', 'Present'),
(167, '4', '99', 2154, '2024-03-07', 'Present'),
(168, '4', '99', 2154, '2024-03-09', 'Present'),
(169, '4', '99', 2154, '2024-03-10', 'Present'),
(170, '4', '99', 2154, '2024-03-11', 'Present');

--
-- Triggers `attendance`
--
DELIMITER $$
CREATE TRIGGER `update_notifications` AFTER INSERT ON `attendance` FOR EACH ROW BEGIN
    DECLARE attendancePercentage DECIMAL(5,2);
    DECLARE notificationMessage TEXT;
    DECLARE totalAttendance INT;
    DECLARE totalPossibleAttendance INT;
    
    -- Calculate total attendance for the course
    SELECT COUNT(*) INTO totalAttendance
    FROM attendance
    WHERE course_id = NEW.course_id AND `status` = 'Present';
    
    -- Calculate total possible attendance for the course
    SELECT COUNT(*) INTO totalPossibleAttendance
    FROM attendance
    WHERE course_id = NEW.course_id;
    
    -- Calculate attendance percentage
    IF totalPossibleAttendance > 0 THEN
        SET attendancePercentage = (totalAttendance / totalPossibleAttendance) * 100.00;
    ELSE
        SET attendancePercentage = 0.00;
    END IF;
    
    -- Prepare notification message
    SET notificationMessage = CONCAT('Your attendance for Course ID ', NEW.course_id, ' is ', attendancePercentage, '%.');
    
    -- Insert or update notification
    INSERT INTO notifications (usn, message)
    VALUES (NEW.usn, notificationMessage)
    ON DUPLICATE KEY UPDATE message = notificationMessage;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `course_id` int(11) NOT NULL,
  `course_name` varchar(255) NOT NULL,
  `year` int(11) NOT NULL,
  `section` varchar(2) NOT NULL,
  `teacher_id` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`course_id`, `course_name`, `year`, `section`, `teacher_id`) VALUES
(1, 'Dbms', 3, 'B', '1'),
(8, 'Dbms', 3, 'A', '88'),
(2152, 'Cn', 3, 'B', '2'),
(2153, 'Atc', 3, 'B', '3'),
(2154, 'Aiml', 3, 'B', '4');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `usn` varchar(15) NOT NULL,
  `message` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `usn`, `message`) VALUES
(78, 'usn69', 'Your attendance for Course ID 1 is 100.00%.'),
(79, 'usn69', 'Your attendance for Course ID 1 is below 85%. Please improve your attendance.'),
(80, 'usn69', 'Your attendance for Course ID 1 is below 85%. Please improve your attendance.'),
(81, 'usn69', 'Your attendance for Course ID 1 is below 85%. Please improve your attendance.'),
(82, 'usn69', 'Your attendance for Course ID 1 is below 85%. Please improve your attendance.'),
(83, 'usn69', 'Your attendance for Course ID 1 is below 85%. Please improve your attendance.'),
(84, 'usn69', 'Your attendance for Course ID 1 is 85.71%.'),
(85, 'usn69', 'Your attendance for Course ID 1 is 87.50%.'),
(86, 'usn69', 'Your attendance for Course ID 1 is 88.89%.'),
(91, '99', 'Your attendance for Course ID 2154 is 100.00%.'),
(92, '99', 'Your attendance for Course ID 2154 is 50.00%.'),
(93, '99', 'Your attendance for Course ID 2154 is 66.67%.'),
(94, '99', 'Your attendance for Course ID 2154 is 75.00%.'),
(95, '99', 'Your attendance for Course ID 2154 is 80.00%.'),
(96, '99', 'Your attendance for Course ID 2154 is 83.33%.'),
(97, '99', 'Your attendance for Course ID 2154 is 85.71%.'),
(98, '99', 'Your attendance for Course ID 2154 is 87.50%.'),
(99, '99', 'Your attendance for Course ID 2154 is 88.89%.'),
(100, '99', 'Your attendance for Course ID 2154 is 90.00%.');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `usn` varchar(15) NOT NULL,
  `name` varchar(255) NOT NULL,
  `year` int(11) NOT NULL,
  `section` varchar(2) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`usn`, `name`, `year`, `section`, `password`) VALUES
('4VP21CS068', 'Preetham', 3, 'b', 'preetham@2003'),
('4VP21CS069', 'Rachitha', 3, 'b', 'rachitha@2003'),
('4VP21CS070', 'Raghavendra', 3, 'b', 'raghu'),
('4VP21CS071', 'Rahul', 3, 'b', 'rahul'),
('4VP21CS072', 'Rakshith', 3, 'b', 'rakshith'),
('4VP21CS073', 'Ranjith', 3, 'b', 'ranjith'),
('4VP21CS074', 'Roopa', 3, 'b', 'roopa'),
('4VP21CS075', 'Karthik', 3, 'b', 'karthik'),
('4VP21CS076', 'Sakshi', 3, 'b', 'sakshi'),
('99', '9', 3, 'b', '9'),
('usn69', 'aka', 3, 'a', '1');

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `id` varchar(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `year` int(11) NOT NULL,
  `section` varchar(2) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`id`, `name`, `year`, `section`, `password`) VALUES
('1', 'Latha', 3, 'B', 'latha'),
('2', 'Jeevitha', 3, 'B', 'jeevitha'),
('3', 'Roopa', 3, 'B', 'roopa'),
('4', 'Bharathi', 3, 'B', 'bharathi'),
('88', 'ak', 3, 'A', '1');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`attendance_id`),
  ADD KEY `teacher_id` (`teacher_id`),
  ADD KEY `usn` (`usn`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`course_id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `usn` (`usn`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`usn`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `attendance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=171;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`),
  ADD CONSTRAINT `attendance_ibfk_2` FOREIGN KEY (`usn`) REFERENCES `students` (`usn`),
  ADD CONSTRAINT `attendance_ibfk_3` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`);

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`usn`) REFERENCES `students` (`usn`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
