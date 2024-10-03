<?php
session_start();
include 'student_functions.php'; // Import các hàm từ student_function.php

// Kiểm tra nếu người dùng đã đăng nhập
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Lấy ID sinh viên từ URL
$studentId = $_GET['id'] ?? null;

if (!$studentId) {
    $_SESSION['error'] = "Không tìm thấy sinh viên.";
    header("Location: student.php");
    exit();
}

// Lấy thông tin sinh viên từ cơ sở dữ liệu
$student = getStudentById($conn, $studentId);

if (!$student) {
    $_SESSION['error'] = "Không tìm thấy sinh viên.";
    header("Location: student.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin cá nhân sinh viên</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Thông tin cá nhân sinh viên</h1>

        <!-- Hiển thị thông báo -->
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

        <!-- Hiển thị thông tin sinh viên dạng ngang -->
        <div class="row">
            <div class="col-md-4 font-weight-bold">Họ:</div>
            <div class="col-md-8"><?php echo htmlspecialchars($student->lastname); ?></div>
        </div>

        <div class="row">
            <div class="col-md-4 font-weight-bold">Tên:</div>
            <div class="col-md-8"><?php echo htmlspecialchars($student->firstname); ?></div>
        </div>

        <div class="row">
            <div class="col-md-4 font-weight-bold">Email:</div>
            <div class="col-md-8"><?php echo htmlspecialchars($student->email); ?></div>
        </div>

        <div class="row">
            <div class="col-md-4 font-weight-bold">Số điện thoại:</div>
            <div class="col-md-8"><?php echo htmlspecialchars($student->phone); ?></div>
        </div>

        <div class="row">
            <div class="col-md-4 font-weight-bold">Ngày sinh:</div>
            <div class="col-md-8"><?php echo htmlspecialchars($student->birthday); ?></div>
        </div>

        <div class="row">
            <div class="col-md-4 font-weight-bold">Giới tính:</div>
            <div class="col-md-8"><?php echo htmlspecialchars($student->gender); ?></div>
        </div>

        <div class="mt-3">
            <a href="student.php" class="btn btn-secondary">Quay lại</a>
            <a href="student_edit.php?id=<?php echo $studentId; ?>" class="btn btn-primary">Chỉnh sửa thông tin cá nhân</a>
        </div>
    </div>
</body>
</html>
