<?php
session_start();
$basePath = '../'; // Đường dẫn gốc
include __DIR__ . '../../../Account/islogin.php';
include __DIR__ . '/../../Connect/connect.php';
include __DIR__ . '/../../LayoutPages/navbar.php';

// Kiểm tra xem class_id có được gửi qua URL hay không
if (!isset($_GET['class_id'])) {
    echo 'Không tìm thấy thông tin lớp học.';
    exit;
}

// Lấy class_id từ URL
$class_id = $_GET['class_id'];

// Truy vấn để lấy thông tin lớp học từ bảng classes
$sql = "CALL GetClassDetailsById(?)";
$stmt = $conn->prepare($sql);
$stmt->execute([$class_id]);

// Lấy kết quả truy vấn
$classData = $stmt->fetch(PDO::FETCH_ASSOC);
$stmt->closeCursor();

// Kiểm tra xem có kết quả hay không
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../Css/class_detail.css">
</head>

<body>

    <!-- Tabs nằm sát mép trái -->
    <div class="side-tabs">
        <ul class="nav nav-tabs flex-column" id="tabMenu">
            <li class="nav-item">
                <a class="nav-link active" id="news-tab" href="#news" data-bs-toggle="tab">Bảng tin</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="attendance-tab" href="#attendance" data-bs-toggle="tab">Danh sách</a>
            </li>
        </ul>
    </div>

    <div class="container mt-5">
        <!-- Card hiển thị thông tin lớp học -->
        <div class="card classroom-card shadow-lg">
            <div class="card-body">
                <h2 data-bs-toggle="modal" data-bs-target="#classModal"><?php echo htmlspecialchars($classData['class_name']); ?></h2>
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

    <!-- Tabs cho Bảng tin và Danh sách điểm danh -->
    <div class="container mt-5 mb-5">
        <div class="tab-content mt-3">
            <!-- Nội dung Bảng tin -->
            <div class="tab-pane fade show active" id="news" role="tabpanel" aria-labelledby="news-tab">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3>Bảng tin lớp học</h3>

                    <!-- Nút để mở modal tạo thông báo -->
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAnnouncementModal">
                        Tạo thông báo
                    </button>
                </div>
                <!-- Modal tạo thông báo -->
                <div class="modal fade" id="createAnnouncementModal" tabindex="-1" aria-labelledby="createAnnouncementModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="createAnnouncementModalLabel">Tạo bảng tin mới</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="create_announcement.php" method="POST">
                                    <div class="mb-3">
                                        <label for="announcementTitle" class="form-label">Tiêu đề</label>
                                        <input type="text" class="form-control" id="announcementTitle" name="title" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="announcementContent" class="form-label">Nội dung</label>
                                        <textarea class="form-control" id="announcementContent" name="content" rows="4" required></textarea>
                                    </div>
                                    <input type="hidden" name="class_id" value="<?php echo htmlspecialchars($class_id); ?>"> <!-- class_id ẩn -->
                                    <button type="submit" class="btn btn-primary">Tạo thông báo</button>
                                </form>
                            </div>s
                        </div>
                    </div>
                </div>

                <?php
                // Truy vấn để lấy bảng tin cho lớp học
                $sql = "SELECT * FROM announcements WHERE class_id = ? ORDER BY created_at DESC";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$class_id]);
                $announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($announcements) {
                    foreach ($announcements as $announcement) {
                ?>
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($announcement['title']); ?></h5>
                                <p class="card-text"><?php echo nl2br(htmlspecialchars($announcement['content'])); ?></p>
                                <p class="card-text"><small class="text-muted">Ngày tạo: <?php echo $announcement['created_at']; ?></small></p>
                            </div>
                        </div>
                <?php
                    }
                } else {
                    echo '<p class="text-center">Chưa có bảng tin nào.</p>';
                }
                ?>
            </div>

            <!-- Nội dung Danh sách điểm danh -->
            <div class="tab-pane fade" id="attendance" role="tabpanel" aria-labelledby="attendance-tab">
                <div id="attendanceList" style="display: inline;">
                    <?php include '../Attendance/attendance_list.php'; ?> <!-- Gọi file danh sách điểm danh -->
                </div>

                <div id="attendanceEdit" style="display: none;">
                    <?php include '../Attendance/attendance_edit.php'; ?> <!-- Gọi file chỉnh sửa danh sách điểm danh -->
                </div>
            </div>
        </div>
    </div>

    <script src="../JavaScript/class_detail.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

</body>

</html>