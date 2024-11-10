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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                                <td colspan="3" class="text-center">Không có lịch học nào.</td>
                            </tr>
                        <?php else: ?>
                            <?php 
                            $nextDayFound = false; // Biến để kiểm tra ngày tiếp theo đã được tìm thấy chưa
                            foreach ($schedules as $schedule): 
                                $scheduleDate = $schedule['date'];
                            ?>
                                <tr class="<?php 
                                    if ($scheduleDate < $currentDate) {
                                        echo 'past-date text-muted'; // Ngày đã qua
                                    } elseif ($scheduleDate === $currentDate) {
                                        echo 'today'; // Ngày hiện tại
                                    } elseif ($scheduleDate > $currentDate && !$nextDayFound) {
                                        echo 'next-day'; // Ngày tiếp theo
                                        $nextDayFound = true; // Đánh dấu là đã tìm thấy ngày tiếp theo
                                    } 
                                ?>">
                                    <td>
                                        <?php 
                                        echo date('d/m/Y', strtotime($scheduleDate)); 
                                        if ($scheduleDate < $currentDate) {
                                            echo ' (pass)'; // Thêm chữ "pass" cho ngày đã qua
                                        }
                                        ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($schedule['start_time']); ?></td>
                                    <td><?php echo htmlspecialchars($schedule['end_time']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                <a href="<?php echo $basePath; ?>Class/class_manage.php" class="btn btn-primary mt-3">Quay lại</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>