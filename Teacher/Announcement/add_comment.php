<?php
session_start();
include __DIR__ . '/../../Connect/connect.php';

// Kiểm tra nếu dữ liệu từ form đã được gửi lên
if (isset($_POST['content']) && isset($_POST['announcement_id'])) {
    $content = trim($_POST['content']); // Lấy dữ liệu từ form
    $announcement_id = $_POST['announcement_id'];
    $user_id = $_SESSION['user_id']; // Giả sử user_id đã được lưu trong session khi đăng nhập

    // Kiểm tra xem nội dung bình luận có rỗng không
    if (!empty($content)) {
        $stmt = $conn->prepare("CALL AddComment(:announcement_id, :user_id, :content)");
        $stmt->bindParam(':announcement_id', $announcement_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':content', $content);

        try {
            // Thực thi câu truy vấn
            $stmt->execute([$announcement_id, $user_id, $content]);
            header("Location: class_detail_announcement.php?class_id=" . $_GET['class_id']); // Điều hướng về trang chi tiết lớp học
            exit();
        } catch (PDOException $e) {
            echo "Lỗi khi thêm bình luận: " . $e->getMessage();
        }
    } else {
        echo "Bình luận không được để trống.";
    }
} else {
    echo "Thiếu dữ liệu bình luận.";
}
