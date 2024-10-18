<?php
// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    $isLoggedIn = false;
} else {
    $isLoggedIn = true;
    // Lấy thông tin người dùng từ phiên
    $user_id = $_SESSION['user_id'];

    // include __DIR__ . '/../Connect/connect.php';

    // Chuẩn bị câu lệnh SQL để lấy thông tin giáo viên
    $sql = "SELECT * FROM teachers WHERE teacher_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$user_id]);

    // Lấy kết quả thông tin giáo viên
    $teacherData = $stmt->fetchObject();
    $stmt->closeCursor();  // Đóng kết quả của truy vấn trước

    if ($teacherData) {
        $greeting = htmlspecialchars($teacherData->lastname) . " " . htmlspecialchars($teacherData->firstname);
    } else {
        $greeting = "Thông tin giáo viên không tìm thấy.";
    }
}
?>

<nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
    <div class="container-fluid">
        <a href="<?php echo $basePath; ?>index.php" class="navbar-brand"><h2><i class="bi bi-journal-medical">TLT</i></h2></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
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
            </ul>

            <ul class="navbar-nav ms-auto">
                <!-- Nút Thêm Lớp -->
                <li class="nav-item">
                    <a class="btn btn-success" href="<?php echo $basePath; ?>Class/class_create.php" style="margin-right: 10px;">Tạo lớp học</a>
                </li>

                <?php if ($isLoggedIn): ?>
                    <li class="nav-item">
                        <div class="btn-group">
                            <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-badge-fill"></i>
                                <?php echo $greeting; ?>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?php echo $basePath; ?>Information/information.php">Thông tin cá nhân</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?php echo $basePath; ?>../Account/logout.php">Đăng xuất</a></li>
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
        display: none; /* Ẩn mũi tên mặc định của Bootstrap */
    }
</style>
