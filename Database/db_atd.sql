-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th9 10, 2024 lúc 05:16 PM
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
-- Cấu trúc bảng cho bảng `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'password', '2024-09-08 13:28:05', '2024-09-10 15:10:59'),
(2, 'admin1', 'password1', '2024-09-08 13:28:05', '2024-09-10 15:11:55');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `semester`
--

CREATE TABLE `semester` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `is_active` int(2) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `semester`
--

INSERT INTO `semester` (`id`, `name`, `is_active`, `start_date`, `end_date`) VALUES
(1, 'Học kỳ 1', 1, '2024-01-01', '2024-06-30'),
(2, 'Học kỳ 2', 1, '2024-07-01', '2024-12-31'),
(3, 'Học kỳ 3', 1, '2025-01-01', '2025-06-30');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `student`
--

CREATE TABLE `student` (
  `id` char(10) NOT NULL,
  `lastname` varchar(50) DEFAULT NULL,
  `firstname` varchar(50) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `class` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `student`
--

INSERT INTO `student` (`id`, `lastname`, `firstname`, `username`, `password`, `email`, `phone`, `class`, `created_at`, `updated_at`) VALUES
('2001210123', 'Trần Văn', 'Tùng', '2001210123', 'password909', 'tranvantung@gmail.com', '0911234567', '12DHTH11', '2024-09-08 14:56:25', '2024-09-08 14:56:25'),
('2001210224', 'Nguyễn Hữu', 'Thông', '2001210224', 'password', 'huuthong@gmail.com', '0901234567', '12DHTH14', '2024-09-08 14:56:25', '2024-09-08 14:56:25'),
('2001211234', 'Phạm Thị', 'Vân', '2001211234', 'password010', 'phamthivan@gmail.com', '0912345678', '12DHTH12', '2024-09-08 14:56:25', '2024-09-08 14:56:25'),
('2001211785', 'Phùng Vĩnh', 'Luân', '2001211785', 'password', 'vinhluan171@gmail.com', '0902345678', '12DHTH07', '2024-09-08 14:56:25', '2024-09-08 14:56:25'),
('2001212345', 'Lê Minh', 'Cường', '2001212345', 'password101', 'leminhcuong@gmail.com', '0903456789', '12DHTH03', '2024-09-08 14:56:25', '2024-09-08 14:56:25'),
('2001212346', 'Hoàng Thị', 'Mai', '2001212346', 'password121', 'hoangthimai@gmail.com', '0913456789', '12DHTH13', '2024-09-08 14:56:25', '2024-09-08 14:56:25'),
('2001213456', 'Trương Thị', 'Lan', '2001213456', 'password202', 'truongthilan@gmail.com', '0904567890', '12DHTH04', '2024-09-08 14:56:25', '2024-09-08 14:56:25'),
('2001213457', 'Nguyễn Văn', 'Hùng', '2001213457', 'password232', 'nguyenvanhung@gmail.com', '0914567890', '12DHTH14', '2024-09-08 14:56:25', '2024-09-08 14:56:25'),
('2001214567', 'Hoàng Văn', 'Linh', '2001214567', 'password303', 'hoangvanlinh@gmail.com', '0905678901', '12DHTH05', '2024-09-08 14:56:25', '2024-09-08 14:56:25'),
('2001214568', 'Bùi Văn', 'Long', '2001214568', 'password343', 'buivanlong@gmail.com', '0915678901', '12DHTH15', '2024-09-08 14:56:25', '2024-09-08 14:56:25'),
('2001215678', 'Bùi Thị', 'Hồng', '2001215678', 'password404', 'buithihong@gmail.com', '0906789012', '12DHTH06', '2024-09-08 14:56:25', '2024-09-08 14:56:25'),
('2001215679', 'Lê Thị', 'Như', '2001215679', 'password454', 'lethinh@gmail.com', '0916789012', '12DHTH16', '2024-09-08 14:56:25', '2024-09-08 14:56:25'),
('2001216114', 'Đinh Văn', 'Tài', '2001216114', 'password', 'dinhvantai079@gmail.com', '0901234578', '12DHTH02', '2024-09-08 14:56:25', '2024-09-08 14:56:25'),
('2001216780', 'Trương Văn', 'Dũng', '2001216780', 'password565', 'truongvandung@gmail.com', '0917890123', '12DHTH17', '2024-09-08 14:56:25', '2024-09-08 14:56:25'),
('2001216789', 'Vũ Văn', 'Bình', '2001216789', 'password505', 'vuvanhbinh@gmail.com', '0907890123', '12DHTH07', '2024-09-08 14:56:25', '2024-09-08 14:56:25'),
('2001217890', 'Nguyễn Thị', 'Hoa', '2001217890', 'password606', 'nguyenthihua@gmail.com', '0908901234', '12DHTH08', '2024-09-08 14:56:25', '2024-09-08 14:56:25'),
('2001217891', 'Vũ Thị', 'Hạnh', '2001217891', 'password676', 'vuthihanh@gmail.com', '0918901234', '12DHTH18', '2024-09-08 14:56:25', '2024-09-08 14:56:25'),
('2001218901', 'Đặng Văn', 'Quân', '2001218901', 'password707', 'dangvanquan@gmail.com', '0909012345', '12DHTH09', '2024-09-08 14:56:25', '2024-09-08 14:56:25'),
('2001218902', 'Đặng Thị', 'Thu', '2001218902', 'password787', 'dangthithu@gmail.com', '0920123456', '12DHTH19', '2024-09-08 14:56:25', '2024-09-08 14:56:25'),
('2001219012', 'Lương Thị', 'Ngân', '2001219012', 'password808', 'luongthingan@gmail.com', '0910123456', '12DHTH10', '2024-09-08 14:56:25', '2024-09-08 14:56:25'),
('2001219013', 'Lương Văn', 'Tâm', '2001219013', 'password898', 'luongvantam@gmail.com', '0921234567', '12DHTH20', '2024-09-08 14:56:25', '2024-09-08 14:56:25');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `teacher`
--

CREATE TABLE `teacher` (
  `id` int(11) NOT NULL,
  `lastname` varchar(50) DEFAULT NULL,
  `firstname` varchar(50) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `teacher`
--

INSERT INTO `teacher` (`id`, `lastname`, `firstname`, `username`, `password`, `email`, `phone`, `created_at`, `updated_at`) VALUES
(1, 'Trần Thị Vân', 'Anh', 'vanAnh123', 'password123', 'vanAnh123@example.com', '0903456789', '2024-09-08 13:46:33', '2024-09-08 13:46:33'),
(2, 'Trần Văn', 'Hùng', 'HungTV', 'password456', 'HungTV@example.com', '0903456790', '2024-09-08 13:46:33', '2024-09-08 13:46:33'),
(3, 'Nguyễn Văn', 'Tùng', 'NguyenVT', 'password789', 'NguyenVT@example.com', '0904567890', '2024-09-08 13:46:33', '2024-09-08 13:46:33');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Chỉ mục cho bảng `semester`
--
ALTER TABLE `semester`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Chỉ mục cho bảng `teacher`
--
ALTER TABLE `teacher`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `semester`
--
ALTER TABLE `semester`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `teacher`
--
ALTER TABLE `teacher`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
