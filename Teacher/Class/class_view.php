<?php
session_start();
$basePath = '../'; // Đường dẫn gốc
include __DIR__ . '/../../Connect/connect.php';
include __DIR__ . '/../../LayoutPages/navbar.php';
include __DIR__ . '/../../Account/islogin.php';

// Nhận giá trị từ URL
$classId = isset($_GET['class_id']) ? $_GET['class_id'] : null;

// Kiểm tra xem classId có hợp lệ không
if (!$classId) {
    echo '<div class="alert alert-danger">Không tìm thấy lớp học.</div>';
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


// Gọi thủ tục để lấy lịch học
$sql_schedules = "CALL GetSchedulesByClassId(?)"; // Sử dụng thủ tục lấy lịch học
$stmt_schedules = $conn->prepare($sql_schedules);
$stmt_schedules->execute([$classId]);
$schedules = $stmt_schedules->fetchAll(PDO::FETCH_ASSOC);
$stmt_schedules->closeCursor();

// Ngày hiện tại
$currentDate = date('Y-m-d');
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xem Lịch Học</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../Css/class_view.css">
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
                <a style="color: black;" class="nav-link" id="attendance-tab"
                    href="class_detail_list.php?class_id=<?php echo htmlspecialchars($class_id); ?>">Danh sách</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" id="attendance-tab"
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

    <div class="container mt-5">
        <div class="card mb-4">
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Ngày học</th>
                            <th>Tiết bắt đầu</th>
                            <th>Tiết kết thúc</th>
                            <th style="width: 1%;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($schedules)): ?>
                            <tr>
                                <td colspan="5" class="text-center">Không có lịch học nào.</td>
                            </tr>
                        <?php else: ?>
                            <?php
                            $nextDayFound = false;
                            foreach ($schedules as $schedule):
                                $scheduleDate = $schedule['date'];
                            ?>
                                <tr class="<?php
                                            if ($scheduleDate < $currentDate) {
                                                echo 'past-date text-muted';
                                            } elseif ($scheduleDate === $currentDate) {
                                                echo 'today';
                                            } elseif ($scheduleDate > $currentDate && !$nextDayFound) {
                                                echo 'next-day';
                                                $nextDayFound = true;
                                            }
                                            ?>">
                                    <td>
                                        <?php
                                        echo date('d/m/Y', strtotime($scheduleDate));
                                        if ($scheduleDate < $currentDate) {
                                            echo ' (pass)';
                                        }
                                        ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($schedule['start_time']); ?></td>
                                    <td><?php echo htmlspecialchars($schedule['end_time']); ?></td>
                                    <td>
                                        <!-- Dropdown for Edit and Delete -->
                                        <div class="dropdown">
                                            <button class="btn btn-link btn-sm text-black" type="button" id="dropdownMenuButton<?php echo $schedule['schedule_id']; ?>" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton<?php echo $schedule['schedule_id']; ?>">
                                                <li>
                                                    <a class="dropdown-item" href="#" onclick="openEditModal(<?php echo $schedule['schedule_id']; ?>, '<?php echo $schedule['date']; ?>', <?php echo $schedule['start_time']; ?>, <?php echo $schedule['end_time']; ?>)">Sửa</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item text-danger" href="delete_schedule.php?schedule_id=<?php echo $schedule['schedule_id']; ?>&class_id=<?php echo $classId; ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa lịch học này?');">Xóa</a>

                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>

                </table>
                <div class="container d-flex justify-content-between mt-3">
                    <a href="<?php echo $basePath; ?>Class/class_manage.php" class="btn btn-primary">Quay lại</a>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createScheduleModal">Tạo lịch học</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tạo Lịch Học -->
    <div class="modal fade" id="createScheduleModal" tabindex="-1" aria-labelledby="createScheduleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="create_schedule.php" method="post">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createScheduleModalLabel">Tạo lịch học mới</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Hidden input for class_id -->
                        <input type="hidden" name="class_id" value="<?php echo htmlspecialchars($class_id); ?>">

                        <div class="mb-3">
                            <label for="schedule_date" class="form-label">Ngày học</label>
                            <input type="date" class="form-control" name="schedule_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="start_time" class="form-label">Tiết bắt đầu</label>
                            <input type="number" class="form-control" name="start_time" required>
                        </div>
                        <div class="mb-3">
                            <label for="end_time" class="form-label">Tiết kết thúc</label>
                            <input type="number" class="form-control" name="end_time" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary">Tạo lịch học</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editScheduleModal" tabindex="-1" aria-labelledby="editScheduleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="edit_schedule.php" method="post">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editScheduleModalLabel">Chỉnh sửa lịch học</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Hidden input for schedule_id -->
                        <input type="hidden" name="schedule_id" id="schedule_id">

                        <!-- Hidden input for class_id -->
                        <input type="hidden" name="class_id" id="class_id" value="<?php echo htmlspecialchars($classId); ?>">

                        <div class="mb-3">
                            <label for="date" class="form-label">Ngày học</label>
                            <input type="date" class="form-control" name="date" id="date" required>
                        </div>
                        <div class="mb-3">
                            <label for="start_time" class="form-label">Tiết bắt đầu</label>
                            <input type="number" class="form-control" name="start_time" id="start_time" required>
                        </div>
                        <div class="mb-3">
                            <label for="end_time" class="form-label">Tiết kết thúc</label>
                            <input type="number" class="form-control" name="end_time" id="end_time" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../JavaScript/class_view.js"></script>

    <?php if (isset($_GET['error']) && $_GET['error'] === 'duplicate'): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const modalElement = document.getElementById('editScheduleModal');
                const modal = new bootstrap.Modal(modalElement);

                // Kiểm tra nếu có lỗi duplicate
                if (window.location.search.includes('error=duplicate')) {
                    // Hiển thị thông báo lỗi trong modal
                    const errorMessage = "Không thể cập nhật lịch học. Ngày này đã được đặt trước.";
                    modal.show();

                    // Thêm thông báo vào modal
                    const errorDiv = document.createElement('div');
                    errorDiv.classList.add('alert', 'alert-danger', 'mt-2');
                    errorDiv.textContent = errorMessage;

                    const modalBody = document.querySelector('#editScheduleModal .modal-body');
                    modalBody.insertBefore(errorDiv, modalBody.firstChild);
                }

                // Lắng nghe sự kiện modal đóng
                modalElement.addEventListener('hidden.bs.modal', function() {
                    // Lấy URL hiện tại
                    const currentUrl = new URL(window.location.href);

                    // Lấy giá trị class_id
                    const classId = currentUrl.searchParams.get('class_id');

                    // Nếu có class_id, loại bỏ error=duplicate và tải lại trang
                    if (classId) {
                        currentUrl.searchParams.delete('error'); // Loại bỏ tham số error
                        currentUrl.searchParams.set('class_id', classId); // Đảm bảo class_id còn lại
                        window.location.href = currentUrl.toString(); // Tải lại trang mà không có tham số error
                    } else {
                        // Nếu không có class_id, chỉ tải lại trang
                        window.location.reload();
                    }
                });
            });
        </script>
    <?php endif; ?>

</body>

</html>