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

    // Gọi thủ tục
    $sql = "CALL UpdateTeacherById(?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$teacher_id, $lastname, $firstname, $birthday, $gender, $email, $phone]);

    // Chuyển hướng về trang thông tin giáo viên sau khi cập nhật
    header("Location: information.php");
    exit;
}
