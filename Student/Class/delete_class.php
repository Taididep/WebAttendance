<?php
session_start();

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Bạn không có quyền truy cập.']);
    exit;
}

// Kết nối đến cơ sở dữ liệu
include __DIR__ . '/../../Connect/connect.php';
include __DIR__ . '/../../Account/islogin.php';

// Kiểm tra xem có gửi class_id qua GET không
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['class_id'])) {
    $classId = $_GET['class_id'];
    $studentId = $_SESSION['user_id']; // Giả sử ID sinh viên được lưu trong session

    if (!empty($classId)) {
        try {
            // Cập nhật trạng thái lớp học thành 0
            $sql = "CALL UpdateStudentClassStatusToInactive(?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$classId, $studentId]);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                header("Location: class_manage.php?message=Cập nhật trạng thái lớp thành công.");
                exit;
            } else {
                header("Location: class_manage.php?message=Lớp không tồn tại hoặc bạn chưa đăng ký lớp này.");
                exit;
            }
        } catch (Exception $e) {
            header("Location: class_manage.php?message=" . urlencode($e->getMessage()));
            exit;
        }
    } else {
        header("Location: class_manage.php?message=ID lớp không hợp lệ.");
        exit;
    }
} else {
    header("Location: class_manage.php?message=Yêu cầu không hợp lệ.");
    exit;
}
