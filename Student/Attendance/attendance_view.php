<?php
session_start();
$basePath = '../'; // Đường dẫn gốc
include __DIR__ . '/../../Connect/connect.php';
include __DIR__ . '/../../LayoutPages/navbar_student.php';
include __DIR__ . '/../../Account/islogin.php';

// Kiểm tra xem class_id và schedule_id có được gửi qua URL hay không
if (!isset($_GET['class_id']) || !isset($_GET['schedule_id'])) {
    echo 'Không tìm thấy thông tin lớp học hoặc lịch học.';
    exit;
}

// Lấy class_id và schedule_id từ URL
$class_id = $_GET['class_id'];
$schedule_id = $_GET['schedule_id'];

// Lấy thông tin lớp học
$sqlClass = "SELECT class_name FROM classes WHERE class_id = ?";
$stmtClass = $conn->prepare($sqlClass);
$stmtClass->execute([$class_id]);
$classInfo = $stmtClass->fetch(PDO::FETCH_ASSOC);
$stmtClass->closeCursor(); // Đóng con trỏ

if (!$classInfo) {
    echo 'Không tìm thấy thông tin lớp học.';
    exit;
}

// Lấy danh sách các schedule_id cho lớp học hiện tại
$sqlSchedules = "SELECT schedule_id FROM schedules WHERE class_id = ? ORDER BY date";
$stmtSchedules = $conn->prepare($sqlSchedules);
$stmtSchedules->execute([$class_id]);
$scheduleList = $stmtSchedules->fetchAll(PDO::FETCH_COLUMN);
$stmtSchedules->closeCursor();

// Xác định thứ tự buổi học của schedule_id hiện tại
$scheduleOrder = array_search($schedule_id, $scheduleList) + 1;

if (!$scheduleOrder) {
    echo 'Không tìm thấy thông tin buổi học.';
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Điểm danh - <?php echo htmlspecialchars($classInfo['class_name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            max-width: 600px;
            margin-top: 100px;
        }

        .btn-primary {
            width: 100%;
        }
    </style>
</head>

<body>
    <div class="container mt-6">
        <div class="border rounded p-4 shadow">
            <h2 class="mb-4 text-center">Điểm danh lớp: <?php echo htmlspecialchars($classInfo['class_name']); ?></h2>
            <h4 class="mb-4 text-center">Buổi học: <?php echo "Buổi số " . $scheduleOrder; ?></h4> <!-- Hiển thị số buổi dựa vào thứ tự -->

            <!-- Nút Điểm danh -->
            <form action="process_attendance.php" method="POST">
                <input type="hidden" name="class_id" value="<?php echo htmlspecialchars($class_id); ?>">
                <input type="hidden" name="schedule_id" value="<?php echo htmlspecialchars($schedule_id); ?>">

                <div class="text-center">
                    <button style="width: 30%;" type="submit" class="btn btn-primary">Điểm danh</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>