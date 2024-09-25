<?php
include 'functions.php';

session_start();

// Xử lý khi form được submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $classId = $_POST['class_id'];
    $attendanceDate = $_POST['attendance_date'];
    $statuses = $_POST['status']; // Mảng chứa trạng thái của từng sinh viên
    $notes = $_POST['note']; // Mảng chứa ghi chú của từng sinh viên

    foreach ($statuses as $studentId => $status) {
        $note = isset($notes[$studentId]) ? $notes[$studentId] : ''; // Ghi chú có thể rỗng

        $attendanceRecord = checkAttendanceExists($conn, $classId, $studentId, $attendanceDate);        // Kiểm tra xem bản ghi có tồn tại không

        if ($attendanceRecord) {
            updateAttendance($conn, $classId, $studentId, $attendanceDate, $status, $note);            // Nếu tồn tại, tiến hành cập nhật
        }
    }

    // Đặt thông báo vào session
    $_SESSION['message'] = "Cập nhật điểm danh thành công!";
    
    // Chuyển hướng về trang attendance.php
    header("Location: attendance.php?class_id=" . $classId . "&attendance_date=" . $attendanceDate);
    exit();
}
?>
