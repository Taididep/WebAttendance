<?php
session_start();

// Đường dẫn gốc
$basePath = '../';
include __DIR__ . '/../../Connect/connect.php';
include __DIR__ . '/../../Account/islogin.php';

// Kiểm tra nếu có tham số course_id trong URL
if (isset($_GET['course_id']) && is_numeric($_GET['course_id'])) {
    $courseId = $_GET['course_id'];

    // Thực hiện truy vấn xóa khóa học
    $stmt = $conn->prepare("CALL DeleteCourse(:course_id)");
    $stmt->bindParam(':course_id', $courseId);

    // Liên kết giá trị và thực hiện truy vấn
    $stmt->bindParam(':course_id', $courseId, PDO::PARAM_INT);

    if ($stmt->execute()) {
        // Chuyển hướng sau khi xóa thành công
        header("Location: {$basePath}Course/course_manage.php?message=delete_success");
        exit();
    } else {
        // Nếu có lỗi, thông báo
        header("Location: {$basePath}Course/course_manage.php?message=delete_error");
        exit();
    }
} else {
    // Nếu không có course_id hợp lệ, chuyển hướng về trang quản lý
    header("Location: {$basePath}Course/course_manage.php?message=invalid_id");
    exit();
}
