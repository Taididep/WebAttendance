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

// Xử lý cập nhật thông tin sinh viên
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_student'])) {
    $lastname = trim($_POST['lastname']);
    $firstname = trim($_POST['firstname']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $birthday = $_POST['birthday'];
    $gender = $_POST['gender'];

    // Cập nhật thông tin sinh viên
    if (updateStudentProfile($conn, $studentId, $lastname, $firstname, $email, $phone, $birthday, $gender)) {
        $_SESSION['message'] = "Thông tin cá nhân đã được cập nhật.";
        header("Location: student.php");
        exit();
    } else {
        $_SESSION['error'] = "Không thể cập nhật thông tin.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa thông tin cá nhân</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Chỉnh sửa thông tin cá nhân</h1>

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

        <form method="post">
            <div class="form-group">
                <label for="lastname">Họ:</label>
                <input type="text" name="lastname" id="lastname" value="<?php echo htmlspecialchars($student->lastname); ?>" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="firstname">Tên:</label>
                <input type="text" name="firstname" id="firstname" value="<?php echo htmlspecialchars($student->firstname); ?>" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($student->email); ?>" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="phone">Số điện thoại:</label>
                <input type="text" name="phone" id="phone" value="<?php echo htmlspecialchars($student->phone); ?>" class="form-control">
            </div>

            <div class="form-group">
                <label for="birthday">Ngày sinh:</label>
                <input type="date" name="birthday" id="birthday" value="<?php echo htmlspecialchars($student->birthday); ?>" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="gender">Giới tính:</label>
                <select name="gender" id="gender" class="form-control" required>
                    <option value="Nam" <?php if ($student->gender == 'Nam') echo 'selected'; ?>>Nam</option>
                    <option value="Nữ" <?php if ($student->gender == 'Nữ') echo 'selected'; ?>>Nữ</option>
                </select>
            </div>

            <button type="submit" name="update_student" class="btn btn-primary">Cập nhật</button>
            <a href="student.php" class="btn btn-secondary">Quay lại</a>
        </form>
    </div>
</body>
</html>
