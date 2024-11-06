<?php
session_start();
include __DIR__ . '/../../Connect/connect.php';

// Kiểm tra nếu người dùng chưa đăng nhập hoặc không có quyền
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Kiểm tra nếu có dữ liệu gửi lên từ form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $class_id = $_POST['class_id'];
    $title = $_POST['title'];
    $content = $_POST['content'];

    // Kiểm tra các giá trị nhập vào
    if (empty($title) || empty($content)) {
        echo 'Tiêu đề và nội dung không được để trống.';
        exit;
    }

    // Truy vấn để lưu thông báo vào cơ sở dữ liệu
    $sql = "INSERT INTO announcements (class_id, title, content) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$class_id, $title, $content]);

    // Kiểm tra xem có lỗi không
    if ($stmt->rowCount() > 0) {
        header("Location: class_detail.php?class_id=" . $class_id); // Chuyển hướng lại trang chi tiết lớp học
        exit;
    } else {
        echo 'Có lỗi khi tạo bảng tin.';
    }
}
