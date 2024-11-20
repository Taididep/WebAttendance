<?php
include __DIR__ . '/../../Connect/connect.php';
include __DIR__ . '/../../Account/islogin.php';

if (isset($_GET['schedule_id'])) {
    $scheduleId = $_GET['schedule_id'];

    $sql = "DELETE FROM schedules WHERE schedule_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$scheduleId]);

    header('Location: class_view.php?class_id=' . $_GET['class_id']);
    exit;
}
