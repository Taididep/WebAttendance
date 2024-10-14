-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 14, 2024 at 02:34 PM
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
-- Database: `db_atd`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetAllSemesters` ()   BEGIN
    SELECT semester_id, semester_name FROM semesters;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetAttendanceList` (IN `classId` INT, IN `semesterId` INT)   BEGIN
    SELECT a.*, s.firstname, s.lastname
    FROM attendance a
    JOIN students s ON a.student_id = s.student_id
    WHERE a.class_id = classId AND a.semester_id = semesterId;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetClassesBySemester` (IN `semester_id` INT)   BEGIN
    SELECT 
        c.class_id, 
        c.class_name, 
        co.course_name, 
        s.semester_name, 
        t.lastname, 
        t.firstname 
    FROM classes c
    JOIN courses co ON c.course_id = co.course_id
    JOIN semesters s ON c.semester_id = s.semester_id
    JOIN teachers t ON c.teacher_id = t.teacher_id
    WHERE c.semester_id = semester_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetStudentInfo` (IN `userId` INT)   BEGIN
    SELECT s.*
    FROM students s
    JOIN users u ON s.student_id = u.username -- Giả sử username là student_id
    WHERE u.user_id = userId;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetTeacherInfo` (IN `teacher_id_param` INT)   BEGIN
    SELECT lastname, firstname 
    FROM teachers 
    WHERE teacher_id = teacher_id_param;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetUserInfoByUsername` (IN `input_username` VARCHAR(255))   BEGIN
    SELECT u.user_id, u.username, u.password, r.role_name 
    FROM users u
    JOIN user_roles ur ON u.user_id = ur.user_id
    JOIN roles r ON ur.role_id = r.role_id
    WHERE u.username = input_username;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `MarkAttendance` (IN `studentId` INT, IN `classId` INT, IN `semesterId` INT)   BEGIN
    INSERT INTO attendance (student_id, class_id, semester_id, attendance_date)
    VALUES (studentId, classId, semesterId, CURDATE());
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `attendance_details`
--

CREATE TABLE `attendance_details` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `attendance_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `class_id` char(36) NOT NULL DEFAULT uuid(),
  `class_name` varchar(255) NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `semester_id` int(11) DEFAULT NULL,
  `teacher_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`class_id`, `class_name`, `course_id`, `semester_id`, `teacher_id`) VALUES
('670cb3ea259008.77375751', 'AV2 Luan', 12, 2, 1000001234),
('ee7262a0-84c9-11ef-bbd7-04421aee9db3', 'CSDL VanAnh', 25, 2, 1000001234);

-- --------------------------------------------------------

--
-- Table structure for table `class_students`
--

CREATE TABLE `class_students` (
  `stt` int(5) NOT NULL,
  `class_id` char(36) NOT NULL,
  `student_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `class_students`
--

INSERT INTO `class_students` (`stt`, `class_id`, `student_id`) VALUES
(2, 'ee7262a0-84c9-11ef-bbd7-04421aee9db3', 2001210224),
(3, 'ee7262a0-84c9-11ef-bbd7-04421aee9db3', 2001211785),
(4, 'ee7262a0-84c9-11ef-bbd7-04421aee9db3', 2001212345),
(5, 'ee7262a0-84c9-11ef-bbd7-04421aee9db3', 2001213456),
(6, 'ee7262a0-84c9-11ef-bbd7-04421aee9db3', 2001214567),
(7, 'ee7262a0-84c9-11ef-bbd7-04421aee9db3', 2001215678),
(1, 'ee7262a0-84c9-11ef-bbd7-04421aee9db3', 2001216114),
(8, 'ee7262a0-84c9-11ef-bbd7-04421aee9db3', 2001217890);

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `course_id` int(11) NOT NULL,
  `course_name` varchar(255) NOT NULL,
  `course_type_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`course_id`, `course_name`, `course_type_id`) VALUES
(1, 'Nhập môn lập trình', 3),
(2, 'Thực hành nhập môn lập trình', 5),
(3, 'Kỹ năng ứng dụng Công nghệ Thông tin', 5),
(4, 'Giải tích', 3),
(5, 'Hệ điều hành', 3),
(6, 'Thực hành Hệ điều hành', 4),
(7, 'Kiến trúc máy tính', 3),
(8, 'Kỹ thuật lập trình', 3),
(9, 'Thực hành kỹ thuật lập trình', 4),
(10, 'Đại số tuyến tính', 3),
(11, 'Anh Văn 1', 3),
(12, 'Anh Văn 2', 3),
(13, 'Cấu trúc dữ liệu và giải thuật', 3),
(14, 'Mạng máy tính', 3),
(15, 'Thực hành cấu trúc dữ liệu và giải thuật', 4),
(16, 'Thực hành mạng máy tính', 4),
(17, 'Cấu trúc rời rạc', 3),
(18, 'Thực hành Cấu trúc rời rạc', 4),
(19, 'Phương pháp nghiên cứu khoa học', 3),
(20, 'Phân tích thiết kế thuật toán', 3),
(21, 'Thiết kế web', 6),
(22, 'Lập trình hướng đối tượng', 3),
(23, 'Thực hành lập trình hướng đối tượng', 4),
(24, 'Cơ sở dữ liệu', 3),
(25, 'Thực hành cơ sở dữ liệu', 4),
(26, 'Anh văn 3', 3),
(27, 'Hệ quản trị cơ sở dữ liệu', 3),
(28, 'Thực hành hệ quản trị cơ sở dữ liệu', 4),
(29, 'Lập trình Web', 6),
(30, 'Trí tuệ nhân tạo', 3),
(31, 'Thực hành trí tuệ nhân tạo', 4),
(32, 'Công Nghệ Java', 6),
(33, 'Phân tích thiết kế hệ thống thông tin', 3),
(34, 'Thực hành phân tích thiết kế hệ thống thông tin', 4),
(35, 'Lập trình mã nguồn mở', 6),
(36, 'Phát triển ứng dụng di động', 6),
(37, 'Ảo hóa và điện toán đám mây', 3),
(38, 'Công nghệ phần mềm nâng cao', 3),
(39, 'Kiểm định phần mềm', 3),
(40, 'Thực hành kiểm định phần mềm', 4),
(41, 'Phát triển phần mềm ứng dụng thông minh', 6);

-- --------------------------------------------------------

--
-- Table structure for table `course_types`
--

CREATE TABLE `course_types` (
  `course_type_id` int(11) NOT NULL,
  `course_type_name` varchar(255) NOT NULL,
  `credits` int(11) NOT NULL,
  `theory_periods` int(11) NOT NULL,
  `practice_periods` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_types`
--

INSERT INTO `course_types` (`course_type_id`, `course_type_name`, `credits`, `theory_periods`, `practice_periods`) VALUES
(1, 'Lý thuyết', 1, 15, 0),
(2, 'Lý thuyết', 2, 30, 0),
(3, 'Lý thuyết', 3, 45, 0),
(4, 'Thực Hành', 1, 0, 30),
(5, 'Thực Hành', 2, 0, 60),
(6, 'Lý thuyết và Thực hành', 3, 15, 60);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`) VALUES
(1, 'admin'),
(3, 'student'),
(2, 'teacher');

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `schedule_id` int(11) NOT NULL,
  `class_id` char(36) NOT NULL,
  `date` date NOT NULL,
  `start_time` int(11) NOT NULL,
  `end_time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedules`
--

INSERT INTO `schedules` (`schedule_id`, `class_id`, `date`, `start_time`, `end_time`) VALUES
(1, 'ee7262a0-84c9-11ef-bbd7-04421aee9db3', '2024-10-07', 1, 3),
(2, 'ee7262a0-84c9-11ef-bbd7-04421aee9db3', '2024-10-14', 4, 6);

-- --------------------------------------------------------

--
-- Table structure for table `semesters`
--

CREATE TABLE `semesters` (
  `semester_id` int(11) NOT NULL,
  `semester_name` varchar(50) NOT NULL,
  `is_active` int(2) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `semesters`
--

INSERT INTO `semesters` (`semester_id`, `semester_name`, `is_active`, `start_date`, `end_date`) VALUES
(1, 'HK3 (Hè 2023 - 2024)', 0, '2024-07-10', '2024-08-07'),
(2, 'HK1 (2024 - 2025)', 1, '2024-08-15', '2025-12-17');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` int(11) NOT NULL,
  `lastname` varchar(50) DEFAULT NULL,
  `firstname` varchar(50) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `class` varchar(50) DEFAULT NULL,
  `birthday` date NOT NULL,
  `gender` enum('Nam','Nữ') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `lastname`, `firstname`, `email`, `phone`, `class`, `birthday`, `gender`) VALUES
(2001210123, 'Trần Văn', 'Tùng', 'tranvantung@gmail.com', '0911234567', '12DHTH11', '2003-03-01', 'Nam'),
(2001210224, 'Nguyễn Hữu', 'Thông', 'huuthong@gmail.com', '0901234567', '12DHTH14', '2003-03-15', 'Nam'),
(2001211234, 'Phạm Thị', 'Vân', 'phamthivan@gmail.com', '0912345678', '14DHTH12', '2005-10-17', 'Nữ'),
(2001211785, 'Phùng Vĩnh', 'Luân', 'vinhluan171@gmail.com', '0902345678', '12DHTH07', '2003-07-22', 'Nam'),
(2001212345, 'Lê Minh', 'Cường', 'leminhcuong@gmail.com', '0903456789', '12DHTH03', '2003-02-11', 'Nam'),
(2001212346, 'Hoàng Thị', 'Mai', 'hoangthimai@gmail.com', '0913456789', '13DHTH13', '2004-08-12', 'Nữ'),
(2001213456, 'Trương Thị', 'Lan', 'truongthilan@gmail.com', '0904567890', '12DHTH04', '2003-06-06', 'Nữ'),
(2001213457, 'Nguyễn Văn', 'Hùng', 'nguyenvanhung@gmail.com', '0914567890', '12DHTH14', '2002-05-05', 'Nam'),
(2001214567, 'Hoàng Văn', 'Linh', 'hoangvanlinh@gmail.com', '0905678901', '12DHTH05', '2003-04-08', 'Nam'),
(2001214568, 'Bùi Văn', 'Long', 'buivanlong@gmail.com', '0915678901', '13DHTH15', '2004-02-02', 'Nam'),
(2001215678, 'Bùi Thị', 'Hồng', 'buithihong@gmail.com', '0906789012', '12DHTH06', '2003-12-19', 'Nữ'),
(2001215679, 'Lê Thị', 'Như', 'lethinh@gmail.com', '0916789012', '12DHTH16', '2003-11-11', 'Nữ'),
(2001216114, 'Đinh Văn', 'Tài', 'dinhvantai079@gmail.com', '0901234578', '12DHTH02', '2003-03-30', 'Nam'),
(2001216780, 'Trương Văn', 'Dũng', 'truongvandung@gmail.com', '0917890123', '12DHTH17', '2003-01-01', 'Nam'),
(2001216789, 'Vũ Văn', 'Bình', 'vuvanhbinh@gmail.com', '0907890123', '11DHTH07', '2002-10-10', 'Nam'),
(2001217890, 'Nguyễn Thị', 'Hoa', 'nguyenthihua@gmail.com', '0908901234', '11DHTH08', '2002-11-17', 'Nữ'),
(2001217891, 'Vũ Thị', 'Hạnh', 'vuthihanh@gmail.com', '0918901234', '13DHTH18', '2004-05-17', 'Nữ'),
(2001218901, 'Đặng Văn', 'Quân', 'dangvanquan@gmail.com', '0909012345', '11DHTH09', '2002-05-30', 'Nam'),
(2001218902, 'Đặng Thị', 'Thu', 'dangthithu@gmail.com', '0920123456', '13DHTH19', '2004-11-12', 'Nữ'),
(2001219012, 'Lương Thị', 'Ngân', 'luongthingan@gmail.com', '0910123456', '12DHTH10', '2003-11-16', 'Nữ'),
(2001219017, 'Lý Minh', 'Anh', 'phungvinhluan2003@gmail.com', '0944939018', '12DHTH07', '2003-11-08', 'Nữ');

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `teacher_id` int(11) NOT NULL,
  `lastname` varchar(50) DEFAULT NULL,
  `firstname` varchar(50) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `birthday` date NOT NULL,
  `gender` enum('Nam','Nữ') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`teacher_id`, `lastname`, `firstname`, `email`, `phone`, `birthday`, `gender`) VALUES
(1000001234, 'Trần Thị Vân', 'Anh', 'vanAnh123@example.com', '0903456789', '1995-01-01', 'Nữ'),
(1000001235, 'Trần Văn', 'Hùng', 'HungTV@example.com', '0903456790', '1990-01-01', 'Nam'),
(1000001236, 'Nguyễn Văn', 'Tùng', 'NguyenVT@example.com', '0904567890', '1992-01-01', 'Nam');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`) VALUES
(1000001234, 'vanAnh123', '$2y$10$WZZ9ziqz02krLQJsSW8lruYX4WDRU2QzGsZ0Le0ZS//gCh021cGvW'),
(1000001235, '1000001235', '$2y$10$PBpe4kyIqX.RX4f/KslZYeKxOOr0Hlck4of6Z6oJLia0R1AWTEHC.'),
(1000001236, '1000001236', '$2y$10$MMAunS7wvIsZ2UHe19Xr6OVzGFuQyLy6Xsi/rsLvSdfERNQIKpM0u'),
(2001210123, '2001210123', '$2y$10$z1syiVxj4fFVzEJ/HGaIP.u3Op7M7plk9KaAmdodnOTx8F4cc5vxW'),
(2001210224, '2001210224', '$2y$10$6hYJciz1kxXo7NiVTuQmGOA4/IBmFwdUy4u5xOpwLp8x3lo4gSUFm'),
(2001211234, '2001211234', '$2y$10$yfLCOYrdMrZDw2aAWvLs/u9v1PMrZaiaOiBceEKjdPDgXeMLy/h4W'),
(2001211785, '2001211785', '$2y$10$bQOsHOYsBLEbA1Xlo.wSdO/.4cYEFVewdCBpIixKnSSqQHHdqnqk.'),
(2001212345, '2001212345', '$2y$10$CIWBBd2tVfVu67PWFtniFuEl7IFDO3MDjAezgoN.EtAdEK8wxDq3i'),
(2001212346, '2001212346', '$2y$10$VGVqgwg6a8iQhkkahX.ekusagyb3w3wWuZ6b1PRYEhA4F6BhqvKyK'),
(2001213456, '2001213456', '$2y$10$b.ahnojABn3xmEfT41E5Y./L7XsiW.XMSxy2xVOu0Gm6LuKiXE6IO'),
(2001213457, '2001213457', '$2y$10$PR.VkWql0Rqk1Ec/rCjPwORXpXunZ03nN2QPXrzF0BAnBD5XuoJh.'),
(2001214567, '2001214567', '$2y$10$MYQUxdzoBgzhNCvEV5sQGOIfwSLxrSIY9qTyW0F8KgFSETZDHVV46'),
(2001214568, '2001214568', '$2y$10$UnfrYs4jMy9Q8r67BAEVvuDHQO7/yhq13I77EmMdxW1O3mBVt9wo.'),
(2001215678, '2001215678', '$2y$10$1fhFReDh3YI/Y.gU/5qzuOxizPZ.ZZFu1nEnCeotDsasLTExSe/9O'),
(2001215679, '2001215679', '$2y$10$PDMEa.qk0VBGWmGv1wSSiu4iu/ZODjaCfc7c0nMna6XoX//emIl2q'),
(2001216114, '2001216114', '$2y$10$AbbaW861JhICgXOEpG0iE.SIquT3hSQK9E9iCfMpDsI8b0mDq/n8K'),
(2001216780, '2001216780', '$2y$10$XQUAcWPeht0yDlDbu/zLgOEjiPGVuHygw2T9zDO1RX6ei4BxM0Sn2'),
(2001216789, '2001216789', '$2y$10$UywktTRVWKEfzkb8MoLMZe8Osb1Re1lEU0UoHMu8HLhyyOTWIdgCS'),
(2001217890, '2001217890', '$2y$10$bv7ceSD5oZdukkXKjI0qmeasYZFCJIXsW91ynKjaZj/VaN789svwu'),
(2001217891, '2001217891', '$2y$10$IYGbs8ePv72iI6dfxQ8w4u7uU6uvTYcL2fHQzyBPhm.nx3V9zOlpe'),
(2001218901, '2001218901', '$2y$10$1ZuRSmbmqF0WdIUtQOfTfu/L8fq1f9usIEmZ0oYTO6nT8O5ZpyN7a'),
(2001218902, '2001218902', '$2y$10$iXCJ7dsX8Z8Uy3YVpgxem.8a7yr5Ef9r7kAA5ays0XckYVjmIeqby'),
(2001219012, '2001219012', '$2y$10$AIZ6OefoLjlsCGAp9hCxY.pFJn.E5PCcfg1GjtRXUCRe6RSrvQ.hm'),
(2001219017, 'thankiemsama', '$2y$10$reeSO8C5/V.TfvSDhl1wgeFi8PAHUcVHWAOoeSsxmZBB.3Q/Rxfnu');

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES
(1000001234, 2),
(1000001235, 2),
(1000001236, 2),
(2001210123, 3),
(2001210224, 3),
(2001211234, 3),
(2001211785, 3),
(2001212345, 3),
(2001212346, 3),
(2001213456, 3),
(2001213457, 3),
(2001214567, 3),
(2001214568, 3),
(2001215678, 3),
(2001215679, 3),
(2001216114, 3),
(2001216780, 3),
(2001216789, 3),
(2001217890, 3),
(2001217891, 3),
(2001218901, 3),
(2001218902, 3),
(2001219012, 3),
(2001219017, 3);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance_details`
--
ALTER TABLE `attendance_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`class_id`),
  ADD KEY `fk_course_id` (`course_id`),
  ADD KEY `fk_teacher_id` (`teacher_id`),
  ADD KEY `fk_semester_id` (`semester_id`);

--
-- Indexes for table `class_students`
--
ALTER TABLE `class_students`
  ADD PRIMARY KEY (`class_id`,`student_id`),
  ADD KEY `fk_class_student_student_id` (`student_id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`course_id`),
  ADD KEY `course_type_id` (`course_type_id`);

--
-- Indexes for table `course_types`
--
ALTER TABLE `course_types`
  ADD PRIMARY KEY (`course_type_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`schedule_id`),
  ADD KEY `class_id` (`class_id`);

--
-- Indexes for table `semesters`
--
ALTER TABLE `semesters`
  ADD PRIMARY KEY (`semester_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`teacher_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`user_id`,`role_id`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance_details`
--
ALTER TABLE `attendance_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `course_types`
--
ALTER TABLE `course_types`
  MODIFY `course_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `semesters`
--
ALTER TABLE `semesters`
  MODIFY `semester_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2001219018;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `teacher_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1000001237;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2001219018;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance_details`
--
ALTER TABLE `attendance_details`
  ADD CONSTRAINT `attendance_details_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`);

--
-- Constraints for table `classes`
--
ALTER TABLE `classes`
  ADD CONSTRAINT `fk_course_id` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`),
  ADD CONSTRAINT `fk_semester_id` FOREIGN KEY (`semester_id`) REFERENCES `semesters` (`semester_id`),
  ADD CONSTRAINT `fk_teacher_id` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`teacher_id`);

--
-- Constraints for table `class_students`
--
ALTER TABLE `class_students`
  ADD CONSTRAINT `fk_class_student_class_id` FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`),
  ADD CONSTRAINT `fk_class_student_student_id` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`);

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`course_type_id`) REFERENCES `course_types` (`course_type_id`) ON DELETE SET NULL;

--
-- Constraints for table `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `schedules_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`);

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `teachers`
--
ALTER TABLE `teachers`
  ADD CONSTRAINT `teachers_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `user_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
