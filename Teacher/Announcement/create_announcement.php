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
    $stmt = $conn->prepare("CALL AddAnnouncement(:class_id, :title, :content)");
    $stmt->bindParam(':class_id', $class_id);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':content', $content);
    $stmt->closeCursor();     

    if ($stmt->rowCount() > 0) {
        header("Location: Announcement/class_detail_announcement.php?class_id=" . $class_id); // Chuyển hướng về trang chi tiết lớp học
        exit;
    } else {
        echo 'Có lỗi khi tạo bảng tin.';
    }
}
