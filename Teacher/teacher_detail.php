<?php
session_start();
include '../connect/connect.php';

// Kiểm tra nếu người dùng đã đăng nhập
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'teacher') {
    header("Location: ../index.php");
    exit;
}

// Lấy thông tin giáo viên từ URL
$teacherId = $_GET['id'] ?? null;

if (!$teacherId) {
    $_SESSION['error'] = "Không tìm thấy giáo viên.";
    header("Location: teacher.php");
    exit;
}

// Lấy thông tin giáo viên từ cơ sở dữ liệu
$sql = "SELECT * FROM teachers WHERE id = ?";
$stm = $conn->prepare($sql);
$stm->execute([$teacherId]);
$teacher = $stm->fetch(PDO::FETCH_OBJ);

if (!$teacher) {
    $_SESSION['error'] = "Không tìm thấy giáo viên.";
    header("Location: teacher.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin cá nhân giáo viên</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Thông tin cá nhân giáo viên</h1>

        <!-- Hiển thị thông báo lỗi hoặc thành công -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <!-- Hiển thị thông tin giáo viên dạng ngang -->
        <div class="row">
            <div class="col-md-4 font-weight-bold">Họ:</div>
            <div class="col-md-8"><?php echo htmlspecialchars($teacher->lastname); ?></div>
        </div>

        <div class="row">
            <div class="col-md-4 font-weight-bold">Tên:</div>
            <div class="col-md-8"><?php echo htmlspecialchars($teacher->firstname); ?></div>
        </div>

        <div class="row">
            <div class="col-md-4 font-weight-bold">Email:</div>
            <div class="col-md-8"><?php echo htmlspecialchars($teacher->email); ?></div>
        </div>

        <div class="row">
            <div class="col-md-4 font-weight-bold">Số điện thoại:</div>
            <div class="col-md-8"><?php echo htmlspecialchars($teacher->phone); ?></div>
        </div>

        <div class="row">
            <div class="col-md-4 font-weight-bold">Ngày sinh:</div>
            <div class="col-md-8"><?php echo htmlspecialchars($teacher->birthday); ?></div>
        </div>

        <div class="row">
            <div class="col-md-4 font-weight-bold">Giới tính:</div>
            <div class="col-md-8"><?php echo htmlspecialchars($teacher->gender); ?></div>
        </div>

        <div class="mt-3">
            <a href="teacher.php" class="btn btn-secondary">Quay lại</a>
            <a href="teacher_edit.php?id=<?php echo $teacherId; ?>" class="btn btn-primary">Chỉnh sửa thông tin cá nhân</a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
