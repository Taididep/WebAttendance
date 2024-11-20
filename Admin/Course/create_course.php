<?php
session_start();
include __DIR__ . '/../../Connect/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy thông tin từ form
    $courseId = $_POST['course_id'];
    $courseName = $_POST['course_name'];
    $courseTypeId = $_POST['course_type_id'];

    // Kiểm tra thông tin
    if (empty($courseId) || empty($courseName) || empty($courseTypeId)) {
        echo "Vui lòng điền đầy đủ thông tin.";
    } else {
        // Thực hiện truy vấn để thêm môn học
        $sql = "INSERT INTO courses (course_id, course_name, course_type_id) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt->execute([$courseId, $courseName, $courseTypeId])) {
            echo "Thêm môn học thành công!";
        } else {
            echo "Có lỗi xảy ra, vui lòng thử lại.";
        }
    }
}
