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
    $sql = "UPDATE students SET lastname = ?, firstname = ?, birthday = ?, gender = ?, email = ?, phone = ? WHERE student_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$lastname, $firstname, $birthday, $gender, $email, $phone, $student_id]);
    
    // Chuyển hướng về trang thông tin sau khi cập nhật
    header("Location: information.php");
    exit;
}
?>
