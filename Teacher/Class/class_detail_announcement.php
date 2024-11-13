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
                <a class="nav-link active" id="news-tab"
                    href="class_detail_announcement.php?class_id=<?php echo htmlspecialchars($class_id); ?>">Bảng
                    tin</a>
            </li>
            <li class="nav-item">
                <a style="color: black;" class="nav-link" id="attendance-tab"
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
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Bảng tin lớp học</h3>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAnnouncementModal">
                Tạo thông báo
            </button>
        </div>

        <!-- Modal tạo thông báo -->
        <div class="modal fade" id="createAnnouncementModal" tabindex="-1"
            aria-labelledby="createAnnouncementModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createAnnouncementModalLabel">Tạo bảng tin mới</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="../Announcement/create_announcement.php" method="POST">
                            <div class="mb-3">
                                <label for="announcementTitle" class="form-label">Tiêu đề</label>
                                <input type="text" class="form-control" id="announcementTitle" name="title" required>
                            </div>
                            <div class="mb-3">
                                <label for="announcementContent" class="form-label">Nội dung</label>
                                <textarea class="form-control" id="announcementContent" name="content" rows="4"
                                    required></textarea>
                            </div>
                            <input type="hidden" name="class_id" value="<?php echo htmlspecialchars($class_id); ?>">
                            <button type="submit" class="btn btn-primary">Tạo thông báo</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bao gồm bảng tin -->
        <?php include '../Announcement/announcement.php'; ?>
    </div>

    <script src="../JavaScript/class_detail.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

</body>

</html>