<?php
include __DIR__ . '/../../Connect/connect.php';

if (isset($_POST['announcement_id']) && isset($_POST['content'])) {
    $announcement_id = $_POST['announcement_id'];
    $content = $_POST['content'];

    $stmt = $conn->prepare("CALL UpdateAnnouncementContent(:announcement_id, :content)");
    $stmt->bindParam(':announcement_id', $announcement_id);
    $stmt->bindParam(':content', $content);

    try {
        // Execute the procedure
        $stmt->execute();
        echo "Nội dung thông báo đã được cập nhật thành công!";
    } catch (PDOException $e) {
        echo "Lỗi khi cập nhật thông báo: " . $e->getMessage();
    }
}
