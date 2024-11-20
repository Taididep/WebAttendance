<?php
ob_start(); // Bật bộ đệm đầu ra

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    // Chuyển hướng đến trang đăng nhập nếu chưa đăng nhập
    header("Location: /../login_view.php");
    exit;
}

// Lấy vai trò hiện tại từ session
$currentRole = $_SESSION['role'];

// Lấy URL hiện tại
$pageUrl = $_SERVER['REQUEST_URI'];

// Kiểm tra vai trò phù hợp với trang
if (strpos($pageUrl, '/Teacher/') !== false && $currentRole !== 'teacher') {
    // Nếu là trang Teacher nhưng vai trò không phải Teacher
    header("Location: /../login_view.php");
    exit;
} elseif (strpos($pageUrl, '/Student/') !== false && $currentRole !== 'student') {
    // Nếu là trang Student nhưng vai trò không phải Student
    header("Location: /../login_view.php");
    exit;
} elseif (strpos($pageUrl, '/Admin/') !== false && $currentRole !== 'admin') {
    // Nếu là trang Admin nhưng vai trò không phải Admin
    header("Location: /../login_view.php");
    exit;
}