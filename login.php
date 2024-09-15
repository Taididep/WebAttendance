<?php
include 'Connect/connect.php';
session_start();

// Lấy thông tin từ form
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$role = $_POST['role'] ?? '';

// Tạo biến để lưu thông tin người dùng
$userData = null;
$pageRedirect = 'index.php'; // Trang index

// Kiểm tra vai trò người dùng và thực hiện truy vấn tương ứng
if ($role == 'admin') {
    $sql = "SELECT * FROM admin WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$username, $password]);
    //$userData = $stmt->fetch(PDO::FETCH_OBJ);
    $userData = $stmt->fetchObject();
    $pageRedirect = 'Role/admin.php';

} elseif ($role == 'teacher') {
    $sql = "SELECT * FROM teacher WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$username, $password]);
    //$userData = $stmt->fetch(PDO::FETCH_OBJ);
    $userData = $stmt->fetchObject();
    $pageRedirect = 'Role/teacher.php';

} elseif ($role == 'student') {
    $sql = "SELECT * FROM student WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$username, $password]);
    //$userData = $stmt->fetch(PDO::FETCH_OBJ);
    $userData = $stmt->fetchObject();
    $pageRedirect = 'Role/student.php';
}

// Xử lý kết quả đăng nhập
if ($userData === false) {
    $error = "Đăng nhập thất bại";
    header("Location: index.php?error=" . urlencode($error));
    exit;
} else {
    $_SESSION['username'] = $username;
    $_SESSION['role'] = $role;
    header("Location: $pageRedirect");
    exit;
}
?>