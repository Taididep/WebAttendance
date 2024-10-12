<?php
session_start();
include '../Connect/connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $studentId = $_POST['student_id'];
    $classId = $_POST['class_id'];
    $semesterId = $_POST['semester_id'];

    // Gọi thủ tục lưu trữ để điểm danh
    $sql = "CALL MarkAttendance(?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt->execute([$studentId, $classId, $semesterId])) {
        echo '<div class="alert alert-success">Bạn đã điểm danh thành công!</div>';
    } else {
        echo '<div class="alert alert-danger">Điểm danh không thành công.</div>';
    }
    $stmt->closeCursor();
} else {
    echo '<div class="alert alert-danger">Yêu cầu không hợp lệ.</div>';
}
?>