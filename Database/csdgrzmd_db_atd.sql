-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th10 05, 2024 lúc 02:29 PM
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
CREATE DEFINER=`root`@`localhost` PROCEDURE `AddSchedules` (IN `p_class_id` CHAR(36), IN `p_dates` TEXT, IN `p_start_times` TEXT, IN `p_end_times` TEXT)   BEGIN
    DECLARE v_date VARCHAR(10);
    DECLARE v_start_time TIME;
    DECLARE v_end_time TIME;
    DECLARE done INT DEFAULT FALSE;

    DECLARE i INT DEFAULT 0;
    DECLARE n INT DEFAULT 0;

    -- Đếm số phần tử trong các chuỗi JSON
    SET n = JSON_LENGTH(p_dates);

    read_loop: LOOP
        -- Lấy giá trị từ JSON
        SET v_date = JSON_UNQUOTE(JSON_EXTRACT(p_dates, CONCAT('$[', i, ']')));
        SET v_start_time = JSON_UNQUOTE(JSON_EXTRACT(p_start_times, CONCAT('$[', i, ']')));
        SET v_end_time = JSON_UNQUOTE(JSON_EXTRACT(p_end_times, CONCAT('$[', i, ']')));

        -- Nếu i >= n, kết thúc vòng lặp
        IF i >= n THEN
            LEAVE read_loop;
        END IF;

        -- Thêm lịch học vào bảng schedules
        INSERT INTO schedules (class_id, date, start_time, end_time) VALUES (p_class_id, v_date, v_start_time, v_end_time);

        SET i = i + 1; -- Tăng chỉ số
    END LOOP;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetAllSemesters` ()   BEGIN
    SELECT semester_id, semester_name 
    FROM semesters
    ORDER BY semester_id DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetAttendanceByScheduleId` (IN `scheduleId` INT, IN `class_id` CHAR(36))   BEGIN
    SELECT a.student_id, a.status
    FROM attendances a
    JOIN schedules s ON a.schedule_id = s.schedule_id
    WHERE a.schedule_id = scheduleId AND s.class_id = class_id;
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetClassesBySemesterAndStudent` (IN `semester_id` INT, IN `student_id` INT)   BEGIN
    SELECT 
        c.class_id, 
        c.class_name,
        co.course_name,
        t.lastname,
        t.firstname
    FROM classes c
    JOIN class_students cs ON c.class_id = cs.class_id
    JOIN courses co ON c.course_id = co.course_id
    JOIN teachers t ON c.teacher_id = t.teacher_id
    WHERE c.semester_id = semester_id AND cs.student_id = student_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetClassesBySemesterAndTeacher` (IN `semester_id` INT, IN `teacher_id` INT)   BEGIN
    SELECT 
        c.class_id, 
        c.class_name,
        co.course_name
    FROM classes c
    JOIN courses co ON c.course_id = co.course_id
    WHERE c.semester_id = semester_id AND c.teacher_id = teacher_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetCoursePeriodsByClassId` (IN `p_class_id` CHAR(36))   BEGIN
    SELECT ct.theory_periods, ct.practice_periods
    FROM classes c
    JOIN courses co ON c.course_id = co.course_id
    JOIN course_types ct ON co.course_type_id = ct.course_type_id
    WHERE c.class_id = p_class_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetDistinctDatesByClassId` (IN `classId` CHAR(8))   BEGIN
    SELECT schedule_id, date
    FROM schedules
    WHERE class_id = classId;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetSchedulesAndAttendanceByClassId` (IN `classId` CHAR(36))   BEGIN
    SELECT 
        sch.schedule_id,
        sch.date, 
        a.student_id, 
        a.status 
    FROM schedules sch
    LEFT JOIN attendances a 
        ON sch.schedule_id = a.schedule_id 
        AND a.student_id IN (SELECT student_id FROM class_students WHERE class_id = classId)
    WHERE sch.class_id = classId;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetStudentsByClassId` (IN `classId` CHAR(36))   BEGIN
    SELECT s.student_id, s.lastname, s.firstname, s.class, s.birthday, s.gender
    FROM students s
    JOIN class_students cs ON s.student_id = cs.student_id
    WHERE cs.class_id = classId;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetStudentsByClassIdAndStudentId` (IN `classId` CHAR(36), IN `studentId` INT)   BEGIN
    SELECT s.*
    FROM students s
    JOIN class_students cs ON s.student_id = cs.student_id
    WHERE cs.class_id = classId AND s.student_id = studentId;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetStudentSchedules` (IN `startDate` DATE, IN `endDate` DATE, IN `semesterId` INT, IN `student_id` INT)   BEGIN
    SELECT 
        c.class_name,
        co.course_name,
        s.date,
        s.start_time,
        s.end_time,
        CASE 
            WHEN s.end_time < 7 THEN 'Sáng'
            WHEN s.end_time >= 7 AND s.end_time < 13 THEN 'Chiều'
            ELSE 'Tối'
        END AS ca_hoc 
    FROM 
        schedules s
    JOIN 
        classes c ON s.class_id = c.class_id
    JOIN 
        courses co ON c.course_id = co.course_id
    JOIN 
        class_students cs ON c.class_id = cs.class_id
    WHERE 
        s.date BETWEEN startDate AND endDate
        AND c.semester_id = semesterId
        AND cs.student_id = student_id
    ORDER BY 
        s.date, c.class_name;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetTeacherInfo` (IN `teacher_id_param` INT)   BEGIN
    SELECT lastname, firstname 
    FROM teachers 
    WHERE teacher_id = teacher_id_param;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetTeacherSchedules` (IN `startDate` DATE, IN `endDate` DATE, IN `semesterId` INT, IN `teacher_id` INT)   BEGIN
    SELECT 
        c.class_name,
        co.course_name,
        s.date,
        s.start_time,
        s.end_time,
        CASE 
            WHEN s.end_time < 7 THEN 'Sáng'
            WHEN s.end_time >= 7 AND s.end_time < 13 THEN 'Chiều'
            ELSE 'Tối'
        END AS ca_hoc 
    FROM 
        schedules s
    JOIN 
        classes c ON s.class_id = c.class_id
    JOIN 
        courses co ON c.course_id = co.course_id
    WHERE 
        s.date BETWEEN startDate AND endDate
        AND c.semester_id = semesterId
        AND c.teacher_id = teacher_id -- Thêm điều kiện lọc theo teacher_id
    ORDER BY 
        s.date, c.class_name;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetUserInfoByUsername` (IN `input_username` VARCHAR(255))   BEGIN
    SELECT u.user_id, u.username, u.password, r.role_name 
    FROM users u
    JOIN user_roles ur ON u.user_id = ur.user_id
    JOIN roles r ON ur.role_id = r.role_id
    WHERE u.username = input_username;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateOrInsertAttendance` (IN `p_schedule_id` INT, IN `p_student_id` INT, IN `p_status` INT)   BEGIN
    DECLARE attendanceExists INT;
    
    -- Kiểm tra xem điểm danh đã tồn tại hay chưa
    SELECT COUNT(*) INTO attendanceExists
    FROM attendances
    WHERE schedule_id = p_schedule_id AND student_id = p_student_id;
    
    IF attendanceExists > 0 THEN
        -- Nếu đã tồn tại, cập nhật trạng thái
        UPDATE attendances
        SET status = p_status
        WHERE schedule_id = p_schedule_id AND student_id = p_student_id;
    ELSE
        -- Nếu chưa tồn tại, thêm mới
        INSERT INTO attendances (schedule_id, student_id, status)
        VALUES (p_schedule_id, p_student_id, p_status);
    END IF;
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
(1, 16, 2001216114, 1, '2024-11-05 11:09:42'),
(2, 17, 2001216114, 1, '2024-11-05 11:36:34'),
(3, 18, 2001216114, 0, '2024-11-05 11:09:42'),
(4, 19, 2001216114, 0, '2024-11-05 11:09:42'),
(5, 20, 2001216114, 0, '2024-11-05 11:09:42'),
(6, 21, 2001216114, 0, '2024-11-05 11:09:42'),
(7, 22, 2001216114, 0, '2024-11-05 11:09:42'),
(8, 23, 2001216114, 0, '2024-11-05 11:09:42'),
(9, 24, 2001216114, 0, '2024-11-05 11:09:42'),
(10, 25, 2001216114, 0, '2024-11-05 11:09:43'),
(11, 26, 2001216114, 0, '2024-11-05 11:09:43'),
(12, 27, 2001216114, 0, '2024-11-05 11:09:43'),
(13, 28, 2001216114, 0, '2024-11-05 11:09:43'),
(14, 29, 2001216114, 0, '2024-11-05 11:09:43'),
(15, 30, 2001216114, 0, '2024-11-05 11:09:43'),
(16, 31, 2001216114, 0, '2024-11-05 11:09:43'),
(17, 32, 2001216114, 0, '2024-11-05 11:09:43'),
(18, 33, 2001216114, 0, '2024-11-05 11:09:43'),
(19, 34, 2001216114, 0, '2024-11-05 11:09:43'),
(20, 35, 2001216114, 0, '2024-11-05 11:09:43'),
(21, 36, 2001216114, 0, '2024-11-05 11:09:43'),
(22, 37, 2001216114, 0, '2024-11-05 11:09:43'),
(23, 38, 2001216114, 0, '2024-11-05 11:09:43'),
(24, 39, 2001216114, 0, '2024-11-05 11:09:43'),
(25, 40, 2001216114, 0, '2024-11-05 11:09:43'),
(26, 41, 2001216114, 0, '2024-11-05 11:09:43'),
(27, 42, 2001216114, 0, '2024-11-05 11:09:43'),
(28, 43, 2001216114, 0, '2024-11-05 11:09:43'),
(29, 44, 2001216114, 0, '2024-11-05 11:09:43'),
(30, 45, 2001216114, 0, '2024-11-05 11:09:43'),
(31, 16, 2001210224, 1, '2024-11-05 12:57:05'),
(32, 17, 2001210224, 1, '2024-11-05 12:57:05'),
(33, 18, 2001210224, 0, '2024-11-05 12:57:05'),
(34, 19, 2001210224, 0, '2024-11-05 12:57:05'),
(35, 20, 2001210224, 0, '2024-11-05 12:57:05'),
(36, 21, 2001210224, 0, '2024-11-05 12:57:05'),
(37, 22, 2001210224, 0, '2024-11-05 12:57:05'),
(38, 23, 2001210224, 0, '2024-11-05 12:57:05'),
(39, 24, 2001210224, 0, '2024-11-05 12:57:05'),
(40, 25, 2001210224, 0, '2024-11-05 12:57:05'),
(41, 26, 2001210224, 0, '2024-11-05 12:57:05'),
(42, 27, 2001210224, 0, '2024-11-05 12:57:05'),
(43, 28, 2001210224, 0, '2024-11-05 12:57:05'),
(44, 29, 2001210224, 0, '2024-11-05 12:57:05'),
(45, 30, 2001210224, 0, '2024-11-05 12:57:05'),
(46, 16, 2001214567, 0, '2024-11-05 12:57:05'),
(47, 17, 2001214567, 0, '2024-11-05 12:57:05'),
(48, 18, 2001214567, 0, '2024-11-05 12:57:05'),
(49, 19, 2001214567, 0, '2024-11-05 12:57:05'),
(50, 20, 2001214567, 0, '2024-11-05 12:57:05'),
(51, 21, 2001214567, 0, '2024-11-05 12:57:05'),
(52, 22, 2001214567, 0, '2024-11-05 12:57:06'),
(53, 23, 2001214567, 0, '2024-11-05 12:57:06'),
(54, 24, 2001214567, 0, '2024-11-05 12:57:06'),
(55, 25, 2001214567, 0, '2024-11-05 12:57:06'),
(56, 26, 2001214567, 0, '2024-11-05 12:57:06'),
(57, 27, 2001214567, 0, '2024-11-05 12:57:06'),
(58, 28, 2001214567, 0, '2024-11-05 12:57:06'),
(59, 29, 2001214567, 0, '2024-11-05 12:57:06'),
(60, 30, 2001214567, 0, '2024-11-05 12:57:06'),
(61, 16, 2001214568, 0, '2024-11-05 12:57:06'),
(62, 17, 2001214568, 0, '2024-11-05 12:57:06'),
(63, 18, 2001214568, 0, '2024-11-05 12:57:06'),
(64, 19, 2001214568, 0, '2024-11-05 12:57:06'),
(65, 20, 2001214568, 0, '2024-11-05 12:57:06'),
(66, 21, 2001214568, 0, '2024-11-05 12:57:06'),
(67, 22, 2001214568, 0, '2024-11-05 12:57:06'),
(68, 23, 2001214568, 0, '2024-11-05 12:57:06'),
(69, 24, 2001214568, 0, '2024-11-05 12:57:06'),
(70, 25, 2001214568, 0, '2024-11-05 12:57:06'),
(71, 26, 2001214568, 0, '2024-11-05 12:57:06'),
(72, 27, 2001214568, 0, '2024-11-05 12:57:06'),
(73, 28, 2001214568, 0, '2024-11-05 12:57:06'),
(74, 29, 2001214568, 0, '2024-11-05 12:57:06'),
(75, 30, 2001214568, 0, '2024-11-05 12:57:07'),
(76, 16, 2001215678, 0, '2024-11-05 12:57:07'),
(77, 17, 2001215678, 0, '2024-11-05 12:57:07'),
(78, 18, 2001215678, 0, '2024-11-05 12:57:07'),
(79, 19, 2001215678, 0, '2024-11-05 12:57:07'),
(80, 20, 2001215678, 0, '2024-11-05 12:57:07'),
(81, 21, 2001215678, 0, '2024-11-05 12:57:07'),
(82, 22, 2001215678, 0, '2024-11-05 12:57:07'),
(83, 23, 2001215678, 0, '2024-11-05 12:57:07'),
(84, 24, 2001215678, 0, '2024-11-05 12:57:07'),
(85, 25, 2001215678, 0, '2024-11-05 12:57:07'),
(86, 26, 2001215678, 0, '2024-11-05 12:57:07'),
(87, 27, 2001215678, 0, '2024-11-05 12:57:07'),
(88, 28, 2001215678, 0, '2024-11-05 12:57:07'),
(89, 29, 2001215678, 0, '2024-11-05 12:57:07'),
(90, 30, 2001215678, 0, '2024-11-05 12:57:07'),
(91, 16, 2001216780, 0, '2024-11-05 12:57:07'),
(92, 17, 2001216780, 0, '2024-11-05 12:57:07'),
(93, 18, 2001216780, 0, '2024-11-05 12:57:07'),
(94, 19, 2001216780, 0, '2024-11-05 12:57:07'),
(95, 20, 2001216780, 0, '2024-11-05 12:57:07'),
(96, 21, 2001216780, 0, '2024-11-05 12:57:07'),
(97, 22, 2001216780, 0, '2024-11-05 12:57:07'),
(98, 23, 2001216780, 0, '2024-11-05 12:57:07'),
(99, 24, 2001216780, 0, '2024-11-05 12:57:08'),
(100, 25, 2001216780, 0, '2024-11-05 12:57:08'),
(101, 26, 2001216780, 0, '2024-11-05 12:57:08'),
(102, 27, 2001216780, 0, '2024-11-05 12:57:08'),
(103, 28, 2001216780, 0, '2024-11-05 12:57:08'),
(104, 29, 2001216780, 0, '2024-11-05 12:57:08'),
(105, 30, 2001216780, 0, '2024-11-05 12:57:08'),
(106, 16, 2001216789, 0, '2024-11-05 12:57:08'),
(107, 17, 2001216789, 0, '2024-11-05 12:57:08'),
(108, 18, 2001216789, 0, '2024-11-05 12:57:08'),
(109, 19, 2001216789, 0, '2024-11-05 12:57:08'),
(110, 20, 2001216789, 0, '2024-11-05 12:57:08'),
(111, 21, 2001216789, 0, '2024-11-05 12:57:08'),
(112, 22, 2001216789, 0, '2024-11-05 12:57:08'),
(113, 23, 2001216789, 0, '2024-11-05 12:57:08'),
(114, 24, 2001216789, 0, '2024-11-05 12:57:08'),
(115, 25, 2001216789, 0, '2024-11-05 12:57:08'),
(116, 26, 2001216789, 0, '2024-11-05 12:57:08'),
(117, 27, 2001216789, 0, '2024-11-05 12:57:08'),
(118, 28, 2001216789, 0, '2024-11-05 12:57:08'),
(119, 29, 2001216789, 0, '2024-11-05 12:57:08'),
(120, 30, 2001216789, 0, '2024-11-05 12:57:08'),
(121, 16, 2001217890, 0, '2024-11-05 12:57:08'),
(122, 17, 2001217890, 0, '2024-11-05 12:57:08'),
(123, 18, 2001217890, 0, '2024-11-05 12:57:08'),
(124, 19, 2001217890, 0, '2024-11-05 12:57:08'),
(125, 20, 2001217890, 0, '2024-11-05 12:57:08'),
(126, 21, 2001217890, 0, '2024-11-05 12:57:08'),
(127, 22, 2001217890, 0, '2024-11-05 12:57:08'),
(128, 23, 2001217890, 0, '2024-11-05 12:57:08'),
(129, 24, 2001217890, 0, '2024-11-05 12:57:08'),
(130, 25, 2001217890, 0, '2024-11-05 12:57:08'),
(131, 26, 2001217890, 0, '2024-11-05 12:57:08'),
(132, 27, 2001217890, 0, '2024-11-05 12:57:08'),
(133, 28, 2001217890, 0, '2024-11-05 12:57:08'),
(134, 29, 2001217890, 0, '2024-11-05 12:57:08'),
(135, 30, 2001217890, 0, '2024-11-05 12:57:08'),
(136, 16, 2001218902, 0, '2024-11-05 12:57:09'),
(137, 17, 2001218902, 0, '2024-11-05 12:57:09'),
(138, 18, 2001218902, 0, '2024-11-05 12:57:09'),
(139, 19, 2001218902, 0, '2024-11-05 12:57:09'),
(140, 20, 2001218902, 0, '2024-11-05 12:57:09'),
(141, 21, 2001218902, 0, '2024-11-05 12:57:09'),
(142, 22, 2001218902, 0, '2024-11-05 12:57:09'),
(143, 23, 2001218902, 0, '2024-11-05 12:57:09'),
(144, 24, 2001218902, 0, '2024-11-05 12:57:09'),
(145, 25, 2001218902, 0, '2024-11-05 12:57:09'),
(146, 26, 2001218902, 0, '2024-11-05 12:57:09'),
(147, 27, 2001218902, 0, '2024-11-05 12:57:09'),
(148, 28, 2001218902, 0, '2024-11-05 12:57:09'),
(149, 29, 2001218902, 0, '2024-11-05 12:57:09'),
(150, 30, 2001218902, 0, '2024-11-05 12:57:09'),
(151, 16, 2001219012, 0, '2024-11-05 12:57:09'),
(152, 17, 2001219012, 0, '2024-11-05 12:57:09'),
(153, 18, 2001219012, 0, '2024-11-05 12:57:09'),
(154, 19, 2001219012, 0, '2024-11-05 12:57:09'),
(155, 20, 2001219012, 0, '2024-11-05 12:57:09'),
(156, 21, 2001219012, 0, '2024-11-05 12:57:09'),
(157, 22, 2001219012, 0, '2024-11-05 12:57:09'),
(158, 23, 2001219012, 0, '2024-11-05 12:57:09'),
(159, 24, 2001219012, 0, '2024-11-05 12:57:09'),
(160, 25, 2001219012, 0, '2024-11-05 12:57:09'),
(161, 26, 2001219012, 0, '2024-11-05 12:57:09'),
(162, 27, 2001219012, 0, '2024-11-05 12:57:09'),
(163, 28, 2001219012, 0, '2024-11-05 12:57:09'),
(164, 29, 2001219012, 0, '2024-11-05 12:57:09'),
(165, 30, 2001219012, 0, '2024-11-05 12:57:09');

--
-- Bẫy `attendances`
--
DELIMITER $$
CREATE TRIGGER `after_attendance_insert` AFTER INSERT ON `attendances` FOR EACH ROW BEGIN
    DECLARE total_present INT DEFAULT 0;
    DECLARE total_absent INT DEFAULT 0;
    DECLARE total_late INT DEFAULT 0;

    -- Tính tổng số lần có mặt cho sinh viên
    SELECT COUNT(*) INTO total_present
    FROM attendances
    WHERE student_id = NEW.student_id AND status = 1;

    -- Tính tổng số lần vắng mặt cho sinh viên
    SELECT COUNT(*) INTO total_absent
    FROM attendances
    WHERE student_id = NEW.student_id AND status = 0;

    -- Tính tổng số lần muộn cho sinh viên
    SELECT COUNT(*) INTO total_late
    FROM attendances
    WHERE student_id = NEW.student_id AND status = 2;

    -- Kiểm tra xem bản ghi báo cáo đã tồn tại hay chưa
    IF EXISTS (SELECT 1 FROM attendance_reports WHERE class_id = (SELECT class_id FROM schedules WHERE schedule_id = NEW.schedule_id) AND student_id = NEW.student_id) THEN
        -- Nếu đã tồn tại, cập nhật bản ghi
        UPDATE attendance_reports
        SET total_present = total_present,
            total_absent = total_absent,
            total_late = total_late
        WHERE class_id = (SELECT class_id FROM schedules WHERE schedule_id = NEW.schedule_id) 
          AND student_id = NEW.student_id;
    ELSE
        -- Nếu chưa tồn tại, thêm mới bản ghi
        INSERT INTO attendance_reports (class_id, student_id, total_present, total_absent, total_late)
        VALUES ((SELECT class_id FROM schedules WHERE schedule_id = NEW.schedule_id), 
                NEW.student_id, 
                total_present, 
                total_absent, 
                total_late);
    END IF;
END
$$
DELIMITER ;

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

--
-- Đang đổ dữ liệu cho bảng `attendance_reports`
--

INSERT INTO `attendance_reports` (`report_id`, `class_id`, `student_id`, `total_present`, `total_absent`, `total_late`) VALUES
(1, 'a409fd1d', 2001210224, 2, 13, 0),
(2, 'a409fd1d', 2001214567, 0, 15, 0),
(3, 'a409fd1d', 2001214568, 0, 15, 0),
(4, 'a409fd1d', 2001215678, 0, 15, 0),
(5, 'a409fd1d', 2001216780, 0, 15, 0),
(6, 'a409fd1d', 2001216789, 0, 15, 0),
(7, 'a409fd1d', 2001217890, 0, 15, 0),
(8, 'a409fd1d', 2001218902, 0, 15, 0),
(9, 'a409fd1d', 2001219012, 0, 15, 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `classes`
--

CREATE TABLE `classes` (
  `class_id` char(8) NOT NULL DEFAULT substr(replace(convert(uuid() using utf8mb4),'-',''),1,8),
  `class_name` varchar(255) NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `semester_id` int(11) DEFAULT NULL,
  `teacher_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `classes`
--

INSERT INTO `classes` (`class_id`, `class_name`, `course_id`, `semester_id`, `teacher_id`) VALUES
('28d7e18c', 'TH KTLT T3(7-11)', 9, 2, 1000001234),
('a409fd1d', 'NMLT Vân Anh (T2 1-3)', 1, 2, 1000001234),
('d4b9ea2c', 'HDH T4 (4-6)', 5, 2, 1000001234);

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
('a409fd1d', 2001210224),
('a409fd1d', 2001214567),
('a409fd1d', 2001214568),
('a409fd1d', 2001215678),
('a409fd1d', 2001216114),
('a409fd1d', 2001216780),
('a409fd1d', 2001216789),
('a409fd1d', 2001217890),
('a409fd1d', 2001218902),
('a409fd1d', 2001219012);

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
(16, 'a409fd1d', '2024-11-05', 1, 3),
(17, 'a409fd1d', '2024-11-12', 1, 3),
(18, 'a409fd1d', '2024-11-19', 1, 3),
(19, 'a409fd1d', '2024-11-26', 1, 3),
(20, 'a409fd1d', '2024-12-03', 1, 3),
(21, 'a409fd1d', '2024-12-10', 1, 3),
(22, 'a409fd1d', '2024-12-17', 1, 3),
(23, 'a409fd1d', '2024-12-24', 1, 3),
(24, 'a409fd1d', '2024-12-31', 1, 3),
(25, 'a409fd1d', '2025-01-07', 1, 3),
(26, 'a409fd1d', '2025-01-14', 1, 3),
(27, 'a409fd1d', '2025-01-21', 1, 3),
(28, 'a409fd1d', '2025-01-28', 1, 3),
(29, 'a409fd1d', '2025-02-04', 1, 3),
(30, 'a409fd1d', '2025-02-11', 1, 3),
(31, 'd4b9ea2c', '2024-11-06', 4, 6),
(32, 'd4b9ea2c', '2024-11-13', 4, 6),
(33, 'd4b9ea2c', '2024-11-20', 4, 6),
(34, 'd4b9ea2c', '2024-11-27', 4, 6),
(35, 'd4b9ea2c', '2024-12-04', 4, 6),
(36, 'd4b9ea2c', '2024-12-11', 4, 6),
(37, 'd4b9ea2c', '2024-12-18', 4, 6),
(38, 'd4b9ea2c', '2024-12-25', 4, 6),
(39, 'd4b9ea2c', '2025-01-01', 4, 6),
(40, 'd4b9ea2c', '2025-01-08', 4, 6),
(41, 'd4b9ea2c', '2025-01-15', 4, 6),
(42, 'd4b9ea2c', '2025-01-22', 4, 6),
(43, 'd4b9ea2c', '2025-01-29', 4, 6),
(44, 'd4b9ea2c', '2025-02-05', 4, 6),
(45, 'd4b9ea2c', '2025-02-12', 4, 6),
(46, '28d7e18c', '2024-11-05', 7, 11),
(47, '28d7e18c', '2024-11-12', 7, 11),
(48, '28d7e18c', '2024-11-19', 7, 11),
(49, '28d7e18c', '2024-11-26', 7, 11),
(50, '28d7e18c', '2024-12-03', 7, 11),
(51, '28d7e18c', '2024-12-10', 7, 11),
(52, '28d7e18c', '2024-12-17', 7, 11),
(53, '28d7e18c', '2024-12-24', 7, 11);

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
(1000001234, '1000001234', '$2y$10$4fhM2Q0vP6grGN.pbFApVONMOI5b41zd93DoPt1RafqjB1si6LtLC'),
(1000001235, '1000001235', '$2y$10$P3vImaR.pFuxTs8c43oT1.XPMXGtAk5NqZBazDAelrv8usUORCw5e'),
(1000001236, '1000001236', '$2y$10$tyeWDRjI15fzNm2W5YcM6Okq2Wwt81gLZBlJBleRlClF4sWV.LnRS'),
(2001210123, '2001210123', '$2y$10$jxw0hNSfkHGIDszeNylgYeRVSN2mBQwYPBTto7fEXLSBiu8qryTt2'),
(2001210224, '2001210224', '$2y$10$uxuCxVxf7dHmGRCosIzlRu6gdw41hAwdN9lcnD2G7n68sb1v2Lo1K'),
(2001211234, '2001211234', '$2y$10$51CmYKF1zVxEte6HS0kgtuVgW7iVIuDwh1LsPwxl.s5KVdVTdWXqK'),
(2001211785, '2001211785', '$2y$10$Jw4turFpAI7VkdKxqgGJHO2xN8MLB/3vft6r/aIDRJ.IHvaeQbV1.'),
(2001212345, '2001212345', '$2y$10$F.uqWYp9SISAI5r0u.W7tuLampAQ0GuQIRYRUtKjCF/D6fAPgsyJC'),
(2001212346, '2001212346', '$2y$10$BgraF.STjSOYBrKESZvxHuaWXjx5Lnlzpr46VBg6VO/UTc3x1CdwG'),
(2001213456, '2001213456', '$2y$10$mYugKpNhsxTik09itrOSa.QKzpeLuv1TTnvKJewcudejy3gDxRLgO'),
(2001213457, '2001213457', '$2y$10$URyiH8TFaxb6Qh9X7Zn/6.MQlBTrzAtAjJaFbCIkg1xtc.poXE0R6'),
(2001214567, '2001214567', '$2y$10$XR1cfbSXQjljKIWoJDUbEOlN4vk.dI.t8.ogItKszZIxfEmZk8WqS'),
(2001214568, '2001214568', '$2y$10$g971uABGfT3oFHL53YCgCuaL0zyyLPK1R6IH/A88H.zKHE1hxaqg2'),
(2001215678, '2001215678', '$2y$10$Tg3Pa2FlioD.whEKLCg.jOXpv6KbkbhFd2V2cpw1KxmK1CKVI5IIW'),
(2001215679, '2001215679', '$2y$10$VxVJJ86ZOmiE3RraLFaeYOeIPx1a4M4jYKp2BbRI0jk2eFRSqHSqe'),
(2001216114, '2001216114', '$2y$10$C7vOaj1ckLWO.vRi8PvdGeCamW6AiLiGRirFbTrfGDFKV3VzgYCsS'),
(2001216780, '2001216780', '$2y$10$oRf1DdXgpSyvALTk.BlmSuLDqigk3t76Wm95wvd1..B6o2aJawutK'),
(2001216789, '2001216789', '$2y$10$OnzCVCYPBvYEdMq4NJcHDO4hPfHP3l33ZO4l4enj0FbDc15WFz22O'),
(2001217890, '2001217890', '$2y$10$1E04RyXbXUo.Dj80UB357eyKDT5EynWHjG9QIWSJmQlKVqXxDRYP.'),
(2001217891, '2001217891', '$2y$10$KB/51VXfiDBAszgYJXmqDuxmZHi68DRbszhRzkXuWvGd6f.7jL0qS'),
(2001218901, '2001218901', '$2y$10$l0lYUwjX/xJAL6xWDvYZyeD8hyWztXKj2S/aTQu3r39oB5jcxqd8O'),
(2001218902, '2001218902', '$2y$10$XqkzEaGOuT7MmMNldNEHhu3twAQVSYe.mjUQA9rbFibcPWqs88B66'),
(2001219012, '2001219012', '$2y$10$wynEYi.KyKkc2HoI9CHZBeKlmpIdF/fhbyy8zVX8wRfYbQb3RNkS.');

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
  MODIFY `attendance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=166;

--
-- AUTO_INCREMENT cho bảng `attendance_reports`
--
ALTER TABLE `attendance_reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

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
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

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
  ADD CONSTRAINT `fk_schedule_id` FOREIGN KEY (`schedule_id`) REFERENCES `schedules` (`schedule_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_student_id` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `attendance_reports`
--
ALTER TABLE `attendance_reports`
  ADD CONSTRAINT `fk_attendance_report_class_id` FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_attendance_report_student_id` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE;

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
  ADD CONSTRAINT `fk_class_student_class_id` FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_class_student_student_id` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`course_type_id`) REFERENCES `course_types` (`course_type_id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `schedules_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`) ON DELETE CASCADE;

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
