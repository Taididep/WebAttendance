<?php
session_start();
include __DIR__ . '/../../Connect/connect.php';
include __DIR__ . '/../../Account/islogin.php';

// Kiểm tra xem class_id và schedule_id có được gửi qua URL hay không
if (!isset($_GET['class_id']) || !isset($_GET['schedule_id'])) {
    echo 'Không tìm thấy thông tin lớp học hoặc buổi học.';
    exit;
}

// Lấy class_id và schedule_id từ URL
$class_id = $_GET['class_id'];
$schedule_id = $_GET['schedule_id'];

// Đặt lại status thành 0 cho buổi học
$updateSql = "UPDATE schedules SET status = 0 WHERE schedule_id = ?";
$updateStmt = $conn->prepare($updateSql);
$updateStmt->execute([$schedule_id]);

// Chuyển hướng người dùng trở lại trang chi tiết lớp
header("Location: ../Class/class_detail_list.php?class_id=" . urlencode($class_id));
exit;
