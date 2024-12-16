<?php
session_start();
$basePath = '../'; // Đường dẫn gốc
include __DIR__ . '/../../Connect/connect.php';
include __DIR__ . '/../../Account/islogin.php';

// Kiểm tra xem class_id có được gửi qua URL hay không
if (!isset($_GET['class_id'])) {
    echo 'Không tìm thấy thông tin lớp học.';
    exit;
}

$class_id = $_GET['class_id'];

// Lấy thông tin lớp
$sqlClass = "CALL GetClassDetailsById(?)";
$stmtClass = $conn->prepare($sqlClass);
$stmtClass->execute([$class_id]);
$classInfo = $stmtClass->fetch(PDO::FETCH_ASSOC);
$stmtClass->closeCursor();

// Lấy thông tin sinh viên trong lớp
$sqlStudents = "CALL GetStudentsByClassId(?)";
$stmtStudents = $conn->prepare($sqlStudents);
$stmtStudents->execute([$class_id]);
$students = $stmtStudents->fetchAll(PDO::FETCH_ASSOC);
$stmtStudents->closeCursor();

// Lấy thông tin điểm danh
$sqlAttendance = "CALL GetSchedulesAndAttendanceByClassId(?)";
$stmtAttendance = $conn->prepare($sqlAttendance);
$stmtAttendance->execute([$class_id]);
$attendanceData = $stmtAttendance->fetchAll(PDO::FETCH_ASSOC);
$stmtAttendance->closeCursor();

// Chuyển đổi dữ liệu điểm danh thành mảng để dễ truy xuất
$attendanceMap = [];
foreach ($attendanceData as $record) {
    $attendanceMap[$record['student_id']][$record['date']] = $record['status'];
}

// Lấy danh sách ngày điểm danh từ bảng schedules
$sqlSchedules = "CALL GetDistinctDatesByClassId(?)";
$stmtSchedules = $conn->prepare($sqlSchedules);
$stmtSchedules->execute([$class_id]);
$schedules = $stmtSchedules->fetchAll(PDO::FETCH_ASSOC);
$stmtSchedules->closeCursor();

$currentDate = date('Y-m-d'); // Lấy ngày hiện tại

// Hàm tính số lượng học sinh có mặt, muộn và vắng mặt cho một buổi học cụ thể
function calculateAttendance($students, $attendanceMap, $scheduleDate)
{
    $presentCount = 0;
    $lateCount = 0;
    $absentCount = 0;

    foreach ($students as $student) {
        if (isset($attendanceMap[$student['student_id']][$scheduleDate])) {
            $status = $attendanceMap[$student['student_id']][$scheduleDate];
            if ($status === '1') {
                $presentCount++;
            } elseif ($status === '2') {
                $lateCount++;
            } else {
                $absentCount++;
            }
        }
    }

    return [$presentCount, $lateCount, $absentCount];
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Báo cáo điểm danh - <?php echo htmlspecialchars($classInfo['class_name']); ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../Css/attendance_report.css">
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center">Báo cáo điểm danh cho lớp: <?php echo htmlspecialchars($classInfo['class_name']); ?></h2>

        <!-- Nhập buổi học -->
        <div class="attendance-input">
            <label for="attendanceInputList">Nhập buổi học:</label>
            <div class="input-group">
                <input type="number" id="attendanceInputList" min="1" max="<?php echo count($schedules); ?>" class="form-control" placeholder="Nhập buổi (1, 2, ...)" required>
                <div class="input-group-append">
                    <button type="button" id="confirmAttendanceBtnList" class="btn btn-primary">Xác nhận</button>
                    <!-- <a href="export_statistics.php?class_id=<?php echo urlencode($class_id); ?>" class="btn btn-success btn-custom">Xuất Excel</a> -->
                </div>
            </div>
        </div>

        <canvas id="attendanceChart" style="width:100%; height:400px;"></canvas>

        <div class="table-responsive mt-4">
            <?php if (empty($students)): ?>
                <div class="alert alert-warning text-center">Lớp hiện chưa có học sinh nào</div>
            <?php else: ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <?php foreach ($schedules as $index => $schedule): ?>
                                <?php if ($schedule['date'] < $currentDate): ?>
                                    <th class="schedule-header" data-index="<?php echo $index; ?>">
                                        <?php echo 'Buổi ' . ($index + 1) . ' - ' . date('d/m', strtotime($schedule['date'])); ?>
                                    </th>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <script>
        let attendanceData = <?php echo json_encode($attendanceMap); ?>;
        let schedules = <?php echo json_encode($schedules); ?>;
        let students = <?php echo json_encode($students); ?>;

        const ctx = document.getElementById('attendanceChart').getContext('2d');
        const attendanceChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Có mặt', 'Muộn', 'Vắng mặt'],
                datasets: [{
                    label: 'Số lượng học sinh',
                    data: [0, 0, 0], // Mặc định là 0
                    backgroundColor: [
                        'rgba(76, 175, 80, 0.7)', // Có mặt
                        'rgba(255, 235, 59, 0.7)', // Muộn
                        'rgba(244, 67, 54, 0.7)' // Vắng mặt
                    ],
                    borderColor: [
                        'rgba(76, 175, 80, 1)',
                        'rgba(255, 235, 59, 1)',
                        'rgba(244, 67, 54, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Số lượng học sinh',
                            color: '#333',
                        },
                        ticks: {
                            color: '#333',
                        },
                    },
                    x: {
                        ticks: {
                            color: '#333',
                        }
                    }
                },
                plugins: {
                    legend: {
                        labels: {
                            color: '#333',
                        }
                    }
                }
            }
        });

        // Xử lý sự kiện khi người dùng nhập buổi học
        document.getElementById('confirmAttendanceBtnList').addEventListener('click', function() {
            const input = document.getElementById('attendanceInputList');
            const selectedIndex = parseInt(input.value) - 1; // Chỉ số bắt đầu từ 0
            const selectedSchedule = schedules[selectedIndex];

            if (selectedSchedule) {
                // Tính toán số lượng học sinh có mặt, muộn, vắng mặt cho buổi học
                const [presentCount, lateCount, absentCount] = calculateAttendance(students, attendanceData, selectedSchedule.date);

                // Cập nhật biểu đồ
                attendanceChart.data.datasets[0].data = [presentCount, lateCount, absentCount];
                attendanceChart.update();
            }
        });

        function calculateAttendance(students, attendanceMap, scheduleDate) {
            let presentCount = 0;
            let lateCount = 0;
            let absentCount = 0;

            students.forEach(student => {
                if (attendanceMap[student.student_id] && attendanceMap[student.student_id][scheduleDate]) {
                    const status = attendanceMap[student.student_id][scheduleDate];
                    if (status === '1') presentCount++;
                    else if (status === '2') lateCount++;
                    else absentCount++;
                }
            });

            return [presentCount, lateCount, absentCount];
        }
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>