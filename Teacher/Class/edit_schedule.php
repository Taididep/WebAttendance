<?php
include __DIR__ . '/../../Connect/connect.php';
include __DIR__ . '/../../Account/islogin.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $scheduleId = $_POST['schedule_id'];
    $date = $_POST['date'];
    $startTime = $_POST['start_time'];
    $endTime = $_POST['end_time'];
    $classId = $_POST['class_id'];

    $sql = "UPDATE schedules SET date = ?, start_time = ?, end_time = ? WHERE schedule_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$date, $startTime, $endTime, $scheduleId]);

    header('Location: class_view.php?class_id=' . $classId);
    exit;
}
