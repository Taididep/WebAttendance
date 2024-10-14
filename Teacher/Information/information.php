<?php
session_start();

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login_view.php");
    exit;
}

// Lấy thông tin người dùng từ phiên
$user_id = $_SESSION['user_id'];

// Kết nối đến cơ sở dữ liệu để lấy thông tin chi tiết về giáo viên
include '../../Connect/connect.php';

// Chuẩn bị câu lệnh SQL để lấy thông tin giáo viên
$sql = "SELECT * FROM teachers WHERE teacher_id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$user_id]);

// Lấy kết quả thông tin giáo viên
$teacherData = $stmt->fetchObject();
$stmt->closeCursor();

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa thông tin cá nhân</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Chỉnh sửa thông tin cá nhân</h2>
    <form id="editForm" method="POST" action="update_teacher.php">
        <input type="hidden" name="teacher_id" value="<?php echo htmlspecialchars($teacherData->teacher_id); ?>">
        <div class="mb-3">
            <label for="lastname" class="form-label">Họ</label>
            <input type="text" class="form-control" id="lastname" name="lastname" value="<?php echo htmlspecialchars($teacherData->lastname); ?>" required>
        </div>
        <div class="mb-3">
            <label for="firstname" class="form-label">Tên</label>
            <input type="text" class="form-control" id="firstname" name="firstname" value="<?php echo htmlspecialchars($teacherData->firstname); ?>" required>
        </div>
        <div class="mb-3">
            <label for="birthday" class="form-label">Ngày sinh</label>
            <input type="date" class="form-control" id="birthday" name="birthday" value="<?php echo htmlspecialchars($teacherData->birthday); ?>" required>
        </div>
        <div class="mb-3">
            <label for="gender" class="form-label">Giới tính</label>
            <select class="form-select" id="gender" name="gender" required>
                <option value="Nam" <?php echo ($teacherData->gender == 'Nam') ? 'selected' : ''; ?>>Nam</option>
                <option value="Nữ" <?php echo ($teacherData->gender == 'Nữ') ? 'selected' : ''; ?>>Nữ</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($teacherData->email); ?>" required>
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">Điện thoại</label>
            <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($teacherData->phone); ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Cập nhật</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
