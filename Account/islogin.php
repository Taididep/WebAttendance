<?php
if (!isset($_SESSION['user_id'])) {
    // Chuyển hướng đến trang đăng nhập nếu chưa đăng nhập
    header("Location: ../login_view.php");
    exit;
}
?>
