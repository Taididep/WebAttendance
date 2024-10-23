<?php
session_start();
$basePath = '../'; // Đường dẫn gốc
include __DIR__ . '/../../Connect/connect.php';
include __DIR__ . '/../../LayoutPages/navbar.php';
include __DIR__ . '/../../Account/islogin.php';

// Lấy user_id (teacher_id) từ session
$teacherId = $_SESSION['user_id'];

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
        // Thực hiện truy vấn để thêm lớp học
        $sql = "INSERT INTO classes (class_name, course_id, semester_id, teacher_id) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt->execute([$className, $courseId, $semesterId, $teacherId])) {
            $successMessage = "Tạo lớp học thành công!";
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
    <title>Tạo lớp học</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Tạo lớp học mới</h2>
    <hr>
    <?php if (isset($errorMessage)): ?>
        <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
    <?php elseif (isset($successMessage)): ?>
        <div class="alert alert-success"><?php echo $successMessage; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="class_name" class="form-label">Tên lớp học</label>
            <input type="text" class="form-control" id="class_name" name="class_name" required>
        </div>
        <div class="mb-3">
            <label for="course_id" class="form-label">Khóa học</label>
            <select class="form-select" id="course_id" name="course_id" required>
                <option value="">Chọn khóa học</option>
                <?php foreach ($courses as $course): ?>
                    <option value="<?php echo $course['course_id']; ?>">
                        <?php echo htmlspecialchars($course['course_id']) . ' - ' . htmlspecialchars($course['course_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="semester_id" class="form-label">Học kỳ</label>
            <select class="form-select" id="semester_id" name="semester_id" required>
                <option value="">Chọn học kỳ</option>
                <?php foreach ($semesters as $semester): ?>
                    <option value="<?php echo $semester['semester_id']; ?>"><?php echo htmlspecialchars($semester['semester_name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Tạo lớp học</button>
        <a href="<?php echo $basePath; ?>Class/class_manage.php" class="btn btn-secondary">Quay lại</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>