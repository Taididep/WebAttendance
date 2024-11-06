<?php
session_start();
$basePath = '../'; // Đường dẫn gốc
include __DIR__ . '/../../Connect/connect.php';
include __DIR__ . '/../../LayoutPages/navbar.php';
include __DIR__ . '/../../Account/islogin.php';

// Kiểm tra nếu người dùng đã gửi form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy thông tin từ form
    $semesterName = $_POST['semester_name'];
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];
    $isActive = isset($_POST['is_active']) ? 1 : 0; // Kiểm tra trạng thái hoạt động

    // Kiểm tra thông tin
    if (empty($semesterName) || empty($startDate) || empty($endDate)) {
        $errorMessage = "Vui lòng điền đầy đủ thông tin.";
    } else {
        // Thực hiện truy vấn để thêm học kỳ
        $sql = "INSERT INTO semesters (semester_name, start_date, end_date, is_active) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt->execute([$semesterName, $startDate, $endDate, $isActive])) {
            $successMessage = "Thêm học kỳ thành công!";
            header("Location: {$basePath}Class/class_manage.php"); // Chuyển hướng sau khi thêm thành công
            exit();
        } else {
            $errorMessage = "Có lỗi xảy ra, vui lòng thử lại.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm học kỳ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Thêm học kỳ mới</h2>
    <?php if (isset($errorMessage)): ?>
        <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
    <?php elseif (isset($successMessage)): ?>
        <div class="alert alert-success"><?php echo $successMessage; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="semester_name" class="form-label">Tên học kỳ</label>
            <input type="text" class="form-control" id="semester_name" name="semester_name" required>
        </div>

        <div class="mb-3">
            <label for="start_date" class="form-label">Ngày bắt đầu</label>
            <input type="date" class="form-control" id="start_date" name="start_date" required>
        </div>

        <div class="mb-3">
            <label for="end_date" class="form-label">Ngày kết thúc</label>
            <input type="date" class="form-control" id="end_date" name="end_date" required>
        </div>

        <div class="mb-3">
            <label for="is_active" class="form-label">Trạng thái hoạt động</label>
            <select class="form-select" id="is_active" name="is_active">
                <option value="1" selected>Có</option>
                <option value="0">Không</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Thêm</button>
        <a href="<?php echo $basePath; ?>Class/class_manage.php" class="btn btn-secondary">Quay lại</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>