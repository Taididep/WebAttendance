<?php
session_start();
$basePath = '../'; // Đường dẫn gốc
include __DIR__ . '/../../Connect/connect.php';
include __DIR__ . '/../../LayoutPages/navbar.php';
include __DIR__ . '/../../../Account/islogin.php';

// Kiểm tra xem người dùng có phải là quản trị viên không
if ($_SESSION['role'] !== 'admin') {
    header("Location: {$basePath}Class/class_manage.php");
    exit();
}

// Kiểm tra nếu có class_id trong URL
if (!isset($_GET['class_id'])) {
    header("Location: {$basePath}Class/class_manage.php");
    exit();
}

// Lấy thông tin lớp học
$classId = $_GET['class_id'];
$classQuery = $conn->prepare("CALL GetClassById(?)"); // Lấy lớp học theo ID
$classQuery->execute([$classId]);
$class = $classQuery->fetch(PDO::FETCH_ASSOC);
$classQuery->closeCursor();

if (!$class) {
    header("Location: {$basePath}Class/class_manage.php");
    exit();
}

// Lấy danh sách khóa học và học kỳ để hiển thị trong form
$courses = $conn->query("CALL GetAllCourses()")->fetchAll(PDO::FETCH_ASSOC);
$semesters = $conn->query("CALL GetAllSemesters()")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa lớp học</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Chỉnh sửa lớp học</h2>

    <form method="POST" action="edit_class.php?class_id=<?php echo $classId; ?>">
        <div class="mb-3">
            <label for="class_name" class="form-label">Tên lớp học</label>
            <input type="text" class="form-control" id="class_name" name="class_name" value="<?php echo isset($class['class_name']) ? htmlspecialchars($class['class_name']) : ''; ?>" required>
        </div>
        <div class="mb-3">
            <label for="course_id" class="form-label">Khóa học</label>
            <select class="form-select" id="course_id" name="course_id" required>
                <option value="">Chọn khóa học</option>
                <?php foreach ($courses as $course): ?>
                    <option value="<?php echo $course['course_id']; ?>" <?php echo $course['course_id'] == $class['course_id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($course['course_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="semester_id" class="form-label">Học kỳ</label>
            <select class="form-select" id="semester_id" name="semester_id" required>
                <option value="">Chọn học kỳ</option>
                <?php foreach ($semesters as $semester): ?>
                    <option value="<?php echo $semester['semester_id']; ?>" <?php echo $semester['semester_id'] == $class['semester_id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($semester['semester_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Cập nhật lớp học</button>
        <a href="<?php echo $basePath; ?>Class/class_manage.php" class="btn btn-secondary">Quay lại</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>