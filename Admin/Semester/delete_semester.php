<?php
session_start();

// Kết nối cơ sở dữ liệu
$basePath = '../'; // Đường dẫn gốc
include __DIR__ . '/../../Connect/connect.php';
include __DIR__ . '/../../Account/islogin.php';

// Kiểm tra nếu có tham số semester_id trong URL
if (isset($_GET['semester_id']) && is_numeric($_GET['semester_id'])) {
    $semesterId = $_GET['semester_id'];

    // Thực hiện truy vấn xóa học kỳ
    $stmt = $conn->prepare("CALL DeleteSemester(:semester_id)");

    // Liên kết giá trị và thực hiện truy vấn
    $stmt->bindParam(':semester_id', $semesterId, PDO::PARAM_INT);

    if ($stmt->execute()) {
        // Chuyển hướng sau khi xóa thành công
        header("Location: {$basePath}Semester/semester_manage.php?message=delete_success");
        exit();
    } else {
        // Nếu có lỗi, thông báo
        header("Location: {$basePath}Semester/semester_manage.php?message=delete_error");
        exit();
    }
} else {
    // Nếu không có semester_id hợp lệ, chuyển hướng về trang quản lý
    header("Location: {$basePath}Semester/semester_manage.php?message=invalid_id");
    exit();
}
