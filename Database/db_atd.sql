-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th10 20, 2024 lúc 07:51 PM
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
    SELECT 
        s.semester_id,
        s.semester_name,
        s.start_date,
        s.end_date,
        s.is_active,
        COUNT(c.class_id) AS total_classes
    FROM 
        semesters s
    LEFT JOIN 
        classes c ON s.semester_id = c.semester_id
    GROUP BY 
        s.semester_id, s.semester_name, s.start_date, s.end_date, s.is_active
    ORDER BY 
        s.start_date DESC; -- Sắp xếp theo ngày bắt đầu mới nhất
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetAttendanceByScheduleId` (IN `scheduleId` INT, IN `class_id` CHAR(36))   BEGIN
    SELECT a.student_id, a.status
    FROM attendances a
    JOIN schedules s ON a.schedule_id = s.schedule_id
    WHERE a.schedule_id = scheduleId AND s.class_id = class_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetAttendanceReportByClassId` (IN `input_class_id` CHAR(36))   BEGIN
    SELECT 
        ar.student_id,
        ar.total_present,
        ar.total_absent,
        ar.total_late
    FROM 
        attendance_reports ar
    WHERE 
        ar.class_id = input_class_id;
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
        t.lastname, 
        t.firstname
    FROM classes c
    JOIN courses co ON c.course_id = co.course_id
    JOIN teachers t ON c.teacher_id = t.teacher_id
    WHERE c.semester_id = semester_id;
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
    WHERE c.semester_id = semester_id AND cs.student_id = student_id AND cs.status = 1;
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetClassInfoById` (IN `classId` CHAR(8))   BEGIN
    SELECT 
        c.class_name, 
        co.course_name, 
        CONCAT(t.lastname, ' ', t.firstname) AS teacher_fullname,
        s.semester_name 
    FROM 
        classes c
    JOIN 
        courses co ON c.course_id = co.course_id
    JOIN 
        teachers t ON c.teacher_id = t.teacher_id
    JOIN 
        semesters s ON c.semester_id = s.semester_id
    WHERE 
        c.class_id = classId;
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetScheduleById` (IN `schedule_id` INT)   BEGIN
    SELECT * FROM schedules WHERE id = schedule_id;
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetSchedulesByClassId` (IN `classId` VARCHAR(36))   BEGIN
    SELECT 
        sch.schedule_id,
        sch.date,
        sch.start_time,
        sch.end_time
    FROM schedules sch
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
        AND c.teacher_id = teacher_id
    ORDER BY 
        s.date, s.start_time;
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
-- Cấu trúc bảng cho bảng `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `lastname` varchar(50) DEFAULT NULL,
  `firstname` varchar(50) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `admins`
--

INSERT INTO `admins` (`admin_id`, `lastname`, `firstname`, `email`, `phone`) VALUES
(1, '', 'Admin', 'an.nguyen@example.com', '0123456789'),
(2, 'Tran', 'Binh', 'binh.tran@example.com', '0987654321');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `announcements`
--

CREATE TABLE `announcements` (
  `announcement_id` int(11) NOT NULL,
  `class_id` char(8) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `announcements`
--

INSERT INTO `announcements` (`announcement_id`, `class_id`, `title`, `content`, `created_at`, `updated_at`) VALUES
(8, 'a409fd1d', 'Báo cáo tiến độ', 'Vào lúc 8h30 sẽ báo cáo đồ án', '2024-11-06 17:26:32', '2024-11-08 16:53:57'),
(9, 'a409fd1d', 'Thông báo khẩn', '30/11 sẽ hết hạn đồ án chuyên ngành, các em tranh thủ nha.', '2024-11-06 18:25:00', '2024-11-08 16:52:48'),
(14, 'a409fd1d', 'aaa', 'aaa', '2024-11-10 15:21:04', '2024-11-10 15:21:04');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `attendances`
--

CREATE TABLE `attendances` (
  `attendance_id` int(11) NOT NULL,
  `schedule_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `attendances`
--

INSERT INTO `attendances` (`attendance_id`, `schedule_id`, `student_id`, `status`, `updated_at`) VALUES
(1, 16, 2001210224, 0, '2024-11-20 11:39:50'),
(2, 17, 2001210224, 0, '2024-11-20 11:39:50'),
(3, 18, 2001210224, 0, '2024-11-20 11:39:50'),
(4, 16, 2001214567, 0, '2024-11-20 11:39:50'),
(5, 17, 2001214567, 0, '2024-11-20 11:39:50'),
(6, 18, 2001214567, 0, '2024-11-20 11:39:50'),
(7, 16, 2001214568, 0, '2024-11-20 11:39:50'),
(8, 17, 2001214568, 0, '2024-11-20 11:39:50'),
(9, 18, 2001214568, 0, '2024-11-20 11:39:50'),
(10, 16, 2001215678, 0, '2024-11-20 11:39:50'),
(11, 17, 2001215678, 0, '2024-11-20 11:39:50'),
(12, 18, 2001215678, 0, '2024-11-20 11:39:50'),
(13, 16, 2001215679, 0, '2024-11-20 11:39:51'),
(14, 17, 2001215679, 0, '2024-11-20 11:39:51'),
(15, 18, 2001215679, 0, '2024-11-20 11:39:51'),
(16, 16, 2001216114, 0, '2024-11-20 11:39:51'),
(17, 17, 2001216114, 2, '2024-11-20 11:39:51'),
(18, 18, 2001216114, 2, '2024-11-20 11:39:51'),
(19, 16, 2001216780, 0, '2024-11-20 11:39:51'),
(20, 17, 2001216780, 0, '2024-11-20 11:39:51'),
(21, 18, 2001216780, 0, '2024-11-20 11:39:51');

--
-- Bẫy `attendances`
--
DELIMITER $$
CREATE TRIGGER `after_attendance_insert` AFTER INSERT ON `attendances` FOR EACH ROW BEGIN
    DECLARE total_present INT DEFAULT 0;
    DECLARE total_absent INT DEFAULT 0;
    DECLARE total_late INT DEFAULT 0;
    DECLARE total INT DEFAULT 0;
    DECLARE current_class_id CHAR(36);

    -- Lấy class_id hiện tại từ bảng schedules
    SET current_class_id = (SELECT class_id FROM schedules WHERE schedule_id = NEW.schedule_id);

    -- Tính tổng số lần có mặt cho sinh viên trong class_id đó
    SELECT COUNT(*) INTO total_present
    FROM attendances
    WHERE student_id = NEW.student_id 
      AND status = 1
      AND schedule_id IN (SELECT schedule_id FROM schedules WHERE class_id = current_class_id);

    -- Tính tổng số lần vắng mặt cho sinh viên trong class_id đó
    SELECT COUNT(*) INTO total_absent
    FROM attendances
    WHERE student_id = NEW.student_id 
      AND status = 0
      AND schedule_id IN (SELECT schedule_id FROM schedules WHERE class_id = current_class_id);

    -- Tính tổng số lần muộn cho sinh viên trong class_id đó
    SELECT COUNT(*) INTO total_late
    FROM attendances
    WHERE student_id = NEW.student_id 
      AND status = 2
      AND schedule_id IN (SELECT schedule_id FROM schedules WHERE class_id = current_class_id);

    -- Tính tổng số dòng dữ liệu trong schedules cho class_id đó
    SELECT COUNT(*) INTO total
    FROM schedules
    WHERE class_id = current_class_id;

    -- Kiểm tra xem bản ghi báo cáo đã tồn tại hay chưa
    IF EXISTS (SELECT 1 FROM attendance_reports WHERE class_id = current_class_id AND student_id = NEW.student_id) THEN
        -- Nếu đã tồn tại, cập nhật bản ghi
        UPDATE attendance_reports
        SET total_present = total_present,
            total_absent = total_absent,
            total_late = total_late,
            total = total
        WHERE class_id = current_class_id
          AND student_id = NEW.student_id;
    ELSE
        -- Nếu chưa tồn tại, thêm mới bản ghi
        INSERT INTO attendance_reports (class_id, student_id, total_present, total_absent, total_late, total)
        VALUES (current_class_id, 
                NEW.student_id, 
                total_present, 
                total_absent, 
                total_late,
                total);
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_attendance_update` AFTER UPDATE ON `attendances` FOR EACH ROW BEGIN
    DECLARE total_present INT DEFAULT 0;
    DECLARE total_absent INT DEFAULT 0;
    DECLARE total_late INT DEFAULT 0;
    DECLARE total INT DEFAULT 0;
    DECLARE current_class_id CHAR(36);

    -- Lấy class_id hiện tại từ bảng schedules
    SET current_class_id = (SELECT class_id FROM schedules WHERE schedule_id = NEW.schedule_id);

    -- Tính tổng số lần có mặt cho sinh viên trong class_id đó
    SELECT COUNT(*) INTO total_present
    FROM attendances
    WHERE student_id = NEW.student_id 
      AND status = 1
      AND schedule_id IN (SELECT schedule_id FROM schedules WHERE class_id = current_class_id);

    -- Tính tổng số lần vắng mặt cho sinh viên trong class_id đó
    SELECT COUNT(*) INTO total_absent
    FROM attendances
    WHERE student_id = NEW.student_id 
      AND status = 0
      AND schedule_id IN (SELECT schedule_id FROM schedules WHERE class_id = current_class_id);

    -- Tính tổng số lần muộn cho sinh viên trong class_id đó
    SELECT COUNT(*) INTO total_late
    FROM attendances
    WHERE student_id = NEW.student_id 
      AND status = 2
      AND schedule_id IN (SELECT schedule_id FROM schedules WHERE class_id = current_class_id);

    -- Tính tổng số dòng dữ liệu trong schedules cho class_id đó
    SELECT COUNT(*) INTO total
    FROM schedules
    WHERE class_id = current_class_id;

    -- Kiểm tra xem bản ghi báo cáo đã tồn tại hay chưa
    IF EXISTS (SELECT 1 FROM attendance_reports WHERE class_id = current_class_id AND student_id = NEW.student_id) THEN
        -- Nếu đã tồn tại, cập nhật bản ghi
        UPDATE attendance_reports
        SET total_present = total_present,
            total_absent = total_absent,
            total_late = total_late,
            total = total
        WHERE class_id = current_class_id
          AND student_id = NEW.student_id;
    ELSE
        -- Nếu chưa tồn tại, thêm mới bản ghi
        INSERT INTO attendance_reports (class_id, student_id, total_present, total_absent, total_late, total)
        VALUES (current_class_id, 
                NEW.student_id, 
                total_present, 
                total_absent, 
                total_late,
                total);
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `before_insert_attendance` BEFORE INSERT ON `attendances` FOR EACH ROW BEGIN
    DECLARE schedule_date DATE;
    
    -- Lấy ngày từ bảng schedules dựa trên schedule_id
    SELECT date INTO schedule_date
    FROM schedules
    WHERE schedule_id = NEW.schedule_id;
    
    -- Kiểm tra nếu ngày nhỏ hơn hoặc bằng ngày hiện tại và status ban đầu là -1
    IF schedule_date <= CURDATE() AND NEW.status = -1 THEN
        SET NEW.status = 0;  -- Gán status = 0
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `before_update_attendance` BEFORE UPDATE ON `attendances` FOR EACH ROW BEGIN
    DECLARE schedule_date DATE;
    
    -- Lấy ngày từ bảng schedules dựa trên schedule_id
    SELECT date INTO schedule_date
    FROM schedules
    WHERE schedule_id = NEW.schedule_id;
    
    -- Kiểm tra nếu ngày nhỏ hơn hoặc bằng ngày hiện tại và status ban đầu là -1
    IF schedule_date <= CURDATE() AND NEW.status = -1 THEN
        SET NEW.status = 0;  -- Gán status = 0
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
  `total_late` int(11) DEFAULT 0,
  `total` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `attendance_reports`
--

INSERT INTO `attendance_reports` (`report_id`, `class_id`, `student_id`, `total_present`, `total_absent`, `total_late`, `total`) VALUES
(1, 'a409fd1d', 2001210224, 0, 3, 0, 15),
(2, 'a409fd1d', 2001214567, 0, 3, 0, 15),
(3, 'a409fd1d', 2001214568, 0, 3, 0, 15),
(4, 'a409fd1d', 2001215678, 0, 3, 0, 15),
(5, 'a409fd1d', 2001215679, 0, 3, 0, 15),
(6, 'a409fd1d', 2001216114, 0, 1, 2, 15),
(7, 'a409fd1d', 2001216780, 0, 3, 0, 15);

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
('1432cd49', 'KTMT T4 (1 - 3)', 7, 2, 1000001234),
('a409fd1d', 'NMLT Vân Anh (T2 1-3)', 1, 2, 1000001234);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `class_students`
--

CREATE TABLE `class_students` (
  `class_id` char(36) NOT NULL,
  `student_id` int(11) NOT NULL,
  `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `class_students`
--

INSERT INTO `class_students` (`class_id`, `student_id`, `status`) VALUES
('1432cd49', 2001216114, 1),
('a409fd1d', 2001210224, 0),
('a409fd1d', 2001214567, 0),
('a409fd1d', 2001214568, 0),
('a409fd1d', 2001215678, 1),
('a409fd1d', 2001215679, 0),
('a409fd1d', 2001216114, 1),
('a409fd1d', 2001216780, 1),
('a409fd1d', 2001216789, 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `comments`
--

CREATE TABLE `comments` (
  `comment_id` int(11) NOT NULL,
  `announcement_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `comments`
--

INSERT INTO `comments` (`comment_id`, `announcement_id`, `user_id`, `content`, `created_at`) VALUES
(3, 8, 1000001234, 'Mai cô sẽ coi', '2024-11-06 17:27:02'),
(4, 8, 1000001234, 'Các em làm tới đâu ?', '2024-11-06 17:27:33'),
(5, 8, 2001216114, 'Dạ em hiểu rồi', '2024-11-06 18:13:26'),
(6, 9, 2001216114, 'Dạ em hiểu rồi ạ', '2024-11-07 05:36:49'),
(7, 9, 1000001234, 'ok', '2024-11-07 05:37:12');

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
(41, 'Phát triển phần mềm ứng dụng thông minh', 6),
(42, 'taidz', 1),
(43, 'mon hoc', 3);

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
  `date` datetime NOT NULL,
  `start_time` int(11) NOT NULL,
  `end_time` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `schedules`
--

INSERT INTO `schedules` (`schedule_id`, `class_id`, `date`, `start_time`, `end_time`, `status`) VALUES
(16, 'a409fd1d', '2024-11-05 20:56:28', 1, 3, 0),
(17, 'a409fd1d', '2024-11-11 00:00:00', 1, 3, 0),
(18, 'a409fd1d', '2024-11-15 00:00:00', 1, 3, 1),
(19, 'a409fd1d', '2024-11-25 00:00:00', 1, 3, 0),
(20, 'a409fd1d', '2024-12-03 00:00:00', 1, 3, 0),
(21, 'a409fd1d', '2024-12-10 00:00:00', 1, 3, 0),
(22, 'a409fd1d', '2024-12-17 00:00:00', 1, 3, 0),
(23, 'a409fd1d', '2024-12-24 00:00:00', 1, 3, 0),
(24, 'a409fd1d', '2024-12-31 00:00:00', 1, 3, 0),
(25, 'a409fd1d', '2025-01-07 00:00:00', 1, 3, 0),
(26, 'a409fd1d', '2025-01-14 00:00:00', 1, 3, 0),
(27, 'a409fd1d', '2025-01-21 00:00:00', 1, 3, 0),
(28, 'a409fd1d', '2025-01-28 00:00:00', 1, 3, 0),
(29, 'a409fd1d', '2025-02-04 00:00:00', 1, 3, 0),
(30, 'a409fd1d', '2025-02-11 00:00:00', 1, 3, 0),
(99, '1432cd49', '2024-11-06 00:00:00', 1, 3, 0),
(100, '1432cd49', '2024-11-11 00:00:00', 1, 3, 0),
(101, '1432cd49', '2024-11-20 00:00:00', 1, 3, 0),
(102, '1432cd49', '2024-11-27 00:00:00', 1, 3, 0),
(103, '1432cd49', '2024-12-04 00:00:00', 1, 3, 0),
(104, '1432cd49', '2024-12-11 00:00:00', 1, 3, 0),
(105, '1432cd49', '2024-12-18 00:00:00', 1, 3, 0),
(106, '1432cd49', '2024-12-25 00:00:00', 1, 3, 0),
(107, '1432cd49', '2025-01-01 00:00:00', 1, 3, 0),
(108, '1432cd49', '2025-01-08 00:00:00', 1, 3, 0),
(109, '1432cd49', '2025-01-15 00:00:00', 1, 3, 0),
(110, '1432cd49', '2025-01-22 00:00:00', 1, 3, 0),
(111, '1432cd49', '2025-01-29 00:00:00', 1, 3, 0),
(112, '1432cd49', '2025-02-05 00:00:00', 1, 3, 0),
(113, '1432cd49', '2025-02-12 00:00:00', 1, 3, 0);

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
(2, 'HK1 (2024 - 2025)', 1, '2024-08-15', '2025-12-17'),
(3, 'HK2 (2021)', 1, '2013-03-30', '2005-08-09'),
(4, 'HK2 (2024)', 1, '2013-03-30', '2019-08-09'),
(5, 'HK2 (2024)', 1, '2013-03-30', '2019-08-09');

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
  `gender` enum('Nam','Nữ') DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `students`
--

INSERT INTO `students` (`student_id`, `lastname`, `firstname`, `email`, `phone`, `class`, `birthday`, `gender`, `avatar`) VALUES
(2001210123, 'Trần Văn', 'Tùng', 'tranvantung@gmail.com', '0911234567', '12DHTH11', '2003-03-01', 'Nam', NULL),
(2001210224, 'Nguyễn Hữu', 'Thông', 'huuthong@gmail.com', '0901234567', '12DHTH14', '2003-03-15', 'Nam', NULL),
(2001211234, 'Phạm Thị', 'Vân', 'phamthivan@gmail.com', '0912345678', '14DHTH12', '2005-10-17', 'Nữ', NULL),
(2001211785, 'Phùng Vĩnh', 'Luân', 'vinhluan171@gmail.com', '0902345678', '12DHTH07', '2003-07-22', 'Nam', NULL),
(2001212345, 'Lê Minh', 'Cường', 'leminhcuong@gmail.com', '0903456789', '12DHTH03', '2003-02-11', 'Nam', NULL),
(2001212346, 'Hoàng Thị', 'Mai', 'hoangthimai@gmail.com', '0913456789', '13DHTH13', '2004-08-12', 'Nữ', NULL),
(2001213456, 'Trương Thị', 'Lan', 'truongthilan@gmail.com', '0904567890', '12DHTH04', '2003-06-06', 'Nữ', NULL),
(2001213457, 'Nguyễn Văn', 'Hùng', 'nguyenvanhung@gmail.com', '0914567890', '12DHTH14', '2002-05-05', 'Nam', NULL),
(2001214567, 'Hoàng Văn', 'Linh', 'hoangvanlinh@gmail.com', '0905678901', '12DHTH05', '2003-04-08', 'Nam', NULL),
(2001214568, 'Bùi Văn', 'Long', 'buivanlong@gmail.com', '0915678901', '13DHTH15', '2004-02-02', 'Nam', NULL),
(2001215678, 'Bùi Thị', 'Hồng', 'buithihong@gmail.com', '0906789012', '12DHTH06', '2003-12-19', 'Nữ', NULL),
(2001215679, 'Lê Thị', 'Như', 'lethinh@gmail.com', '0916789012', '12DHTH16', '2003-11-11', 'Nữ', NULL),
(2001216114, 'Đinh Văn', 'Tài', 'dinhvantai079@gmail.com', '0901234578', '12DHTH02', '2003-03-30', 'Nam', NULL),
(2001216780, 'Trương Văn', 'Dũng', 'truongvandung@gmail.com', '0917890123', '12DHTH17', '2003-01-01', 'Nam', NULL),
(2001216789, 'Vũ Văn', 'Bình', 'vuvanhbinh@gmail.com', '0907890123', '11DHTH07', '2002-10-10', 'Nam', NULL),
(2001217890, 'Nguyễn Thị', 'Hoa', 'nguyenthihua@gmail.com', '0908901234', '11DHTH08', '2002-11-17', 'Nữ', NULL),
(2001217891, 'Vũ Thị', 'Hạnh', 'vuthihanh@gmail.com', '0918901234', '13DHTH18', '2004-05-17', 'Nữ', NULL),
(2001218901, 'Đặng Văn', 'Quân', 'dangvanquan@gmail.com', '0909012345', '11DHTH09', '2002-05-30', 'Nam', NULL),
(2001218902, 'Đặng Thị', 'Thu', 'dangthithu@gmail.com', '0920123456', '13DHTH19', '2004-11-12', 'Nữ', NULL),
(2001219012, 'Lương Thị', 'Ngân', 'luongthingan@gmail.com', '0910123456', '12DHTH10', '2003-11-16', 'Nữ', NULL);

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
  `gender` enum('Nam','Nữ') DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `teachers`
--

INSERT INTO `teachers` (`teacher_id`, `lastname`, `firstname`, `email`, `phone`, `birthday`, `gender`, `avatar`) VALUES
(1000001234, 'Trần Thị Vân', 'Anh', 'vanAnh123@example.com', '0903456789', '1995-01-01', 'Nữ', '../../Image/Avatar/new_teacher.jpeg'),
(1000001235, 'Trần Văn', 'Hùng', 'HungTV@example.com', '0903456790', '1990-01-01', 'Nam', NULL),
(1000001236, 'Nguyễn Văn', 'Tùng', 'NguyenVT@example.com', '0904567890', '1992-01-01', 'Nam', NULL);

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
(1, 'admin1', '$2y$10$cfG.Glj5s4GTocadoW.QDupZ/HMVIOwf74xes7gEjqWx5J8fV3GuO'),
(2, 'admin2', '$2y$10$dqut0FsGPQwl.1S1n9m9MuXqwbynWDAutKAs9/kUe5D9uev1zRKpK'),
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
(1, 1),
(2, 1),
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
-- Chỉ mục cho bảng `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`);

--
-- Chỉ mục cho bảng `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`announcement_id`),
  ADD KEY `class_id` (`class_id`);

--
-- Chỉ mục cho bảng `attendances`
--
ALTER TABLE `attendances`
  ADD PRIMARY KEY (`attendance_id`),
  ADD UNIQUE KEY `unique_attendance` (`schedule_id`,`student_id`),
  ADD KEY `fk_attendance_student_class` (`student_id`);

--
-- Chỉ mục cho bảng `attendance_reports`
--
ALTER TABLE `attendance_reports`
  ADD PRIMARY KEY (`report_id`),
  ADD UNIQUE KEY `unique_report` (`class_id`,`student_id`),
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
-- Chỉ mục cho bảng `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `announcement_id` (`announcement_id`),
  ADD KEY `user_id` (`user_id`);

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
-- AUTO_INCREMENT cho bảng `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `announcements`
--
ALTER TABLE `announcements`
  MODIFY `announcement_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT cho bảng `attendances`
--
ALTER TABLE `attendances`
  MODIFY `attendance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT cho bảng `attendance_reports`
--
ALTER TABLE `attendance_reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `comments`
--
ALTER TABLE `comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT cho bảng `courses`
--
ALTER TABLE `courses`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1000199;

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
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=134;

--
-- AUTO_INCREMENT cho bảng `semesters`
--
ALTER TABLE `semesters`
  MODIFY `semester_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

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
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2001219017;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `admins`
--
ALTER TABLE `admins`
  ADD CONSTRAINT `admins_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `announcements`
--
ALTER TABLE `announcements`
  ADD CONSTRAINT `announcements_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `attendances`
--
ALTER TABLE `attendances`
  ADD CONSTRAINT `fk_attendance_schedule_id` FOREIGN KEY (`schedule_id`) REFERENCES `schedules` (`schedule_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_attendance_student_class` FOREIGN KEY (`student_id`) REFERENCES `class_students` (`student_id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `attendance_reports`
--
ALTER TABLE `attendance_reports`
  ADD CONSTRAINT `fk_attendance_report_class_id` FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_attendance_report_class_student` FOREIGN KEY (`class_id`,`student_id`) REFERENCES `class_students` (`class_id`, `student_id`) ON DELETE CASCADE,
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
-- Các ràng buộc cho bảng `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`announcement_id`) REFERENCES `announcements` (`announcement_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

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
