<?php
session_start();
$basePath = '../'; // Đường dẫn gốc
include __DIR__ . '/../../Connect/connect.php';
include __DIR__ . '/../../LayoutPages/navbar_student.php';
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

// Lấy user_id của sinh viên từ session (giả sử đây là student_id)
$student_id = $_SESSION['user_id'];

// Truy vấn để lấy lịch học, thông tin lớp và môn học trong khoảng thời gian từ thứ Hai đến Chủ Nhật, và lọc theo học kỳ và student_id
$sql = "
    SELECT 
        c.class_name,
        co.course_name,
        s.date,
        s.start_time,
        s.end_time,
        CASE 
            WHEN s.end_time < 7 THEN 'Sáng'
            WHEN s.end_time >= 7 AND s.end_time < 13 THEN 'Chiều'
            ELSE 'Tối'
        END AS ca_hoc 
    FROM 
        schedules s
    JOIN 
        classes c ON s.class_id = c.class_id
    JOIN 
        courses co ON c.course_id = co.course_id
    JOIN 
        class_students cs ON c.class_id = cs.class_id
    WHERE 
        s.date BETWEEN ? AND ?
        AND c.semester_id = ?
        AND cs.student_id = ? -- Thêm điều kiện lọc theo student_id
    ORDER BY 
        s.date, c.class_name
";

$stmt = $conn->prepare($sql);
$stmt->execute([$startDate->format('Y-m-d'), $endDate->format('Y-m-d'), $semesterId, $student_id]);
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
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Lịch học từ <?php echo $startDate->format('d/m/Y'); ?> đến <?php echo $endDate->format('d/m/Y'); ?></h2>
    <hr>
    
    <!-- Form chọn học kỳ và nút điều hướng tuần -->
    <form method="GET" class="d-flex justify-content-between align-items-center mb-3">
        <input type="hidden" name="week" value="<?php echo $startDate->format('Y-m-d'); ?>">
        <div class="mb-0 me-2" style="flex: 1;">
            <select name="semester_id" id="semester_id" class="form-select" required onchange="this.form.submit()">
                <?php foreach ($semesters as $semester): ?>
                    <option value="<?php echo $semester['semester_id']; ?>" <?php echo $semester['semester_id'] == $semesterId ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($semester['semester_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="d-flex">
            <!-- Nút điều hướng tuần -->
            <a href="?week=<?php echo $previousWeek->format('Y-m-d'); ?>&semester_id=<?php echo $semesterId; ?>" class="btn btn-primary me-2 d-flex align-items-center" style="height: 100%;">
                Trở về
            </a>
            <a href="?week=<?php echo $nextWeek->format('Y-m-d'); ?>&semester_id=<?php echo $semesterId; ?>" class="btn btn-primary d-flex align-items-center" style="height: 100%;">
                Tiếp
            </a>
        </div>
    </form>

    <table class="table table-bordered text-center">
        <thead>
            <tr>
                <th class="text-center align-middle">Ca học</th>
                <?php foreach ($daysOfWeek as $index => $day): ?>
                    <?php
                    // Lấy ngày cụ thể trong tuần
                    $currentDate = clone $startDate;
                    $currentDate->modify("+$index days");
                    $formattedDate = $currentDate->format('d/m/Y');
                    ?>
                    <th class="text-center align-middle">
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
                    <td class="text-center align-middle"><?php echo $shift; ?></td>
                    <?php foreach ($daysOfWeek as $index => $day): ?>
                        <?php
                        // Lấy ngày cụ thể trong tuần
                        $currentDate = clone $startDate;
                        $currentDate->modify("+$index days");
                        $formattedDate = $currentDate->format('Y-m-d');
                        ?>
                        <td class="text-center">
                            <?php if (isset($weeklySchedules[$formattedDate])): ?>
                                <div class="schedule-boxes">
                                    <?php foreach ($weeklySchedules[$formattedDate] as $schedule): ?>
                                        <?php if ($schedule['ca_hoc'] === $shift): ?>
                                            <div class="schedule-item border p-3 mb-2 bg-info-subtle">
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
