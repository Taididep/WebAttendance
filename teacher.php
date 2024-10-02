<?php
session_start();
include 'connect/connect.php';

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'teacher') {
    header("Location: index.php");
    exit;
}

// Thông tin người dùng
$username = $_SESSION['username'];
$role = $_SESSION['role'];

// Xử lý đăng xuất
if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: ../index.php");
    exit();
}

// Xử lý thêm lớp học
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_class'])) {
    $className = trim($_POST['class_name']);
    $courseName = trim($_POST['course_name']);
    $teacherId = $_SESSION['user_id'];
    $selectedSemesterId = $_POST['semester_id'] ?? null;

    // Kiểm tra xem môn học đã tồn tại chưa
    $courseSql = "SELECT id FROM courses WHERE name = ?";
    $courseStm = $conn->prepare($courseSql);
    $courseStm->execute([$courseName]);
    $course = $courseStm->fetch(PDO::FETCH_OBJ);

    if ($course) {
        $courseId = $course->id;
    } else {
        $insertCourseSql = "INSERT INTO courses (name) VALUES (?)";
        $insertCourseStm = $conn->prepare($insertCourseSql);
        if ($insertCourseStm->execute([$courseName])) {
            $courseId = $conn->lastInsertId();
        } else {
            $_SESSION['error'] = "Không thể thêm môn học vào cơ sở dữ liệu.";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    }

    // Thêm lớp vào cơ sở dữ liệu
    $insertSql = "INSERT INTO classes (name, course_id, teacher_id, semester_id) VALUES (?, ?, ?, ?)";
    $insertStm = $conn->prepare($insertSql);
    
    if ($insertStm->execute([$className, $courseId, $teacherId, $selectedSemesterId])) {
        $_SESSION['message'] = "Lớp học đã được thêm thành công.";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        $_SESSION['error'] = "Không thể thêm lớp học vào cơ sở dữ liệu.";
    }
}

// Xử lý xóa lớp học
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_classes'])) {
    $classIds = $_POST['class_ids'] ?? [];

    if (!empty($classIds)) {
        // Bắt đầu giao dịch
        $conn->beginTransaction();
        try {
            // Bước 1: Xóa các bản ghi attendance liên quan
            $deleteAttendanceSql = "DELETE FROM attendances WHERE class_id IN (" . implode(',', array_map('intval', $classIds)) . ")";
            $attendanceDeleted = $conn->exec($deleteAttendanceSql);
            if ($attendanceDeleted === false) {
                throw new Exception("Không xóa được bản ghi attendance.");
            }

            // Bước 2: Xóa các bản ghi class_students liên quan
            $deleteClassStudentsSql = "DELETE FROM class_students WHERE class_id IN (" . implode(',', array_map('intval', $classIds)) . ")";
            $classStudentsDeleted = $conn->exec($deleteClassStudentsSql);
            if ($classStudentsDeleted === false) {
                throw new Exception("Không xóa được bản ghi class_students.");
            }

            // Bước 3: Xóa lớp học
            $deleteClassesSql = "DELETE FROM classes WHERE id IN (" . implode(',', array_map('intval', $classIds)) . ")";
            $classesDeleted = $conn->exec($deleteClassesSql);
            if ($classesDeleted === false) {
                throw new Exception("Không xóa được bản ghi classes.");
            }

            // Commit giao dịch
            $conn->commit();
            $_SESSION['message'] = "Lớp học đã được xóa thành công.";
        } catch (Exception $e) {
            // Rollback nếu có lỗi
            $conn->rollBack();
            $_SESSION['error'] = "Không thể xóa lớp học: " . $e->getMessage();
        }
    } else {
        $_SESSION['error'] = "Không có lớp học nào được chọn để xóa.";
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Xử lý chỉnh sửa lớp học
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_class'])) {
    $classId = $_POST['class_id'];
    $className = trim($_POST['class_name']);
    $courseName = trim($_POST['course_name']);

    // Get course ID
    $courseSql = "SELECT id FROM courses WHERE name = ?";
    $courseStm = $conn->prepare($courseSql);
    $courseStm->execute([$courseName]);
    $course = $courseStm->fetch(PDO::FETCH_OBJ);

    if ($course) {
        $courseId = $course->id;
    } else {
        // Insert new course if not exists
        $insertCourseSql = "INSERT INTO courses (name) VALUES (?)";
        $insertCourseStm = $conn->prepare($insertCourseSql);
        if ($insertCourseStm->execute([$courseName])) {
            $courseId = $conn->lastInsertId();
        } else {
            $_SESSION['error'] = "Không thể thêm môn học vào cơ sở dữ liệu.";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    }

    // Update class
    $updateSql = "UPDATE classes SET name = ?, course_id = ? WHERE id = ?";
    $updateStm = $conn->prepare($updateSql);
    
    if ($updateStm->execute([$className, $courseId, $classId])) {
        $_SESSION['message'] = "Thông tin lớp học đã được cập nhật thành công.";
    } else {
        $_SESSION['error'] = "Không thể cập nhật thông tin lớp học.";
    }
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Xử lý chỉnh sửa thông tin cá nhân
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_profile'])) {
    $lastname = trim($_POST['lastname']);
    $firstname = trim($_POST['firstname']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $birthday = $_POST['birthday'];
    $gender = $_POST['gender'];

    $updateProfileSql = "UPDATE teachers SET lastname = ?, firstname = ?, email = ?, phone = ?, birthday = ?, gender = ? WHERE id = ?";
    $updateProfileStm = $conn->prepare($updateProfileSql);
    
    if ($updateProfileStm->execute([$lastname, $firstname, $email, $phone, $birthday, $gender, $_SESSION['user_id']])) {
        $_SESSION['message'] = "Thông tin cá nhân đã được cập nhật thành công.";
    } else {
        $_SESSION['error'] = "Không thể cập nhật thông tin cá nhân.";
    }
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Lấy thông tin người dùng
$username = $_SESSION['username'] ?? '';
$userSql = "SELECT id FROM users WHERE username = ?";
$userStm = $conn->prepare($userSql);
$userStm->execute([$username]);
$user = $userStm->fetch(PDO::FETCH_OBJ);

// Lấy thông tin giáo viên
if ($user) {
    $_SESSION['user_id'] = $user->id;
    $teacherSql = "SELECT lastname, firstname, email, phone, birthday, gender FROM teachers WHERE id = ?";
    $teacherStm = $conn->prepare($teacherSql);
    $teacherStm->execute([$user->id]);
    $teacherData = $teacherStm->fetch(PDO::FETCH_OBJ);
}

// Lấy danh sách học kỳ
$semesterSql = "SELECT * FROM semesters ORDER BY id DESC";
$semesterStm = $conn->prepare($semesterSql);
$semesterStm->execute();
$semesters = $semesterStm->fetchAll(PDO::FETCH_OBJ);

$selectedSemesterId = $_POST['semester_id'] ?? ($semesters[0]->id ?? null);

// Lấy danh sách các lớp học
$classesSql = "
    SELECT 
        c.id, 
        c.name AS class_name, 
        cr.name AS course_name,
        (SELECT COUNT(*) FROM class_students cs WHERE cs.class_id = c.id) AS student_count
    FROM 
        classes c
    JOIN 
        courses cr ON c.course_id = cr.id
    WHERE 
        c.teacher_id = ? 
        AND c.semester_id = ?
";
$classesStm = $conn->prepare($classesSql);
$classesStm->execute([$user->id, $selectedSemesterId]);
$classes = $classesStm->fetchAll(PDO::FETCH_OBJ);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard</title>
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
                    <?php foreach ($semesters as $semester): ?>
                        <option value="<?php echo htmlspecialchars($semester->id); ?>"
                            <?php if ($semester->id == $selectedSemesterId) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($semester->name . " (" . date('d/m/Y', strtotime($semester->start_date)) . " - " . date('d/m/Y', strtotime($semester->end_date)) . ")"); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>

        <?php if (!empty($teacherData)): ?>
            <h2 class='mb-4'>Xin chào, <?php echo htmlspecialchars($teacherData->lastname) . " " . htmlspecialchars($teacherData->firstname); ?></h2>
        <?php endif; ?>

        <div class="mb-4">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addClassModal">
                Thêm lớp học
            </button>
            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteClassModal">
                Xóa lớp học
            </button>
        </div>

        <!-- Modal thêm lớp học -->
        <div class="modal fade" id="addClassModal" tabindex="-1" aria-labelledby="addClassModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addClassModalLabel">Thêm lớp học</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="post">
                            <div class="form-group">
                                <label for="class_name">Tên lớp:</label>
                                <input type="text" name="class_name" id="class_name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="course_name">Tên môn học:</label>
                                <input type="text" name="course_name" id="course_name" class="form-control" required>
                            </div>
                            <input type="hidden" name="semester_id" value="<?php echo htmlspecialchars($selectedSemesterId); ?>">
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                                <button type="submit" name="add_class" class="btn btn-primary">Thêm</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

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
                                <label for="lastname">Họ:</label>
                                <input type="text" name="lastname" value="<?php echo htmlspecialchars($teacherData->lastname); ?>" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="firstname">Tên:</label>
                                <input type="text" name="firstname" value="<?php echo htmlspecialchars($teacherData->firstname); ?>" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email:</label>
                                <input type="email" name="email" value="<?php echo htmlspecialchars($teacherData->email); ?>" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="phone">Điện thoại:</label>
                                <input type="text" name="phone" value="<?php echo htmlspecialchars($teacherData->phone); ?>" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="birthday">Ngày sinh:</label>
                                <input type="date" name="birthday" value="<?php echo htmlspecialchars($teacherData->birthday); ?>" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="gender">Giới tính:</label>
                                <select name="gender" class="form-control">
                                    <option value="male" <?php if ($teacherData->gender === 'male') echo 'selected'; ?>>Nam</option>
                                    <option value="female" <?php if ($teacherData->gender === 'female') echo 'selected'; ?>>Nữ</option>
                                    <option value="other" <?php if ($teacherData->gender === 'other') echo 'selected'; ?>>Khác</option>
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

        <h3 class="mb-4">Danh sách các lớp học:</h3>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID lớp</th>
                    <th>Tên lớp</th>
                    <th>Tên Môn Học</th>
                    <th>Số lượng sinh viên</th>
                    <th>Hành động</th>
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
                                <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editClassModal<?php echo $class->id; ?>">
                                    Chỉnh sửa
                                </button>

                                <!-- Modal chỉnh sửa lớp học -->
                                <div class="modal fade" id="editClassModal<?php echo $class->id; ?>" tabindex="-1" aria-labelledby="editClassModalLabel<?php echo $class->id; ?>" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editClassModalLabel<?php echo $class->id; ?>">Chỉnh sửa lớp học</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span>&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="post">
                                                    <input type="hidden" name="class_id" value="<?php echo htmlspecialchars($class->id); ?>">
                                                    <div class="form-group">
                                                        <label for="class_name">Tên lớp:</label>
                                                        <input type="text" name="class_name" value="<?php echo htmlspecialchars($class->class_name); ?>" class="form-control" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="course_name">Tên môn học:</label>
                                                        <input type="text" name="course_name" value="<?php echo htmlspecialchars($class->course_name); ?>" class="form-control" required>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                                                        <button type="submit" name="edit_class" class="btn btn-primary">Cập nhật</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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

        <!-- Modal xác nhận xóa lớp học -->
        <div class="modal fade" id="deleteClassModal" tabindex="-1" aria-labelledby="deleteClassModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteClassModalLabel">Chọn lớp học để xóa</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span>&times;</span>
                        </button>
                    </div>

                    <!-- Display messages -->
                    <?php if (isset($_SESSION['message'])): ?>
                        <div class="message"><?= $_SESSION['message']; unset($_SESSION['message']); ?></div>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
                    <?php endif; ?>

                    <div class="modal-body">
                        <form method="post" id="deleteClassForm">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Chọn</th>
                                        <th>ID lớp</th>
                                        <th>Tên lớp</th>
                                        <th>Tên Môn Học</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($classes)): ?>
                                        <?php foreach ($classes as $class): ?>
                                            <tr>
                                                <td>
                                                    <input type="checkbox" name="class_ids[]" value="<?php echo htmlspecialchars($class->id); ?>">
                                                </td>
                                                <td><?php echo htmlspecialchars($class->id); ?></td>
                                                <td><?php echo htmlspecialchars($class->class_name); ?></td>
                                                <td><?php echo htmlspecialchars($class->course_name); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center">Không có lớp học nào để xóa.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                                <button type="submit" name="delete_classes" class="btn btn-danger">Xóa đã chọn</button>
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