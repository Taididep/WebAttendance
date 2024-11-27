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
    $stmt = $conn->prepare("CALL GetCourseById(:course_id)");
    $stmt->bindParam(':course_id', $courseId, PDO::PARAM_INT);
    
    try {
        // Execute the procedure
        $stmt->execute();
        $course = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Lỗi khi lấy thông tin khóa học: " . $e->getMessage();
    }

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
        $stmt = $conn->prepare("CALL UpdateCourse(:course_name, :course_type_id, :course_id)");
        $stmt->bindParam(':course_name', $courseName);
        $stmt->bindParam(':course_type_id', $courseTypeId);
        $stmt->bindParam(':course_id', $courseId);
        
        try {
            // Execute the procedure
            if ($stmt->execute()) {
                echo "Cập nhật khóa học thành công";
            } else {
                echo "Có lỗi xảy ra, vui lòng thử lại.";
            }
        } catch (PDOException $e) {
            echo "Lỗi: " . $e->getMessage();
        }
    }
}
