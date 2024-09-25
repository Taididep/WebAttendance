<?php
session_start();
include 'functions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $classId = isset($_POST['class_id']) ? (int)$_POST['class_id'] : 0;
    $newAttendanceDate = isset($_POST['new_attendance_date']) ? $_POST['new_attendance_date'] : '';

    if ($classId > 0 && !empty($newAttendanceDate)) {
        if (createAttendanceDate($conn, $classId, $newAttendanceDate)) {
            $_SESSION['message'] = "Thêm lớp điểm danh thành công!";
        } else {
            $_SESSION['message'] = "Lỗi: Không thể tạo lớp điểm danh.";
        }
    } else {
        $_SESSION['message'] = "Lỗi: Dữ liệu không hợp lệ.";
    }

    header("Location: attendance.php?class_id=" . $classId);
    exit();
}
?>
