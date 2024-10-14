<?php
session_start();
include '../Connect/connect.php'; // Kết nối cơ sở dữ liệu

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_id = $_POST['class_id'];

    // Xóa lớp học
    $deleteSql = "DELETE FROM classes WHERE class_id = ?";
    $deleteStmt = $conn->prepare($deleteSql);
    
    if ($deleteStmt->execute([$class_id])) {
        echo json_encode(['message' => 'Lớp học đã được xóa thành công.']);
    } else {
        echo json_encode(['message' => 'Có lỗi xảy ra khi xóa lớp học.']);
    }
}
?>