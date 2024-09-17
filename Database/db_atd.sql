-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th9 17, 2024 lúc 07:17 PM
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

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `attendances`
--

CREATE TABLE `attendances` (
  `id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `attendance_date` date NOT NULL,
  `status` enum('Present','Absent','Late') NOT NULL,
  `note` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `attendances`
--

INSERT INTO `attendances` (`id`, `class_id`, `student_id`, `attendance_date`, `status`, `note`) VALUES
(1, 1, 2001216114, '2024-09-01', 'Present', NULL),
(2, 1, 2001210224, '2024-09-01', 'Absent', 'Sick'),
(3, 1, 2001211785, '2024-09-01', 'Late', 'Traffic'),
(4, 1, 2001212345, '2024-09-01', 'Present', NULL),
(5, 1, 2001213456, '2024-09-01', 'Present', NULL),
(6, 1, 2001214567, '2024-09-01', 'Present', NULL),
(7, 1, 2001215678, '2024-09-01', 'Absent', 'Family Emergency'),
(8, 1, 2001216789, '2024-09-01', 'Present', NULL),
(9, 1, 2001217890, '2024-09-01', 'Present', NULL),
(10, 1, 2001218901, '2024-09-01', 'Late', 'Doctor Appointment'),
(11, 1, 2001219012, '2024-09-01', 'Present', NULL),
(12, 1, 2001210123, '2024-09-01', 'Absent', 'Holiday'),
(13, 1, 2001211234, '2024-09-01', 'Present', NULL),
(14, 1, 2001212346, '2024-09-01', 'Present', NULL),
(15, 1, 2001213457, '2024-09-01', 'Absent', 'Sick'),
(16, 1, 2001214568, '2024-09-01', 'Present', NULL),
(17, 1, 2001215679, '2024-09-01', 'Late', 'Car Problem'),
(18, 1, 2001216780, '2024-09-01', 'Present', NULL),
(19, 1, 2001217891, '2024-09-01', 'Absent', 'Family Emergency'),
(20, 1, 2001218902, '2024-09-01', 'Present', NULL),
(21, 1, 2001219013, '2024-09-01', 'Late', 'Traffic'),
(22, 1, 2001223456, '2024-09-01', 'Present', NULL),
(23, 1, 2001224567, '2024-09-01', 'Present', NULL),
(24, 1, 2001225678, '2024-09-01', 'Absent', 'Doctor Appointment'),
(25, 1, 2001226789, '2024-09-01', 'Present', NULL),
(26, 1, 2001227890, '2024-09-01', 'Late', 'Traffic'),
(27, 1, 2001228901, '2024-09-01', 'Present', NULL),
(28, 1, 2001229012, '2024-09-01', 'Present', NULL),
(29, 1, 2001230123, '2024-09-01', 'Absent', 'Holiday'),
(30, 1, 2001231234, '2024-09-01', 'Present', NULL),
(31, 1, 2001232345, '2024-09-01', 'Present', NULL),
(32, 1, 2001233456, '2024-09-01', 'Late', 'Doctor Appointment'),
(33, 1, 2001234567, '2024-09-01', 'Present', NULL),
(34, 2, 2001214567, '2024-09-01', 'Present', NULL),
(35, 2, 2001215678, '2024-09-01', 'Late', 'Doctor Appointment'),
(36, 2, 2001216789, '2024-09-01', 'Present', NULL),
(37, 2, 2001217890, '2024-09-01', 'Absent', 'Holiday'),
(38, 2, 2001218901, '2024-09-01', 'Present', NULL),
(39, 2, 2001219012, '2024-09-01', 'Absent', 'Family Emergency'),
(40, 2, 2001210123, '2024-09-01', 'Present', NULL),
(41, 2, 2001211234, '2024-09-01', 'Present', NULL),
(42, 2, 2001212346, '2024-09-01', 'Absent', 'Travel'),
(43, 2, 2001213457, '2024-09-01', 'Present', NULL),
(44, 2, 2001214568, '2024-09-01', 'Late', 'Traffic'),
(45, 2, 2001215679, '2024-09-01', 'Present', NULL),
(46, 2, 2001216780, '2024-09-01', 'Absent', 'Family'),
(47, 2, 2001217891, '2024-09-01', 'Present', NULL),
(48, 2, 2001218902, '2024-09-01', 'Present', NULL),
(49, 2, 2001219013, '2024-09-01', 'Absent', 'Holiday'),
(50, 2, 2001223456, '2024-09-01', 'Present', NULL),
(51, 2, 2001224567, '2024-09-01', 'Present', NULL),
(52, 2, 2001225678, '2024-09-01', 'Late', 'Car Problem'),
(53, 2, 2001226789, '2024-09-01', 'Present', NULL),
(54, 2, 2001227890, '2024-09-01', 'Absent', 'Family Emergency'),
(55, 2, 2001228901, '2024-09-01', 'Present', NULL),
(56, 2, 2001229012, '2024-09-01', 'Present', NULL),
(57, 2, 2001230123, '2024-09-01', 'Absent', 'Travel'),
(58, 2, 2001231234, '2024-09-01', 'Present', NULL),
(59, 2, 2001232345, '2024-09-01', 'Present', NULL),
(60, 2, 2001233456, '2024-09-01', 'Late', 'Doctor Appointment'),
(61, 2, 2001234567, '2024-09-01', 'Present', NULL),
(62, 2, 2001214567, '2024-09-01', 'Present', NULL),
(63, 2, 2001215678, '2024-09-01', 'Late', 'Doctor Appointment'),
(64, 2, 2001216789, '2024-09-01', 'Present', NULL),
(65, 2, 2001217890, '2024-09-01', 'Absent', 'Holiday'),
(66, 2, 2001218901, '2024-09-01', 'Present', NULL),
(67, 2, 2001219012, '2024-09-01', 'Absent', 'Family Emergency'),
(68, 2, 2001210123, '2024-09-01', 'Present', NULL),
(69, 2, 2001211234, '2024-09-01', 'Present', NULL),
(70, 2, 2001212346, '2024-09-01', 'Absent', 'Travel'),
(71, 2, 2001213457, '2024-09-01', 'Present', NULL),
(72, 2, 2001214568, '2024-09-01', 'Late', 'Traffic'),
(73, 2, 2001215679, '2024-09-01', 'Present', NULL),
(74, 2, 2001216780, '2024-09-01', 'Absent', 'Family'),
(75, 2, 2001217891, '2024-09-01', 'Present', NULL),
(76, 2, 2001218902, '2024-09-01', 'Present', NULL),
(77, 2, 2001219013, '2024-09-01', 'Absent', 'Holiday'),
(78, 2, 2001223456, '2024-09-01', 'Present', NULL),
(79, 2, 2001224567, '2024-09-01', 'Present', NULL),
(80, 2, 2001225678, '2024-09-01', 'Late', 'Car Problem'),
(81, 2, 2001226789, '2024-09-01', 'Present', NULL),
(82, 2, 2001227890, '2024-09-01', 'Absent', 'Family Emergency'),
(83, 2, 2001228901, '2024-09-01', 'Present', NULL),
(84, 2, 2001229012, '2024-09-01', 'Present', NULL),
(85, 2, 2001230123, '2024-09-01', 'Absent', 'Travel'),
(86, 2, 2001231234, '2024-09-01', 'Present', NULL),
(87, 2, 2001232345, '2024-09-01', 'Present', NULL),
(88, 2, 2001233456, '2024-09-01', 'Late', 'Doctor Appointment'),
(89, 2, 2001234567, '2024-09-01', 'Present', NULL),
(90, 3, 2001219012, '2024-09-01', 'Present', NULL),
(91, 3, 2001210123, '2024-09-01', 'Late', 'Car Problem'),
(92, 3, 2001211234, '2024-09-01', 'Present', NULL),
(93, 3, 2001212346, '2024-09-01', 'Absent', 'Sick'),
(94, 3, 2001213457, '2024-09-01', 'Present', NULL),
(95, 3, 2001214568, '2024-09-01', 'Late', 'Family Emergency'),
(96, 3, 2001215679, '2024-09-01', 'Present', NULL),
(97, 3, 2001216780, '2024-09-01', 'Absent', 'Travel'),
(98, 3, 2001217891, '2024-09-01', 'Present', NULL),
(99, 3, 2001218902, '2024-09-01', 'Present', NULL),
(100, 3, 2001219013, '2024-09-01', 'Late', 'Traffic'),
(101, 3, 2001223456, '2024-09-01', 'Present', NULL),
(102, 3, 2001224567, '2024-09-01', 'Present', NULL),
(103, 3, 2001225678, '2024-09-01', 'Absent', 'Family Emergency'),
(104, 3, 2001226789, '2024-09-01', 'Present', NULL),
(105, 3, 2001227890, '2024-09-01', 'Late', 'Doctor Appointment'),
(106, 3, 2001228901, '2024-09-01', 'Present', NULL),
(107, 3, 2001229012, '2024-09-01', 'Present', NULL),
(108, 3, 2001230123, '2024-09-01', 'Absent', 'Holiday'),
(109, 3, 2001231234, '2024-09-01', 'Present', NULL),
(110, 3, 2001232345, '2024-09-01', 'Present', NULL),
(111, 3, 2001233456, '2024-09-01', 'Late', 'Car Problem'),
(112, 3, 2001234567, '2024-09-01', 'Present', NULL),
(113, 3, 2001235678, '2024-09-01', 'Absent', 'Sick'),
(114, 3, 2001236789, '2024-09-01', 'Present', NULL),
(115, 3, 2001237890, '2024-09-01', 'Present', NULL),
(116, 3, 2001238901, '2024-09-01', 'Absent', 'Holiday'),
(117, 3, 2001240123, '2024-09-01', 'Present', NULL),
(118, 3, 2001241234, '2024-09-01', 'Present', NULL);

--
-- Bẫy `attendances`
--
DELIMITER $$
CREATE TRIGGER `update_attendance_report_after_insert` AFTER INSERT ON `attendances` FOR EACH ROW BEGIN
    -- Insert or update attendance report for the new attendance record
    INSERT INTO attendance_reports (class_id, student_id, total_present, total_absent, total_late)
    VALUES (
        NEW.class_id,
        NEW.student_id,
        (CASE WHEN NEW.status = 'Present' THEN 1 ELSE 0 END),
        (CASE WHEN NEW.status = 'Absent' THEN 1 ELSE 0 END),
        (CASE WHEN NEW.status = 'Late' THEN 1 ELSE 0 END)
    )
    ON DUPLICATE KEY UPDATE
        total_present = total_present + (CASE WHEN NEW.status = 'Present' THEN 1 ELSE 0 END),
        total_absent = total_absent + (CASE WHEN NEW.status = 'Absent' THEN 1 ELSE 0 END),
        total_late = total_late + (CASE WHEN NEW.status = 'Late' THEN 1 ELSE 0 END);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `attendance_reports`
--

CREATE TABLE `attendance_reports` (
  `id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `total_present` int(11) DEFAULT 0,
  `total_absent` int(11) DEFAULT 0,
  `total_late` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `attendance_reports`
--

INSERT INTO `attendance_reports` (`id`, `class_id`, `student_id`, `total_present`, `total_absent`, `total_late`) VALUES
(1, 1, 2001210123, 0, 1, 0),
(2, 1, 2001210224, 0, 1, 0),
(3, 1, 2001211234, 1, 0, 0),
(4, 1, 2001211785, 0, 0, 1),
(5, 1, 2001212345, 1, 0, 0),
(6, 1, 2001212346, 1, 0, 0),
(7, 1, 2001213456, 1, 0, 0),
(8, 1, 2001213457, 0, 1, 0),
(9, 1, 2001214567, 1, 0, 0),
(10, 1, 2001214568, 1, 0, 0),
(11, 1, 2001215678, 0, 1, 0),
(12, 1, 2001215679, 0, 0, 1),
(13, 1, 2001216114, 1, 0, 0),
(14, 1, 2001216780, 1, 0, 0),
(15, 1, 2001216789, 1, 0, 0),
(16, 1, 2001217890, 1, 0, 0),
(17, 1, 2001217891, 0, 1, 0),
(18, 1, 2001218901, 0, 0, 1),
(19, 1, 2001218902, 1, 0, 0),
(20, 1, 2001219012, 1, 0, 0),
(21, 1, 2001219013, 0, 0, 1),
(22, 1, 2001223456, 1, 0, 0),
(23, 1, 2001224567, 1, 0, 0),
(24, 1, 2001225678, 0, 1, 0),
(25, 1, 2001226789, 1, 0, 0),
(26, 1, 2001227890, 0, 0, 1),
(27, 1, 2001228901, 1, 0, 0),
(28, 1, 2001229012, 1, 0, 0),
(29, 1, 2001230123, 0, 1, 0),
(30, 1, 2001231234, 1, 0, 0),
(31, 1, 2001232345, 1, 0, 0),
(32, 1, 2001233456, 0, 0, 1),
(33, 1, 2001234567, 1, 0, 0),
(64, 2, 2001210123, 2, 0, 0),
(65, 2, 2001211234, 2, 0, 0),
(66, 2, 2001212346, 0, 2, 0),
(67, 2, 2001213457, 2, 0, 0),
(68, 2, 2001214567, 2, 0, 0),
(69, 2, 2001214568, 0, 0, 2),
(70, 2, 2001215678, 0, 0, 2),
(71, 2, 2001215679, 2, 0, 0),
(72, 2, 2001216780, 0, 2, 0),
(73, 2, 2001216789, 2, 0, 0),
(74, 2, 2001217890, 0, 2, 0),
(75, 2, 2001217891, 2, 0, 0),
(76, 2, 2001218901, 2, 0, 0),
(77, 2, 2001218902, 2, 0, 0),
(78, 2, 2001219012, 0, 2, 0),
(79, 2, 2001219013, 0, 2, 0),
(80, 2, 2001223456, 2, 0, 0),
(81, 2, 2001224567, 2, 0, 0),
(82, 2, 2001225678, 0, 0, 2),
(83, 2, 2001226789, 2, 0, 0),
(84, 2, 2001227890, 0, 2, 0),
(85, 2, 2001228901, 2, 0, 0),
(86, 2, 2001229012, 2, 0, 0),
(87, 2, 2001230123, 0, 2, 0),
(88, 2, 2001231234, 2, 0, 0),
(89, 2, 2001232345, 2, 0, 0),
(90, 2, 2001233456, 0, 0, 2),
(91, 2, 2001234567, 2, 0, 0),
(95, 3, 2001210123, 0, 0, 1),
(96, 3, 2001211234, 1, 0, 0),
(97, 3, 2001212346, 0, 1, 0),
(98, 3, 2001213457, 1, 0, 0),
(99, 3, 2001214568, 0, 0, 1),
(100, 3, 2001215679, 1, 0, 0),
(101, 3, 2001216780, 0, 1, 0),
(102, 3, 2001217891, 1, 0, 0),
(103, 3, 2001218902, 1, 0, 0),
(104, 3, 2001219012, 1, 0, 0),
(105, 3, 2001219013, 0, 0, 1),
(106, 3, 2001223456, 1, 0, 0),
(107, 3, 2001224567, 1, 0, 0),
(108, 3, 2001225678, 0, 1, 0),
(109, 3, 2001226789, 1, 0, 0),
(110, 3, 2001227890, 0, 0, 1),
(111, 3, 2001228901, 1, 0, 0),
(112, 3, 2001229012, 1, 0, 0),
(113, 3, 2001230123, 0, 1, 0),
(114, 3, 2001231234, 1, 0, 0),
(115, 3, 2001232345, 1, 0, 0),
(116, 3, 2001233456, 0, 0, 1),
(117, 3, 2001234567, 1, 0, 0),
(118, 3, 2001235678, 0, 1, 0),
(119, 3, 2001236789, 1, 0, 0),
(120, 3, 2001237890, 1, 0, 0),
(121, 3, 2001238901, 0, 1, 0),
(122, 3, 2001240123, 1, 0, 0),
(123, 3, 2001241234, 1, 0, 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `classes`
--

CREATE TABLE `classes` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  `semester_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `classes`
--

INSERT INTO `classes` (`id`, `name`, `course_id`, `teacher_id`, `semester_id`) VALUES
(1, 'A103', 32, 1000001234, 9),
(2, 'A204', 33, 1000001234, 9),
(3, 'A405', 34, 1000001238, 9),
(4, 'A201', 35, 1000001238, 9),
(5, 'B402', 36, 1000001238, 9),
(6, 'A402', 37, 1000001234, 9),
(7, 'A307', 38, 1000001234, 9),
(8, 'B504', 39, 1000001236, 9),
(9, 'A203', 40, 1000001235, 9),
(10, 'A102', 41, 1000001237, 9);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `class_students`
--

CREATE TABLE `class_students` (
  `class_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `class_students`
--

INSERT INTO `class_students` (`class_id`, `student_id`) VALUES
(1, 2001210123),
(1, 2001210224),
(1, 2001211234),
(1, 2001211785),
(1, 2001212345),
(1, 2001212346),
(1, 2001213456),
(1, 2001213457),
(1, 2001214567),
(1, 2001214568),
(1, 2001215678),
(1, 2001215679),
(1, 2001216114),
(1, 2001216780),
(1, 2001216789),
(1, 2001217890),
(1, 2001217891),
(1, 2001218901),
(1, 2001218902),
(1, 2001219012),
(1, 2001219013),
(1, 2001223456),
(1, 2001224567),
(1, 2001225678),
(1, 2001226789),
(1, 2001227890),
(1, 2001228901),
(1, 2001229012),
(1, 2001230123),
(1, 2001231234),
(2, 2001210123),
(2, 2001210224),
(2, 2001211234),
(2, 2001211785),
(2, 2001212345),
(2, 2001212346),
(2, 2001213456),
(2, 2001213457),
(2, 2001214567),
(2, 2001214568),
(2, 2001215678),
(2, 2001215679),
(2, 2001216114),
(2, 2001216780),
(2, 2001216789),
(2, 2001217890),
(2, 2001217891),
(2, 2001218901),
(2, 2001218902),
(2, 2001219012),
(2, 2001219013),
(2, 2001223456),
(2, 2001224567),
(2, 2001225678),
(2, 2001226789),
(2, 2001227890),
(2, 2001228901),
(2, 2001229012),
(2, 2001230123),
(2, 2001231234),
(3, 2001210123),
(3, 2001210224),
(3, 2001211234),
(3, 2001211785),
(3, 2001212345),
(3, 2001212346),
(3, 2001213456),
(3, 2001213457),
(3, 2001214567),
(3, 2001214568),
(3, 2001215678),
(3, 2001215679),
(3, 2001216114),
(3, 2001216780),
(3, 2001216789),
(3, 2001217890),
(3, 2001217891),
(3, 2001218901),
(3, 2001218902),
(3, 2001219012),
(3, 2001219013),
(3, 2001223456),
(3, 2001224567),
(3, 2001225678),
(3, 2001226789),
(3, 2001227890),
(3, 2001228901),
(3, 2001229012),
(3, 2001230123),
(3, 2001231234),
(4, 2001210123),
(4, 2001210224),
(4, 2001211234),
(4, 2001211785),
(4, 2001212345),
(4, 2001212346),
(4, 2001213456),
(4, 2001213457),
(4, 2001214567),
(4, 2001214568),
(4, 2001215678),
(4, 2001215679),
(4, 2001216114),
(4, 2001216780),
(4, 2001216789),
(4, 2001217890),
(4, 2001217891),
(4, 2001218901),
(4, 2001218902),
(4, 2001219012),
(4, 2001219013),
(4, 2001223456),
(4, 2001224567),
(4, 2001225678),
(4, 2001226789),
(4, 2001227890),
(4, 2001228901),
(4, 2001229012),
(4, 2001230123),
(4, 2001231234),
(5, 2001210123),
(5, 2001210224),
(5, 2001211234),
(5, 2001211785),
(5, 2001212345),
(5, 2001212346),
(5, 2001213456),
(5, 2001213457),
(5, 2001214567),
(5, 2001214568),
(5, 2001215678),
(5, 2001215679),
(5, 2001216114),
(5, 2001216780),
(5, 2001216789),
(5, 2001217890),
(5, 2001217891),
(5, 2001218901),
(5, 2001218902),
(5, 2001219012),
(5, 2001219013),
(5, 2001223456),
(5, 2001224567),
(5, 2001225678),
(5, 2001226789),
(5, 2001227890),
(5, 2001228901),
(5, 2001229012),
(5, 2001230123),
(5, 2001231234);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `credits` int(11) NOT NULL,
  `theory_hours` int(11) NOT NULL,
  `practice_hours` int(11) NOT NULL,
  `term_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `courses`
--

INSERT INTO `courses` (`id`, `name`, `credits`, `theory_hours`, `practice_hours`, `term_id`) VALUES
(1, 'Nhập môn lập trình', 3, 45, 0, 1),
(2, 'Thực hành nhập môn lập trình', 2, 0, 60, 1),
(3, 'Kỹ năng ứng dụng Công nghệ Thông tin', 2, 0, 75, 1),
(4, 'Giải tích', 3, 45, 0, 1),
(5, 'Hệ điều hành', 3, 45, 0, 2),
(6, 'Thực hành Hệ điều hành', 1, 0, 30, 2),
(7, 'Kiến trúc máy tính', 3, 45, 0, 2),
(8, 'Kỹ thuật lập trình', 2, 30, 0, 2),
(9, 'Thực hành kỹ thuật lập trình', 1, 0, 30, 2),
(10, 'Đại số tuyến tính', 2, 30, 0, 2),
(11, 'Anh Văn 1', 3, 45, 0, 2),
(12, 'Anh Văn 2', 3, 45, 0, 2),
(13, 'Cấu trúc dữ liệu và giải thuật', 3, 45, 0, 3),
(14, 'Mạng máy tính', 3, 45, 0, 3),
(15, 'Thực hành cấu trúc dữ liệu và giải thuật', 1, 0, 60, 3),
(16, 'Thực hành mạng máy tính', 1, 0, 30, 3),
(17, 'Cấu trúc rời rạc', 3, 45, 0, 3),
(18, 'Thực hành Cấu trúc rời rạc', 1, 0, 30, 3),
(19, 'Phương pháp nghiên cứu khoa học', 2, 30, 0, 3),
(20, 'Phân tích thiết kế thuật toán', 2, 30, 0, 3),
(21, 'Thiết kế web', 3, 15, 60, 4),
(22, 'Lập trình hướng đối tượng', 3, 45, 0, 4),
(23, 'Thực hành lập trình hướng đối tượng', 1, 0, 30, 4),
(24, 'Cơ sở dữ liệu', 3, 45, 0, 4),
(25, 'Thực hành cơ sở dữ liệu', 1, 0, 30, 4),
(26, 'Anh văn 3', 3, 45, 0, 4),
(27, 'Hệ quản trị cơ sở dữ liệu', 3, 45, 0, 5),
(28, 'Thực hành hệ quản trị cơ sở dữ liệu', 1, 0, 30, 5),
(29, 'Lập trình Web', 3, 15, 60, 5),
(30, 'Trí tuệ nhân tạo', 3, 45, 0, 5),
(31, 'Thực hành trí tuệ nhân tạo', 1, 0, 30, 5),
(32, 'Công Nghệ Java', 3, 15, 60, 6),
(33, 'Phân tích thiết kế hệ thống thông tin', 3, 45, 0, 6),
(34, 'Thực hành phân tích thiết kế hệ thống thông tin', 1, 0, 30, 6),
(35, 'Lập trình mã nguồn mở', 3, 15, 60, 6),
(36, 'Phát triển ứng dụng di động', 3, 15, 60, 6),
(37, 'Ảo hóa và điện toán đám mây', 2, 30, 0, 6),
(38, 'Công nghệ phần mềm nâng cao', 3, 45, 0, 7),
(39, 'Kiểm định phần mềm', 2, 30, 0, 7),
(40, 'Thực hành kiểm định phần mềm', 1, 0, 30, 7),
(41, 'Phát triển phần mềm ứng dụng thông minh', 3, 15, 60, 7);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `schedules`
--

CREATE TABLE `schedules` (
  `id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `day_of_week` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `schedules`
--

INSERT INTO `schedules` (`id`, `class_id`, `day_of_week`, `start_time`, `end_time`) VALUES
(1, 1, 'Monday', '07:00:00', '09:15:00'),
(2, 2, 'Tuesday', '09:40:00', '11:55:00'),
(3, 3, 'Wednesday', '07:00:00', '10:45:00'),
(4, 4, 'Thursday', '12:30:00', '14:45:00'),
(5, 5, 'Friday', '15:10:00', '17:25:00'),
(6, 6, 'Saturday', '12:30:00', '16:15:00'),
(7, 7, 'Sunday', '18:00:00', '20:15:00'),
(8, 8, 'Monday', '18:00:00', '21:00:00'),
(9, 9, 'Tuesday', '07:00:00', '09:15:00'),
(10, 10, 'Wednesday', '09:40:00', '11:55:00');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `semesters`
--

CREATE TABLE `semesters` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `is_active` int(2) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `semesters`
--

INSERT INTO `semesters` (`id`, `name`, `is_active`, `start_date`, `end_date`) VALUES
(1, 'HK1 (2021 - 2022)', 0, '2021-03-10', '2022-01-18'),
(2, 'HK2 (2021 - 2022)', 0, '2022-03-04', '2022-05-11'),
(3, 'HK1 (2022 - 2023)', 0, '2022-09-10', '2022-12-30'),
(4, 'HK2 (2022 - 2023)', 0, '2026-02-08', '2023-06-05'),
(5, 'HK3 (Hè 2022 - 2023)', 0, '2023-07-03', '2023-07-14'),
(6, 'HK1 (2023 - 2024)', 0, '2023-10-30', '2023-12-15'),
(7, 'HK2 (2023 - 2024)', 0, '2024-01-17', '2025-04-26'),
(8, 'HK3 (Hè 2023 - 2024)', 0, '2024-07-10', '2024-08-07'),
(9, 'HK1 (2024 - 2025)', 1, '2024-08-15', '2025-12-17');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `lastname` varchar(50) DEFAULT NULL,
  `firstname` varchar(50) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `class` varchar(50) DEFAULT NULL,
  `birthday` date NOT NULL,
  `gender` enum('Nam','Nữ','Khác') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `students`
--

INSERT INTO `students` (`id`, `lastname`, `firstname`, `email`, `phone`, `class`, `birthday`, `gender`) VALUES
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
(2001219013, 'Lương Văn', 'Tâm', 'luongvantam@gmail.com', '0921234567', '14DHTH20', '2004-01-01', 'Nam'),
(2001223456, 'Nguyễn Văn', 'Duy', 'nguyenduy@gmail.com', '0912345678', '12DHTH01', '2003-02-20', 'Nam'),
(2001224567, 'Lê Thị', 'Mai', 'lethimai@gmail.com', '0913456789', '12DHTH02', '2003-03-25', 'Nữ'),
(2001225678, 'Trần Văn', 'Khải', 'tranvankhai@gmail.com', '0914567890', '12DHTH03', '2003-04-15', 'Nam'),
(2001226789, 'Nguyễn Thị', 'Thu', 'nguyenhuu@gmail.com', '0915678901', '12DHTH04', '2003-05-22', 'Nữ'),
(2001227890, 'Bùi Văn', 'Thắng', 'buivanthang@gmail.com', '0916789012', '12DHTH05', '2003-06-30', 'Nam'),
(2001228901, 'Phạm Thị', 'Như', 'phamthinh@gmail.com', '0917890123', '12DHTH06', '2003-07-10', 'Nữ'),
(2001229012, 'Lương Thị', 'Vân', 'luongthivan@gmail.com', '0920123456', '12DHTH07', '2003-08-18', 'Nữ'),
(2001230123, 'Trần Văn', 'Đạt', 'tranvandat@gmail.com', '0921234567', '12DHTH08', '2003-09-25', 'Nam'),
(2001231234, 'Nguyễn Văn', 'Sơn', 'nguyenvanson@gmail.com', '0922345678', '12DHTH09', '2003-10-05', 'Nam'),
(2001232345, 'Lê Thị', 'Lan', 'lethilan@gmail.com', '0923456789', '12DHTH10', '2003-11-12', 'Nữ'),
(2001233456, 'Bùi Văn', 'Hoàng', 'buivanhoang@gmail.com', '0924567890', '12DHTH11', '2003-12-20', 'Nam'),
(2001234567, 'Trần Thị', 'Thu', 'tranthithu@gmail.com', '0925678901', '13DHTH01', '2004-01-30', 'Nữ'),
(2001235678, 'Nguyễn Thị', 'Hoa', 'nguyenthihua@gmail.com', '0926789012', '13DHTH02', '2004-02-15', 'Nữ'),
(2001236789, 'Lương Văn', 'Hùng', 'luongvanhung@gmail.com', '0927890123', '13DHTH03', '2004-03-22', 'Nam'),
(2001237890, 'Bùi Thị', 'Ngân', 'buithingan@gmail.com', '0928901234', '13DHTH04', '2004-04-28', 'Nữ'),
(2001238901, 'Nguyễn Văn', 'Hòa', 'nguyenvanhoag@gmail.com', '0930123456', '13DHTH05', '2004-05-30', 'Nam'),
(2001239012, 'Trần Thị', 'Vân', 'tranthivang@gmail.com', '0931234567', '13DHTH06', '2004-06-10', 'Nữ'),
(2001240123, 'Lê Văn', 'Cường', 'levancuong@gmail.com', '0932345678', '13DHTH07', '2004-07-15', 'Nam'),
(2001241234, 'Nguyễn Thị', 'Thúy', 'nguyenthithuy@gmail.com', '0933456789', '13DHTH08', '2004-08-20', 'Nữ'),
(2001242345, 'Bùi Văn', 'Tùng', 'buivantung@gmail.com', '0934567890', '13DHTH09', '2004-09-30', 'Nam'),
(2001243456, 'Lương Thị', 'Tâm', 'luongthitam@gmail.com', '0935678901', '13DHTH10', '2004-10-10', 'Nữ'),
(2001244567, 'Nguyễn Văn', 'Long', 'nguyenvanlong@gmail.com', '0936789012', '13DHTH11', '2004-11-15', 'Nam'),
(2001245678, 'Trần Thị', 'Duyên', 'tranthiduyen@gmail.com', '0937890123', '14DHTH01', '2005-01-20', 'Nữ'),
(2001246789, 'Lê Thị', 'Như', 'lethinh@gmail.com', '0938901234', '14DHTH02', '2005-02-25', 'Nữ'),
(2001247890, 'Bùi Văn', 'Hòa', 'buivanhola@gmail.com', '0939012345', '14DHTH03', '2005-03-15', 'Nam'),
(2001248901, 'Nguyễn Thị', 'Hương', 'nguyenthihuong@gmail.com', '0940123456', '14DHTH04', '2005-04-20', 'Nữ'),
(2001249012, 'Lương Văn', 'Khải', 'luongvankhai@gmail.com', '0941234567', '14DHTH05', '2005-05-10', 'Nam'),
(2001250123, 'Bùi Thị', 'Thu', 'buithithu@gmail.com', '0942345678', '14DHTH06', '2005-06-30', 'Nữ'),
(2001251234, 'Nguyễn Văn', 'Vũ', 'nguyenvanvu@gmail.com', '0943456789', '14DHTH07', '2005-07-25', 'Nam'),
(2001252345, 'Lê Thị', 'Hạnh', 'lethihanh@gmail.com', '0944567890', '14DHTH08', '2005-08-15', 'Nữ'),
(2001253456, 'Trần Văn', 'Thịnh', 'tranvanthinh@gmail.com', '0945678901', '14DHTH09', '2005-09-10', 'Nam'),
(2001254567, 'Bùi Văn', 'Dũng', 'buivandung@gmail.com', '0946789012', '14DHTH10', '2005-10-25', 'Nam');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `teachers`
--

CREATE TABLE `teachers` (
  `id` int(11) NOT NULL,
  `lastname` varchar(50) DEFAULT NULL,
  `firstname` varchar(50) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `birthday` date NOT NULL,
  `gender` enum('Nam','Nữ','Other') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `teachers`
--

INSERT INTO `teachers` (`id`, `lastname`, `firstname`, `email`, `phone`, `birthday`, `gender`) VALUES
(1000001234, 'Trần Thị Vân', 'Anh', 'vanAnh123@example.com', '0903456789', '1995-01-01', 'Nữ'),
(1000001235, 'Trần Văn', 'Hùng', 'HungTV@example.com', '0903456790', '1990-01-01', 'Nam'),
(1000001236, 'Nguyễn Văn', 'Tùng', 'NguyenVT@example.com', '0904567890', '1992-01-01', 'Nam'),
(1000001237, 'Nguyễn Thị', 'Mai', 'nguyenmai123@example.com', '0905678901', '1985-02-14', 'Nữ'),
(1000001238, 'Lê Văn Sĩ', 'Bình', 'lebinh456@example.com', '0906789012', '1988-03-21', 'Nam'),
(1000001239, 'Phạm Thị Thu', 'Hương', 'phamhuong789@example.com', '0907890123', '1990-04-15', 'Nữ'),
(1000001240, 'Đặng Văn', 'Cường', 'dangcuong101@example.com', '0908901234', '1987-05-30', 'Nam'),
(1000001241, 'Bùi Thị', 'Linh', 'builinh202@example.com', '0909012345', '1991-06-25', 'Nữ'),
(1000001242, 'Trương Văn', 'Hải', 'truonghai303@example.com', '0910123456', '1986-07-10', 'Nam'),
(1000001243, 'Vũ Thị', 'Lan', 'vulan404@example.com', '0911234567', '1989-08-22', 'Nữ'),
(1000001244, 'Lê Thị Quỳnh', 'Mai', 'lemai505@example.com', '0912345678', '1993-09-18', 'Nữ'),
(1000001245, 'Nguyễn Hữu', 'Tâm', 'nguyentam606@example.com', '0913456789', '1994-10-12', 'Nam'),
(1000001246, 'Hoàng Thị', 'Ngọc', 'hoangngoc707@example.com', '0914567890', '1984-11-05', 'Nữ');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `terms`
--

CREATE TABLE `terms` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `terms`
--

INSERT INTO `terms` (`id`, `name`) VALUES
(1, 'Học kỳ 1'),
(2, 'Học kỳ 2'),
(3, 'Học kỳ 3'),
(4, 'Học kỳ 4'),
(5, 'Học kỳ 5'),
(6, 'Học kỳ 6'),
(7, 'Học kỳ 7'),
(8, 'Học kỳ 8');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('student','teacher','admin') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `created_at`, `updated_at`) VALUES
(1000001234, 'vanAnh123', 'password123', 'teacher', '2024-09-16 16:41:43', '2024-09-16 16:41:43'),
(1000001235, 'HungTV', 'password456', 'teacher', '2024-09-16 16:41:43', '2024-09-16 16:41:43'),
(1000001236, 'NguyenVT', 'password789', 'teacher', '2024-09-16 16:41:43', '2024-09-16 16:41:43'),
(1000001237, 'nguyenMai123', 'password101', 'teacher', '2024-09-16 16:41:43', '2024-09-16 16:41:43'),
(1000001238, 'leBinh456', 'password202', 'teacher', '2024-09-16 16:41:43', '2024-09-16 16:41:43'),
(1000001239, 'phamHuong789', 'password303', 'teacher', '2024-09-16 16:41:43', '2024-09-16 16:41:43'),
(1000001240, 'dangCuong101', 'password404', 'teacher', '2024-09-16 16:41:43', '2024-09-16 16:41:43'),
(1000001241, 'buiLinh202', 'password505', 'teacher', '2024-09-16 16:41:43', '2024-09-16 16:41:43'),
(1000001242, 'truongHai303', 'password606', 'teacher', '2024-09-16 16:41:43', '2024-09-16 16:41:43'),
(1000001243, 'vulan404', 'password707', 'teacher', '2024-09-16 16:41:43', '2024-09-16 16:41:43'),
(1000001244, 'leMai505', 'password808', 'teacher', '2024-09-16 16:41:43', '2024-09-16 16:41:43'),
(1000001245, 'nguyenTam606', 'password909', 'teacher', '2024-09-16 16:41:43', '2024-09-16 16:41:43'),
(1000001246, 'hoangNgoc707', 'password010', 'teacher', '2024-09-16 16:41:43', '2024-09-16 16:41:43'),
(2001210123, '2001210123', 'password909', 'student', '2024-09-16 16:41:43', '2024-09-16 16:41:43'),
(2001210224, '2001210224', 'password', 'student', '2024-09-16 16:41:43', '2024-09-16 16:41:43'),
(2001211234, '2001211234', 'password010', 'student', '2024-09-16 16:41:43', '2024-09-16 16:41:43'),
(2001211785, '2001211785', 'password', 'student', '2024-09-16 16:41:43', '2024-09-16 16:41:43'),
(2001212345, '2001212345', 'password101', 'student', '2024-09-16 16:41:43', '2024-09-16 16:41:43'),
(2001212346, '2001212346', 'password121', 'student', '2024-09-16 16:41:43', '2024-09-16 16:41:43'),
(2001213456, '2001213456', 'password202', 'student', '2024-09-16 16:41:43', '2024-09-16 16:41:43'),
(2001213457, '2001213457', 'password232', 'student', '2024-09-16 16:41:43', '2024-09-16 16:41:43'),
(2001214567, '2001214567', 'password303', 'student', '2024-09-16 16:41:43', '2024-09-16 16:41:43'),
(2001214568, '2001214568', 'password343', 'student', '2024-09-16 16:41:43', '2024-09-16 16:41:43'),
(2001215678, '2001215678', 'password404', 'student', '2024-09-16 16:41:43', '2024-09-16 16:41:43'),
(2001215679, '2001215679', 'password454', 'student', '2024-09-16 16:41:43', '2024-09-16 16:41:43'),
(2001216114, '2001216114', 'password', 'student', '2024-09-16 16:41:43', '2024-09-16 16:41:43'),
(2001216780, '2001216780', 'password565', 'student', '2024-09-16 16:41:43', '2024-09-16 16:41:43'),
(2001216789, '2001216789', 'password505', 'student', '2024-09-16 16:41:43', '2024-09-16 16:41:43'),
(2001217890, '2001217890', 'password606', 'student', '2024-09-16 16:41:43', '2024-09-16 16:41:43'),
(2001217891, '2001217891', 'password676', 'student', '2024-09-16 16:41:43', '2024-09-16 16:41:43'),
(2001218901, '2001218901', 'password707', 'student', '2024-09-16 16:41:43', '2024-09-16 16:41:43'),
(2001218902, '2001218902', 'password787', 'student', '2024-09-16 16:41:43', '2024-09-16 16:41:43'),
(2001219012, '2001219012', 'password808', 'student', '2024-09-16 16:41:43', '2024-09-16 16:41:43'),
(2001219013, '2001219013', 'password898', 'student', '2024-09-16 16:41:43', '2024-09-16 16:41:43'),
(2001223456, '2001223456', 'password909', 'student', '2024-09-16 16:41:43', '2024-09-16 16:41:43'),
(2001224567, '2001224567', 'password010', 'student', '2024-09-16 16:41:43', '2024-09-16 16:41:43'),
(2001225678, '2001225678', 'password121', 'student', '2024-09-16 16:41:43', '2024-09-16 16:41:43'),
(2001226789, '2001226789', 'password232', 'student', '2024-09-16 16:41:43', '2024-09-16 16:41:43'),
(2001227890, '2001227890', 'password343', 'student', '2024-09-16 16:41:43', '2024-09-16 16:41:43'),
(2001228901, '2001228901', 'password454', 'student', '2024-09-16 16:41:43', '2024-09-16 16:41:43'),
(2001229012, '2001229012', 'password565', 'student', '2024-09-16 16:41:43', '2024-09-16 16:41:43'),
(2001230123, '2001230123', 'password676', 'student', '2024-09-16 16:41:43', '2024-09-16 16:41:43'),
(2001231234, '2001231234', 'password787', 'student', '2024-09-16 16:41:43', '2024-09-16 16:41:43'),
(2001232345, '2001232345', 'password898', 'student', '2024-09-16 16:41:43', '2024-09-16 16:41:43'),
(2001233456, '2001233456', 'password909', 'student', '2024-09-16 16:41:43', '2024-09-16 16:41:43'),
(2001234567, '2001234567', 'password010', 'student', '2024-09-16 16:41:43', '2024-09-16 16:41:43'),
(2001235678, '2001235678', 'password121', 'student', '2024-09-16 16:41:43', '2024-09-16 16:41:43'),
(2001236789, '2001236789', 'password232', 'student', '2024-09-16 16:41:43', '2024-09-16 16:41:43'),
(2001237890, '2001237890', 'password343', 'student', '2024-09-16 16:41:43', '2024-09-16 16:41:43'),
(2001238901, '2001238901', 'password454', 'student', '2024-09-16 16:41:43', '2024-09-16 16:41:43'),
(2001239012, '2001239012', 'password565', 'student', '2024-09-16 16:41:43', '2024-09-16 16:41:43'),
(2001240123, '2001240123', 'password676', 'student', '2024-09-16 16:41:43', '2024-09-16 16:41:43'),
(2001241234, '2001241234', 'password787', 'student', '2024-09-16 16:41:43', '2024-09-16 16:41:43'),
(2001242345, '2001242345', 'password898', 'student', '2024-09-16 16:41:43', '2024-09-16 16:41:43'),
(2001243456, '2001243456', 'password909', 'student', '2024-09-16 16:41:43', '2024-09-16 16:41:43'),
(2001244567, '2001244567', 'password010', 'student', '2024-09-16 16:41:43', '2024-09-16 16:41:43'),
(2001245678, '2001245678', 'password121', 'student', '2024-09-16 16:41:43', '2024-09-16 16:41:43'),
(2001246789, '2001246789', 'password232', 'student', '2024-09-16 16:41:43', '2024-09-16 16:41:43'),
(2001247890, '2001247890', 'password343', 'student', '2024-09-16 16:41:43', '2024-09-16 16:41:43'),
(2001248901, '2001248901', 'password454', 'student', '2024-09-16 16:41:43', '2024-09-16 16:41:43'),
(2001249012, '2001249012', 'password565', 'student', '2024-09-16 16:41:43', '2024-09-16 16:41:43'),
(2001250123, '2001250123', 'password676', 'student', '2024-09-16 16:41:43', '2024-09-16 16:41:43'),
(2001251234, '2001251234', 'password787', 'student', '2024-09-16 16:41:43', '2024-09-16 16:41:43'),
(2001252345, '2001252345', 'password898', 'student', '2024-09-16 16:41:43', '2024-09-16 16:41:43'),
(2001253456, '2001253456', 'password909', 'student', '2024-09-16 16:41:43', '2024-09-16 16:41:43'),
(2001254567, '2001254567', 'password010', 'student', '2024-09-16 16:41:43', '2024-09-16 16:41:43');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `attendances`
--
ALTER TABLE `attendances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_class_id` (`class_id`),
  ADD KEY `fk_student_id` (`student_id`);

--
-- Chỉ mục cho bảng `attendance_reports`
--
ALTER TABLE `attendance_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_attendance_report_class_id` (`class_id`),
  ADD KEY `fk_attendance_report_student_id` (`student_id`);

--
-- Chỉ mục cho bảng `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`),
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
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_term_id` (`term_id`);

--
-- Chỉ mục cho bảng `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_schedule_class_id` (`class_id`);

--
-- Chỉ mục cho bảng `semesters`
--
ALTER TABLE `semesters`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `terms`
--
ALTER TABLE `terms`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `attendances`
--
ALTER TABLE `attendances`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=119;

--
-- AUTO_INCREMENT cho bảng `attendance_reports`
--
ALTER TABLE `attendance_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=126;

--
-- AUTO_INCREMENT cho bảng `classes`
--
ALTER TABLE `classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT cho bảng `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `semesters`
--
ALTER TABLE `semesters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT cho bảng `terms`
--
ALTER TABLE `terms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `attendances`
--
ALTER TABLE `attendances`
  ADD CONSTRAINT `fk_class_id` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`),
  ADD CONSTRAINT `fk_student_id` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`);

--
-- Các ràng buộc cho bảng `attendance_reports`
--
ALTER TABLE `attendance_reports`
  ADD CONSTRAINT `fk_attendance_report_class_id` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`),
  ADD CONSTRAINT `fk_attendance_report_student_id` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`);

--
-- Các ràng buộc cho bảng `classes`
--
ALTER TABLE `classes`
  ADD CONSTRAINT `fk_course_id` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`),
  ADD CONSTRAINT `fk_semester_id` FOREIGN KEY (`semester_id`) REFERENCES `semesters` (`id`),
  ADD CONSTRAINT `fk_teacher_id` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`);

--
-- Các ràng buộc cho bảng `class_students`
--
ALTER TABLE `class_students`
  ADD CONSTRAINT `fk_class_student_class_id` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`),
  ADD CONSTRAINT `fk_class_student_student_id` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`);

--
-- Các ràng buộc cho bảng `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `fk_term_id` FOREIGN KEY (`term_id`) REFERENCES `terms` (`id`);

--
-- Các ràng buộc cho bảng `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `fk_schedule_class_id` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`);

--
-- Các ràng buộc cho bảng `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `teachers`
--
ALTER TABLE `teachers`
  ADD CONSTRAINT `teachers_ibfk_1` FOREIGN KEY (`id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
