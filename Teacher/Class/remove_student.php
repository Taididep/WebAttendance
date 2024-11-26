<?php
$basePath = '../'; // Đường dẫn gốc
include __DIR__ . '/../../Connect/connect.php';

// Kiểm tra xem student_id và class_id có được gửi qua POST hay không
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['student_id']) && isset($_POST['class_id'])) {
    $student_id = $_POST['student_id'];
    $class_id = $_POST['class_id'];

    // Ghi log để kiểm tra
    error_log("Removing Student ID: $student_id from Class ID: $class_id"); // Ghi log ID

    // Gọi thủ tục lưu trữ
    $sql = "CALL RemoveStudentFromClass(?, ?)";
    $stmt = $conn->prepare($sql);

    // Thực thi thủ tục
    if ($stmt->execute([$class_id, $student_id])) {
        echo json_encode(['success' => true, 'message' => 'Đã xóa sinh viên ra khỏi lớp thành công.']);
    } else {
        $errorInfo = $stmt->errorInfo();
        error_log("SQL Error: " . implode(", ", $errorInfo)); // Ghi log lỗi SQL
        echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra: ' . $errorInfo[2]]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Thông tin không hợp lệ.']);
}
