<?php
session_start();
include __DIR__ . '/../../Connect/connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: {$basePath}login_view.php");
    exit();
}

$user_id = $_SESSION['user_id'];
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
    $stmt = $conn->prepare("SELECT status FROM class_students WHERE class_id = ? AND student_id = ?");
    $stmt->execute([$class_id, $user_id]);
    $studentClass = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($studentClass) {
        if ($studentClass['status'] == 1) {
            echo "Bạn đã tham gia lớp này rồi.";
        } else {
            // Nếu chưa kích hoạt, cập nhật status thành 1
            $stmt = $conn->prepare("UPDATE class_students SET status = 1 WHERE class_id = ? AND student_id = ?");
            $stmt->execute([$class_id, $user_id]);
            echo "Tham gia lớp học thành công.";
        }
    } else {
        echo "Bạn không có trong danh sách lớp";
    }

    $conn->commit();
} catch (Exception $e) {
    $conn->rollBack();
    echo "Lỗi: " . $e->getMessage();
}
