<?php
    include 'connect/connect.php';
    session_start();

    // Lấy thông tin từ form
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Tạo biến để lưu thông tin người dùng
    $userData = null;
    $pageRedirect = 'index.php'; // Trang index

    // Tạo truy vấn để lấy thông tin người dùng
    $sql = "SELECT * FROM users WHERE username = ? AND password = ?";

    // Chuẩn bị câu lệnh SQL và thực thi
    $stmt = $conn->prepare($sql);
    $stmt->execute([$username, $password]);

    // Lấy kết quả
    $userData = $stmt->fetchObject();

    // Xử lý kết quả đăng nhập
    if ($userData === false) {
        $error = "Đăng nhập thất bại";
        header("Location: index.php?error=" . urlencode($error));
        exit;
    } else {
        // Lưu thông tin người dùng vào phiên
        $_SESSION['username'] = $userData->username;
        $_SESSION['role'] = $userData->role;

        // Chuyển hướng theo vai trò người dùng
        if ($userData->role == 'admin') {
            $pageRedirect = 'admin.php';
        } elseif ($userData->role == 'teacher') {
            $pageRedirect = 'Teacher/teacher.php';
        } elseif ($userData->role == 'student') {
            $pageRedirect = 'Student/student.php';
        }

        header("Location: $pageRedirect");
        exit;
    }
?>