<?php
session_start();
$basePath = '../'; // Đường dẫn gốc
include __DIR__ . '/../../Connect/connect.php';
include __DIR__ . '/../../LayoutPages/navbar.php';
include __DIR__ . '/../../Account/islogin.php';

// Kiểm tra xem class_id có được gửi qua POST hay không
if (!isset($_POST['class_id']) || !isset($_POST['attendance'])) {
    echo 'Dữ liệu không hợp lệ.';
    exit;
}

$class_id = $_POST['class_id'];

$attendanceData = $_POST['attendance'];

// Lặp qua từng sinh viên và trạng thái điểm danh
foreach ($attendanceData as $student_id => $schedules) {
    foreach ($schedules as $schedule_id => $status) {

        if ($status != -1 && $status != 0 && $status != 1 && $status != 2) {
            continue;
        }

        $sql = "CALL UpdateOrInsertAttendance(?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$schedule_id, $student_id, $status]);
    }
}

header("Location: ../Class/class_detail_list.php?class_id=" . urlencode($class_id) . "&message=Thay đổi đã được lưu thành công.");
exit;
