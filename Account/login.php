<?php
include '../Connect/connect.php';
session_start();

// Lấy thông tin từ form
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// Kiểm tra nếu người dùng nhập đầy đủ thông tin
if (empty($username) || empty($password)) {
    $error = "Vui lòng nhập đầy đủ thông tin!";
    header("Location: ../login_view.php?error=" . urlencode($error));
    exit;
}

// Tạo biến để lưu thông tin người dùng
$userData = null;
$pageRedirect = '../login_view.php'; // Trang index

// Gọi thủ tục lưu trữ để lấy thông tin người dùng
$sql = "CALL GetUserInfoByUsername(?)";

// Chuẩn bị câu lệnh SQL và thực thi
$stmt = $conn->prepare($sql);
$stmt->execute([$username]);

// Lấy kết quả
$userData = $stmt->fetchObject();

// Xử lý kết quả đăng nhập
if ($userData === false) {
    // Trường hợp không tìm thấy người dùng
    $error = "Đăng nhập thất bại";
    header("Location: ../login_view.php?error=" . urlencode($error));
    exit;
} else {
    // Kiểm tra mật khẩu bằng password_verify() (giả sử bạn đã lưu mật khẩu dưới dạng hash)
    if (password_verify($password, $userData->password)) {
        // Lưu thông tin người dùng vào phiên
        $_SESSION['user_id'] = $userData->user_id; // Lưu user_id
        $_SESSION['username'] = $userData->username;
        $_SESSION['role'] = $userData->role_name;

        // Chuyển hướng theo vai trò người dùng
        if ($userData->role_name == 'admin') {
            $pageRedirect = '../Admin/index.php';
        } elseif ($userData->role_name == 'teacher') {
            $pageRedirect = '../Teacher/index.php';
        } elseif ($userData->role_name == 'student') {
            $pageRedirect = '../Student/index.php';
        }

        header("Location: $pageRedirect");
        exit;
    } else {
        // Trường hợp mật khẩu sai
        $error = "Sai mật khẩu";
        header("Location: ../login_view.php?error=" . urlencode($error));
        exit;
    }
}
?>
