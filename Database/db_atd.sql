-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th10 20, 2024 lúc 05:37 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `db_atd`
--

DELIMITER $$
--
-- Thủ tục
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetAllSemesters` ()   BEGIN
    SELECT semester_id, semester_name 
    FROM semesters
    ORDER BY semester_id DESC; -- Sắp xếp theo semester_id giảm dần
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetAttendanceByClassId` (IN `class_id` INT)   BEGIN
    SELECT sch.date, a.student_id, a.status 
    FROM schedules sch
    LEFT JOIN attendances a ON sch.schedule_id = a.schedule_id
    WHERE sch.class_id = class_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetAttendanceByScheduleId` (IN `scheduleId` INT)   BEGIN
    SELECT student_id, status
    FROM attendances
    WHERE schedule_id = scheduleId;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetClassDetailsById` (IN `classId` CHAR(36))   BEGIN
    SELECT 
        c.class_id,
        c.class_name,
        s.semester_name,
        co.course_name,
        CONCAT(t.lastname, ' ', t.firstname) AS teacher_fullname
    FROM 
        classes c
    JOIN 
        semesters s ON c.semester_id = s.semester_id
    JOIN 
        courses co ON c.course_id = co.course_id
    JOIN 
        teachers t ON c.teacher_id = t.teacher_id
    WHERE 
        c.class_id = classId;
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetDistinctDatesByClassId` (IN `class_id` INT)   BEGIN
    SELECT DISTINCT date FROM schedules WHERE class_id = class_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetSchedulesByClassId` (IN `class_id` INT)   BEGIN
    SELECT schedule_id, date FROM schedules WHERE class_id = class_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetStudentsByClassId` (IN `class_id` INT)   BEGIN
    SELECT s.student_id, s.lastname, s.firstname, s.class, s.birthday, c.class_name 
    FROM students s
    JOIN class_students cs ON s.student_id = cs.student_id
    JOIN classes c ON cs.class_id = c.class_id
    WHERE c.class_id = class_id;
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

DELIMITER ;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `attendances`
--

CREATE TABLE `attendances` (
  `attendance_id` int(11) NOT NULL,
  `schedule_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL COMMENT '0: Absent, 1: Present, 2: Late',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `attendances`
--

INSERT INTO `attendances` (`attendance_id`, `schedule_id`, `student_id`, `status`, `updated_at`) VALUES
(1, 1, 2001210224, 1, '2024-10-19 16:51:51'),
(2, 2, 2001210224, 0, '2024-10-19 16:44:17'),
(3, 3, 2001210224, 0, '2024-10-19 13:49:04'),
(4, 4, 2001210224, 0, '2024-10-19 13:49:04'),
(5, 5, 2001210224, 0, '2024-10-19 13:49:04'),
(6, 6, 2001210224, 0, '2024-10-19 13:49:04'),
(7, 7, 2001210224, 0, '2024-10-19 13:49:04'),
(8, 8, 2001210224, 0, '2024-10-19 13:49:04'),
(9, 9, 2001210224, 0, '2024-10-19 13:49:04'),
(10, 10, 2001210224, 0, '2024-10-19 13:49:04'),
(11, 1, 2001211785, 1, '2024-10-19 16:51:51'),
(12, 2, 2001211785, 0, '2024-10-19 16:44:17'),
(13, 3, 2001211785, 0, '2024-10-19 13:49:04'),
(14, 4, 2001211785, 0, '2024-10-19 13:49:04'),
(15, 5, 2001211785, 0, '2024-10-19 13:49:04'),
(16, 6, 2001211785, 0, '2024-10-19 13:49:04'),
(17, 7, 2001211785, 0, '2024-10-19 13:49:04'),
(18, 8, 2001211785, 0, '2024-10-19 13:49:04'),
(19, 9, 2001211785, 0, '2024-10-19 13:49:04'),
(20, 10, 2001211785, 0, '2024-10-19 13:49:04'),
(21, 1, 2001212345, 1, '2024-10-19 17:06:06'),
(22, 2, 2001212345, 0, '2024-10-19 16:44:17'),
(23, 3, 2001212345, 0, '2024-10-19 13:49:04'),
(24, 4, 2001212345, 0, '2024-10-19 13:49:04'),
(25, 5, 2001212345, 0, '2024-10-19 13:49:04'),
(26, 6, 2001212345, 0, '2024-10-19 13:49:04'),
(27, 7, 2001212345, 0, '2024-10-19 13:49:04'),
(28, 8, 2001212345, 0, '2024-10-19 13:49:04'),
(29, 9, 2001212345, 0, '2024-10-19 13:49:05'),
(30, 10, 2001212345, 0, '2024-10-19 13:49:05'),
(31, 1, 2001213456, 0, '2024-10-19 16:44:17'),
(32, 2, 2001213456, 0, '2024-10-19 16:44:17'),
(33, 3, 2001213456, 0, '2024-10-19 13:49:05'),
(34, 4, 2001213456, 0, '2024-10-19 13:49:05'),
(35, 5, 2001213456, 0, '2024-10-19 13:49:05'),
(36, 6, 2001213456, 0, '2024-10-19 13:49:05'),
(37, 7, 2001213456, 0, '2024-10-19 13:49:05'),
(38, 8, 2001213456, 0, '2024-10-19 13:49:05'),
(39, 9, 2001213456, 0, '2024-10-19 13:49:05'),
(40, 10, 2001213456, 0, '2024-10-19 13:49:05'),
(41, 1, 2001214567, 0, '2024-10-19 16:44:17'),
(42, 2, 2001214567, 0, '2024-10-19 16:44:17'),
(43, 3, 2001214567, 0, '2024-10-19 13:49:05'),
(44, 4, 2001214567, 0, '2024-10-19 13:49:05'),
(45, 5, 2001214567, 0, '2024-10-19 13:49:05'),
(46, 6, 2001214567, 0, '2024-10-19 13:49:05'),
(47, 7, 2001214567, 0, '2024-10-19 13:49:05'),
(48, 8, 2001214567, 0, '2024-10-19 13:49:05'),
(49, 9, 2001214567, 0, '2024-10-19 13:49:05'),
(50, 10, 2001214567, 0, '2024-10-19 13:49:05'),
(51, 1, 2001215678, 0, '2024-10-19 16:44:17'),
(52, 2, 2001215678, 0, '2024-10-19 16:44:18'),
(53, 3, 2001215678, 0, '2024-10-19 13:49:05'),
(54, 4, 2001215678, 0, '2024-10-19 13:49:05'),
(55, 5, 2001215678, 0, '2024-10-19 13:49:05'),
(56, 6, 2001215678, 0, '2024-10-19 13:49:05'),
(57, 7, 2001215678, 0, '2024-10-19 13:49:05'),
(58, 8, 2001215678, 0, '2024-10-19 13:49:05'),
(59, 9, 2001215678, 0, '2024-10-19 13:49:05'),
(60, 10, 2001215678, 0, '2024-10-19 13:49:05'),
(61, 1, 2001216114, 0, '2024-10-19 16:44:18'),
(62, 2, 2001216114, 0, '2024-10-19 16:44:18'),
(63, 3, 2001216114, 0, '2024-10-19 13:49:05'),
(64, 4, 2001216114, 0, '2024-10-19 13:49:05'),
(65, 5, 2001216114, 0, '2024-10-19 13:49:05'),
(66, 6, 2001216114, 0, '2024-10-19 13:49:05'),
(67, 7, 2001216114, 0, '2024-10-19 13:49:06'),
(68, 8, 2001216114, 0, '2024-10-19 13:49:06'),
(69, 9, 2001216114, 0, '2024-10-19 13:49:06'),
(70, 10, 2001216114, 0, '2024-10-19 13:49:06'),
(71, 1, 2001217890, 0, '2024-10-19 16:44:18'),
(72, 2, 2001217890, 0, '2024-10-19 16:44:18'),
(73, 3, 2001217890, 0, '2024-10-19 13:49:06'),
(74, 4, 2001217890, 0, '2024-10-19 13:49:06'),
(75, 5, 2001217890, 0, '2024-10-19 13:49:06'),
(76, 6, 2001217890, 0, '2024-10-19 13:49:06'),
(77, 7, 2001217890, 0, '2024-10-19 13:49:06'),
(78, 8, 2001217890, 0, '2024-10-19 13:49:06'),
(79, 9, 2001217890, 0, '2024-10-19 13:49:06'),
(80, 10, 2001217890, 0, '2024-10-19 13:49:06');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `attendance_reports`
--

CREATE TABLE `attendance_reports` (
  `report_id` int(11) NOT NULL,
  `class_id` char(36) NOT NULL,
  `student_id` int(11) NOT NULL,
  `total_present` int(11) DEFAULT 0,
  `total_absent` int(11) DEFAULT 0,
  `total_late` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `classes`
--

CREATE TABLE `classes` (
  `class_id` char(36) NOT NULL DEFAULT uuid(),
  `class_name` varchar(255) NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `semester_id` int(11) DEFAULT NULL,
  `teacher_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `classes`
--

INSERT INTO `classes` (`class_id`, `class_name`, `course_id`, `semester_id`, `teacher_id`) VALUES
('ee7262a0-84c9-11ef-bbd7-04421aee9db3', 'CSDL VanAnh', 25, 2, 1000001234);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `class_students`
--

CREATE TABLE `class_students` (
  `class_id` char(36) NOT NULL,
  `student_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `class_students`
--

INSERT INTO `class_students` (`class_id`, `student_id`) VALUES
('ee7262a0-84c9-11ef-bbd7-04421aee9db3', 2001210224),
('ee7262a0-84c9-11ef-bbd7-04421aee9db3', 2001211785),
('ee7262a0-84c9-11ef-bbd7-04421aee9db3', 2001212345),
('ee7262a0-84c9-11ef-bbd7-04421aee9db3', 2001213456),
('ee7262a0-84c9-11ef-bbd7-04421aee9db3', 2001214567),
('ee7262a0-84c9-11ef-bbd7-04421aee9db3', 2001215678),
('ee7262a0-84c9-11ef-bbd7-04421aee9db3', 2001216114),
('ee7262a0-84c9-11ef-bbd7-04421aee9db3', 2001217890);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `courses`
--

CREATE TABLE `courses` (
  `course_id` int(11) NOT NULL,
  `course_name` varchar(255) NOT NULL,
  `course_type_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `courses`
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
-- Cấu trúc bảng cho bảng `course_types`
--

CREATE TABLE `course_types` (
  `course_type_id` int(11) NOT NULL,
  `course_type_name` varchar(255) NOT NULL,
  `credits` int(11) NOT NULL,
  `theory_periods` int(11) NOT NULL,
  `practice_periods` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `course_types`
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
-- Cấu trúc bảng cho bảng `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`) VALUES
(1, 'admin'),
(3, 'student'),
(2, 'teacher');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `schedules`
--

CREATE TABLE `schedules` (
  `schedule_id` int(11) NOT NULL,
  `class_id` char(36) NOT NULL,
  `date` date NOT NULL,
  `start_time` int(11) NOT NULL,
  `end_time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `schedules`
--

INSERT INTO `schedules` (`schedule_id`, `class_id`, `date`, `start_time`, `end_time`) VALUES
(1, 'ee7262a0-84c9-11ef-bbd7-04421aee9db3', '2024-10-07', 1, 3),
(2, 'ee7262a0-84c9-11ef-bbd7-04421aee9db3', '2024-10-14', 1, 3),
(3, 'ee7262a0-84c9-11ef-bbd7-04421aee9db3', '2024-10-21', 1, 3),
(4, 'ee7262a0-84c9-11ef-bbd7-04421aee9db3', '2024-10-28', 1, 3),
(5, 'ee7262a0-84c9-11ef-bbd7-04421aee9db3', '2024-11-04', 1, 3),
(6, 'ee7262a0-84c9-11ef-bbd7-04421aee9db3', '2024-11-11', 1, 3),
(7, 'ee7262a0-84c9-11ef-bbd7-04421aee9db3', '2024-11-18', 1, 3),
(8, 'ee7262a0-84c9-11ef-bbd7-04421aee9db3', '2024-11-25', 1, 3),
(9, 'ee7262a0-84c9-11ef-bbd7-04421aee9db3', '2024-12-02', 1, 3),
(10, 'ee7262a0-84c9-11ef-bbd7-04421aee9db3', '2024-12-09', 1, 3);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `semesters`
--

CREATE TABLE `semesters` (
  `semester_id` int(11) NOT NULL,
  `semester_name` varchar(50) NOT NULL,
  `is_active` int(2) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `semesters`
--

INSERT INTO `semesters` (`semester_id`, `semester_name`, `is_active`, `start_date`, `end_date`) VALUES
(1, 'HK3 (Hè 2023 - 2024)', 0, '2024-07-10', '2024-08-07'),
(2, 'HK1 (2024 - 2025)', 1, '2024-08-15', '2025-12-17');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `students`
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
-- Đang đổ dữ liệu cho bảng `students`
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
(2001219012, 'Lương Thị', 'Ngân', 'luongthingan@gmail.com', '0910123456', '12DHTH10', '2003-11-16', 'Nữ');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `teachers`
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
-- Đang đổ dữ liệu cho bảng `teachers`
--

INSERT INTO `teachers` (`teacher_id`, `lastname`, `firstname`, `email`, `phone`, `birthday`, `gender`) VALUES
(1000001234, 'Trần Thị Vân', 'Anh', 'vanAnh123@example.com', '0903456789', '1995-01-01', 'Nữ'),
(1000001235, 'Trần Văn', 'Hùng', 'HungTV@example.com', '0903456790', '1990-01-01', 'Nam'),
(1000001236, 'Nguyễn Văn', 'Tùng', 'NguyenVT@example.com', '0904567890', '1992-01-01', 'Nam');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
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
(2001219012, '2001219012', '$2y$10$AIZ6OefoLjlsCGAp9hCxY.pFJn.E5PCcfg1GjtRXUCRe6RSrvQ.hm');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `user_roles`
--

CREATE TABLE `user_roles` (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `user_roles`
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
(2001219012, 3);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `attendances`
--
ALTER TABLE `attendances`
  ADD PRIMARY KEY (`attendance_id`),
  ADD UNIQUE KEY `unique_attendance` (`schedule_id`,`student_id`),
  ADD KEY `fk_student_id` (`student_id`);

--
-- Chỉ mục cho bảng `attendance_reports`
--
ALTER TABLE `attendance_reports`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `fk_attendance_report_class_id` (`class_id`),
  ADD KEY `fk_attendance_report_student_id` (`student_id`);

--
-- Chỉ mục cho bảng `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`class_id`),
  ADD KEY `fk_course_id` (`course_id`),
  ADD KEY `fk_teacher_id` (`teacher_id`),
  ADD KEY `fk_semester_id` (`semester_id`);

--
-- Chỉ mục cho bảng `class_students`
--
ALTER TABLE `class_students`
  ADD PRIMARY KEY (`class_id`,`student_id`),
  ADD KEY `fk_class_student_student_id` (`student_id`);

--
-- Chỉ mục cho bảng `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`course_id`),
  ADD KEY `course_type_id` (`course_type_id`);

--
-- Chỉ mục cho bảng `course_types`
--
ALTER TABLE `course_types`
  ADD PRIMARY KEY (`course_type_id`);

--
-- Chỉ mục cho bảng `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Chỉ mục cho bảng `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`schedule_id`),
  ADD KEY `class_id` (`class_id`);

--
-- Chỉ mục cho bảng `semesters`
--
ALTER TABLE `semesters`
  ADD PRIMARY KEY (`semester_id`);

--
-- Chỉ mục cho bảng `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`);

--
-- Chỉ mục cho bảng `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`teacher_id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Chỉ mục cho bảng `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`user_id`,`role_id`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `attendances`
--
ALTER TABLE `attendances`
  MODIFY `attendance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT cho bảng `attendance_reports`
--
ALTER TABLE `attendance_reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `courses`
--
ALTER TABLE `courses`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT cho bảng `course_types`
--
ALTER TABLE `course_types`
  MODIFY `course_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `schedules`
--
ALTER TABLE `schedules`
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT cho bảng `semesters`
--
ALTER TABLE `semesters`
  MODIFY `semester_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2001219013;

--
-- AUTO_INCREMENT cho bảng `teachers`
--
ALTER TABLE `teachers`
  MODIFY `teacher_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1000001237;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2001219013;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `attendances`
--
ALTER TABLE `attendances`
  ADD CONSTRAINT `fk_schedule_id` FOREIGN KEY (`schedule_id`) REFERENCES `schedules` (`schedule_id`),
  ADD CONSTRAINT `fk_student_id` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`);

--
-- Các ràng buộc cho bảng `attendance_reports`
--
ALTER TABLE `attendance_reports`
  ADD CONSTRAINT `fk_attendance_report_class_id` FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`),
  ADD CONSTRAINT `fk_attendance_report_student_id` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`);

--
-- Các ràng buộc cho bảng `classes`
--
ALTER TABLE `classes`
  ADD CONSTRAINT `fk_course_id` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`),
  ADD CONSTRAINT `fk_semester_id` FOREIGN KEY (`semester_id`) REFERENCES `semesters` (`semester_id`),
  ADD CONSTRAINT `fk_teacher_id` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`teacher_id`);

--
-- Các ràng buộc cho bảng `class_students`
--
ALTER TABLE `class_students`
  ADD CONSTRAINT `fk_class_student_class_id` FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`),
  ADD CONSTRAINT `fk_class_student_student_id` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`);

--
-- Các ràng buộc cho bảng `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`course_type_id`) REFERENCES `course_types` (`course_type_id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `schedules_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`);

--
-- Các ràng buộc cho bảng `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `teachers`
--
ALTER TABLE `teachers`
  ADD CONSTRAINT `teachers_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `user_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
