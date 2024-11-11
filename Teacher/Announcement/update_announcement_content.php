<?php
include __DIR__ . '/../../Connect/connect.php';

if (isset($_POST['announcement_id']) && isset($_POST['content'])) {
    $announcement_id = $_POST['announcement_id'];
    $content = $_POST['content'];

    $sql = "UPDATE announcements SET content = ? WHERE announcement_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$content, $announcement_id]);
}
