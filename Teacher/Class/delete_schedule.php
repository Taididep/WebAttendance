<?php
session_start();
include __DIR__ . '/../../Connect/connect.php';
include __DIR__ . '/../../Account/islogin.php';

// Lấy class_id từ URL
$classId = isset($_GET['class_id']) ? $_GET['class_id'] : null;

// Kiểm tra nếu có schedule_id trong URL
if (isset($_GET['schedule_id'])) {
    $scheduleId = $_GET['schedule_id'];

    // Gọi thủ tục để xóa lịch học
    $sql = "CALL DeleteSchedule(?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$scheduleId]);

    // Sau khi xóa thành công, chuyển hướng về trang class_view.php
    header('Location: class_view.php?class_id=' . $classId);
    exit;
}
