<?php
session_start();
$basePath = '../'; // Base path
include __DIR__ . '../../../Account/islogin.php';
include __DIR__ . '/../../Connect/connect.php';
include __DIR__ . '/../../LayoutPages/navbar_student.php';

// Check if class_id is sent through URL
if (!isset($_GET['class_id'])) {
    echo 'Không tìm thấy thông tin lớp học.';
    exit;
}

// Get class_id from URL
$class_id = $_GET['class_id'];

// Get user_id from session
$student_id = $_SESSION['user_id'];

// Query to get class details from the database
$sql = "CALL GetClassDetailsById(?)";
$stmt = $conn->prepare($sql);
$stmt->execute([$class_id]);

// Fetch the result
$classData = $stmt->fetch(PDO::FETCH_ASSOC);
$stmt->closeCursor();

// Check if result exists
if (!$classData) {
    echo 'Không tìm thấy thông tin lớp học.';
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin chi tiết lớp học</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../Css/class_detail.css">
</head>

<body>
    <div class="side-tabs">
        <ul class="nav nav-tabs flex-column" id="tabMenu">
            <li class="nav-item">
                <a class="nav-link" id="news-tab"
                    href="class_detail_announcement.php?class_id=<?php echo htmlspecialchars($class_id); ?>">Bảng
                    tin</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" id="attendance-tab"
                    href="class_detail_list.php?class_id=<?php echo htmlspecialchars($class_id); ?>">Danh sách</a>
            </li>
        </ul>
    </div>

    <div class="container mt-5">
        <div class="card classroom-card shadow-lg">
            <div class="card-body">
                <h2 data-bs-toggle="modal" data-bs-target="#classModal">
                    <?php echo htmlspecialchars($classData['class_name']); ?>
                </h2>
                <hr>
                <div>
                    <h5><?php echo htmlspecialchars($classData['semester_name']); ?></h5>
                    <h5><?php echo htmlspecialchars($classData['course_name']); ?></h5>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-5 mb-5">
        <div class="tab-content mt-3">
            <div id="attendanceList">
                <?php include '../Attendance/attendance_list.php'; ?>
            </div>
        </div>
    </div>

    <script src="../JavaScript/class_detail.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>


</body>

</html>