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

?>

<!DOCTYPE html>
<html lang="en">

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
            <h2 class="mb-4 text-center">Điểm danh lớp: <?php echo htmlspecialchars($classInfo['class_name']); ?></h2>

            <div id="statusMessage" class="mb-4 text-center">Bắt đầu điểm danh</div>
            <div id="timer" class="text-center fs-4"></div>

            <form id="attendanceForm" action="process_attendance.php" method="POST">
                <input type="hidden" name="class_id" value="<?php echo htmlspecialchars($class_id); ?>">
                <input type="hidden" name="schedule_id" value="<?php echo htmlspecialchars($schedule_id); ?>">
                <input type="hidden" id="attendanceStatus" name="status" value="1"> <!-- Giá trị mặc định là 'đúng giờ' -->

                <div class="text-center mt-3">
                    <button type="submit" class="btn btn-primary">Điểm danh</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let timer = 15 * 60; // 15 minutes in seconds
        const statusMessage = document.getElementById('statusMessage');
        const timerDisplay = document.getElementById('timer');
        const attendanceStatus = document.getElementById('attendanceStatus');
        const form = document.getElementById('attendanceForm');

        function startTimer() {
            const countdown = setInterval(() => {
                let minutes = Math.floor(timer / 60);
                let seconds = timer % 60;
                timerDisplay.innerHTML = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;

                if (timer > 0) {
                    timer--;

                    if (timer >= 10 * 60) { // Within the first 10 minutes
                        statusMessage.className = 'status-green';
                        statusMessage.innerText = 'Đúng giờ';
                        attendanceStatus.value = 1;
                    } else if (timer >= 5 * 60) { // Between 5-10 minutes
                        statusMessage.className = 'status-yellow';
                        statusMessage.innerText = 'Trễ';
                        attendanceStatus.value = 2;
                    } else { // Less than 5 minutes
                        statusMessage.className = 'status-red';
                        statusMessage.innerText = 'Vắng';
                        attendanceStatus.value = 3;
                    }

                } else {
                    clearInterval(countdown);
                    statusMessage.className = 'status-red';
                    statusMessage.innerText = 'Vắng mặt';
                    attendanceStatus.value = 3;
                    form.querySelector('button').disabled = true; // Disable button if time is up
                }
            }, 1000);
        }

        window.onload = startTimer;
    </script>
</body>

</html>
