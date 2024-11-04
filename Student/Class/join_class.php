<?php
session_start();
include __DIR__ . '/../../Connect/connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: {$basePath}login_view.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Đây là ID lớp mà sinh viên muốn tham gia, có thể lấy từ URL hoặc nhập từ một form khác
$class_id = $_POST['class_id'] ?? '';

if ($class_id === '') {
    echo "Vui lòng nhập mã lớp học.";
    exit();
}

try {
    $conn->beginTransaction();

    // Kiểm tra xem class_id có tồn tại không
    $stmt = $conn->prepare("SELECT class_id FROM classes WHERE class_id = ?");
    $stmt->execute([$class_id]);
    $class = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$class) {
        echo "Mã lớp không tồn tại.";
        exit();
    }

    // Kiểm tra xem sinh viên đã tham gia lớp học này chưa
    $stmt = $conn->prepare("SELECT * FROM class_students WHERE class_id = ? AND student_id = ?");
    $stmt->execute([$class_id, $user_id]);

    if ($stmt->rowCount() > 0) {
        echo "Bạn đã tham gia lớp học này rồi.";
        exit();
    }

    // Thêm bản ghi mới vào bảng class_students
    $stmt = $conn->prepare("INSERT INTO class_students (class_id, student_id) VALUES (?, ?)");
    $stmt->execute([$class_id, $user_id]);

    $conn->commit();

    echo "Tham gia lớp học thành công.";
} catch (Exception $e) {
    $conn->rollBack();
    echo "Lỗi: " . $e->getMessage();
}
