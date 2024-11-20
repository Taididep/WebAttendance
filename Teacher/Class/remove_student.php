<?php
$basePath = '../'; // Đường dẫn gốc
include __DIR__ . '/../../Connect/connect.php';
include __DIR__ . '/../../Account/islogin.php';

// Kiểm tra xem student_id và class_id có được gửi qua POST hay không
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['student_id']) && isset($_POST['class_id'])) {
    $student_id = $_POST['student_id'];
    $class_id = $_POST['class_id'];

    // Ghi log để kiểm tra
    error_log("Removing Student ID: $student_id from Class ID: $class_id"); // Ghi log ID

    // Xóa sinh viên khỏi lớp
    $sql = "DELETE FROM class_students WHERE class_id = ? AND student_id = ?";
    $stmt = $conn->prepare($sql);

    // Thực thi câu lệnh SQL
    if ($stmt->execute([$class_id, $student_id])) {
        echo json_encode(['success' => true, 'message' => 'Đã đá sinh viên ra khỏi lớp thành công.']);
    } else {
        $errorInfo = $stmt->errorInfo();
        error_log("SQL Error: " . implode(", ", $errorInfo)); // Ghi log lỗi SQL
        echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra: ' . $errorInfo[2]]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Thông tin không hợp lệ.']);
}
?>