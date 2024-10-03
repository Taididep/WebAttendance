<?php
// Kết nối và import các hàm từ student_function.php
include 'student_functions.php';
session_start();

// Xử lý đăng xuất
if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: ../index.php");
    exit();
}

// Lấy thông tin người dùng
$username = $_SESSION['username'] ?? null;
if (!$username) {
    header("Location: ../login.php");
    exit();
}

// Lấy ID người dùng dựa trên tên đăng nhập
$user = getUserByUsername($conn, $username);
if ($user) {
    // Lấy thông tin sinh viên dựa trên ID người dùng
    $studentData = getStudentById($conn, $user->id);
}

// Lấy tất cả học kỳ đang hoạt động
$semesters = getActiveSemesters($conn);

// Lấy ID học kỳ đã chọn từ form
$selectedSemesterId = $_POST['semester_id'] ?? null;

// Lấy lớp học cho học kỳ đã chọn
$classes = [];
if ($selectedSemesterId) {
    $classes = getClassesBySemesterId($conn, $selectedSemesterId);
}

// Xử lý chỉnh sửa thông tin cá nhân
if (isset($_POST['edit_profile'])) {
    $lastname = $_POST['lastname'];
    $firstname = $_POST['firstname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $birthday = $_POST['birthday'];
    $gender = $_POST['gender'];

    if (updateStudentProfile($conn, $user->id, $lastname, $firstname, $email, $phone, $birthday, $gender)) {
        $_SESSION['message'] = 'Thông tin cá nhân đã được cập nhật!';
    } else {
        $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật thông tin.';
    }
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <header class="mb-4 bg-success text-white p-3">
            <div class="d-flex justify-content-between align-items-center">
                <h1>Dashboard</h1>
                <div>
                    <button type="button" class="btn btn-warning" onclick="window.location.href='student_detail.php?id=<?php echo $user->id; ?>'">
                        Thông tin cá nhân
                    </button>
                    <form method="post" style="display:inline;">
                        <button type="submit" name="logout" class="btn btn-danger">Đăng xuất</button>
                    </form>
                </div>
            </div>
        </header>

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

        <form method="post" class="mb-4">
            <div class="form-group">
                <label for="semester_id">Chọn học kỳ:</label>
                <select name="semester_id" id="semester_id" class="form-control" onchange="this.form.submit()">
                    <option value="">-- Chọn học kỳ --</option>
                    <?php foreach ($semesters as $semester): ?>
                        <option value="<?php echo htmlspecialchars($semester->id); ?>"
                            <?php if ($semester->id == $selectedSemesterId) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($semester->name . " (" . date('d/m/Y', strtotime($semester->start_date)) . " - " . date('d/m/Y', strtotime($semester->end_date)) . ")"); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>

        <?php if (!empty($studentData)): ?>
            <h2 class='mb-4'>Xin chào, <?php echo htmlspecialchars($studentData->lastname) . " " . htmlspecialchars($studentData->firstname); ?></h2>
            <p>Mã số sinh viên: <strong><?php echo htmlspecialchars($studentData->id); ?></strong></p>
        <?php endif; ?>

        <!-- Các lớp học sẽ hiển thị dưới đây nếu có -->
        <!-- (Tùy chỉnh theo yêu cầu của bạn) -->

    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
