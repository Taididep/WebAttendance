<?php
session_start();
include '../Connect/connect.php';

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

// Xử lý cập nhật thông tin giáo viên
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_teacher'])) {
    $lastname = trim($_POST['lastname']);
    $firstname = trim($_POST['firstname']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $birthday = $_POST['birthday'];
    $gender = $_POST['gender'];

    // Cập nhật thông tin giáo viên
    $updateSql = "UPDATE teachers SET lastname = ?, firstname = ?, email = ?, phone = ?, birthday = ?, gender = ? WHERE id = ?";
    $updateStm = $conn->prepare($updateSql);
    
    if ($updateStm->execute([$lastname, $firstname, $email, $phone, $birthday, $gender, $teacherId])) {
        $_SESSION['message'] = "Thông tin cá nhân đã được cập nhật.";
        header("Location: teacher.php");
        exit;
    } else {
        $_SESSION['error'] = "Không thể cập nhật thông tin.";
    }
}
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
        <h1>Chỉnh sửa thông tin cá nhân</h1>

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

        <form method="post">
            <div class="form-group">
                <label for="lastname">Họ:</label>
                <input type="text" name="lastname" id="lastname" value="<?php echo htmlspecialchars($teacher->lastname); ?>" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="firstname">Tên:</label>
                <input type="text" name="firstname" id="firstname" value="<?php echo htmlspecialchars($teacher->firstname); ?>" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($teacher->email); ?>" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="phone">Số điện thoại:</label>
                <input type="text" name="phone" id="phone" value="<?php echo htmlspecialchars($teacher->phone); ?>" class="form-control">
            </div>

            <div class="form-group">
                <label for="birthday">Ngày sinh:</label>
                <input type="date" name="birthday" id="birthday" value="<?php echo htmlspecialchars($teacher->birthday); ?>" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="gender">Giới tính:</label>
                <select name="gender" id="gender" class="form-control" required>
                    <option value="Nam" <?php if ($teacher->gender == 'Nam') echo 'selected'; ?>>Nam</option>
                    <option value="Nữ" <?php if ($teacher->gender == 'Nữ') echo 'selected'; ?>>Nữ</option>
                </select>
            </div>

            <button type="submit" name="update_teacher" class="btn btn-primary">Cập nhật</button>
            <a href="teacher.php" class="btn btn-secondary">Quay lại</a>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
