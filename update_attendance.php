<?php
session_start();
include 'functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy thông tin từ form
    $classId = isset($_POST['class_id']) ? (int)$_POST['class_id'] : 0;
    $attendanceDate = isset($_POST['attendance_date']) ? $_POST['attendance_date'] : '';
    $statuses = isset($_POST['status']) ? $_POST['status'] : [];
    $notes = isset($_POST['note']) ? $_POST['note'] : [];

    if ($classId <= 0 || empty($attendanceDate)) {
        echo "<p class='text-danger'>Lỗi: Thông tin không hợp lệ.</p>";
        exit();
    }

    foreach ($statuses as $studentId => $status) {
        // Chuẩn bị câu truy vấn để cập nhật hoặc thêm mới
        $note = isset($notes[$studentId]) ? $notes[$studentId] : '';

        // Kiểm tra xem bản ghi đã tồn tại chưa
        $existingAttendance = getAttendanceByStudent($conn, $classId, $studentId, $attendanceDate);

        if ($existingAttendance) {
            // Cập nhật trạng thái và ghi chú
            $query = "UPDATE attendances SET status = ?, note = ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$status, $note, $existingAttendance['id']]);
        } else {
            // Thêm mới bản ghi
            $query = "INSERT INTO attendances (class_id, student_id, attendance_date, status, note) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->execute([$classId, $studentId, $attendanceDate, $status, $note]);
        }
    }

    echo "<p class='text-success'>Cập nhật điểm danh thành công!</p>";
    header("Location: attendance.php?class_id=$classId&attendance_date=$attendanceDate");
    exit();
}
?>
