<?php
include __DIR__ . '/../../Connect/connect.php';
// include __DIR__ . '/../../Account/islogin.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $scheduleId = $_POST['schedule_id'];
    $date = $_POST['date'];
    $startTime = $_POST['start_time'];
    $endTime = $_POST['end_time'];
    $classId = $_POST['class_id'];

    // Kiểm tra lịch trùng
    $sql_check = "SELECT COUNT(*) FROM schedules 
                  WHERE class_id = ? AND date = ? AND schedule_id != ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->execute([$classId, $date, $scheduleId]);
    $count = $stmt_check->fetchColumn();

    if ($count > 0) {
        // Nếu trùng, quay lại trang với thông báo lỗi
        header('Location: class_view.php?class_id=' . $classId . '&error=duplicate');
        exit;
    }

    // Nếu không trùng, thực hiện cập nhật
    $sql = "CALL UpdateSchedule(?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$date, $startTime, $endTime, $scheduleId]);

    header('Location: class_view.php?class_id=' . $classId);
    exit;
}
