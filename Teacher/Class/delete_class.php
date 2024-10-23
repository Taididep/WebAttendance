<?php
    session_start();

    // Kiểm tra xem người dùng đã đăng nhập chưa
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Bạn không có quyền truy cập.']);
        exit;
    }

    // Kết nối đến cơ sở dữ liệu
    include __DIR__ . '/../../Connect/connect.php';

    // Kiểm tra xem có gửi class_id qua GET không
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['class_id'])) {
        $classId = $_GET['class_id'];

        if (!empty($classId)) {
            try {
                // Xóa lớp học
                $sql = "DELETE FROM classes WHERE class_id = :class_id";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':class_id', $classId);
                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Lớp không tồn tại hoặc đã được xóa.']);
                }
                exit;
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                exit;
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'ID lớp không hợp lệ.']);
            exit;
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Yêu cầu không hợp lệ.']);
        exit;
    }
?>