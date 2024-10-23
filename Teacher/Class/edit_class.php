<?php
session_start();
$basePath = '../'; // Đường dẫn gốc
include __DIR__ . '/../../Connect/connect.php';
include __DIR__ . '/../../LayoutPages/navbar.php';
include __DIR__ . '/../../Account/islogin.php';

// Lấy user_id (teacher_id) từ session
$teacherId = $_SESSION['user_id']; // Giả sử bạn đã lưu user_id vào session sau khi đăng nhập

// Kiểm tra nếu có class_id trong URL
if (!isset($_GET['class_id'])) {
    header("Location: {$basePath}Class/class_manage.php");
    exit();
}

// Lấy thông tin lớp học
$classId = $_GET['class_id'];
$classQuery = $conn->prepare("SELECT * FROM classes WHERE class_id = ? AND teacher_id = ?");
$classQuery->execute([$classId, $teacherId]);
$class = $classQuery->fetch(PDO::FETCH_ASSOC);

if (!$class) {
    header("Location: {$basePath}Class/class_manage.php");
    exit();
}

// Kiểm tra nếu người dùng đã gửi form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy thông tin từ form
    $className = $_POST['class_name'];
    $courseId = $_POST['course_id'];
    $semesterId = $_POST['semester_id'];

    // Kiểm tra thông tin
    if (empty($className) || empty($courseId) || empty($semesterId)) {
        $errorMessage = "Vui lòng điền đầy đủ thông tin.";
    } else {
        // Thực hiện truy vấn để cập nhật lớp học
        $sql = "UPDATE classes SET class_name = ?, course_id = ?, semester_id = ? WHERE class_id = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt->execute([$className, $courseId, $semesterId, $classId])) {
            $successMessage = "Cập nhật lớp học thành công!";
            header("Location: {$basePath}Class/class_manage.php");
            exit();
        } else {
            $errorMessage = "Có lỗi xảy ra, vui lòng thử lại.";
        }
    }
}

// Lấy danh sách khóa học và học kỳ để hiển thị trong form
$courses = $conn->query("SELECT * FROM courses")->fetchAll(PDO::FETCH_ASSOC);
$semesters = $conn->query("SELECT * FROM semesters")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa lớp học</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Chỉnh sửa lớp học</h2>
    <?php if (isset($errorMessage)): ?>
        <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
    <?php elseif (isset($successMessage)): ?>
        <div class="alert alert-success"><?php echo $successMessage; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="class_name" class="form-label">Tên lớp học</label>
            <input type="text" class="form-control" id="class_name" name="class_name" value="<?php echo htmlspecialchars($class['class_name']); ?>" required readonly>
        </div>
        <div class="mb-3">
            <label for="course_id" class="form-label">Khóa học</label>
            <select class="form-select" id="course_id" name="course_id" required disabled>
                <option value="">Chọn khóa học</option>
                <?php foreach ($courses as $course): ?>
                    <option value="<?php echo $course['course_id']; ?>" <?php echo $course['course_id'] == $class['course_id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($course['course_id']) . ' - ' . htmlspecialchars($course['course_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="semester_id" class="form-label">Học kỳ</label>
            <select class="form-select" id="semester_id" name="semester_id" required disabled>
                <option value="">Chọn học kỳ</option>
                <?php foreach ($semesters as $semester): ?>
                    <option value="<?php echo $semester['semester_id']; ?>" <?php echo $semester['semester_id'] == $class['semester_id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($semester['semester_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <button type="button" class="btn btn-warning" id="editBtn">Chỉnh sửa</button>
        <button type="submit" class="btn btn-primary" style="display: none;" id="updateBtn">Cập nhật lớp học</button>
        <a href="<?php echo $basePath; ?>Class/class_manage.php" class="btn btn-secondary">Quay lại</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('editBtn').addEventListener('click', function() {
        document.getElementById('class_name').readOnly = false;
        document.getElementById('course_id').disabled = false;
        document.getElementById('semester_id').disabled = false;
        document.getElementById('updateBtn').style.display = 'inline-block'; // Hiển thị nút cập nhật
        this.style.display = 'none'; // Ẩn nút chỉnh sửa
    });
</script>
</body>
</html>