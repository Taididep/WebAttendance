<?php
    session_start();
    session_unset(); // Xóa tất cả các biến session
    session_destroy(); // Hủy session hiện tại
    header("Location: ../login_view.php"); // Chuyển hướng về trang đăng nhập
    exit;
?>

