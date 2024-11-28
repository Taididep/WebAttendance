<?php
session_start();
include __DIR__ . '/../../Connect/connect.php';
include __DIR__ . '/../../Account/islogin.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $admin_id = $_POST['admin_id']; // Thay đổi biến từ student_id thành admin_id
    $lastname = $_POST['lastname'];
    $firstname = $_POST['firstname'];
    // Bỏ cột birthday vì nó không có trong bảng admins
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    // Cập nhật thông tin admin
    $sql = "CALL UpdateAdmin(?, ?, ?, ?, ?)"; // Thay đổi thủ tục gọi
    $stmt = $conn->prepare($sql);
    $stmt->execute([$admin_id, $lastname, $firstname, $email, $phone]);
    
    // Chuyển hướng về trang thông tin sau khi cập nhật
    header("Location: information.php");
    exit;
}
?>