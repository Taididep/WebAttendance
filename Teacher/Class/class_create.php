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
    $startDate = $_POST['start_date'];
    $startPeriod = $_POST['start_period'];
    $endPeriod = $_POST['end_period'];
    $createSchedule = isset($_POST['create_schedule']) ? true : false;

    // Kiểm tra thông tin
    if (empty($className) || empty($courseId) || empty($semesterId) || empty($startDate) || empty($startPeriod) || empty($endPeriod)) {
        $errorMessage = "Vui lòng điền đầy đủ thông tin.";
    } else {
        // Thực hiện truy vấn để thêm lớp học
        $sql = "INSERT INTO classes (class_name, course_id, semester_id, teacher_id) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt->execute([$className, $courseId, $semesterId, $teacherId])) {
            $successMessage = "Tạo lớp học thành công!";

            // Lấy class_id vừa mới được thêm vào
            $sqlGetClassId = "SELECT class_id FROM classes WHERE class_name = ? AND teacher_id = ? ORDER BY class_id DESC LIMIT 1";
            $stmtGetClassId = $conn->prepare($sqlGetClassId);
            $stmtGetClassId->execute([$className, $teacherId]);
            $classId = $stmtGetClassId->fetchColumn();

            // Kiểm tra nếu muốn tạo lịch học
            if ($createSchedule) {
                header("Location: {$basePath}Class/add_schedule.php?class_id={$classId}&start_date={$startDate}&start_period={$startPeriod}&end_period={$endPeriod}");
                exit();
            }
        } else {
            $errorMessage = "Có lỗi xảy ra, vui lòng thử lại.";
        }
    }
}

// Lấy danh sách khóa học và học kỳ
$courses = $conn->query("SELECT * FROM courses")->fetchAll(PDO::FETCH_ASSOC);
$sql_semesters = "CALL GetAllSemesters()";
$stmt_semesters = $conn->prepare($sql_semesters);
$stmt_semesters->execute();
$semesters = $stmt_semesters->fetchAll(PDO::FETCH_ASSOC);
$stmt_semesters->closeCursor();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tạo lớp học</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">
    <style>
        body {
            background: linear-gradient(to right, #e3f2fd, #bbdefb);
            font-family: 'Arial', sans-serif;
        }
        .container {
            max-width: 800px;
            margin-top: 50px;
            padding: 20px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }
        h2 {
            color: #007bff;
            font-weight: bold;
        }
        .alert {
            margin-top: 20px;
        }
        .btn {
            transition: background-color 0.3s, transform 0.3s;
        }
        .btn-primary {
            background-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }
        .btn-secondary {
            background-color: #6c757d;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
            transform: translateY(-2px);
        }
        .form-label {
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="container">
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
                <option value="" disabled selected>Chọn khóa học</option>
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
                <option value="" disabled selected>Chọn học kỳ</option>
                <?php foreach ($semesters as $semester): ?>
                    <option value="<?php echo $semester['semester_id']; ?>"><?php echo htmlspecialchars($semester['semester_name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="start_date" class="form-label">Ngày bắt đầu học</label>
            <input type="date" class="form-control" id="start_date" name="start_date" required>
        </div>

        <div class="mb-3">
            <label for="start_period" class="form-label">Tiết bắt đầu</label>
            <input type="number" class="form-control" id="start_period" name="start_period" required min="1" max="17" placeholder="Nhập tiết bắt đầu">
        </div>

        <div class="mb-3">
            <label for="end_period" class="form-label">Tiết kết thúc</label>
            <input type="number" class="form-control" id="end_period" name="end_period" required min="1" max="17" placeholder="Nhập tiết kết thúc">
        </div>

        <div class="form-check mb-3">
            <input type="checkbox" class="form-check-input" id="create_schedule" name="create_schedule">
            <label class="form-check-label" for="create_schedule">Tạo lịch học ngay sau khi tạo lớp</label>
        </div>

        <button type="submit" class="btn btn-primary">Tạo lớp học</button>
        <a href="<?php echo $basePath; ?>Class/class_manage.php" class="btn btn-secondary">Quay lại</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
