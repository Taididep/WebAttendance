<?php
include 'connect/connect.php'; // Đảm bảo bạn đã kết nối với CSDL
include 'function.php';

// Lấy class_id và attendance_date từ POST
$class_id = $_POST['class_id'];
$attendance_date = $_POST['attendance_date'];

// Lấy danh sách sinh viên trong lớp
$students = getStudentsByClassId($conn, $class_id);

// Gán trạng thái điểm danh cho tất cả sinh viên
foreach ($students as $student) {
    // Thêm dữ liệu điểm danh vào database với trạng thái "Absent"
    $stmt = $conn->prepare("INSERT INTO attendances (class_id, student_id, attendance_date, status) VALUES (:class_id, :student_id, :attendance_date, :status)");
    $stmt->bindValue(':class_id', $class_id);
    $stmt->bindValue(':student_id', $student['student_id']);
    $stmt->bindValue(':attendance_date', $attendance_date);
    $stmt->bindValue(':status', 'Absent'); // Mặc định tất cả đều vắng mặt
    $stmt->execute();
}

// Chuyển hướng trở lại danh sách điểm danh
header("Location: attendance_list.php?class_id=" . $class_id);
exit();
