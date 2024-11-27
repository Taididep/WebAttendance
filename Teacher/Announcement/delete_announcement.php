<?php
include __DIR__ . '/../../Connect/connect.php';

// Kiểm tra nếu có announcement_id và class_id
if (isset($_GET['announcement_id']) && isset($_GET['class_id'])) {
    $announcement_id = $_GET['announcement_id'];
    $class_id = $_GET['class_id'];

    // Truy vấn xóa thông báo
    $stmt = $conn->prepare("CALL DeleteAnnouncement(:announcement_id, :class_id)");
    $stmt->bindParam(':announcement_id', $announcement_id);
    $stmt->bindParam(':class_id', $class_id);
    $stmt->closeCursor();

    if ($stmt->execute([$announcement_id, $class_id])) {
        // Sau khi xóa thành công, chuyển hướng về trang chi tiết lớp học
        header("Location: class_detail_announcement.php?class_id=" . $class_id);
        exit();
    } else {
        echo "Xóa thông báo thất bại. Vui lòng thử lại.";
    }
} else {
    echo 'Không tìm thấy thông báo để xóa.';
    exit();
}
