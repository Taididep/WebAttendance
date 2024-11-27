<?php
include __DIR__ . '/../../Connect/connect.php';

if (isset($_POST['announcement_id']) && isset($_POST['title'])) {
    $announcement_id = $_POST['announcement_id'];
    $title = $_POST['title'];

    $stmt = $conn->prepare("CALL UpdateAnnouncementTitle(:announcement_id, :title)");
    $stmt->bindParam(':announcement_id', $announcement_id);
    $stmt->bindParam(':title', $title);

    try {
        // Execute the procedure
        $stmt->execute();
        echo "Tiêu đề thông báo đã được cập nhật thành công!";
    } catch (PDOException $e) {
        echo "Lỗi khi cập nhật thông báo: " . $e->getMessage();
    }
}
?>
