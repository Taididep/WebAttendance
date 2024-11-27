<?php
session_start();
include __DIR__ . '/../../Connect/connect.php';
include __DIR__ . '/../../Account/islogin.php';

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
    $sql = "CALL GetClassById(?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$class_id]);
    $class = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt->closeCursor();  // Close the cursor to allow the next query

    if (!$class) {
        echo "Mã lớp không tồn tại.";
        exit();
    }

    // Kiểm tra xem sinh viên đã tham gia lớp học này chưa
    $sql = "CALL GetStudentClassStatus(?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$class_id, $user_id]);
    $studentClass = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt->closeCursor();  // Close the cursor to allow the next query

    if ($studentClass) {
        if ($studentClass['status'] == 1) {
            echo "Bạn đã tham gia lớp này rồi.";
        } else {
            // Nếu chưa kích hoạt, cập nhật status thành 1
            $sql = "CALL UpdateStudentClassStatus(?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$class_id, $user_id]);
            $stmt->closeCursor();  // Close the cursor to allow the next query
            
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
