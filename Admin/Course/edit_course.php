<?php
session_start();

// Kết nối cơ sở dữ liệu
$basePath = '../'; // Đường dẫn gốc
include __DIR__ . '/../../Connect/connect.php';
include __DIR__ . '/../../Account/islogin.php';

// Kiểm tra nếu có tham số course_id trong URL
if (isset($_GET['course_id']) && is_numeric($_GET['course_id'])) {
    $courseId = $_GET['course_id'];

    // Lấy thông tin khóa học từ cơ sở dữ liệu
    $sql = "SELECT * FROM courses WHERE course_id = :course_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':course_id', $courseId, PDO::PARAM_INT);
    $stmt->execute();
    $course = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$course) {
        // Nếu không tìm thấy khóa học, chuyển hướng về trang quản lý khóa học
        header("Location: {$basePath}Course/course_manage.php?message=not_found");
        exit();
    }
} else {
    // Nếu không có course_id hợp lệ, chuyển hướng về trang quản lý khóa học
    header("Location: {$basePath}Course/course_manage.php?message=invalid_id");
    exit();
}

// Kiểm tra nếu form được gửi đi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu từ form
    $courseId = $_GET['course_id']; // Nhận course_id từ URL
    $courseName = $_POST['course_name'];
    $courseTypeId = $_POST['course_type_id'];

    // Kiểm tra thông tin
    if (empty($courseName) || empty($courseTypeId)) {
        echo "Vui lòng điền đầy đủ thông tin.";
    } else {
        // Cập nhật dữ liệu khóa học
        $sql = "UPDATE courses SET course_name = ?, course_type_id = ? WHERE course_id = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt->execute([$courseName, $courseTypeId, $courseId])) {
            echo "Cập nhật khóa học thành công";
        } else {
            echo "Có lỗi xảy ra, vui lòng thử lại.";
        }
    }
}
