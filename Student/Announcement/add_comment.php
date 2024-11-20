<?php
session_start();
include __DIR__ . '/../../Connect/connect.php';
include __DIR__ . '/../../Account/islogin.php';

// Kiểm tra nếu dữ liệu từ form đã được gửi lên
if (isset($_POST['content']) && isset($_POST['announcement_id'])) {
    $content = trim($_POST['content']); // Lấy dữ liệu từ form
    $announcement_id = $_POST['announcement_id'];
    $user_id = $_SESSION['user_id']; // Giả sử user_id đã được lưu trong session khi đăng nhập

    // Kiểm tra xem nội dung bình luận có rỗng không
    if (!empty($content)) {
        $sql = "INSERT INTO comments (announcement_id, user_id, content) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);

        try {
            // Thực thi câu truy vấn
            $stmt->execute([$announcement_id, $user_id, $content]);
            header("Location: class_detail.php?class_id=" . $_GET['class_id']); // Điều hướng về trang chi tiết lớp học
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
