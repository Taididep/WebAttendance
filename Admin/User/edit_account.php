<?php
session_start();

$basePath = '../'; // Đường dẫn gốc
include __DIR__ . '/../../Connect/connect.php';
include __DIR__ . '/../../LayoutPages/navbar_admin.php';
include __DIR__ . '/../../Account/islogin.php';

// Kiểm tra nếu có user_id trong URL
if (!isset($_GET['user_id'])) {
    header("Location: account_manage.php"); // Nếu không có user_id, chuyển hướng về trang quản lý tài khoản
    exit();
}

$user_id = $_GET['user_id'];

// Truy vấn SQL để lấy thông tin người dùng và vai trò hiện tại
$sql_user = "SELECT u.username, r.role_name, ur.role_id
             FROM users u
             JOIN user_roles ur ON u.user_id = ur.user_id
             JOIN roles r ON ur.role_id = r.role_id
             WHERE u.user_id = :user_id";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt_user->execute();
$user = $stmt_user->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: account_manage.php"); // Nếu không tìm thấy người dùng, chuyển hướng về trang quản lý tài khoản
    exit();
}

$stmt_user->closeCursor();

// Xử lý khi người dùng gửi form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_role = $_POST['role'];

    // Kiểm tra xem role_id có hợp lệ không
    $sql_check_role = "SELECT role_id FROM roles WHERE role_id = :role_id";
    $stmt_check_role = $conn->prepare($sql_check_role);
    $stmt_check_role->bindParam(':role_id', $new_role, PDO::PARAM_INT);
    $stmt_check_role->execute();

    if ($stmt_check_role->rowCount() == 0) {
        $_SESSION['error_message'] = "Vai trò không hợp lệ!";
        header("Location: edit_account.php?user_id=" . $user_id);
        exit();
    }

    // Cập nhật vai trò người dùng
    $sql_update = "UPDATE user_roles SET role_id = :role_id WHERE user_id = :user_id";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bindParam(':role_id', $new_role, PDO::PARAM_INT);
    $stmt_update->bindParam(':user_id', $user_id, PDO::PARAM_INT);

    if ($stmt_update->execute()) {
        $_SESSION['success_message'] = "Vai trò của tài khoản đã được cập nhật thành công!";
        header("Location: user_manage.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Có lỗi xảy ra khi cập nhật vai trò!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa tài khoản</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="container">
        <div class="card p-4">
            <h2 class="mb-4 text-center">Sửa tài khoản: <?php echo htmlspecialchars($user['username']); ?></h2>

            <!-- Hiển thị thông báo lỗi hoặc thành công -->
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success">
                    <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                </div>
            <?php elseif (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger">
                    <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                </div>
            <?php endif; ?>

            <!-- Form sửa tài khoản -->
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="username" class="form-label">Tên đăng nhập</label>
                    <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                </div>
                <div class="mb-3">
                    <label for="role" class="form-label">Vai trò</label>
                    <select class="form-select" id="role" name="role" required>
                        <option value="1" <?php echo $user['role_name'] == 'admin' ? 'selected' : ''; ?>>Quản trị viên</option>
                        <option value="2" <?php echo $user['role_name'] == 'teacher' ? 'selected' : ''; ?>>Giảng viên</option>
                        <option value="3" <?php echo $user['role_name'] == 'student' ? 'selected' : ''; ?>>Sinh viên</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Cập nhật vai trò</button>
                <a href="user_manage.php" class="btn btn-secondary ms-2">Quay lại</a>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>

</html>
