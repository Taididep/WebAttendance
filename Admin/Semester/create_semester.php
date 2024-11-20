<?php
session_start();

$basePath = '../'; // Đường dẫn gốc
include __DIR__ . '/../../Connect/connect.php';
include __DIR__ . '/../../LayoutPages/navbar.php';
include __DIR__ . '/../../Account/islogin.php';

// Kiểm tra nếu người dùng đã gửi form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy thông tin từ form
    $semesterName = $_POST['semester_name'];
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];
    $isActive = isset($_POST['is_active']) ? 1 : 0; // Kiểm tra trạng thái hoạt động

    // Kiểm tra thông tin
    if (empty($semesterName) || empty($startDate) || empty($endDate)) {
        $errorMessage = "Vui lòng điền đầy đủ thông tin.";
    } else {
        // Thực hiện truy vấn để thêm học kỳ
        $sql = "INSERT INTO semesters (semester_name, start_date, end_date, is_active) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt->execute([$semesterName, $startDate, $endDate, $isActive])) {
            $successMessage = "Thêm học kỳ thành công!";
            header("Location: {$basePath}Class/class_manage.php"); // Chuyển hướng sau khi thêm thành công
            exit();
        } else {
            $errorMessage = "Có lỗi xảy ra, vui lòng thử lại.";
        }
    }
}
?>