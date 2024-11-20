<?php
session_start();
include __DIR__ . '/../../Connect/connect.php';
include __DIR__ . '/../../Account/islogin.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['schedule_id']) || !isset($_POST['time'])) {
        echo 'Thông tin không hợp lệ.';
        exit;
    }

    $schedule_id = $_POST['schedule_id'];
    $time = $_POST['time'];

    $sql = "SELECT date FROM schedules WHERE schedule_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$schedule_id]);
    $scheduleData = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$scheduleData) {
        echo 'Không tìm thấy buổi học.';
        exit;
    }

    $date = new DateTime($scheduleData['date']);
    $date->setTime(...explode(':', $time));

    $updateSql = "UPDATE schedules SET date = ? WHERE schedule_id = ?";
    $updateStmt = $conn->prepare($updateSql);
    if ($updateStmt->execute([$date->format('Y-m-d H:i:s'), $schedule_id])) {
        echo 'Thời gian đã được cập nhật thành công!';
    } else {
        echo 'Có lỗi xảy ra khi cập nhật thời gian.';
    }
}
?>