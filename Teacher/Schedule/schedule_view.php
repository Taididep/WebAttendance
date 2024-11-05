<?php
session_start();
$basePath = '../'; // Đường dẫn gốc
include __DIR__ . '/../../Connect/connect.php';
include __DIR__ . '/../../LayoutPages/navbar.php';
include __DIR__ . '/../../Account/islogin.php';

// Gọi thủ tục để lấy danh sách học kỳ
$sql_semesters = "CALL GetAllSemesters()"; // Gọi thủ tục
$stmt_semesters = $conn->prepare($sql_semesters);
$stmt_semesters->execute();
$semesters = $stmt_semesters->fetchAll(PDO::FETCH_ASSOC);
$stmt_semesters->closeCursor(); // Đóng kết quả của truy vấn trước

// Lấy semester_id của học kỳ đầu tiên trong danh sách
$defaultSemesterId = !empty($semesters) ? $semesters[0]['semester_id'] : null;

// Kiểm tra xem tuần và học kỳ có được gửi qua URL hay không, nếu không thì sử dụng tuần hiện tại và học kỳ mặc định
$semesterId = isset($_GET['semester_id']) ? $_GET['semester_id'] : $defaultSemesterId;
if (!isset($_GET['week'])) {
    $startDate = new DateTime('now');
} else {
    $startDate = new DateTime($_GET['week']); // Ngày bắt đầu của tuần
}
$startDate->modify('monday this week'); // Đặt lại ngày bắt đầu về thứ Hai
$endDate = clone $startDate;
$endDate->modify('sunday this week'); // Đặt ngày kết thúc về Chủ Nhật

// Lấy user_id của giáo viên từ session (giả sử đây là teacher_id)
$teacher_id = $_SESSION['user_id'];

// Truy vấn để lấy lịch học
$sql = "CALL GetTeacherSchedules(?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->execute([$startDate->format('Y-m-d'), $endDate->format('Y-m-d'), $semesterId, $teacher_id]);
$schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt->closeCursor(); // Đóng con trỏ

// Tạo mảng để tổ chức lịch học theo ngày
$weeklySchedules = [];
foreach ($schedules as $schedule) {
    $weeklySchedules[$schedule['date']][] = $schedule;
}

// Tạo một mảng chứa tên ngày trong tuần
$daysOfWeek = ['Thứ Hai', 'Thứ Ba', 'Thứ Tư', 'Thứ Năm', 'Thứ Sáu', 'Thứ Bảy', 'Chủ Nhật'];

// Tính toán tuần trước và tuần sau
$previousWeek = clone $startDate;
$previousWeek->modify('-1 week');

$nextWeek = clone $startDate;
$nextWeek->modify('+1 week');
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch học theo tuần</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">
    <style>
        body {
            background-color: #f1f3f5;
            font-family: 'Roboto', sans-serif;
        }
        .container {
            margin-top: 50px;
        }
        h2 {
            color: #0056b3;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.1);
        }
        .custom-select {
            border: 1px solid #007bff; /* Đường viền xanh */
            border-radius: 25px; /* Bo tròn viền */
            padding: 10px; /* Padding bên trong */
            background-color: #ffffff; /* Màu nền trắng */
            transition: border-color 0.3s; /* Hiệu ứng chuyển màu */
        }
        .custom-select:focus {
            border-color: #0056b3; /* Đổi màu viền khi focus */
            box-shadow: 0 0 5px rgba(0, 86, 179, 0.5); /* Hiệu ứng bóng */
        }
        .schedule-item {
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 10px;
            background-color: #d1ecf1;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .schedule-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        .table {
            border-collapse: collapse;
            margin-top: 20px;
        }
        .table th, .table td {
            border: 1px solid #dee2e6; /* Đường viền cho các ô */
            padding: 1rem;
        }
        .table th {
            background-color: #007bff; /* Màu nền cho tiêu đề */
            color: #fff;
        }
        .table td {
            background-color: #ffffff;
            transition: background-color 0.3s;
        }
        .table td:hover {
            background-color: #f8f9fa; /* Nền xám nhạt khi hover */
        }
        .btn-primary, .btn-secondary, .btn-success {
            border-radius: 25px; /* Bo tròn viền */
            transition: background-color 0.3s, transform 0.3s; /* Hiệu ứng chuyển màu */
            padding: 10px 20px; /* Thay đổi kích thước nút */
        }
        .btn-primary:hover, .btn-secondary:hover, .btn-success:hover {
            transform: scale(1.05); /* Tăng kích thước khi hover */
        }
    </style>
</head>

<body>
    <div class="container">
        <h2 class="text-center">Lịch học từ <?php echo $startDate->format('d/m/Y'); ?> đến <?php echo $endDate->format('d/m/Y'); ?></h2>
        <hr>

        <!-- Form chọn học kỳ và nút điều hướng tuần -->
        <form method="GET" class="d-flex justify-content-between align-items-center mb-3">
            <input type="hidden" name="week" value="<?php echo $startDate->format('Y-m-d'); ?>">
            <div class="mb-0 me-2" style="flex: 1;">
                <select name="semester_id" id="semester_id" class="form-select custom-select" required onchange="this.form.submit()">
                    <?php foreach ($semesters as $semester): ?>
                        <option value="<?php echo $semester['semester_id']; ?>" <?php echo $semester['semester_id'] == $semesterId ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($semester['semester_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="d-flex">
                <a href="?week=<?php echo $previousWeek->format('Y-m-d'); ?>&semester_id=<?php echo $semesterId; ?>" class="btn btn-primary me-2 d-flex align-items-center">
                    <i class="bi bi-arrow-left"></i> Trở về
                </a>
                <a href="?week=<?php echo (new DateTime('now'))->modify('monday this week')->format('Y-m-d'); ?>&semester_id=<?php echo $semesterId; ?>" class="btn btn-secondary me-2 d-flex align-items-center">
                    <i class="bi bi-calendar-week"></i> Hiện tại
                </a>
                <a href="?week=<?php echo $nextWeek->format('Y-m-d'); ?>&semester_id=<?php echo $semesterId; ?>" class="btn btn-primary me-2 d-flex align-items-center">
                    Tiếp <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <!-- Nút in lịch -->
            <button class="btn btn-success" onclick="window.print()">
                <i class="bi bi-printer"></i> In lịch
            </button>
        </form>

        <table class="table table-bordered text-center">
            <thead>
                <tr>
                    <th class="align-middle">Ca học</th>
                    <?php foreach ($daysOfWeek as $index => $day): ?>
                        <?php
                        // Lấy ngày cụ thể trong tuần
                        $currentDate = clone $startDate;
                        $currentDate->modify("+$index days");
                        $formattedDate = $currentDate->format('d/m/Y');
                        ?>
                        <th class="align-middle">
                            <?php echo $day; ?><br><?php echo $formattedDate; ?>
                        </th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php
                // Mảng để giữ các ca học
                $shifts = ['Sáng', 'Chiều', 'Tối'];
                foreach ($shifts as $shift): ?>
                    <tr>
                        <td class="align-middle"><?php echo $shift; ?></td>
                        <?php foreach ($daysOfWeek as $index => $day): ?>
                            <?php
                            // Lấy ngày cụ thể trong tuần
                            $currentDate = clone $startDate;
                            $currentDate->modify("+$index days");
                            $formattedDate = $currentDate->format('Y-m-d');
                            ?>
                            <td>
                                <?php if (isset($weeklySchedules[$formattedDate])): ?>
                                    <div class="schedule-boxes">
                                        <?php foreach ($weeklySchedules[$formattedDate] as $schedule): ?>
                                            <?php if ($schedule['ca_hoc'] === $shift): ?>
                                                <div class="schedule-item">
                                                    <strong><?php echo htmlspecialchars($schedule['class_name']); ?></strong><br>
                                                    <small><?php echo htmlspecialchars($schedule['course_name']); ?></small><br>
                                                    <small>Tiết: <?php echo htmlspecialchars($schedule['start_time']) . ' - ' . htmlspecialchars($schedule['end_time']); ?></small>
                                                </div>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
