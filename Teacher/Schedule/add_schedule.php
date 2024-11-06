<?php
session_start();
$basePath = '../'; // Đường dẫn gốc
include __DIR__ . '/../../Connect/connect.php';
include __DIR__ . '/../../Account/islogin.php';

// Nhận giá trị từ POST
$classId = isset($_POST['class_id']) ? $_POST['class_id'] : null;
$dates = isset($_POST['dates']) ? $_POST['dates'] : [];
$startTimes = isset($_POST['start_time']) ? $_POST['start_time'] : [];
$endTimes = isset($_POST['end_time']) ? $_POST['end_time'] : [];

$errorMessage = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $classId) {
    if (empty($dates) || empty($startTimes) || empty($endTimes)) {
        $errorMessage = "Vui lòng điền đầy đủ thông tin.";
    } else {
        // Chuyển đổi mảng thành JSON để truyền vào thủ tục
        $datesJson = json_encode($dates);
        $startTimesJson = json_encode($startTimes);
        $endTimesJson = json_encode($endTimes);

        // Gọi thủ tục thêm lịch học
        $sql_schedule = "CALL AddSchedules(?, ?, ?, ?)";
        $stmt_schedule = $conn->prepare($sql_schedule);
        $stmt_schedule->execute([$classId, $datesJson, $startTimesJson, $endTimesJson]);

        if ($stmt_schedule->rowCount() > 0) {
            header("Location: {$basePath}Class/class_manage.php");
            exit();
        } else {
            $errorMessage = "Đã xảy ra lỗi khi thêm lịch học.";
        }
    }
}
