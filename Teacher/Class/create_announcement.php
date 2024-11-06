<?php
session_start();
include __DIR__ . '/../../Connect/connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Nhận giá trị từ form
    $class_id = $_POST['class_id'];
    $title = $_POST['title'];
    $content = $_POST['content'];

    // Kiểm tra dữ liệu
    if (empty($title) || empty($content)) {
        echo 'Tiêu đề và nội dung không được để trống.';
        exit;
    }

    // Lưu thông báo vào cơ sở dữ liệu
    $sql = "INSERT INTO announcements (class_id, title, content) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$class_id, $title, $content]);

    if ($stmt->rowCount() > 0) {
        header("Location: class_detail.php?class_id=" . $class_id); // Chuyển hướng về trang chi tiết lớp học
        exit;
    } else {
        echo 'Có lỗi khi tạo bảng tin.';
    }
}
