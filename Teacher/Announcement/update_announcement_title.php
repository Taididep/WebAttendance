<?php
include __DIR__ . '/../../Connect/connect.php';

if (isset($_POST['announcement_id']) && isset($_POST['title'])) {
    $announcement_id = $_POST['announcement_id'];
    $title = $_POST['title'];

    $sql = "UPDATE announcements SET title = ? WHERE announcement_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$title, $announcement_id]);
}
?>
