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
        $stmt = $conn->prepare("CALL AddCourse(:course_id, :course_name, :course_type_id)");
        $stmt->bindParam(':course_id', $courseId);
        $stmt->bindParam(':course_name', $courseName);
        $stmt->bindParam(':course_type_id', $courseTypeId);
        
        try {
            // Execute the procedure
            if ($stmt->execute()) {
                echo "Thêm môn học thành công!";
            } else {
                echo "Có lỗi xảy ra, vui lòng thử lại.";
            }
        } catch (PDOException $e) {
            echo "Lỗi: " . $e->getMessage();
        }
    }
}
