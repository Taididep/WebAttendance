<?php
session_start();
include __DIR__ . '/../../Connect/connect.php';
include __DIR__ . '/../../Account/islogin.php';

// Kiểm tra nếu có class_id trong URL
if (!isset($_GET['class_id'])) {
    header("Location: ../Class/class_manage.php");
    exit();
}

// Lấy user_id (teacher_id) từ session
$teacherId = $_SESSION['user_id'];


$classId = $_GET['class_id'];

// Kiểm tra nếu người dùng đã gửi form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy thông tin từ form
    $className = $_POST['class_name'];
    $courseId = $_POST['course_id'];
    $semesterId = $_POST['semester_id'];

    // Kiểm tra thông tin
    if (empty($className) || empty($courseId) || empty($semesterId)) {
        $errorMessage = "Vui lòng điền đầy đủ thông tin.";
        header("Location: class_edit.php?class_id={$classId}&error=" . urlencode($errorMessage));
        exit();
    }

    // Thực hiện truy vấn để cập nhật lớp học
    $sql = "UPDATE classes SET class_name = ?, course_id = ?, semester_id = ? WHERE class_id = ? AND teacher_id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt->execute([$className, $courseId, $semesterId, $classId, $teacherId])) {
        $successMessage = "Cập nhật lớp học thành công!";
        header("Location: ../Class/class_manage.php");
        exit();
    } else {
        $errorMessage = "Có lỗi xảy ra, vui lòng thử lại.";
        header("Location: class_edit.php?class_id={$classId}&error=" . urlencode($errorMessage));
        exit();
    }
}
