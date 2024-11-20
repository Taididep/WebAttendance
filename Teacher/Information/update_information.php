<?php
session_start();
include __DIR__ . '/../../Connect/connect.php';
include __DIR__ . '/../../Account/islogin.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $teacher_id = $_POST['teacher_id'];
    $lastname = $_POST['lastname'];
    $firstname = $_POST['firstname'];
    $birthday = $_POST['birthday'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    // Cập nhật thông tin giáo viên
    $sql = "UPDATE teachers SET lastname = ?, firstname = ?, birthday = ?, gender = ?, email = ?, phone = ? WHERE teacher_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$lastname, $firstname, $birthday, $gender, $email, $phone, $teacher_id]);
    
    // Chuyển hướng về trang giáo viên sau khi cập nhật
    header("Location: information.php");
    exit;
}
?>