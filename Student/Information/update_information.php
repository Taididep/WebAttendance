<?php
session_start();
include __DIR__ . '/../../Connect/connect.php';
include __DIR__ . '/../../Account/islogin.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_POST['student_id'];
    $lastname = $_POST['lastname'];
    $firstname = $_POST['firstname'];
    $birthday = $_POST['birthday'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    // Cập nhật thông tin sinh viên
    $sql = "CALL UpdateStudentInfo(?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$student_id, $lastname, $firstname, $birthday, $gender, $email, $phone]);
    
    // Chuyển hướng về trang thông tin sau khi cập nhật
    header("Location: information.php");
    exit;
}
?>
