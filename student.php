<?php
// Kết nối cơ sở dữ liệu
include 'connect/connect.php';
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
$userSql = "SELECT id FROM users WHERE username = ?";
$userStm = $conn->prepare($userSql);
$userStm->execute([$username]);
$user = $userStm->fetch(PDO::FETCH_OBJ);

if ($user) {
    // Lấy thông tin sinh viên dựa trên ID người dùng
    $studentSql = "SELECT id, lastname, firstname, email, phone, birthday, gender FROM students WHERE id = ?";
    $studentStm = $conn->prepare($studentSql);
    $studentStm->execute([$user->id]);
    $studentData = $studentStm->fetch(PDO::FETCH_OBJ);
}

// Lấy tất cả học kỳ đang hoạt động
$semesterSql = "SELECT * FROM semesters WHERE is_active = 1";
$semesterStm = $conn->prepare($semesterSql);
$semesterStm->execute();
$semesters = $semesterStm->fetchAll(PDO::FETCH_OBJ);

// Lấy ID học kỳ đã chọn từ form
$selectedSemesterId = $_POST['semester_id'] ?? null;

// Lấy lớp học cho học kỳ đã chọn
if ($selectedSemesterId) {
    $classSql = "SELECT c.id, c.name AS class_name, co.name AS course_name, c.student_count
                  FROM classes c
                  JOIN courses co ON c.course_id = co.id
                  WHERE c.semester_id = ?";
    $classStm = $conn->prepare($classSql);
    $classStm->execute([$selectedSemesterId]);
    $classes = $classStm->fetchAll(PDO::FETCH_OBJ);
} else {
    $classes = [];
}

// Xử lý chỉnh sửa thông tin cá nhân
if (isset($_POST['edit_profile'])) {
    $lastname = $_POST['lastname'];
    $firstname = $_POST['firstname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $birthday = $_POST['birthday'];
    $gender = $_POST['gender'];

    $updateSql = "UPDATE students SET lastname = ?, firstname = ?, email = ?, phone = ?, birthday = ?, gender = ? WHERE id = ?";
    $updateStm = $conn->prepare($updateSql);
    $updateStm->execute([$lastname, $firstname, $email, $phone, $birthday, $gender, $user->id]);

    $_SESSION['message'] = 'Thông tin cá nhân đã được cập nhật!';
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
                    <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#editProfileModal">
                        Chỉnh sửa thông tin
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

        <h3 class="mb-4">Danh sách các lớp học:</h3>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID lớp</th>
                    <th>Tên lớp</th>
                    <th>Tên Môn Học</th>
                    <th>Số lượng sinh viên</th>
                    <th>Điểm danh</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($classes)): ?>
                    <?php foreach ($classes as $class): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($class->id); ?></td>
                            <td><?php echo htmlspecialchars($class->class_name); ?></td>
                            <td><?php echo htmlspecialchars($class->course_name); ?></td>
                            <td><?php echo htmlspecialchars($class->student_count . '/50'); ?></td>
                            <td>
                                <a href="attendance_list.php?class_id=<?php echo htmlspecialchars($class->id); ?>" class="btn btn-info btn-sm">Điểm danh</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">Không có lớp học nào trong học kỳ này.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Modal chỉnh sửa thông tin cá nhân -->
        <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editProfileModalLabel">Chỉnh sửa thông tin cá nhân</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="post">
                            <div class="form-group">
                                <label for="student_id">Mã số sinh viên:</label>
                                <input type="text" name="student_id" value="<?php echo htmlspecialchars($studentData->id ?? ''); ?>" class="form-control" readonly>
                            </div>
                            <div class="form-group">
                                <label for="lastname">Họ:</label>
                                <input type="text" name="lastname" value="<?php echo htmlspecialchars($studentData->lastname ?? ''); ?>" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="firstname">Tên:</label>
                                <input type="text" name="firstname" value="<?php echo htmlspecialchars($studentData->firstname ?? ''); ?>" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email:</label>
                                <input type="email" name="email" value="<?php echo htmlspecialchars($studentData->email ?? ''); ?>" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="phone">Điện thoại:</label>
                                <input type="text" name="phone" value="<?php echo htmlspecialchars($studentData->phone ?? ''); ?>" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="birthday">Ngày sinh:</label>
                                <input type="date" name="birthday" value="<?php echo htmlspecialchars($studentData->birthday ?? ''); ?>" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="gender">Giới tính:</label>
                                <select name="gender" class="form-control">
                                    <option value="Nam" <?php if ($studentData->gender === 'Nam') echo 'selected'; ?>>Nam</option>
                                    <option value="Nữ" <?php if ($studentData->gender === 'Nữ') echo 'selected'; ?>>Nữ</option>
                                    <option value="Khác" <?php if ($studentData->gender === 'Khác') echo 'selected'; ?>>Khác</option>
                                </select>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                                <button type="submit" name="edit_profile" class="btn btn-primary">Cập nhật</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>