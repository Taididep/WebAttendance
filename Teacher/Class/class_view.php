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

// Gọi thủ tục để lấy thông tin lớp học
$sql_class_info = "CALL GetClassInfoById(?)";
$stmt_class_info = $conn->prepare($sql_class_info);
$stmt_class_info->execute([$classId]);
$classInfo = $stmt_class_info->fetch(PDO::FETCH_ASSOC);
$stmt_class_info->closeCursor();

if (!$classInfo) {
    echo '<div class="alert alert-danger">Không tìm thấy thông tin cho lớp học này.</div>';
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
</head>

<body>

    <div class="container mt-5">
        <div class="card mb-4">
            <div class="card-header text-center">
                <h2>Lịch Học Lớp: <?php echo htmlspecialchars($classInfo['class_name']); ?></h2>
            </div>
            <div class="card-body">
                <p><strong>Môn học:</strong> <?php echo htmlspecialchars($classInfo['course_name']); ?></p>
                <p><strong>Giáo viên:</strong> <?php echo htmlspecialchars($classInfo['teacher_fullname']); ?></p>
                <p><strong>Học kỳ:</strong> <?php echo htmlspecialchars($classInfo['semester_name']); ?></p>

                <h4 class="mt-4">Lịch học</h4>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Ngày học</th>
                            <th>Tiết bắt đầu</th>
                            <th>Tiết kết thúc</th>
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
                                            <button class="btn btn-light btn-sm" type="button" id="dropdownMenuButton<?php echo $schedule['schedule_id']; ?>" data-bs-toggle="dropdown" aria-expanded="false">
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
                <a href="<?php echo $basePath; ?>Class/class_manage.php" class="btn btn-primary mt-3">Quay lại</a>
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
</body>

</html>