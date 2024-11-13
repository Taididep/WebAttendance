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
$stmtClass->closeCursor();

if (!$classInfo) {
    echo 'Không tìm thấy thông tin lớp học.';
    exit;
}

// Kiểm tra status của lịch học
$sqlStatus = "SELECT status FROM schedules WHERE schedule_id = ?";
$stmtStatus = $conn->prepare($sqlStatus);
$stmtStatus->execute([$schedule_id]);
$scheduleStatus = $stmtStatus->fetch(PDO::FETCH_ASSOC);
$stmtStatus->closeCursor();

if (!$scheduleStatus) {
    echo 'Không tìm thấy thông tin lịch học.';
    exit;
}

?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Điểm danh - <?php echo htmlspecialchars($classInfo['class_name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
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

        .status-green {
            background-color: #d4edda;
        }

        .status-yellow {
            background-color: #fff3cd;
        }

        .status-red {
            background-color: #f8d7da;
        }
    </style>
</head>

<body>
    <div class="container mt-6">
        <div class="border rounded p-4 shadow">
            <h2 class="mb-4 text-center"><?php echo htmlspecialchars($classInfo['class_name']); ?></h2>

            <div id="timer" class="text-center fs-4"></div>

            <?php if ($scheduleStatus['status'] == 1): ?>
                <!-- Form điểm danh nếu status là 1 -->
                <form id="attendanceForm" action="process_attendance.php" method="POST">
                    <input type="hidden" name="class_id" value="<?php echo htmlspecialchars($class_id); ?>">
                    <input type="hidden" name="schedule_id" value="<?php echo htmlspecialchars($schedule_id); ?>">

                    <div class="text-center mt-3">
                        <button type="submit" class="btn btn-primary">Điểm danh</button>
                    </div>
                </form>
            <?php else: ?>
                <!-- Thông báo nếu status không bằng 1 -->
                <p class="alert alert-warning text-center">Không được điểm danh</p>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>