<!-- class_detail_list.php -->

<?php
session_start();
$basePath = '../';
include __DIR__ . '/../../Connect/connect.php';
include __DIR__ . '/../../LayoutPages/navbar.php';
include __DIR__ . '/../../Account/islogin.php';

if (!isset($_GET['class_id'])) {
    echo 'Không tìm thấy thông tin lớp học.';
    exit;
}

$class_id = $_GET['class_id'];

// Truy vấn chi tiết lớp học
$sql = "CALL GetClassDetailsById(?)";
$stmt = $conn->prepare($sql);
$stmt->execute([$class_id]);
$classData = $stmt->fetch(PDO::FETCH_ASSOC);
$stmt->closeCursor();

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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../Css/class_detail.css">
</head>

<body>
    <div class="side-tabs">
        <ul class="nav nav-tabs flex-column" id="tabMenu">
            <li class="nav-item">
                <a style="color: black;" class="nav-link" id="news-tab"
                    href="class_detail_announcement.php?class_id=<?php echo htmlspecialchars($class_id); ?>">Bảng
                    tin</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" id="attendance-tab"
                    href="class_detail_list.php?class_id=<?php echo htmlspecialchars($class_id); ?>">Danh sách</a>
            </li>
            <li class="nav-item">
                <a style="color: black;" class="nav-link" id="attendance-tab"
                    href="class_view.php?class_id=<?php echo htmlspecialchars($class_id); ?>">Lịch dạy</a>
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

    <!-- Modal hiển thị mã lớp học -->
    <div class="modal fade" id="classModal" tabindex="-1" aria-labelledby="classModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="classModalLabel">Mã lớp học</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php echo htmlspecialchars($classData['class_id']); ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-5 mb-5">
        <div class="tab-content mt-3">
            <div class="tab-pane fade show active" id="attendance" role="tabpanel" aria-labelledby="attendance-tab">
                <div id="attendanceList" style="display: inline;">
                    <?php include '../Attendance/attendance_list.php'; ?>
                </div>
                <div id="attendanceEdit" style="display: none;">
                    <?php include '../Attendance/attendance_edit.php'; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="../JavaScript/class_detail.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

</body>

</html>