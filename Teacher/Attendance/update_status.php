<?php
session_start();
$basePath = '../';
include __DIR__ . '/../../Connect/connect.php';
include __DIR__ . '/../../Account/islogin.php';

if (!isset($_GET['class_id']) || !isset($_GET['schedule_id']) || !isset($_GET['status'])) {
    echo 'Thiếu thông tin.';
    exit;
}

$class_id = $_GET['class_id'];
$schedule_id = $_GET['schedule_id'];
$status = $_GET['status'];

// Cập nhật trạng thái cho buổi học
$updateSql = "CALL UpdateScheduleStatus(?, ?)";
$updateStmt = $conn->prepare($updateSql);
$updateStmt->execute([$schedule_id, $status]);
$updateStmt->closeCursor();


echo 'Cập nhật trạng thái thành công';
