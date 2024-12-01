<?php
ob_start(); // Bật bộ đệm đầu ra

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    $isLoggedIn = false;
} else {
    $isLoggedIn = true;
    // Lấy thông tin người dùng từ phiên
    $user_id = $_SESSION['user_id'];

    // Chuẩn bị câu lệnh SQL để lấy thông tin admin
    $sql = "SELECT * FROM admins WHERE admin_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$user_id]);

    // Lấy kết quả thông tin admin
    $adminData = $stmt->fetchObject();
    $stmt->closeCursor();  // Đóng kết quả của truy vấn trước

    if ($adminData) {
        $greeting = htmlspecialchars($adminData->lastname) . " " . htmlspecialchars($adminData->firstname);
    } else {
        $greeting = "Thông tin admin không tìm thấy.";
    }
}
?>

<nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
    <div class="container-fluid">
        <a href="<?php echo $basePath; ?>index.php" class="navbar-brand">
            <h2><i class="bi bi-journal-medical">TLT</i></h2>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $basePath; ?>index.php">Trang Chủ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $basePath; ?>Class/class_manage.php">Lớp Học</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $basePath; ?>Semester/semester_manage.php">Học Kỳ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $basePath; ?>Course/course_manage.php">Môn Học</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $basePath; ?>User/user_manage.php">Tài khoản</a>
                </li>
            </ul>

            <ul class="navbar-nav ms-auto">
                <?php if ($isLoggedIn): ?>
                    <li class="nav-item">
                        <div class="btn-group">
                            <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="bi bi-person-badge-fill"></i>
                                <?php echo $greeting; ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item"
                                        href="<?php echo $basePath; ?>Information/information.php">Thông tin cá nhân</a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item"
                                        href="<?php echo $basePath; ?>../Account/change-password.php">Đổi mật khẩu</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="<?php echo $basePath; ?>../Account/logout.php">Đăng
                                        xuất</a></li>
                            </ul>
                        </div>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="btn btn-primary" href="<?php echo $basePath; ?>login_view.php">Đăng nhập</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<style>
    nav .dropdown-toggle::after {
        display: none;
        /* Ẩn mũi tên mặc định của Bootstrap */
    }
</style>